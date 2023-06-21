<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('shops')"/>

        <Card>
            <Table :data="filter_shops" :loading="table_loading" :option="table_header">
                <template #open="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #rating="data">
                    <div class="d-flex align-items-center">
                        <Icon color="success" icon="star" type="msr" size="20"/>
                        <span class="font-14" style="margin-top: 2px; margin-left: 2px">
                            {{ getPrecise(data.value) }}
                            <span class="font-13 text-muted">
                                ({{data.row.ratings_count }})
                            </span>
                        </span>
                    </div>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.shops.edit', params: {id: data.row.id}}">
                        <EditActionButton icon="visibility"/>
                    </router-link>
                </template>
                <template #table-actions>
                    <div class="d-flex">
                        <router-link :to="{name: 'admin.shops.on_map'}">
                            <Button color="bluish-purple" radius="md" variant="soft">
                                <Icon icon="home_pin" size="20"></Icon>
                                <span class="ms-1">{{ $t('find_shop') }}</span>
                            </Button>
                        </router-link>
                        <router-link :to="{name: 'admin.shops.create' }" class="ms-2 ">

                            <CreateButton/>
                        </router-link>
                    </div>
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
                            <a :class="[{'active':filter_by==='new'}]" class="nav-link"
                               @click="changeFilter('new')">
                                <span class="title">{{ $t('not_approved') }}</span>
                                <span class="counter">{{ getNotApprovedShopCount }}</span>
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
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IShop} from "@js/types/models/shop";
import {IData} from "@js/types/models/data";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Icon from "@js/components/Icon.vue";
import Button from "@js/components/buttons/Button.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import VItem from "@js/components/VItem.vue";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";


export default defineComponent({
    components: {VItem, EditActionButton, CreateButton, Button, Icon, Card, CardBody, Table, PageHeader, Layout,},
    mixins: [UtilMixin, AdminModuleSelectorMixin],
    data() {
        return {
            table_loading: true,
            shops: [] as IShop[],
            filter_shops: [] as IShop[],
            filter_by: 'active',
        }
    },
    computed: {
        table_header(): ITableOption<IShop> {
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
                        label: this.$t('name'),
                        field: 'name',
                        sort: true,
                    },
                    {
                        label: this.$t('email'),
                        field: 'email',
                    },
                    {
                        label: this.$t('mobile_number'),
                        field: 'mobile_number',
                    },
                    {
                        label: this.$t('open'),
                        field: 'open',
                        sort: true
                    },
                    {
                        label: this.$t('rating'),
                        field: 'rating',
                        sort: true
                    },

                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 120,
                        printNone: true
                    },


                ],
                exports: {
                    autoData: this.generateData,
                    print: {
                        enable: true,
                        auto: true
                    },
                    csv: {
                        enable: true,
                        auto: true
                    },
                    excel: {
                        enable: true,
                        auto: true
                    }
                },
                onRefresh: () => this.getShops(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_shops')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("shops"),
                    active: true,
                },
            ];
        },
        getNotApprovedShopCount() {
            return this.shops.filter((shop) => !shop.approved).length;
        },

    },
    methods: {
        generateData(data: IShop[]) {
            let list = [];
            data?.forEach((shop) => {
                list.push({
                    'ID': shop.id,
                    "Active": shop.active,
                    "Name": shop.name,
                    "Email": shop.email,
                    "Mobile Number": shop.mobile_number,
                    "Open": shop.open,
                    "Address": shop.address,
                    "City": shop.city,
                    "State": shop.state,
                    "Country": shop.country,
                    "Pincode": shop.pincode,
                    "Open for delivery": shop.open_for_delivery,
                    "Take away": shop.take_away,
                    "Self delivery": shop.self_delivery,
                    "Min delivery time": shop.min_delivery_time,
                    "Max delivery time": shop.max_delivery_time,
                    "Delivery time type": shop.delivery_time_type,
                    "Module": shop.module?.title,
                    "Delivery range": shop.delivery_range,
                    "Min order amount": shop.min_order_amount,
                    "Min delivery charge": shop.minimum_delivery_charge,
                    "Delivery cost multiplier": shop.delivery_charge_multiplier,
                    "Need prescription": shop.need_prescription,
                    "Zone": shop.zone?.name ?? 'Global',
                    "Rating": shop.rating,
                    "Total rating": shop.ratings_total,
                    "Shop Plan": shop.shop_plan?.title,
                    "Tax": shop.tax,
                    "Tax type": shop.tax_type,

                });
            });
            return {
                data: list,
                fileName: 'Shops'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_shops = this.shops.filter((shop) => shop.active);
            } else if (this.filter_by == 'new') {
                this.filter_shops = this.shops.filter((shop) => !shop.approved);
            }
        },
        async getShops(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IShop[]>>(Request.addAdminModule(adminAPI.shops.get), {forced: forced});
                this.shops = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        },
        onChangeAdminModule(module_id){
            this.getShops();
        }

    },
    mounted() {
        this.setTitle(this.$t('shops'))
        this.getShops();
    }
});

</script>
