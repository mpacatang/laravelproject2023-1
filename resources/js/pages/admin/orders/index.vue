<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('orders')"/>
        <Card>
            <Table :data="filter_orders" :loading="table_loading" :option="table_header">
                <template #actions="data">
                    <router-link :to="{name: 'admin.orders.edit', params: {id:  data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                    <router-link :to="{name: 'admin.orders.invoice', params: {id:  data.row.id}}" class="ms-2">
                        <Button class="p-1-5" color="teal" flexed-icon radius="md" variant="soft">
                            <Icon icon="print" size="19"/>
                        </Button>
                    </router-link>
                </template>
                <template #customer="data">
                    <p class="mb-0 font-13">{{ data.value.first_name }} {{ data.value.last_name }}</p>
                    <span class="font-13">{{ data.value.mobile_number }}</span>
                </template>
                <template #created_at="data">
                    <p class="mb-0 font-14">{{ getFormattedDate(data.value, {short: true}) }}</p>
                    <span class="font-13">{{ getFormattedTime(data.value) }}</span>
                </template>
                <template #order_amount="data">
                    <p class="mb-0 font-15 fw-medium">{{ getFormattedCurrency(data.value) }}</p>
                    <span class="font-13 text-brown">{{$t(getTypeFromOrder(data.row))}}</span>
                </template>
                <template #payment="data">
                    <p class="mb-0 font-14 fw-medium">{{ getPaymentFromOrder(data.row) }}</p>
                    <span
                        :class="['text-primary font-13',{'text-danger':getPaymentStatusFromOrder(data.row)==='unpaid'}]">
                        {{$t(getPaymentStatusFromOrder(data.row))}}</span>
                </template>
                <template #order_status="data">
                    <div :class="['order_status_'+getStatus(data.row)]"
                         class="d-inline px-1-5 fw-medium rounded py-0-5 font-13">
                        {{ getStatusFromText(data.row) }}
                    </div>
                </template>

                <template #pre-actions>
                    <ul class="nav nav-tabs nav-tabs-custom-icons color-teal border-none" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a :class="[{'active':filter_by==='pos'}]"
                               class="nav-link" @click="changeFilter('pos')">
                                <span class="title">{{ $t('POS') }}</span>
                                <span v-if="getPOSCount!==0" class="counter">{{
                                        getPOSCount
                                    }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a :class="[{'active':filter_by==='active'}]"
                               class="nav-link" @click="changeFilter('active')">
                                <span class="title">{{ $t('active') }}</span>
                                <span v-if="getActiveCount!==0" class="counter">{{
                                        getActiveCount
                                    }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a :class="[{'active':filter_by==='completed'}]" class="nav-link"
                               @click="changeFilter('completed')">
                                <span class="title">{{ $t('completed') }}</span>
                            </a>
                        </li>
                    </ul>

                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.pos.create'}">
                        <Button color="bluish-purple" flexed-icon radius="4">
                            <Icon class="me-1-5" icon="local_convenience_store" size="sm"></Icon>
                            {{ $t('POS') }}
                        </Button>
                    </router-link>
                </template>
            </Table>

        </Card>

    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import Order, {IOrder} from "@js/types/models/order";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import {FcmNotification} from "@js/services/fcm_notification";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";


export default defineComponent({
    components: {EditActionButton, Table, Layout, ...Components},
    mixins: [UtilMixin, AdminModuleSelectorMixin],
    data() {
        return {
            table_loading: true,
            orders: [] as IOrder[],
            filter_orders: [] as IOrder[],
            filter_by: 'pos',
        }
    },
    computed: {
        getCompletedCount() {
            return this.orders.filter((order) => order.complete).length;
        },
        getActiveCount() {
            return this.orders.filter((order) => order.order_type != 'pos' && !order.complete).length;
        },
        getPOSCount() {
            return this.orders.filter((order) => order.order_type == 'pos' && !order.complete).length;
        },
        table_header(): ITableOption<IOrder> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        sort: true,
                        width: 150,
                        labelStyle: {
                            fontWeight: "medium"
                        }
                    },

                    {
                        label: this.$t('shop'),
                        field: 'shop',
                        data: (a) => a.shop.name,
                        onClick:(a) => this.$router.push({name: 'admin.shops.index', params: {id: a.shop.id}}),
                        width: 170


                    },{
                        label: this.$t('customer'),
                        field: 'customer',
                        onCopy: (a) => a.customer.mobile_number

                    },
                    {
                        label: this.$t('order'),
                        field: 'order_amount',
                        sort: {
                            compare: (a, b) => b.order_amount - a.order_amount
                        },

                    },
                    {
                        label: this.$t('status'),
                        field: 'order_status',
                        // data: (order) => this.getStatusFromText(order)
                    },
                    {
                        label: this.$t('payment'),
                        field: 'payment',

                    },

                    {
                        label: this.$t('order_date'),
                        field: 'created_at',
                        width: 150,
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 150,
                        printNone: true
                    },


                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: ()=>this.getOrders(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_orders')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('orders'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IOrder[]) {
            let list = [];
            const self = this;
            data?.forEach((order) => {
                list.push({
                    'ID': order.id,
                    "Status": Order.getStatusText(order),
                    "Payment": Order.getPaymentText(order),
                    "Type": Order.getTypeText(order),
                    "Total": self.getFormattedCurrency(order.total),
                    "Created At": self.getFormattedDateTime(order.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Orders'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_orders = this.orders.filter((order) => !order.complete && order.order_type != 'pos');
            } else if (this.filter_by == 'completed') {
                this.filter_orders = this.orders.filter((order) => order.complete);
            } else if (this.filter_by == 'pos') {
                this.filter_orders = this.orders.filter((order) => order.order_type == 'pos' && !order.complete);
            }
        },
        async getOrders(forced=false) {
            this.table_loading = true;
            try {

                const response = await Request.getAuth<IData<IOrder[]>>(Request.addAdminModule(adminAPI.orders.get), {forced: forced});
                this.orders = response.data.data;

                if (this.getPOSCount == 0) {
                    this.filter_by = this.getActiveCount != 0 ? 'active' : 'completed';
                }
                this.applyFilter();
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
        },
        getStatusFromText(order: IOrder) {
            return Order.getStatusText(order);
        },
        getStatus(order: IOrder) {
            return Order.getLastStatus(order).status;
        },
        getPaymentFromOrder(order: IOrder) {
            return Order.getPaymentTextFromOrder(order);
        },
        getPaymentStatusFromOrder(order: IOrder) {
            return Order.getPaymentStatusFromOrder(order);
        },
        getTypeFromOrder(order: IOrder) {
            return Order.getTypeText(order);
        },
        orderChangeLister() {
            this.getOrders();
        },
        onChangeAdminModule() {
            this.getOrders();
        }
    },
    mounted() {
        FcmNotification.subscribeOrderChangeListener(this.orderChangeLister);
        this.setTitle(this.$t('orders'))
        this.getOrders();
    },
    unmounted(){
        FcmNotification.unsubscribeOrderChangeListener(this.orderChangeLister);
    }
});

</script>
