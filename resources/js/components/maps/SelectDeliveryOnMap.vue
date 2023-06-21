<template>
    <div id="google_map">
        <div id="map" :style="{ height: height.toString()+`px` }" class="rounded"></div>
    </div>
    <Card v-if="selected_delivery" class="p-2 selected-delivery-boy-on-map" style="width: 200px">
        <span class="fw-medium">{{ selected_delivery.first_name + " " + selected_delivery.last_name }}</span>
        <p class="mb-1">~ {{getDistanceText(selected_delivery)}}</p>
        <div class="text-end">
            <Button color="primary" variant="soft" @click="()=>onSelectDelivery(selected_delivery)">{{
                    $t('assign')
                }}
            </Button>
        </div>
    </Card>
</template>


<script lang="ts">


// var script = document.createElement('script');
// script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap';
// script.async = true;
//
import {MapUtil} from "@js/shared/utils";
import {defineComponent, type PropType} from "vue";
import {useLayoutStore} from "@js/services/state/states";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import Button from "@js/components/buttons/Button.vue";
import {IShop, Shop} from "@js/types/models/shop";
import shop_revenues from "@js/pages/seller/shops/payments/shop_revenues.vue";


export default defineComponent({
    components: {Button, Card, CardBody},
    el: '#map',
    mixins: [UtilMixin],
    data() {
        return {
            map: null as google.maps.Map | null,
            selected_delivery: null as IDeliveryBoy
        }
    },
    props: {
        height: {
            type: Number,
            default: 250
        },
        delivery_boys: {
            type: Object as PropType<IDeliveryBoy[]>,
            required: true
        },
        shop: {
            type: Object as PropType<IShop>,
            required: true
        },

        onSelect: {
            type: Function as PropType<(id: number) => void>,
            required: true
        }
    },
    watch: {},
    mounted() {
        console.info(this.shop);
        let lastLocation = {lat: -34.397, lng: 150.644};
        if(this.shop){
            lastLocation = {lat: this.shop.latitude, lng: this.shop.longitude}
        }
        else if(this.delivery_boys.length > 0) {
            lastLocation = {lat: this.delivery_boys[0].latitude, lng: this.delivery_boys[0].longitude}
        }

        this.map = new google.maps.Map(document.getElementById('map'), {
            center: lastLocation,
            zoom: 15,
        });

        if(this.shop){
            this.setShopMarkerToMap(this.shop);
        }

        this.initTheme();

        for (const deliveryBoy of this.delivery_boys) {
            this.setDeliveryMarkerToMap(deliveryBoy);
        }

    },
    methods: {
        getDistance(delivery: IDeliveryBoy): number | null {
            if (delivery.latitude == null || delivery.longitude == null)
                return null;
            return MapUtil.calculateDistance({
                latitude: this.shop.latitude,
                longitude: this.shop.longitude,
            }, {
                latitude: delivery.latitude,
                longitude: delivery.longitude,
            })
        },
        getDistanceText(delivery: IDeliveryBoy): string {
            let distance = this.getDistance(delivery);
            if (distance != null) {
                if (distance > 1000) {
                    return this.getPrecise(distance/1000) + " " + this.$t('KM');
                } else {
                    return this.getPrecise(distance) + " " + this.$t('meter');
                }
            }
            return this.$t("unknown");
        },
        onSelectDelivery(delivery_boy: IDeliveryBoy) {
            if (this.onSelect) {
                this.onSelect(delivery_boy.id);
            }
        },
        initTheme() {
            const self = this;
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
        },
        clickOnDelivery(delivery_boy: IDeliveryBoy) {
            const zoom = this.map.getZoom();
            const pos = {
                lat: parseFloat(String(delivery_boy.latitude)),
                lng: parseFloat(String(delivery_boy.longitude)),
            };
            this.map.panTo(pos);
            this.map.setZoom(zoom > 18 ? zoom : 18);
            this.selected_delivery = delivery_boy;
        },
        setDeliveryMarkerToMap(deliveryBoy: IDeliveryBoy) {
            if (!deliveryBoy.latitude || !deliveryBoy.longitude) {
                return;
            }

            const pos = {
                lat: parseFloat(String(deliveryBoy.latitude)),
                lng: parseFloat(String(deliveryBoy.longitude)),
            };

            let marker = new google.maps.Marker({
                position: pos,
                map: this.map,
                icon: {
                    url: '/assets/images/map/delivery_boy_pin.png',
                    scaledSize: new google.maps.Size(32, 32),

                },
            });
            const self = this;
            google.maps.event.addListener(marker, 'click', function () {
                self.clickOnDelivery(deliveryBoy);
            });

            // this.markers.push(marker);


        },
        setShopMarkerToMap(shop: IShop) {
            console.info("Shop")
            if (!shop.latitude || !shop.longitude) {
                return;
            }


            const pos = {
                lat: parseFloat(String(shop.latitude)),
                lng: parseFloat(String(shop.longitude)),
            };
            new google.maps.Marker({
                position: pos,
                map: this.map,
                icon: {
                    url: Shop.getMapPinFromShop(shop),
                    scaledSize: new google.maps.Size(40, 40),

                },
            });
        },

    },
    beforeUnmount() {

    }
});

</script>

<style scoped>
.selected-delivery-boy-on-map {
    position: absolute;
    top: 50%;
    margin-left: 128px;
    margin-top: 24px;
    left: 50%;
    transform: translate(-50%, -50%) !important;
}
</style>
