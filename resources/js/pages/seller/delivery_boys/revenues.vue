<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('delivery_boy_revenues')"/>
        <Card>
            <Table :data="delivery_boy_revenues" :loading="table_loading" :option="table_header">

                <template #delivery_boy_payout_id="data">
                    <span v-if="data.row.delivery_boy_payout_id==null" class="text-danger">{{ $t('not_paid') }}</span>
                    <span v-else class="text-success">{{ $t('paid') }}</span>
                </template>
                <template #order_id="data">
                    <router-link :to="{name: 'seller.orders.edit', params: {id:data.value}}">#{{
                            data.value
                        }}
                    </router-link>
                </template>
            </Table>

        </Card>

    </Layout>
</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/seller/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Icon from "@js/components/Icon.vue";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {IDeliveryBoyRevenue} from "@js/types/models/revenue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import {dateCompareFromString} from "@js/shared/utils";


export default defineComponent({
    components: {Card, CardBody, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            delivery_boy_revenues: [] as IDeliveryBoyRevenue[],
        }
    },
    computed: {
        table_header(): ITableOption<IDeliveryBoyRevenue> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        labelStyle: {
                            fontWeight: "medium"
                        },
                        width: 150
                    },
                    {
                        label: this.$t('delivery_boy'),
                        field: 'delivery_boy',
                        data: (rev) => rev.delivery_boy.first_name + " " + rev.delivery_boy.last_name
                    },
                    {
                        label: this.$t('revenue'),
                        field: 'revenue',
                        sort: {
                            compare: (a, b) => a.revenue - b.revenue
                        },
                        data: (rev) => this.getFormattedCurrency(rev.revenue)
                    },
                    {
                        label: this.$t('order_id'),
                        field: 'order_id',
                    },
                    {
                        label: this.$t('payout'),
                        field: 'delivery_boy_payout_id',
                    },

                    {
                        label: this.$t('date_&_time'),
                        field: 'created_at',

                        sort: {
                            compare: (a, b) => dateCompareFromString(a.created_at, b.created_at)
                        },
                        data: (rev) => this.getFormattedDateTime(rev.created_at),
                        width: 260
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: () => this.getRevenues(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_revenue')
                }
            }
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('revenues'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IDeliveryBoyRevenue[]) {
            let list = [];
            const self = this;
            data?.forEach((revenues) => {
                list.push({
                    'ID': revenues.id,
                    "Delivery": revenues.delivery_boy.first_name + " " + revenues.delivery_boy.last_name,
                    "Revenue": revenues.revenue,
                    "Order ID": revenues.order_id,
                    "Payout": revenues.delivery_boy_payout_id != null ? "Paid" : "Not Paid",
                    "Time": self.getFormattedDateTime(revenues.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Delivery Revenues'
            };
        },
        async getRevenues(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IDeliveryBoyRevenue[]>>(sellerAPI.delivery_boy_revenues.get, {forced: forced});
                this.delivery_boy_revenues = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;

            }
        }
    },
    mounted() {
        this.setTitle(this.$t('delivery_boy_revenues'))
        this.getRevenues();
    }
});

</script>
