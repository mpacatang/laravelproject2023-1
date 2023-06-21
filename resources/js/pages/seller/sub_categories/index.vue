<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('sub_categories')"/>

        <Card>
            <Table :data="sub_categories" :loading="table_loading" :option="table_header"/>
        </Card>

    </Layout>
</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/seller/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {ISubCategory} from "@js/types/models/category";
import {IData} from "@js/types/models/data";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";


export default defineComponent({
    components: {CardBody, Card, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            sub_categories: [] as ISubCategory[],
        }
    },
    computed: {
        table_header(): ITableOption<ISubCategory> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        sort: true,
                        labelStyle: {
                            fontWeight: "medium"
                        },
                        width: 150
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: true
                    },
                    {
                        label: this.$t('category'),
                        field: 'category',
                        sort: true,
                        data: (sc) => sc.category.name
                    },

                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: () => this.getSubCategories(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_sub_category')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('sub_categories'),
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
        async getSubCategories(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<ISubCategory>>(sellerAPI.sub_categories.get, {forced: forced});
                this.sub_categories = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('sub_categories'));
        this.getSubCategories();
    }
});

</script>
