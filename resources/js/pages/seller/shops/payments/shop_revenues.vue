<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('shop_revenues')"/>
        <Card>
            <Table :data="revenues" :loading="table_loading" :option="table_header">

                <template #shop_payout_id="data">
                    <span v-if="data.row.shop_payout_id==null" class="text-danger">{{ $t('not_paid') }}</span>
                    <span v-else class="text-success">{{ $t('paid') }}</span>
                </template>
                <template #order_id="data">
                    <router-link :to="{name: 'seller.orders.edit', params: {id:data.value}}">#{{ data.value }}
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
import {IShopRevenue} from "@js/types/models/revenue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";


export default defineComponent({
    components: {CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            revenues: [] as IShopRevenue[],
        }
    },
    computed: {
        table_header(): ITableOption<IShopRevenue> {
            return {
                columns: [
                    {
                        label: 'ID',
                        field: 'id',
                        labelStyle: {
                            fontWeight: "medium"
                        },
                        width: 150
                    },
                    {
                        label: this.$t('revenue'),
                        field: 'revenue',
                        sort: {
                            compare: (a, b) => b.revenue - a.revenue
                        },
                        data: (rev) => this.getFormattedCurrency(rev.revenue)
                    },
                    {
                        label: this.$t('pay_to_admin'),
                        field: 'pay_to_admin',
                        sort: {
                            compare: (a, b) => b.pay_to_admin - a.pay_to_admin
                        },
                        data: (rev) => this.getFormattedCurrency(rev.pay_to_admin)

                    },
                    {
                        label: this.$t('take_from_admin'),
                        field: 'take_from_admin',
                        sort: {
                            compare: (a, b) => b.take_from_admin - a.take_from_admin
                        },
                        data: (rev) => this.getFormattedCurrency(rev.take_from_admin)

                    },
                    {
                        label: this.$t('order_id'),
                        field: 'order_id',
                        sort: {
                            compare: (a, b) => b.order_id - a.order_id
                        },
                    },
                    {
                        label: this.$t('payout'),
                        field: 'shop_payout_id',
                    },
                    {
                        label: this.$t('date_&_time'),
                        field: 'created_at',
                        sort: {
                            compare: (a, b) => Date.parse(b.created_at) - Date.parse(a.created_at)
                        },
                        data: (rev) => this.getFormattedDateTime(rev.created_at),
                        width: 250
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
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('shop_revenues'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IShopRevenue[]) {
            let list = [];
            const self = this;
            data?.forEach((revenue) => {
                list.push({
                    'ID': revenue.id,
                    "Order Id": revenue.order_id,
                    "Revenue": self.getFormattedCurrency(revenue.revenue),
                    "Created At": self.getFormattedDateTime(revenue.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Shop Revenues'
            };
        },

        async getRevenues(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IShopRevenue>>(sellerAPI.revenues.get, {forced: forced});
                this.revenues = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('shop_revenues'))
        this.getRevenues();
    }
});

</script>
