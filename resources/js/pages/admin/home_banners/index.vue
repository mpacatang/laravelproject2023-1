<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('home_banners')"/>
        <Card>
            <Table :data="filter_banners" :loading="table_loading" :option="table_header">
                <template #image="data">
                    <NetworkImage rounded :placeholder-src="data.row.shop_id!=null?shopLandscapePlaceholder:productLandscapePlaceholder" :src="data.value"
                                  height="80"/>
                </template>
                <template #shop="data">
                    <span v-if="data.value==null">-</span>
                    <router-link v-else :to="{name: 'customer.shops.show', params: {id: data.value.id}}"
                                 target="_blank">{{ data.value.name }}
                    </router-link>
                </template>
                <template #product="data">
                    <span v-if="data.value==null">-</span>
                    <router-link v-else :to="{name: 'customer.products.show', params: {id: data.value.id}}"
                                 target="_blank">{{ data.value.name }}
                    </router-link>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.home_banners.edit', params: {id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.home_banners.create' }">
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
                                <span v-if="getInactiveBannerCount!==0" class="counter">
                                    {{getInactiveBannerCount }}
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

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Icon from "@js/components/Icon.vue";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IHomeBanner} from "@js/types/models/home_banners";
import NetworkImage from "@js/components/NetworkImage.vue";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Button from "@js/components/buttons/Button.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, CardBody, Card, NetworkImage, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            banners: [] as IHomeBanner[],
            filter_banners: [] as IDeliveryBoy[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveBannerCount() {
            return this.banners.filter((banner) => !banner.active).length;
        },
        table_header(): ITableOption<IHomeBanner> {
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
                        label: this.$t('image'),
                        field: 'image',
                    },

                    {
                        label: this.$t('shop'),
                        field: 'shop',
                        sort: {
                            compare: (a, b) => a.shop == null || b.shop == null ? 0 : a.shop.name.toString().localeCompare(b.shop.name.toString())
                        },
                    },
                    {
                        label: this.$t('product'),
                        field: 'product',
                        sort: {
                            compare: (a, b) => a.product == null || b.product == null ? 0 : a.product.name.toString().localeCompare(b.product.name.toString())
                        },
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 120,
                        printNone: true
                    },

                ],
                exports: {
                    print: {
                        enable: true,
                        auto: true
                    }
                },
                onRefresh: () =>  this.getBanners(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_banners')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('banners'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_banners = this.banners.filter((banner) => banner.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_banners = this.banners.filter((banner) => !banner.active);
            }
        },
        async getBanners(forced=false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IHomeBanner[]>>(adminAPI.home_banners.get, {forced: forced});
                this.banners = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('home_banners'))
        this.getBanners();
    },

});

</script>
