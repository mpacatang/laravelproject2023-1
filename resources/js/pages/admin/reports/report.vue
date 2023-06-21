<template>
    <Layout id="admin-report">
        <PageHeader :items="breadcrumb_items" :title="$t('report')"/>

        <PageLoading :loading="page_loading">
            <Card>
                <CardHeader :title="$t('filter')" icon="screen_search_desktop"></CardHeader>

                <CardBody class="pb-0">

                    <Row>
                        <Col xxl="3" xl="6">

                            <FormSelect :data="shops" :errors="errors" :helper="shop_helper"
                                        :label="$t('shop')"
                                        :on-selected="onChangeShop"
                                        :selected="selected_shop" name="shop_id"/>

                        </Col>
                        <Col xxl="3" xl="6">

                            <DateTimePicker v-model="start_date" :errors="errors" :label="$t('start_date')"
                                            name="start_date"/>

                        </Col>
                        <Col xxl="3" xl="6">

                            <DateTimePicker v-model="end_date" :errors="errors" :label="$t('end_date')"
                                            name="end_date"/>

                        </Col>
                        <Col class="text-end mt-3" xxl="3" xl="6">
                            <LoadingButton :loading="loading" color="bluish-purple" flexed-icon icon="event_available"
                                           icon-size="18"
                                           size="md" variant="soft" @click="todayFilter">
                                {{ $t('today_report') }}
                            </LoadingButton>
                            <LoadingButton :loading="loading" class="ms-2" flexed-icon icon="tune" icon-size="18"
                                           size="md" @click="filter">
                                {{ $t('filter') }}
                            </LoadingButton>
                        </Col>
                    </Row>


                    <!--                        <div class="text-end mt-3">-->
                    <!--                            <LoadingButton :loading="loading" @click="create">{{ $t('create') }}</LoadingButton>-->
                    <!--                        </div>-->


                    <div class="text-end">

                    </div>


                </CardBody>
            </Card>

            <Row>
                <Col xl="3" lg="6">
                    <Card class="data-card">
                        <CardBody>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-indigo text-truncate mb-0 ls-0-5">
                                        {{ $t('revenues') }}</p>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0 mt-1-5">
                                        {{
                                            getFormattedCurrency(getTotalRevenues)
                                        }}</h4>
                                </div>
                                <VItem class="p-2" color="indigo" radius="4" soft>
                                    <Icon class="data-icon" icon="account_balance_wallet" size="26" type="msr"/>
                                </VItem>
                            </div>
                        </CardBody><!-- end card body -->
                    </Card>
                </Col>
                <Col xl="3" lg="6">
                    <Card class="data-card">
                        <CardBody>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-rose text-truncate mb-0 ls-0-5">
                                        {{ $t('admin_commission') }}</p>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0 mt-1-5">
                                        {{ getFormattedCurrency(getTotalAdminCommission) }}
                                    </h4>
                                </div>
                                <VItem class="p-2" color="rose" radius="4" soft>
                                    <Icon class="data-icon" icon="approval_delegation" size="26" type="msr"/>
                                </VItem>
                            </div>
                        </CardBody><!-- end card body -->
                    </Card>
                </Col>
                <Col xl="3" lg="6">
                    <Card class="data-card">
                        <CardBody>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-teal text-truncate mb-0 ls-0-5">
                                        {{ $t('delivery_charge') }}</p>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0 mt-1-5">
                                        {{ getFormattedCurrency(getTotalAdminDeliveryCharge) }}
                                    </h4>
                                </div>
                                <VItem class="p-2" color="teal" radius="4" soft>
                                    <Icon class="data-icon" icon="local_shipping" size="26" type="msr"/>
                                </VItem>
                            </div>
                        </CardBody><!-- end card body -->
                    </Card>
                </Col>
                <Col xl="3" lg="6">
                    <Card class="data-card">
                        <CardBody>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-green text-truncate mb-0 ls-0-5">
                                        {{ $t('total_orders') }}</p>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0 mt-1-5">
                                        {{ getTotalOrders }}
                                    </h4>
                                </div>
                                <VItem class="p-2" color="green" radius="4" soft>
                                    <Icon class="data-icon" icon="shopping_bag" size="26" type="msr"/>
                                </VItem>
                            </div>
                        </CardBody><!-- end card body -->
                    </Card>
                </Col>
            </Row>

            <Card>
                <CardHeader :title="$t('revenues')" icon="payments" type="msr"></CardHeader>
                <Table :data="revenues" :loading="table_loading" :option="table_header">
                    <template #order_id="data">
                        <router-link :to="{name: 'admin.orders.edit', params: {id:data.value}}">#{{ data.value }}
                        </router-link>
                    </template>
                    <template #created_at="data">
                        <span>{{ getFormattedDateTime(data.value,) }}</span>
                    </template>
                </Table>
            </Card>
        </PageLoading>

    </Layout>
</template>


<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import {IShop, Shop} from "@js/types/models/shop";
import {IAdminRevenue} from "@js/types/models/revenue";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import Table from "@js/components/Table.vue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";

export default defineComponent({
    components: {
        CreateButton,
        Table,
        DateTimePicker,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin, AdminModuleSelectorMixin],
    data() {
        const shop_helper= Shop.select_helper();

        return {
            shop_helper,
            loading: false,
            page_loading: true,
            table_loading: false,
            shops: [] as IShop[],
            selected_shop: null,
            start_date: null,
            end_date: null,
            revenues: [] as IAdminRevenue[],

            amount: null
        }
    },
    computed: {
        getTotalRevenues() {

            let total = 0;
            for (let revenue of this.revenues) {
                total += revenue.revenue;
            }
            return total;
        }, getTotalOrders() {
            return this.revenues.length;
        }, getTotalAdminCommission() {
            let total = 0;
            for (let revenue of this.revenues) {
                total += revenue.order_commission;
            }
            return total;
        }, getTotalAdminDeliveryCharge() {

            let total = 0;
            for (let revenue of this.revenues) {
                total += (revenue.delivery_charge + revenue.delivery_commission);
            }
            return total;
        },
        table_header(): ITableOption<IAdminRevenue> {
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
                        label: this.$t('order_commission'),
                        field: 'order_commission',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency(revenue.order_commission)
                    },
                    {
                        label: this.$t('delivery_charge'),
                        field: 'delivery_charge',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency(revenue.delivery_charge)
                    },
                    {
                        label: this.$t('payment_charge'),
                        field: 'payment_charge',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency(revenue.payment_charge)
                    },
                    {
                        label: this.$t('revenue'),
                        field: 'revenue',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency(revenue.revenue)
                    },
                    {
                        label: this.$t('order_id'),
                        field: 'order_id',
                        sort: true,
                    },

                    {
                        label: this.$t('date_&_time'),
                        field: 'created_at',
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: () => this.filter(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_revenue')
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
        generateData(data: IAdminRevenue[]) {
            let list = [];
            const self = this;
            data?.forEach((revenue) => {
                list.push({
                    'ID': revenue.id,
                    "Revenue": revenue.revenue,
                    "Order commission": revenue.order_commission,
                    "Delivery charge": revenue.delivery_charge,
                    "Delivery commission": revenue.delivery_commission,
                    "Payment Charge": revenue.payment_charge,
                    "Collected amount": revenue.collected_payment_from_customer,
                    "Date": self.getFormattedDateTime(revenue.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Report'
            };
        },
        async todayFilter() {
            const date = new Date();
            date.setHours(0, 0, 0, 0);
            this.start_date = date.toISOString();
            date.setHours(23, 59, 59, 0);
            this.end_date = date.toISOString();
            await this.filter();
        },
        async filter(forced = false) {
            try {
                this.clearAllError();

                // if (this.till_date == null) {
                //     this.addError('till_date', this.$t('please_select_till_date'))
                // }
                // if (this.hasAnyError())
                //     return;
                this.table_loading = true;
                let url = Request.addAdminModule(adminAPI.reports.report);
                if (this.start_date != null) {
                    url += ("start_date=" + this.start_date + "&&");
                    if (this.end_date == null) {
                        this.end_date = new Date().toISOString()
                    }
                    url += ("end_date=" + this.end_date + "&&");
                }
                if (this.selected_shop != null) {
                    url += ("shop_id=" + this.selected_shop + "&&");
                }

                const response = await Request.getAuth<IData<IAdminRevenue[]>>(url, {forced: forced});
                this.revenues = response.data.data;
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.table_loading = false;
        },
        async getShops(forced = false) {
            this.page_loading = true;
            try {
                const response = await Request.getAuth<IData<IShop[]>>(Request.addAdminModule(adminAPI.shops.get), {forced: forced});
                this.shops = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.page_loading = false;
        },
        onChangeAdminModule(module_id) {
            this.getShops(true);
            this.selected_shop = null;
        },
        onChangeShop(id) {
            this.selected_shop = id;
        }
    },

    async mounted() {
        this.setTitle(this.$t('report'))
        await this.getShops();
        await this.filter();
    }
});

</script>
