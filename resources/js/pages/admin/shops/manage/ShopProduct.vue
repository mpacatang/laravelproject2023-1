<template>
    <Card>
        <Table :data="filter_products" :loading="table_loading" :option="table_header">

            <template #rating="data">
                <div class="d-flex align-items-center">
                    <Icon color="success" icon="star" type="msr" size="20"/>
                    <span class="font-14" style="margin-top: 2px; margin-left: 2px">
                            {{ getPrecise(data.value) }} <span class="font-13 text-muted">({{
                            data.row.ratings_count
                        }})</span></span>
                </div>
            </template>

            <template #active="data">
                <Icon v-if="data.value" color="success" icon="check_circle"/>
                <Icon v-else color="danger" icon="cancel"/>
            </template>
            <template #food_type="data">

            </template>

            <template #actions="data">
                <router-link :to="{name: 'admin.products.edit', params: {id:data.row.id}}">
                    <EditActionButton/>
                </router-link>
            </template>

            <template v-slot:table-actions>
                <router-link :to="{name: 'admin.products.create' }">
                    <CreateButton/>
                </router-link>
            </template>
            <template v-slot:pre-actions>
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
                            <span v-if="getInactiveProductCount!==0" class="counter">
                                    {{ getInactiveProductCount }}
                                </span>
                        </a>
                    </li>
                </ul>

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
import {IBreadcrumb, IFormSelectOption, ITableOption} from "@js/types/models/models";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import {IProduct} from "@js/types/models/product";
import {IShop} from "@js/types/models/shop";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";


export default defineComponent({
    components: {CreateButton, EditActionButton, Table, Layout, ...Components},
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
            products: [] as IProduct[],
            filter_products: [] as IDeliveryBoy[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveProductCount() {
            return this.products.filter((product) => !product.active).length;
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('products'),
                    active: true,
                },
            ];
        },
        table_header(): ITableOption<IProduct> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        labelStyle: {
                            fontWeight: "medium"
                        },
                        width: 150,
                        onCopy: (product) => product.id.toString()
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: true,
                        onCopy: (product) => product.name,

                    },

                    {
                        label: this.$t('category'),
                        field: 'category',
                        sort: true,
                        data: (product) => product.category?.name,
                        onClick: (product) => this.$router.push({
                            name: 'admin.categories.edit',
                            params: {id: product.category_id}
                        })

                    },
                    {
                        label: this.$t('rating'),
                        field: 'rating',
                        sort: true
                    },
                    {
                        label: this.$t('selling'),
                        field: 'selling_count',
                        sort: true,
                        width: 100
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
                onRefresh: this.getProducts,
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_products')
                }
            }
        },
        shop_helper(): IFormSelectOption {
            return {
                option: {
                    label: 'name',
                    value: 'id'
                }
            };
        },

    },
    methods: {
        generateData(data: IProduct[]) {
            let list = [];
            data?.forEach((product) => {
                list.push({
                    'ID': product.id,
                    "Active": product.active,
                    "Name": product.name,
                    "Shop ID": product.shop_id,
                    "Shop Name": product.shop?.name,
                    "Category": product.category?.name,
                    "Food Type": product.food_type,
                    "Rating": product.rating,
                    "Total Rating": product.ratings_count,
                });
            });
            return {
                data: list,
                fileName: 'Products'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_products = this.products.filter((product) => product.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_products = this.products.filter((product) => !product.active);
            }
        },
        async getProducts() {
            this.table_loading = true;

            try {
                const response = await Request.getAuth<IData<IProduct[]>>(adminAPI.shops.products(this.id));
                this.products = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;

        }
    },
    async mounted() {
        this.setTitle(this.$t('products'));

        await this.getProducts();
    }
});

</script>
