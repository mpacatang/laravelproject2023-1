<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('addons')"/>
        <Card>
            <Table :data="filter_addons" :loading="table_loading" :option="table_header">
                <template #image="data">
                    <NetworkImage :src="data.value" height="40" rounded/>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.addons.edit', params: {id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.addons.create' }">
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
                                <span v-if="getInactiveAddonCount!==0" class="counter">{{ getInactiveAddonCount }}</span>
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
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, Card, NetworkImage, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin, AdminModuleSelectorMixin],
    data() {
        return {
            addons: [] as IAddon[],
            filter_addons: [] as IAddon[],

            table_loading: true,
            filter_by: 'active',

        }
    },
    computed: {
        getInactiveAddonCount() {
            return this.addons.filter((addon) => !addon.active).length;
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: "Addons",
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
                        label: this.$t('image'),
                        field: 'image',
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: true
                    },
                    {
                        label: this.$t('shop'),
                        field: 'shop',
                        data: (addon) => addon.shop.name,
                        sort: true,
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
                onRefresh: ()=>this.getAddons(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_addons')
                }
            };
        }
    },
    methods: {
        generateData(data: IAddon[]) {
            let list = [];
            data?.forEach((addon) => {
                list.push({
                    'ID': addon.id,
                    "Active": addon.active,
                    "Name": addon.name,
                    "Price": addon.price,
                    "Shop ID": addon.shop_id,
                    "Shop Name": addon.shop?.name,
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
                this.filter_addons = this.addons.filter((addon) => addon.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_addons = this.addons.filter((addon) => !addon.active);
            }
        },
        async getAddons(forced=false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IAddon>>(Request.addAdminModule(adminAPI.addons.get), {forced: forced});
                this.addons = response.data.data;
                this.applyFilter();

            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        },
        onChangeAdminModule(){
            this.getAddons();
        }
    },
    mounted() {
        this.setTitle(this.$t('addons'));
        this.getAddons();
    }
});

</script>
