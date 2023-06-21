<template>


    <Card>
        <Table :data="shop_revenues" :loading="table_loading" :option="table_header">
            <template #shop_payout_id="data">
                <span v-if="data.row.shop_payout_id==null" class="text-danger">{{ $t('not_paid') }}</span>
                <span v-else class="text-success">{{ $t('paid') }}</span>
            </template>
            <template #order_id="data">
                <router-link :to="{name: 'admin.orders.edit', params: {id:data.value}}">#{{ data.value }}</router-link>
            </template>
        </Table>

    </Card>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IData} from "@js/types/models/data";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import Icon from "@js/components/Icon.vue";
import PageHeader from "@js/components/PageHeader.vue";
import {IShopRevenue} from "@js/types/models/revenue";


export default defineComponent({
    components: {EditActionButton, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    props: {
        id: {
            type: Number,
            required: true
        },
    },
    data() {
        return {
            table_loading: true,
            shop_revenues: [] as IShopRevenue[],
        }
    },
    computed: {
        table_header(): ITableOption<IShopRevenue> {
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
                        label: this.$t('shop'),
                        field: 'shop',
                        data: (rev) => rev.shop.name
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
                        sort: true
                    },
                    {
                        label: this.$t('payout'),
                        field: 'shop_payout_id',
                    },
                    {
                        label: this.$t('date_&_time'),
                        field: 'created_at',
                        sort: {
                            compare: (a, b) => a.created_at > b.created_at ? 1 : -1
                        },
                        data: (rev) => this.getFormattedDateTime(rev.created_at),
                        width: 260
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: this.getRevenue,
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_shop_revenues')
                }
            };
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
        generateData(data: IShopRevenue[]) {
            let list = [];
            const self = this;
            data?.forEach((revenue) => {
                list.push({
                    'ID': revenue.id,
                    "Order Id": revenue.order_id,
                    "Revenue": self.getFormattedCurrency(revenue.revenue),
                    "Shop ID": revenue.shop_id,
                    "Shop Name": revenue.shop?.name,
                    "Created At": self.getFormattedDateTime(revenue.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Shop Revenues'
            };
        },
        async getRevenue() {
            this.table_loading = true;

            try {
                const response = await Request.getAuth<IData<IShopRevenue[]>>(adminAPI.shops.revenues(this.id));
                this.shop_revenues = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
        }
    },
    mounted() {
        this.setTitle(this.$t('shop_revenues'))
        this.getRevenue();
    }
});

</script>
