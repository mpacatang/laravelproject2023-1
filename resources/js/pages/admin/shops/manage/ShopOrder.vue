<template>
    <Card>
        <Table :data="filter_orders" :loading="table_loading" :option="table_header">

            <template #actions="data">
                <router-link :to="{name: 'admin.orders.edit', params: {id:  data.row.id}}">
                    <EditActionButton/>
                </router-link>
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
                        <a :class="[{'active':filter_by==='delivery'}]"
                           class="nav-link" @click="changeFilter('delivery')">
                            <span class="title">{{ $t('delivery') }}</span>
                            <span v-if="getDeliveryCount!==0" class="counter">{{
                                    getDeliveryCount
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
                <router-link :to="{name: 'admin.pos.create', query: {shop_id: id}}">
                    <Button color="bluish-purple" flexed-icon radius="4">
                        <Icon class="me-1-5" icon="local_convenience_store" size="sm"></Icon>
                        {{ $t('POS') }}
                    </Button>
                </router-link>
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
import Order, {IOrder} from "@js/types/models/order";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import {ITableOption} from "@js/types/models/models";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";


export default defineComponent({
    components: {EditActionButton, Table, Layout, ...Components},
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
            orders: [] as IOrder[],
            filter_orders: [] as IOrder[],
            filter_by: 'pos',

        }
    },
    computed: {
        getCompletedCount() {
            return this.orders.filter((order) => order.complete).length;
        },
        getDeliveryCount() {
            return this.orders.length - this.getCompletedCount - this.getPOSCount;
        },
        getPOSCount() {
            return this.orders.filter((order) => order.order_type == 'pos').length;
        },
        table_header(): ITableOption<IOrder> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        sort: true,
                        width: 150
                    },
                    {
                        label: this.$t('status'),
                        field: 'order_status',
                        data: (order) => this.getStatusFromText(order)
                    },
                    {
                        label: this.$t('payment'),
                        field: 'payment',
                        data: this.getPaymentFromOrder

                    },
                    {
                        label: this.$t('type'),
                        field: 'order_type',
                        data: this.getTypeFromOrder

                    },
                    {
                        label: this.$t('order'),
                        field: 'order',
                        sort: {
                            compare: (a, b) => b.order_amount - a.order_amount
                        },
                        data: (order) => this.getFormattedCurrency(order.total)

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
                onRefresh: this.getOrders,
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_orders')
                }
            };
        },

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
            if (this.filter_by == 'delivery') {
                this.filter_orders = this.orders.filter((order) => !order.complete && order.order_type != 'pos');
            } else if (this.filter_by == 'completed') {
                this.filter_orders = this.orders.filter((order) => order.complete);
            } else if (this.filter_by == 'pos') {
                this.filter_orders = this.orders.filter((order) => order.order_type == 'pos');
            }
        },
        async getOrders() {
            try {
                this.table_loading = true;
                const response = await Request.getAuth<IData<IOrder[]>>(adminAPI.shops.orders(this.id));
                this.orders = response.data.data;
                this.table_loading = false;
                if (this.getPOSCount == 0) {
                    this.filter_by = this.getDeliveryCount != 0 ? 'delivery' : 'completed';
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
        getPaymentFromOrder(order: IOrder) {
            return Order.getPaymentTextFromOrder(order);
        },
        getTypeFromOrder(order: IOrder) {
            return Order.getTypeText(order);
        },


    },
    async mounted() {
        this.setTitle(this.$t('orders'))
        await this.getOrders();
    }
});

</script>
