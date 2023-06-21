<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('categories')"/>
        <Card>
            <Table :data="filter_categories" :loading="table_loading" :option="table_header">
                <template #active="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #image="data">
                    <NetworkImage :src="data.value" height="40" rounded :placeholder-src="categoryPlaceholder"/>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.categories.edit', params: {id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.categories.create' }">
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
                                <span v-if="getInactiveCategoryCount!==0" class="counter">{{
                                        getInactiveCategoryCount
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
import {ICategory} from "@js/types/models/category";
import {IData} from "@js/types/models/data";
import NetworkImage from "@js/components/NetworkImage.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
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
            table_loading: true,
            categories: [] as ICategory[],
            filter_categories: [] as ICategory[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveCategoryCount() {
            return this.categories.filter((category) => !category.active).length;
        },
        table_header(): ITableOption<ICategory> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        width: 150,
                        labelStyle: {
                            fontWeight: "medium"
                        }
                    },
                    {
                        label: this.$t('active'),
                        field: 'active',
                        sort: true,
                        width: 200
                    },
                    {
                        label: this.$t('image'),
                        field: 'image',
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: true,
                        onCopy: (c)=>c.name
                    },
                    {
                        label: this.$t('module'),
                        field: 'module',
                        data: (cat) => cat.module?.title ?? cat.module_id,
                        sort: true
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
                onRefresh: () => this.getCategories(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_category')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('categories'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: ICategory[]) {
            let list = [];
            data?.forEach((category) => {
                list.push({
                    "ID": category.id,
                    "Active": category.active,
                    "Name": category.name,
                    "Module": category.module?.title,
                });
            });
            return {
                data: list,
                fileName: 'Categories'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_categories = this.categories.filter((category) => category.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_categories = this.categories.filter((category) => !category.active);
            }
        },
        async getCategories(forced = false) {
            this.loading = true;
            try {
                const response = await Request.getAuth<IData<ICategory>>(
                    Request.addAdminModule(adminAPI.categories.get),
                    {forced: forced});
                this.categories = response.data.data;
                this.applyFilter();

            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
        },
        onChangeAdminModule(){
            this.getCategories();
        }
    },
    mounted() {
        this.setTitle(this.$t('categories'));
        this.getCategories();
    }
});

</script>
