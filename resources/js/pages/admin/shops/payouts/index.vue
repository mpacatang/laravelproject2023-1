<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('shop_payouts')"/>
        <Card>
            <Table :data="payouts" :loading="table_loading" :option="table_header">

                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.shop_payouts.create' }" >
                        <CreateButton/>
                    </router-link>
                </template>
            </Table>

        </Card>

    </Layout>
</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Icon from "@js/components/Icon.vue";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {IShopPayout} from "@js/types/models/revenue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import Card from "@js/components/Card.vue";
import Button from "@js/components/buttons/Button.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";


export default defineComponent({
    components: {CreateButton, Button, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin, AdminModuleSelectorMixin],
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
                        label: this.$t('shop'),
                        field: 'shop',
                        data: (pay) => pay.shop.name,
                        onClick: (pay) => this.$router.push({name: 'admin.shops.edit', params: {id: pay.shop_id}})
                    },
                    {
                        label: this.$t('pay_to_shop'),
                        field: 'pay_to_shop',
                        sort: {
                            compare: (a, b) => a.pay_to_shop - b.pay_to_shop
                        },
                        data: (rev) => this.getFormattedCurrency(rev.pay_to_shop)
                    },
                    {
                        label: this.$t('take_from_shop'),
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
                    "Shop": payout.shop?.name,
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
                const response = await Request.getAuth<IData<IShopPayout[]>>(Request.addAdminModule(adminAPI.shop_payouts.get), {forced: forced});
                this.payouts = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        },
        onChangeAdminModule(module_id){
            this.getPayouts();
        }
    },
    mounted() {
        this.setTitle(this.$t('shop_payouts'))
        this.getPayouts();
    }
});

</script>
