<template>
    <Layout>
        <PageLoading id="shops-on-map" :loading="page_loading">
            <div class="position-relative">
                <div id="map" class="rounded" style="height: 65vh">

                </div>

                <Card v-if="show_new_shop" class="p-2-5 new-shop" style="width: 200px">
                    <span class="fw-medium">{{ $t('add_new_shop') }}</span>
                    <div class="text-end mt-2">
                        <Button color="primary" variant="soft" @click="createShopAtPin">{{ $t('create') }}</Button>
                    </div>
                </Card>
                <Card v-if="selected_shop" class="mb-0 cursor-pointer selected-shop p-1-5" @click="goToShop(selected_shop)">
                    <div class="d-flex p-1-5 align-items-center">
                        <NetworkImage
                            :src="selected_shop.logo"
                            circular
                            class="img-fluid" height="44"/>
                        <div class="ms-2">
                            <h5 class="fw-medium mt-0 mb-1">{{ selected_shop.name }}</h5>
                            <div>
                                <StarRating :rating="selected_shop.rating" :size="14"/>
                                <span class="ms-1">
                                            ({{ getRatingText(selected_shop) }} - {{ selected_shop.ratings_count }}
                                    )</span>
                            </div>
                        </div>

                    </div>
                    <div class="position-absolute top-0 end-0 m-2">
                        <Icon icon="edit" size="18" color="success"></Icon>
                    </div>
                </Card>
                <div class="mt-3 pb-2">
                    <swiper
                        :freeMode="false"
                        :loop="false"
                        :modules="modules"
                        :navigation="true"
                        :slideToClickedSlide="true"
                        :slidesPerView="getShopsPerView"
                        :spaceBetween="24"
                        :watchSlidesProgress="true"
                        class="mySwiper"
                        @slideChange="slideChanged"
                        @swiper="setControlledSwiper"
                    >
                        <swiper-slide v-for="shop in shops" :key="shop.id">
                            <div class="nav-link" @click="tapOnShop(shop)">
                                <Card class="mb-0 cursor-pointer border shadow-none ">
                                    <div class="d-flex p-2 align-items-center">
                                        <NetworkImage
                                            :src="shop.logo"
                                            circular
                                            class="img-fluid" height="60"/>
                                        <div class="ms-2">
                                            <h5 class="fw-medium mt-0 mb-1">{{ shop.name }}</h5>
                                            <div>
                                                <StarRating :rating="shop.rating" :size="14"/>
                                                <span class="ms-1">
                                            ({{ getRatingText(shop) }} - {{ shop.ratings_count }} {{
                                                        this.$t('reviews')
                                                    }})</span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="position-absolute top-0 end-0 mt-1-5 me-1-5">
                                        <Button class="p-1 px-1-5" color="green" radius="sm" variant="soft"
                                                @click="goToShop(shop)">
                                            <Icon icon="edit" size="16"></Icon>
                                        </Button>
                                    </div>
                                </Card>
                            </div>
                        </swiper-slide>

                    </swiper>
                </div>
            </div>

        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent, ref} from "vue";
import {IShop, Shop} from "@js/types/models/shop";
import {IData} from "@js/types/models/data";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import {IBreadcrumb, ILatLng} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Icon from "@js/components/Icon.vue";
import Button from "@js/components/buttons/Button.vue";
import {useLayoutStore} from "@js/services/state/states";
import {MapUtil} from "@js/shared/utils";
import {Swiper, SwiperSlide} from 'swiper/vue';
import 'swiper/css';
import "swiper/css/free-mode"
import "swiper/css/thumbs"
import {Controller, FreeMode, Navigation, Thumbs, Zoom} from 'swiper';
import PageLoading from "@js/components/PageLoading.vue";
import NetworkImage from "@js/components/NetworkImage.vue";
import StarRating from "@js/components/shared/StarRating.vue";
import {array_get_index} from "@js/shared/array_utils";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";

let marker = null;

export default defineComponent({
    components: {
        EditActionButton,
        StarRating,
        NetworkImage, PageLoading, Button, Icon, Card, CardBody, Table, PageHeader, Swiper, SwiperSlide, Layout,
    },
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            modules: [Zoom, FreeMode, Navigation, Controller, Thumbs],
            shops: [] as IShop[],
            map: null as google.maps.Map | null,
            markers: [] as google.maps.Marker[],
            swiper_controller: ref(null),
            marker: null as google.maps.Marker,
            show_new_shop: false,
            selected_shop: null as IShop,
        }
    },
    computed: {
        getShopsPerView() {
            const width = window.screen.width;
            return width > 1440 ? 4.5 : width > 996 ? 3 : width > 576 ? 2 : 1;
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("shops"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        setControlledSwiper(swiper) {
            this.swiper_controller = swiper;
        },
        slideChanged() {
            this.tapOnShop(this.shops[this.swiper_controller.realIndex]);
        },
        async getShops() {
            this.page_loading = true;
            try {
                const response = await Request.getAuth<IData<IShop[]>>(adminAPI.shops.get, {forced: true});
                this.shops = response.data.data;

            } catch (error) {
                handleException(error);
            } finally {
                this.page_loading = false;
            }
        },
        getRatingText(item: IShop): string {
            return (item.rating).toFixed(1);
        },
        setupMap() {
            const self = this;
            let center = {lat: -34.397, lng: 150.644};
            if (this.shops.length > 0) {
                center = {lat: this.shops[0].latitude, lng: this.shops[0].longitude}
            }
            this.map = new google.maps.Map(document.getElementById('map'), {
                center: center,
                zoom: 17,
            });
            google.maps.event.addListener(this.map, "click", function (event) {
                self.addShopToMap({
                    latitude: event.latLng.lat(),
                    longitude: event.latLng.lng()
                });
                self.$emit('change-location', {latitude: event.latLng.lat(), longitude: event.latLng.lng()})
            });

            let store = useLayoutStore();


            let changeMapTheme = () => {
                if (store.layout.is_dark) {
                    self.map.setOptions({
                        styles: MapUtil.getDarkStyle()
                    })
                } else {
                    self.map.setOptions({
                        styles: null
                    })
                }
            }
            store.$subscribe((mut, state) => {
                changeMapTheme();
            });
            changeMapTheme();


            for (let shop of this.shops) {
                this.setMarkerToMap(shop)
            }

            // if (this.shops.length > 0) {
            //     this.map.setZoom(7.5);
            //     this.map.panTo({
            //         lat: this.shops[0].latitude,
            //         lng: this.shops[0].longitude,
            //     });
            // }
        },
        addShopToMap(latLng: ILatLng) {
            const pos = {
                lat: latLng.latitude,
                lng: latLng.longitude,
            };
            if (marker != null) {
                marker.setMap(null);
            }
            marker = new google.maps.Marker({
                position: pos,
                map: this.map,
                icon: {
                    url: '/assets/images/map/add_shop_pin.png',
                    scaledSize: new google.maps.Size(50, 50),
                },
                animation: 2,
                opacity: 1
            });
            const zoom = this.map.getZoom();
            this.map.setZoom(zoom > 17 ? zoom : 17);
            this.map.panTo(pos);
            this.show_new_shop = true;
        },
        setMarkerToMap(shop: IShop) {
            if (!shop.latitude || !shop.longitude) {
                // if (marker) {
                //     marker.setMap(null);
                //     marker = null;
                // }
                return;
            }

            const pos = {
                lat: parseFloat(String(shop.latitude)),
                lng: parseFloat(String(shop.longitude)),
            };


            let marker = new google.maps.Marker({
                position: pos,
                map: this.map,
                icon: {
                    url: Shop.getMapPinFromShop(shop),
                    scaledSize: new google.maps.Size(50, 50),

                }
            });
            const self = this;
            google.maps.event.addListener(marker, 'click', function () {
                self.tapOnShop(shop);
            });

            this.markers.push(marker);


        },
        tapOnShop(shop: IShop) {
            this.selected_shop = shop;
            let index = array_get_index(this.shops, shop);
            if (index != null) {
                this.swiper_controller.slideTo(index);
            }
            this.moveMapTo(shop)
        },
        moveMapTo(shop: IShop) {
            this.map.setZoom(17);
            const pos = {
                lat: parseFloat(String(shop.latitude)),
                lng: parseFloat(String(shop.longitude)),
            };
            this.map.panTo(pos);

            this.show_new_shop = false;

        },
        createShopAtPin() {
            if (marker != null) {
                this.$router.push({
                    name: 'admin.shops.create',
                    query: {lat: marker.position.lat(), lng: marker.position.lng()}
                })
            }
        },
        goToShop(shop: IShop) {
            this.$router.push({name: 'admin.shops.edit', params: {id: shop.id}})
        }
    },
    async mounted() {
        this.setTitle(this.$t('shops_on_map'))
        await this.getShops();
        this.setupMap()
    },
    beforeUnmount() {
        marker = null;
    }
});

</script>
<style scoped>
.new-shop {
    position: absolute;
    top: 44%;
    margin-left: 24px;
    margin-top: 16px;
    left: 56%;
    transform: translate(-50%, -50%) !important;
}

.selected-shop{
    position: absolute;
    top: 40%;
    margin-left: 144px;
    margin-top: 28px;
    left: 50%;
    transform: translate(-50%, -50%) !important;
}
</style>
