<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('delivery_boys')"/>

        <Card>
            <Table :data="delivery_boys" :option="table_header" auto-translate>
                <template #actions="data">
                    <LoadingButton variant="soft" @click="assignDeliveryBoy(data.row.id)">{{
                            $t('assign')
                        }}
                    </LoadingButton>
                </template>
                <template #avatar="data">
                    <NetworkImage :size="44" :src="data.row.avatar" rounded/>
                </template>
                <template #table-actions="data">
                    <Button class="p-1-5 px-2" color="orange" flexed-icon variant="soft" @click="showMap">
                        <Icon class="me-2" icon="local_shipping" size="18"></Icon>
                        {{ $t('search_on_map') }}
                    </Button>
                </template>
            </Table>

        </Card>

        <VModal v-if="delivery_boys.length>0 && shop" v-model="show_delivery_on_map" xl>
            <SelectDeliveryOnMap :delivery_boys="delivery_boys" :on-select="onDeliverySelect"
                                 height="800" :shop="shop"/>
        </VModal>


    </Layout>
</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/seller/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {ToastNotification} from "@js/services/toast_notification";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import NetworkImage from "@js/components/NetworkImage.vue";
import {MapUtil} from "@js/shared/utils";
import {IOrder} from "@js/types/models/order";
import Card from "@js/components/Card.vue";
import VModal from "@js/components/VModal.vue";
import SelectDeliveryOnMap from "@js/components/maps/SelectDeliveryOnMap.vue";
import Button from "@js/components/buttons/Button.vue";
import Icon from "@js/components/Icon.vue";
import {IShop} from "@js/types/models/shop";
import {useSellerDataStore} from "@js/services/state/states";


export default defineComponent({
    components: {Icon, Button, SelectDeliveryOnMap, VModal, Card, NetworkImage, LoadingButton, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            id: this.$route.params.id,
            delivery_boys: [] as IDeliveryBoy[],
            order: null as IOrder,
            show_delivery_on_map: true,
            shop: {} as IShop

        }
    },


    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [{
                text: this.$t('delivery_boy'),
                active: true,
            }];
        },
        table_header(): ITableOption<IDeliveryBoy> {
            return {
                columns: [
                    {
                        label: this.$t('id'),
                        field: 'id',
                        width: 70

                    },
                    {
                        label: this.$t('avatar'),
                        field: 'avatar',
                        width: 120
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        data: (db) => db.first_name + " " + db.last_name,
                        sort: true

                    },
                    {
                        label: this.$t('mobile_number'),
                        field: 'mobile_number',
                    },
                    {
                        label: this.$t('how_far'),
                        field: 'how_far',
                        data: (del) => "~ "+ this.getDistanceText(del),
                        sort: {
                            compare: (a, b) => this.getDistance(b) - this.getDistance(a)
                        }
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 200

                    },
                ],
                exports: {
                    print: {
                        enable: true,
                        auto: true
                    }
                },
                onRefresh: this.getDeliveryBoys,
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_delivery_boy')
                }
            };
        },
    },
    methods: {
        showMap() {
            this.show_delivery_on_map = true;
        },
        onDeliverySelect(id) {
            this.show_delivery_on_map = false;
            this.assignDeliveryBoy(id);
        },
        getDistance(delivery: IDeliveryBoy): number | null {
            if (delivery.latitude == null || delivery.longitude == null)
                return null;
            return MapUtil.calculateDistance({
                latitude: this.order.shop.latitude,
                longitude: this.order.shop.longitude,
            }, {
                latitude: delivery.latitude,
                longitude: delivery.longitude,
            })
        },
        getDistanceText(delivery: IDeliveryBoy): string {
            let distance = this.getDistance(delivery);
            if (distance != null) {
                if (distance > 1000) {
                    return this.getPrecise(distance / 1000) + " " + this.$t('KM');
                } else {
                    return this.getPrecise(distance) + " " + this.$t('meter');
                }
            }
            return this.$t("unknown");
        },
        async getDeliveryBoys() {
            try {
                const orderResponse = await Request.getAuth<IData<IOrder>>(sellerAPI.orders.show(this.id), {forced: true});
                this.order = orderResponse.data.data;

                const response = await Request.getAuth<IData<IDeliveryBoy>>(sellerAPI.orders.assign_delivery(this.id), {forced: true});
                this.delivery_boys = response.data.data;
            } catch (error) {
                handleException(error);
                this.$router.go(-1);
            }
        },
        async assignDeliveryBoy(id) {
            try {
                const body = {
                    'delivery_boy_id': id
                }
                await Request.postAuth(sellerAPI.orders.assign_delivery(this.id), body);
                ToastNotification.success(this.$t('delivery_boy_assigned'));
                this.$router.go(-1);
            } catch (error) {
                handleException(error);
            }
        },

    },
    async mounted() {
        this.setTitle(this.$t('assign_delivery_boy'));
        const store = useSellerDataStore();
        this.shop = store.shop;
        store.$subscribe((mut, state)=>{
            this.shop = state.shop;
        })

        await this.getDeliveryBoys();
    }
});

</script>
