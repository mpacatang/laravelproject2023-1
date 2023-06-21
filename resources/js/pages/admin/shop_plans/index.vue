<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('shop_plans')+ ' (Monthly)'"/>
        <Card>
            <Table :data="filter_shop_plans" :loading="table_loading" :option="table_header">

                <template #actions="data">
                    <router-link :to="{name: 'admin.shop_plans.edit', params: {id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.shop_plans.create' }">
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
                                <span v-if="getInactiveShopPlanCount!==0" class="counter">{{
                                        getInactiveShopPlanCount
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
import {IAddon} from "@js/types/models/addon";
import {IData} from "@js/types/models/data";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import NetworkImage from "@js/components/NetworkImage.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import Button from "@js/components/buttons/Button.vue";
import {IShopPlan} from "@js/types/models/shop_plan";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, Card, NetworkImage, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            shop_plans: [] as IShopPlan[],
            filter_shop_plans: [] as IShopPlan[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveShopPlanCount() {
            return this.shop_plans.filter((shop_plan) => !shop_plan.active).length;
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('shop_plans'),
                    active: true,
                },
            ];
        },
        table_header(): ITableOption<IAddon> {
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
                        label: this.$t('title'),
                        field: 'title',
                        sort: true
                    },

                    {
                        label: this.$t('price'),
                        field: 'price',
                        data: (addon) => this.getFormattedCurrency(addon.price),
                        sort: {
                            compare: (a, b) => a.price - b.price
                        }
                    },
                    {
                        label: this.$t('products'),
                        field: 'products',
                        sort: true,
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
                onRefresh: ()=>this.getShopPlans(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_plans')
                }
            };
        }
    },
    methods: {
        generateData(data: IShopPlan[]) {
            let list = [];
            data?.forEach((plan) => {
                list.push({
                    'ID': plan.id,
                    "Active": plan.active,
                    "Title": plan.title,
                    "Sub Title": plan.sub_title,
                    "Price": plan.price,
                    "Commission": plan.admin_commission,
                    "Commission Type": plan.admin_commission_type,
                    "Products": plan.products,
                });
            });
            return {
                data: list,
                fileName: 'Shop Plans'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_shop_plans = this.shop_plans.filter((shop_plan) => shop_plan.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_shop_plans = this.shop_plans.filter((shop_plan) => !shop_plan.active);
            }
        },
        async getShopPlans(forced=false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IShopPlan>>(adminAPI.shop_plans.get, {forced: forced});
                this.shop_plans = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
        }
    },
    mounted() {
        this.setTitle(this.$t('shop_plans'));
        this.getShopPlans();
    }
});

</script>
