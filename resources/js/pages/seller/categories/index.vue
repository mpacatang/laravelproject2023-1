<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('categories')"/>
        <Card>
            <Table :data="categories" :loading="table_loading" :option="table_header">
                <template #image="data">
                    <NetworkImage :src="data.value" height="40" rounded :placeholder-src="categoryPlaceholder"/>
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
import {defineComponent} from "vue";
import {ICategory} from "@js/types/models/category";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";


export default defineComponent({
    components: {Layout, ...Components, Table},
    mixins: [UtilMixin],
    data() {
        return {
            categories: [] as ICategory[],
            table_loading: true,
        }
    },
    computed: {
        table_header(): ITableOption<ICategory> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        sort: true,
                        width: 200
                    },
                    {
                        label: this.$t('image'),
                        field: 'image',
                        width: 200
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: true
                    },

                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: ()=> this.getCategories(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_categories')
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
                });
            });
            return {
                data: list,
                fileName: 'Categories'
            };
        },
        async getCategories(forced=false) {
            this.table_loading = true;

            try {
                const response = await Request.getAuth<IData<ICategory>>(sellerAPI.categories.get, {forced: forced});
                this.categories = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;

        }
    },
    mounted() {
        this.setTitle(this.$t('categories'));
        this.getCategories();
    }
});

</script>
