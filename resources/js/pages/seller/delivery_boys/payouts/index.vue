<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('delivery_boy_payouts')"/>
        <Card>
            <Table :data="payouts" :loading="table_loading" :option="table_header">
                <template #delivery_boy="data">
                    <router-link :to="{name: 'seller.delivery_boys.edit', params: {id:data.value?.id}}">
                        {{data.value.first_name}} {{data.value.last_name}}
                    </router-link>

                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'seller.delivery_boy_payouts.create' }">
                        <CreateButton/>
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
import {IDeliveryBoyPayout} from "@js/types/models/revenue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import Button from "@js/components/buttons/Button.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";


export default defineComponent({
    components: {CreateButton, Button, Card, CardBody, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            payouts: [] as IDeliveryBoyPayout[],
        }
    },
    computed: {
        table_header(): ITableOption<IDeliveryBoyPayout> {
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
                    },
                    {
                        label: this.$t('pay_to_delivery_boy'),
                        field: 'pay_to_delivery_boy',
                        sort: {
                            compare: (a, b) => a.take_from_shop - b.take_from_shop
                        },
                        data: (rev) => this.getFormattedCurrency(rev.take_from_shop)
                    },
                    {
                        label: this.$t('take_from_delivery_boy'),
                        field: 'take_from_delivery_boy',
                        sort: {
                            compare: (a, b) => a.pay_to_shop - b.pay_to_shop
                        },
                        data: (rev) => this.getFormattedCurrency(rev.pay_to_shop)
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
                        label: this.$t('payed_at'),
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
                    message: this.$t('there_is_no_any_payout')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('payouts'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IDeliveryBoyPayout[]) {
            let list = [];
            const self = this;
            data?.forEach((payout) => {
                list.push({
                    'ID': payout.id,
                    "Delivery": payout.delivery_boy.first_name + " " + payout.delivery_boy.last_name,
                    // "Amount": payout.amount,
                    "Pay to delivery": payout.take_from_shop,
                    "Take from delivery": payout.pay_to_shop,
                    "Till Date": self.getFormattedDateTime(payout.till_date),
                    "Payed At": self.getFormattedDateTime(payout.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Delivery Payouts'
            };
        },
        async getPayouts(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IDeliveryBoyPayout[]>>(sellerAPI.delivery_boy_payouts.get, {forced: forced});
                this.payouts = response.data.data;

            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('delivery_boy_payouts'))
        this.getPayouts();
    }
});

</script>
