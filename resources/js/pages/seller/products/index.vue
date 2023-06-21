<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('products')"/>

        <Card>
            <Table :data="filter_products" :loading="table_loading" :option="table_header">

                <template #rating="data">
                    <div class="d-flex align-items-center">
                        <Icon color="success" icon="star" type="msr" size="20"/>
                        <span class="font-14" style="margin-top: 2px; margin-left: 2px">
                            {{ data.value }}
                            <span class="font-13 text-muted">
                                ({{ data.row.ratings_count }})
                            </span>
                        </span>
                    </div>
                </template>

                <template #active="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>

                <template #stock="data">
                    <span :class="[{'text-danger fw-medium': hasLowStock(data.row)}]">
                        {{ getStock(data.row) }}
                        <InfoTooltip v-if="hasLowStock(data.row)" :tooltip="$t('low_stock_alert')" class="ms-1 text-danger"/>
                    </span>

                </template>

                <template #name="data">
                    <span class="d-flex align-items-center">
                         <span v-if="data.row.food_type" class="me-1-5">
                            <NetworkImage :src="'/assets/images/food/'+data.row.food_type+'.png'"
                                          object-fit="contain" size="16"></NetworkImage>
                        </span>
                       <span style="margin-top: 2px">{{ data.value }}</span>
                            <span v-if="data.row.need_prescription" class="ms-1-5">
                            <Tooltip :tooltip="$t('need_prescription')">
                               <Icon icon="description" size="12"/>
                             </Tooltip>
                        </span>
                    </span>
                </template>

                <template #actions="data">
                    <router-link :to="{name: 'seller.products.edit', params: {id:data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                    <router-link :to="{name: 'seller.products.reviews', params: {id:data.row.id}}" class="ms-1-5">
                        <Button color="orange" variant="soft" class="p-1-5"  flexed-icon>
                            <Icon icon="grade" size="19"></Icon>
                        </Button>
                    </router-link>

                </template>

                <template v-slot:table-actions>
                    <router-link :to="{name: 'seller.products.import'}" class="me-2">
                        <Button color="bluish-purple" variant="soft" flexed-icon class="fw-medium">
                            <Icon icon="backup" type="msr" class="me-1-5"/>
                            {{$t('import')}}
                        </Button>
                    </router-link>
                    <router-link :to="{name: 'seller.products.create' }">
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
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/seller/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IData} from "@js/types/models/data";
import {defineComponent} from "vue";
import Product, {IProduct} from "@js/types/models/product";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import {Components} from "@js/components/components";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import Tooltip from "@js/components/Tooltip.vue";


export default defineComponent({
    components: {Tooltip, CreateButton, EditActionButton, Table, Layout, ...Components},
    mixins: [UtilMixin],
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

                    },
                    {
                        label: this.$t('rating'),
                        field: 'rating',
                        sort: true,
                        data: (product) => this.getPrecise(product.rating)
                    },
                    {
                        label: this.$t('stock'),
                        field: 'stock',
                        sort: {
                            compare: (a, b) => Product.getMinStock(b) - Product.getMinStock(a)
                        },
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
                onRefresh: () => this.getProducts(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_products')
                }
            }
        },


    },
    methods: {
        getStock: Product.getMinStock,
        hasLowStock:Product.hasLowStock,
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
        async getProducts(forced = false) {
            this.table_loading = true;

            try {
                const response = await Request.getAuth<IData<IProduct[]>>(sellerAPI.products.get, {forced: forced});
                this.products = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;

            }
        }
    },
    async mounted() {
        this.setTitle(this.$t('products'));
        await this.getProducts();
    }
})

</script>
