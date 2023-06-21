<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_shop_payout')"/>

        <Row>
            <Col xl="6">
                <Card>
                    <CardHeader :title="$t('filter')" icon="screen_search_desktop"></CardHeader>

                    <CardBody>

                        <Row>
                            <Col lg="6">

                                <FormSelect :data="shops" :errors="errors" :helper="shop_select_helper"
                                            :label="$t('shop')"
                                            name="shop_id"
                                            :on-selected="onChangeShop" :selected="selected_shop"/>

                            </Col>
                            <Col lg="6">

                                <DateTimePicker v-model="till_date" :errors="errors" :label="$t('till_date')"
                                                name="till_date"/>

                            </Col>
                        </Row>


                        <div class="text-end">
                            <LoadingButton :loading="loading" flexed-icon icon="search" @click="filter">
                                {{ $t('search') }}
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
                                                                  style="width: 180px;">{{ $t('pay') }}
                                    <InfoTooltip class="ms-1" tooltip="What amount need to pay to shop"></InfoTooltip>
                                </span> <span
                                    class="fw-medium">{{ getFormattedCurrency(getTotalPayAmount) }}</span></p>
                                <p class="mb-1 text-nowrap"><span class="d-inline-block"
                                                                  style="width: 180px;">{{ $t('take') }}
                                    <InfoTooltip class="ms-1"
                                                 tooltip="What amount take from shop"></InfoTooltip>

                                </span> <span
                                    class="fw-medium">{{ getFormattedCurrency(getTotalTakeAmount) }}</span></p>

                                <hr class="dashed my-2" style="width: 200px;margin-left: 10px">

                                <div class="mt-1 font-15 fw-medium text-nowrap"><span class="d-inline-block"
                                                                                      style="width: 180px;">{{
                                        getPayableAmount > 0 ? $t('total_pay_to_shop') : $t('total_take_from_shop')
                                    }}
                                     <InfoTooltip class="ms-1"
                                                  tooltip="Calculated amount based on what you take and give to shop"></InfoTooltip>
                                </span>
                                    <span
                                        class="fw-semibold font-16">{{
                                            getFormattedCurrency(Math.abs(getPayableAmount))
                                        }}</span></div>

                            </Col>
                            <Col class="ps-3" lg="6">
                                <FormInput v-model="amount" :errors="errors" :label="$t('amount')" name="amount">
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="You need to pay manually and add entry here"></InfoTooltip>
                                    </template>

                                </FormInput>
                                <div class="text-end">
                                    <CreateButton :loading="loading" @click="create"/>
                                </div>
                            </Col>
                        </Row>




                    </CardBody>
                </Card>
            </Col>

            <Col lg="12">
                <Card>
                    <CardHeader :title="$t('shop_revenue')" icon="payments" type="msr"></CardHeader>
                        <Table :data="shop_revenues" :loading="table_loading" :option="table_header" remove-top-action>

                            <template #shop_payout_id="data">
                                <span v-if="data.row.shop_payout_id==null" class="text-danger">{{
                                        $t('not_paid')
                                    }}</span>
                                <span v-else class="text-success">{{ $t('paid') }}</span>
                            </template>
                            <template #order_id="data">

                                <router-link :to="{name: 'admin.orders.edit', params: {id:data.value}}">#{{ data.value }}</router-link>
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
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import {IShop} from "@js/types/models/shop";
import {IShopRevenue} from "@js/types/models/revenue";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import Table from "@js/components/Table.vue";
import {IBreadcrumb, IFormSelectOption, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        CreateButton,
        Table,
        DateTimePicker,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin, AdminModuleSelectorMixin],
    data() {
        return {
            loading: false,
            table_loading: false,
            shops: [] as IShop[],
            selected_shop: null,
            shop_revenues: [] as IShopRevenue[],
            till_date: new Date().toISOString(),
            amount: null
        }
    },
    computed: {
        shop_select_helper(): IFormSelectOption {
            return {
                option: {
                    value: 'id',
                    label: 'name'
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('shop_payouts'),
                    route: 'admin.shop_payouts.index',
                },
                {
                    text: this.$t("create"),
                    active: true,
                },
            ];
        },
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
                        label: this.$t('pay_to_shop'),
                        field: 'pay_to_shop',
                        sort: {
                            compare: (a, b) => a.take_from_admin - b.take_from_admin
                        },
                        data: (rev) => rev.take_from_admin == 0 ? "-" : this.getFormattedCurrency(rev.take_from_admin)
                    },
                    {
                        label: this.$t('take_from_shop'),
                        field: 'take_from_shop',
                        sort: {
                            compare: (a, b) => a.pay_to_admin - b.pay_to_admin
                        },
                        data: (rev) => rev.pay_to_admin == 0 ? "-" : this.getFormattedCurrency(rev.pay_to_admin)
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
                    print: {
                        enable: true,
                        auto: true
                    }
                },
                onRefresh: this.filter,
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_shop_revenues')
                }
            };
        },
        getTotalPayAmount() {
            let amount = 0;
            this.shop_revenues.forEach(function (revenue) {
                if (revenue.shop_payout_id == null) {
                    amount += revenue.take_from_admin;
                }
            });
            return amount;
        },
        getTotalTakeAmount() {
            let amount = 0;
            this.shop_revenues.forEach(function (revenue) {
                if (revenue.shop_payout_id == null) {
                    amount += revenue.pay_to_admin;
                }
            });
            return amount;
        },
        getPayableAmount() {
            let amount = 0;
            this.shop_revenues.forEach(function (revenue) {
                if (revenue.shop_payout_id == null) {
                    amount += revenue.take_from_admin;
                    amount -= revenue.pay_to_admin;
                }
            });
            return amount;
        }
    },
    methods: {
        async filter() {
            try {
                this.clearAllError();
                if (this.selected_shop == null) {
                    this.addError('shop_id', this.$t('please_choose_shop'))
                }
                if (this.till_date == null) {
                    this.addError('till_date', this.$t('please_select_till_date'))
                }
                if (this.hasAnyError())
                    return;
                this.table_loading = true;
                let url = adminAPI.shop_revenues.get + "?shop_id=" + this.selected_shop + "&till_date=" + this.till_date;
                const response = await Request.getAuth<IData<IShopRevenue[]>>(url);
                this.shop_revenues = response.data.data;
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
                if (this.selected_shop == null) {
                    this.addError('shop_id', this.$t('please_choose_shop'))
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
                const response = await Request.postAuth(adminAPI.shop_payouts.create, {
                    till_date: this.till_date,
                    shop_id: this.selected_shop,
                    amount: this.amount,
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('payout_is_created'));
                    RequestCache.removeSimilar(adminAPI.shop_payouts.get);
                    this.filter().then();
                    this.amount=0;

                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                console.info(error);
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        },
        async getShops(){
            try {
                const response = await Request.getAuth<IData<IShop[]>>(Request.addAdminModule(adminAPI.shops.get));
                this.shops = response.data.data;
            } catch (error) {
                handleException(error);
            }
        },
        onChangeAdminModule(module_id){
            this.getShops();
            this.selected_shop=null;
        },
        onChangeShop(id) {
            this.selected_shop = id;
        }
    },

    async mounted() {
        this.setTitle(this.$t('create_shop_payout'))
        await this.getShops();
    }
});

</script>
