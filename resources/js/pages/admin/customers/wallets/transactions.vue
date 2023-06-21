<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('wallet_transactions')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('filter')" icon="screen_search_desktop" type="msr"></CardHeader>

                        <CardBody>

                            <Row>
                                <Col lg="6">

                                    <FormSelect :data="customers" :helper="customer_select_helper"
                                                :label="$t('customer')"
                                                :errors="errors" name="customer_id"
                                                :on-selected="onChangeCustomer" :selected="selected_customer"/>
                                </Col>
                            </Row>


                            <div class="text-end">
                                <LoadingButton :loading="loading" flexed-icon icon="search" icon-size="18"
                                               @click="filter">{{ $t('search') }}
                                </LoadingButton>
                            </div>


                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
                    <Card :class="[{'item-disabled': customer_wallet==null}]">

                        <CardHeader :title="$t('wallet')" icon="account_balance_wallet" type="msr"></CardHeader>
                        <CardBody>
                            <Row v-if="customer_wallet">
                                <Col lg="6">

                                    <span>{{ $t('current_balance') }}</span>
                                    <h3 class="mt-1">{{ getFormattedCurrency(customer_wallet.balance) }}</h3>

                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="amount" :errors="errors" :label="$t('add_to_wallet')" name="amount"
                                               no-spacing/>

                                </Col>
                            </Row>


                            <div class="text-end mt-3">
                                <LoadingButton :loading="loading" flexed-icon icon="add" @click="addMoney">
                                    {{
                                        $t('add_money')
                                    }}
                                </LoadingButton>
                            </div>


                        </CardBody>
                    </Card>
                </Col>

                <Col lg="12">
                    <Card>
                        <CardHeader :title="$t('customer_transactions')" icon="monitoring" type="msr"></CardHeader>
                        <Table :data="customer_wallet?.transactions" :option="table_header"
                               :table_loading="table_loading" remove-top-action>
                            <template #added="data">
                                    <span v-if="data.value" class="text-success">
                                        <Icon class="me-1" icon="keyboard_double_arrow_up"/>{{ $t('added') }}
                                    </span>
                                <span v-else class="text-danger">
                                        <Icon class="me-1"
                                              icon="keyboard_double_arrow_down"
                                              type="msr"/>{{ $t('used') }}
                                    </span>
                            </template>

                            <template #order_id="data">
                                <router-link v-if="data.value" :to="{name: 'admin.orders.edit', params: {id:data.value}}">#{{
                                        data.value
                                    }}
                                </router-link>
                                <span v-else>-</span>
                            </template>
                        </Table>
                    </Card>
                </Col>
            </Row>

        </PageLoading>

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
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import Table from "@js/components/Table.vue";
import {IBreadcrumb, IFormSelectOption, ITableOption} from "@js/types/models/models";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import {
    Customer,
    CustomerWalletTransaction,
    ICustomer,
    ICustomerWallet,
    ICustomerWalletTransaction
} from "@js/types/models/customer";
import UtilMixin from "@js/shared/mixins/UtilMixin";

export default defineComponent({
    components: {
        Table,
        DateTimePicker,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            table_loading: false,
            customers: [] as ICustomer[],
            selected_customer: null,
            customer_wallet: null as ICustomerWallet,

            till_date: null,
            amount: null
        }
    },
    computed: {
        customer_select_helper: Customer.select_helper,
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("customers"),
                    route: 'admin.customers.index'
                }, {
                    text: this.$t("customer_wallet"),
                    active: true,
                },
            ];
        },
        table_header(): ITableOption<ICustomerWalletTransaction> {
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
                        label: this.$t('status'),
                        field: 'added',
                        sort: true
                    },
                    {
                        label: this.$t('amount'),
                        field: 'amount',
                        data: (trans) => this.getFormattedCurrency(trans.amount),
                        sort: {
                            compare: (a, b) => a.amount - b.amount
                        },

                    },
                    {
                        label: this.$t('order_id'),
                        field: 'order_id',
                        sort: {
                            compare: (a, b) => a.order_id - b.order_id
                        },
                    },

                    {
                        label: this.$t('payment_method'),
                        field: 'payment_method',
                        data:(a)=>CustomerWalletTransaction.getPaymentMethodText(a)
                    },
                    {
                        label: this.$t('date_&_time'),
                        field: 'created_at',
                        data: (trans) => this.getFormattedDateTime(trans.created_at),
                        width: 260,
                        sort: true
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: this.filter,
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_transactions')
                }
            };
        },

    },
    methods: {
        generateData(data: ICustomerWalletTransaction[]) {
            let list = [];
            data?.forEach((trans) => {
                list.push({
                    'ID': trans.id,
                    "Added": trans.added,
                    "Amount": trans.amount,
                    "Order ID": trans.order_id,
                    "Payment Method": trans.payment_method,
                    "Created At": trans.created_at,
                });
            });
            return {
                data: list,
                fileName: 'Customer #' + this.selected_customer
            };
        },
        onChangeCustomer(id) {
            this.selected_customer = id;
        },
        async filter() {
            try {
                this.clearAllError();
                if (this.selected_customer == null) {
                    this.addError('customer_id', this.$t('please_choose_customer'))
                }

                if (this.hasAnyError())
                    return;
                this.loading = true;
                this.table_loading = true;
                const response = await Request.getAuth<IData<ICustomerWallet>>(adminAPI.customers.wallet(this.selected_customer));
                this.customer_wallet = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.loading = false;
            this.table_loading = false;
        },
        async addMoney() {
            try {
                this.clearAllError();
                if (this.selected_customer == null) {
                    this.addError('customer_id', this.$t('please_choose_customer'))
                }
                if (this.amount == null) {
                    this.addError('amount', this.$t('please_enter_amount'))
                }
                if (this.hasAnyError())
                    return;
                if (parseInt(this.amount) <= 0) {
                    this.addError('amount', this.$t('please_enter_amount_more_than') + " 0");
                    return;
                }
                this.loading = true;
                const response = await Request.postAuth(adminAPI.customers.add_money_to_wallet(this.selected_customer), {
                    amount: this.amount,
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('money_added_to_wallet'));
                    this.amount = 0;
                    await this.filter();
                } else {
                    ToastNotification.unknownError();
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
        async getCustomers() {
            try {
                const response = await Request.getAuth<IData<IDeliveryBoy[]>>(adminAPI.customers.get);
                this.customers = response.data.data;
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('customer_transaction'))
        this.getCustomers();
    }
});

</script>
