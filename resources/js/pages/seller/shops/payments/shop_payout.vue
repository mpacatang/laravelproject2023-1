<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('shop_payout')"/>
        <Card>
            <Table :data="payouts" :loading="table_loading" :option="table_header">
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
import {IShopPayout} from "@js/types/models/revenue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import Card from "@js/components/Card.vue";


export default defineComponent({
    components: {Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            payouts: [] as IShopPayout[],
        }
    },
    computed: {
        table_header(): ITableOption<IShopPayout> {
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
                        label: this.$t('taken_from_admin'),
                        field: 'pay_to_shop',
                        sort: {
                            compare: (a, b) => a.pay_to_shop - b.pay_to_shop
                        },
                        data: (rev) => this.getFormattedCurrency(rev.pay_to_shop)
                    },
                    {
                        label: this.$t('paid_to_admin'),
                        field: 'take_from_shop',
                        sort: {
                            compare: (a, b) => a.take_from_shop - b.take_from_shop
                        },
                        data: (rev) => this.getFormattedCurrency(rev.take_from_shop)
                    },
                    {
                        label: this.$t('till_date'),
                        field: 'till_date',
                        sort: {
                            compare: (a, b) => a.till_date > b.till_date ? 1 : -1
                        },
                        data: (rev) => this.getFormattedDateTime(rev.till_date),
                        width: 260
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
                onRefresh: () => this.getPayouts(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_payouts')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('shop_payouts'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IShopPayout[]) {
            let list = [];
            const self = this;
            data?.forEach((payout) => {
                list.push({
                    'ID': payout.id,
                    // "Amount": payout.amount,
                    "Till Date": self.getFormattedDateTime(payout.till_date),
                    "Payed At": self.getFormattedDateTime(payout.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Shop Payouts'
            };
        },
        async getPayouts(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IShopPayout>>(sellerAPI.payouts.get, {forced: forced});
                this.payouts = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('shop_payouts'));
        this.getPayouts();
    }
});

</script>
