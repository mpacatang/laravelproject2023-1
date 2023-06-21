<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('coupons')"/>
        <Card>
            <Table :data="filter_coupons" :option="table_header" :loading="table_loading">
                <template #delivery_free="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.coupons.edit', params: {id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.coupons.create' }">
                        <CreateButton/>
                    </router-link>
                </template>
                <template #pre-actions>
                    <ul class="nav nav-tabs nav-tabs-custom-icons color-teal border-none" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a :class="[{'active':filter_by==='active'}]"
                               class="nav-link" @click="changeFilter('active')">
                                <span class="title">{{ $t('active') }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a :class="[{'active':filter_by==='inactive'}]" class="nav-link"
                               @click="changeFilter('inactive')">
                                <span class="title">{{ $t('inactive') }}</span>
                                <span v-if="getInactiveCouponCount!==0" class="counter">{{
                                        getInactiveCouponCount
                                    }}</span>
                            </a>
                        </li>
                    </ul>

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
import {ICoupon} from "@js/types/models/coupon";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Button from "@js/components/buttons/Button.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            coupons: [] as ICoupon[],
            filter_coupons: [] as ICoupon[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveCouponCount() {
            return this.coupons.filter((coupon) => !coupon.active).length;
        },
        table_header(): ITableOption<ICoupon> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        width: 150,
                        labelStyle: {
                            fontWeight: "medium"
                        },
                    },
                    {
                        label: this.$t('code'),
                        field: 'code',
                        sort: true,
                        labelStyle: {
                            fontWeight: "medium"
                        },
                        onCopy: (c) => c.code
                    },

                    {
                        label: this.$t('duration'),
                        field: 'duration',
                        data: (coupon) => {
                            console.info(coupon);
                            return this.getFormattedDateTime(coupon.started_at, {month: "mmm"}) + " - " +
                                this.getFormattedDateTime(coupon.expired_at, {month: "mmm"})
                        },
                        onCopy: (coupon) => {
                            console.info(coupon);
                            return this.getFormattedDateTime(coupon.started_at, {month: "mmm"}) + " - " +
                                this.getFormattedDateTime(coupon.expired_at, {month: "mmm"})
                        },
                    },
                    {
                        label: this.$t('discount'),
                        field: 'discount',
                        data: (coupon) => {
                            if (coupon.discount != null) {
                                if (coupon.discount_type === 'amount') {
                                    return this.getFormattedCurrency(coupon.discount);
                                } else {
                                    return coupon.discount + " %";
                                }
                            }
                            return "-";
                        }
                    },
                    {
                        label: this.$t('min_order'),
                        field: 'min_order',
                        data: (coupon) => coupon.min_order != null ? this.getFormattedCurrency(coupon.min_order) : "-"
                    },
                    {
                        label: this.$t('max_discount'),
                        field: 'max_discount',
                        data: (coupon) => coupon.max_discount != null ? this.getFormattedCurrency(coupon.max_discount) : "-"
                    },
                    {
                        label: this.$t('delivery_free'),
                        field: 'delivery_free',
                    },
                    {
                        label: this.$t('limit'),
                        field: 'limit',
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 120,
                        printNone: true

                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: ()=>this.getCoupons(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_coupons')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('coupons'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: ICoupon[]) {
            let list = [];
            data?.forEach((coupon) => {
                list.push({
                    "ID": coupon.id,
                    "Code": coupon.code,
                    "Active": coupon.active,
                    "Duration": this.getFormattedDateTime(coupon.started_at) +
                        " - " + this.getFormattedDateTime(coupon.expired_at),
                    "Discount": coupon.discount,
                    "Discount Type": coupon.discount_type,
                    "Min Order": coupon.min_order ?? "-",
                    "Max Discount": coupon.max_discount ?? "-",
                    "First Order": coupon.first_order,
                    "Limit": coupon.limit,
                    "Delivery Free": coupon.delivery_free,
                    "Shop ID": coupon.shop_id ?? "-",
                    "Module ID": coupon.module_id ?? "-",
                    "Zone ID": coupon.zone_id ?? "-",

                });
            });
            return {
                data: list,
                fileName: 'Addons'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_coupons = this.coupons.filter((coupon) => coupon.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_coupons = this.coupons.filter((coupon) => !coupon.active);
            }
        },
        async getCoupons(forced=false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<ICoupon[]>>(adminAPI.coupons.get, {forced: forced});
                this.coupons = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('coupons'))
        this.getCoupons();
    },

});

</script>
