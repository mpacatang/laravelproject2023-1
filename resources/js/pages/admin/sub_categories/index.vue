<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('sub_categories')"/>

        <Card>
            <Table :data="filter_sub_categories" :loading="table_loading" :option="table_header">
                <template #active="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.sub_categories.edit', params: {id:data.row.id} }">
                        <EditActionButton/>

                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.sub_categories.create' }">
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
                                <span v-if="getInactiveSubCategoryCount!==0" class="counter">{{
                                        getInactiveSubCategoryCount
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
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {ISubCategory} from "@js/types/models/category";
import {IData} from "@js/types/models/data";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import Icon from "@js/components/Icon.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";


export default defineComponent({
    components: {CreateButton, EditActionButton, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin, AdminModuleSelectorMixin],
    data() {
        return {
            table_loading: true,
            sub_categories: [] as ISubCategory[],
            filter_sub_categories: [] as ISubCategory[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveSubCategoryCount() {
            return this.sub_categories.filter((sub_category) => !sub_category.active).length;
        },
        table_header(): ITableOption<ISubCategory> {
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
                        label: this.$t('active'),
                        field: 'active',
                        sort: true,
                        width: 200
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: true
                    },
                    {
                        label: this.$t('category'),
                        field: 'category',
                        data: (data) => data.category.name,
                        sort: true,
                        labelStyle: {
                          fontWeight: 'medium'
                        },
                        onClick: (data) => this.$router.push({
                            name: 'admin.categories.edit',
                            params: {id: data.category_id}
                        })

                    },

                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 120
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: ()=>this.getSubCategories(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_sub_categories')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("sub_categories"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: ISubCategory[]) {
            let list = [];
            data?.forEach((cat) => {
                list.push({
                    "ID": cat.id,
                    "Active": cat.active,
                    "Name": cat.name,
                    "Category ID": cat.category_id,
                    "Category Name": cat.category?.name,
                });
            });
            return {
                data: list,
                fileName: 'Sub Categories'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_sub_categories = this.sub_categories.filter((sub_category) => sub_category.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_sub_categories = this.sub_categories.filter((sub_category) => !sub_category.active);
            }
        },
        async getSubCategories(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<ISubCategory>>(Request.addAdminModule(adminAPI.sub_categories.get), {forced: forced});
                this.sub_categories = response.data.data;
                this.applyFilter();

            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
        },
        onChangeAdminModule(module_id){
            this.getSubCategories();
        }
    },
    mounted() {
        this.setTitle(this.$t('sub_categories'))
        this.getSubCategories();
    }
});


</script>
