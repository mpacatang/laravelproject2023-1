<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_delivery_boy_payout')"/>

        <Row>
            <Col xl="6">
                <Card>
                    <CardHeader :title="$t('filter')" icon="tune" type="msr"></CardHeader>

                    <CardBody>

                        <Row>
                            <Col lg="6">
                                <FormSelect :data="delivery_boys" :helper="delivery_boy_select_helper"
                                            :errors="errors"
                                            :label="$t('delivery_boy')"
                                            name="delivery_boy_id"
                                            :on-selected="onChangeDeliveryBoy" :selected="selected_delivery_boy"/>
                            </Col>
                            <Col lg="6">


                                <DateTimePicker v-model="till_date" :errors="errors" name="till_date" :label="$t('till_date')"/>

                            </Col>
                        </Row>

                        <div class="text-end">
                            <LoadingButton :loading="loading" flexed-icon icon="search" @click="filter">{{
                                    $t('search')
                                }}
                            </LoadingButton>
                        </div>


                    </CardBody>
                </Card>
            </Col>

            <Col xl="6">
                <Card :class="[{'item-disabled': getPayableAmount===0}]">

                    <CardHeader :title="$t('payout_entry')" icon="create_new_folder" type="msr"></CardHeader>
                    <CardBody>
                        <Row gap="3">
                            <Col lg="6">
                                <p class="mb-1 text-nowrap"><span class="d-inline-block"
                                                      style="width: 240px;">{{ $t('pay') }}
                                    <InfoTooltip class="ms-1" tooltip="What amount need to pay to delivery"></InfoTooltip>
                                </span> <span
                                    class="fw-medium">{{ getFormattedCurrency(getTotalPayAmount) }}</span></p>
                                <p class="mb-1 text-nowrap"><span class="d-inline-block"
                                                      style="width: 240px;">{{ $t('take') }}
                                    <InfoTooltip class="ms-1"
                                                 tooltip="What amount take from delivery"></InfoTooltip>

                                </span> <span
                                    class="fw-medium">{{ getFormattedCurrency(getTotalTakeAmount) }}</span></p>

                                <hr class="dashed my-2" style="width: 270px;margin-left: 10px">

                                <div class="mt-1 font-15 fw-medium text-nowrap"><span class="d-inline-block"
                                                                          style="width: 240px;">{{
                                        getPayableAmount > 0 ? $t('total_pay_to_delivery_boy') : $t('total_take_from_delivery_boy')
                                    }}
                                     <InfoTooltip class="ms-1"
                                                  tooltip="Calculated amount based on what you take and give to delivery"></InfoTooltip>
                                </span>
                                    <span
                                        class="fw-semibold font-16">{{
                                            getFormattedCurrency(Math.abs(getPayableAmount))
                                        }}</span></div>

                            </Col>
                            <Col lg="6">
                                <FormInput v-model="amount" :errors="errors" :label="$t('amount')" name="amount">
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="You need to pay manually and add entry here"></InfoTooltip>
                                    </template>

                                </FormInput>

                            </Col>
                        </Row>


                        <div class="text-end">
                            <CreateButton :loading="loading" @click="create"/>
                        </div>


                    </CardBody>
                </Card>
            </Col>

            <Col xl="12">
                <Card>
                    <CardHeader :title="$t('delivery_boy_revenue')" icon="account_balance_wallet"
                                type="msr"></CardHeader>
                    <Table :data="delivery_boy_revenues" :loading="table_loading" :option="table_header"
                           remove-top-action>
                        <template #delivery_boy_payout_id="data">
                                <span v-if="data.value==null" class="text-danger">{{
                                        $t('not_paid')
                                    }}</span>
                            <span v-else class="text-success">{{ $t('paid') }}</span>
                        </template>
                        <template #order_id="data">
                            #{{ data.value }}
                            <!--    TODO <router-link :to="{name: 'seller.orders.edit', params: {id:data.value}}">#{{ data.value }}</router-link>-->
                        </template>
                        <template #created_at="data">
                            <span>{{ getFormattedDateTime(data.value,) }}</span>
                        </template>
                    </Table>

                </Card>
            </Col>
        </Row>


    </Layout>
</template>


<script lang="ts">

import Layout from "../../layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import {IDeliveryBoyRevenue} from "@js/types/models/revenue";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import Table from "@js/components/Table.vue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import {DeliveryBoy, IDeliveryBoy} from "@js/types/models/delivery_boy";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        CreateButton,
        Table,
        DateTimePicker,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            loading: false,
            table_loading: false,
            delivery_boys: [] as IDeliveryBoy[],
            selected_delivery_boy: null,
            delivery_boy_revenues: [] as IDeliveryBoyRevenue[],
            till_date: new Date().toISOString(),
            amount: null
        }
    },
    computed: {
        delivery_boy_select_helper: DeliveryBoy.select_helper,
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('delivery_boy_payouts'),
                    route: 'seller.delivery_boy_payouts.index',
                },
                {
                    text: this.$t("create"),
                    active: true,
                },
            ];
        },
        table_header(): ITableOption<IDeliveryBoyRevenue> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                    },
                    {
                        label: this.$t('delivery_boy'),
                        field: 'delivery_boy',
                        data: (rev) => rev.delivery_boy.first_name + " " + rev.delivery_boy.last_name,
                        onClick: (rev) => this.$router.push({
                            name: 'seller.delivery_boys.edit',
                            params: {id: rev.delivery_boy_id}
                        })

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
                            compare: (a, b) => a.created_at > b.created_at ? 1 : -1
                        },
                        data: (rev) => this.getFormattedDateTime(rev.created_at),
                        width: 260
                    },
                ],
                onRefresh: this.filter,
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_payouts')
                }
            };
        },
        getTotalPayAmount() {
            let amount = 0;
            this.delivery_boy_revenues.forEach(function (revenue) {
                if (revenue.delivery_boy_payout_id == null) {
                    amount += revenue.take_from_shop;
                }
            });
            return amount;
        },
        getTotalTakeAmount() {
            let amount = 0;
            this.delivery_boy_revenues.forEach(function (revenue) {
                if (revenue.delivery_boy_payout_id == null) {
                    amount += revenue.pay_to_shop;
                }
            });
            return amount;
        },
        getPayableAmount() {
            let amount = 0;
            this.delivery_boy_revenues.forEach(function (revenue) {
                if (revenue.delivery_boy_payout_id == null) {
                    amount += revenue.take_from_shop;
                    amount -= revenue.pay_to_shop;
                }
            });
            return amount;
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
                    "Payout": revenues.delivery_boy_payout_id!=null?"Paid":"Not Paid",
                    "Time": self.getFormattedDateTime(revenues.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Delivery Revenues'
            };
        },
        async filter() {
            try {
                this.clearAllError();
                if (this.selected_delivery_boy == null) {
                    this.addError('delivery_boy_id', this.$t('please_choose_delivery_boy'))
                }
                if (this.till_date == null) {
                    this.addError('till_date', this.$t('please_select_till_date'))
                }
                if (this.hasAnyError())
                    return;
                this.table_loading = true;
                let url = sellerAPI.delivery_boy_revenues.get + "?delivery_boy_id=" + this.selected_delivery_boy + "&till_date=" + this.till_date;
                const response = await Request.getAuth<IData<IDeliveryBoyRevenue[]>>(url);
                this.delivery_boy_revenues = response.data.data;
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.table_loading = false;
        },
        async create() {
            try {
                this.clearAllError();
                if (this.selected_delivery_boy == null) {
                    this.addError('delivery_boy_id', this.$t('please_choose_delivery_boy'))
                }
                if (this.till_date == null) {
                    this.addError('till_date', this.$t('please_select_till_date'))
                }
                if (this.amount == null) {
                    this.addError('amount', this.$t('please_enter_amount'))
                }
                if (this.hasAnyError())
                    return;
                this.loading = true;
                const response = await Request.postAuth(sellerAPI.delivery_boy_payouts.create, {
                    till_date: this.till_date,
                    delivery_boy_id: this.selected_delivery_boy,
                    amount: this.amount,
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('payout_is_created'));
                    RequestCache.removeSimilar(sellerAPI.delivery_boys.get);
                    NavigatorService.goBackOrRoute({name: 'seller.delivery_boy_payouts.index'});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        },
        onChangeDeliveryBoy(id) {
            this.selected_delivery_boy = id;
        }
    },

    async mounted() {
        this.setTitle(this.$t('create_delivery_boy_payout'));
        try {
            const response = await Request.getAuth<IData<IDeliveryBoy[]>>(sellerAPI.delivery_boys.get);
            this.delivery_boys = response.data.data;

        } catch (error) {
            handleException(error);
        }
    }
});

</script>
