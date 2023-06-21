<template>
    <Layout id="seller-report">
        <PageHeader :items="breadcrumb_items" :title="$t('today_report')"/>

        <Row>
            <Col xl="6">
                <Card>
                    <CardHeader :title="$t('filter')" icon="screen_search_desktop"></CardHeader>

                    <CardBody>

                        <Row>

                            <Col lg="6">

                                <DateTimePicker v-model="start_date" :errors="errors" :label="$t('start_date')"
                                                name="start_date"/>

                            </Col>
                            <Col lg="6">

                                <DateTimePicker v-model="end_date" :errors="errors" :label="$t('end_date')"
                                                name="end_date"/>

                            </Col>

                        </Row>


                        <div class="text-end">
                            <LoadingButton :loading="loading" color="bluish-purple" flexed-icon icon="event_available"
                                           icon-size="18"
                                           size="md" variant="soft" @click="todayFilter">
                                {{ $t('today_report') }}
                            </LoadingButton>
                            <LoadingButton :loading="loading" class="ms-2" flexed-icon icon="tune" icon-size="18"
                                           size="md" @click="filter">
                                {{ $t('filter') }}
                            </LoadingButton>
                        </div>

                    </CardBody>
                </Card>
            </Col>
            <Col xl="6">

                <Row>
                    <Col lg="6">
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
                    <Col lg="6">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-uppercase fw-semibold text-teal text-truncate mb-0 ls-0-5">
                                            {{ $t('delivery_charge') }}</p>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-0 mt-1-5">
                                            {{ getFormattedCurrency(getTotalSellerDeliveryCharge) }}
                                        </h4>
                                    </div>
                                    <VItem class="p-2" color="teal" radius="4" soft>
                                        <Icon class="data-icon" icon="local_shipping" size="26" type="msr"/>
                                    </VItem>
                                </div>
                            </CardBody><!-- end card body -->
                        </Card>
                    </Col>
                    <Col lg="6">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-uppercase fw-semibold text-rose text-truncate mb-0 ls-0-5">
                                            {{ $t('extra_charges') }}</p>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-0 mt-1-5">
                                            {{ getFormattedCurrency(getTotalExtraCharges) }}
                                        </h4>
                                    </div>
                                    <VItem class="p-2" color="rose" radius="4" soft>
                                        <Icon class="data-icon" icon="approval_delegation" size="26" type="msr"/>
                                    </VItem>
                                </div>
                            </CardBody><!-- end card body -->
                        </Card>
                    </Col>
                    <Col lg="6">
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
            </Col>
        </Row>


        <Card>
            <CardHeader :title="$t('revenues')" icon="payments" type="msr"></CardHeader>
            <Table :data="revenues" :loading="table_loading" :option="table_header">
                <template #order_id="data">
                    <router-link :to="{name: 'seller.orders.edit', params: {id:data.value}}">#{{ data.value }}
                    </router-link>
                </template>
                <template #created_at="data">
                    <span>{{ getFormattedDateTime(data.value,) }}</span>
                </template>
            </Table>
        </Card>

    </Layout>
</template>


<script lang="ts">

import Layout from "@js/pages/seller/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
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
            shops: [] as IShop[],
            selected_shop: null,
            start_date: null,
            end_date: null,
            revenues: [] as IShopRevenue[],

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
        getTotalRevenues() {

            let total = 0;
            for (let revenue of this.revenues) {
                total += revenue.revenue;
            }
            return total;
        }, getTotalOrders() {
            return this.revenues.length;
        }, getTotalExtraCharges() {
            let total = 0;
            for (let revenue of this.revenues) {
                total += (revenue.packaging_charge+revenue.tax);
            }
            return total;
        }, getTotalSellerDeliveryCharge() {

            let total = 0;
            for (let revenue of this.revenues) {
                total += (revenue.delivery_charge);
            }
            return total;
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
                        label: this.$t('order_amount'),
                        field: 'order_amount',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency(revenue.order_amount)
                    },
                    {
                        label: this.$t('extra_charges'),
                        field: 'extra_charges',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency((revenue.tax + revenue.packaging_charge))
                    },
                    {
                        label: this.$t('delivery_charge'),
                        field: 'delivery_charge',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency((revenue.delivery_charge))
                    },
                    {
                        label: this.$t('delivery_charge'),
                        field: 'delivery_charge',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency(revenue.delivery_charge)
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
                onRefresh: this.filter,
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
        generateData(data: IShopRevenue[]) {
            let list = [];
            const self = this;
            data?.forEach((revenue) => {
                list.push({
                    'ID': revenue.id,
                    "Revenue": revenue.revenue,
                    "Order amount": revenue.order_amount,
                    "Delivery charge": revenue.delivery_charge,
                    "Packaging charge": revenue.packaging_charge,
                    "Tax": revenue.tax,
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
        async filter() {
            try {
                this.clearAllError();
                this.table_loading = true;
                this.loading = true;
                let url = sellerAPI.reports.report + "?";
                if (this.start_date != null) {
                    url += ("start_date=" + this.start_date + "&&");
                    if (this.end_date == null) {
                        this.end_date = new Date().toISOString()
                    }
                    url += ("end_date=" + this.end_date + "&&");
                }
                const response = await Request.getAuth<IData<IShopRevenue[]>>(url, {forced: true});
                this.revenues = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
            this.loading = false;
        },

    },

    async mounted() {
        this.setTitle(this.$t('today_report'));
        await this.todayFilter();

    }
});

</script>
