<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('modules')"/>
        <Card>
            <Table :data="modules" :loading="table_loading" :option="table_header">
                <template #active="data">
                    <Icon v-if="data.value" class="text-success" icon="task_alt"></Icon>
                    <Icon v-else class="text-danger" icon="cancel"></Icon>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.modules.edit', params: {id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
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
import {IModule} from "@js/types/models/module";

export default defineComponent({
    components: {CreateButton, EditActionButton, Button, Card, NetworkImage, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            modules: [] as IModule[],
        }
    },
    computed: {

        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('modules'),
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
                        label: this.$t('active'),
                        field: 'active',
                        sort: true,
                        width: 150

                    },
                    {
                        label: this.$t('title'),
                        field: 'title',
                        sort: true,
                        labelStyle: {
                            fontWeight: "medium"
                        },
                    },
                    {
                        label: this.$t('shops'),
                        field: 'shops_count',
                        sort: true,
                    },
                    {
                        label: this.$t('products'),
                        field: 'products_count',
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
                onRefresh: ()=>this.getModules(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_modules')
                }
            };
        }
    },
    methods: {
        generateData(data: IModule[]) {
            let list = [];
            data?.forEach((module) => {
                list.push({
                    'ID': module.id,
                    "Title": module.title,
                    "Product Count": module.products_count,
                    "Shop Count": module.shops_count,

                });
            });
            return {
                data: list,
                fileName: 'Modules'
            };
        },

        async getModules(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IModule>>(adminAPI.modules.get, {forced: forced});
                this.modules = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;

        }
    },
    mounted() {
        this.setTitle(this.$t('modules'));
        this.getModules();
    }
});

</script>
