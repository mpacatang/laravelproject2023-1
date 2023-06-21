<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('revenues')"/>
        <Card>
            <Table :data="revenues" :loading="table_loading" :option="table_header">
                <template #order_id="data">
                    <router-link :to="{name: 'admin.orders.edit', params: {id:data.value}}">#{{ data.value }}
                    </router-link>
                </template>
                <template #created_at="data">
                    <span>{{ getFormattedDateTime(data.value,) }}</span>
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
import {IAdminRevenue} from "@js/types/models/revenue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";


export default defineComponent({
    components: {Card, CardBody, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            revenues: [] as IAdminRevenue[],
        }
    },
    computed: {
        table_header(): ITableOption<IAdminRevenue> {
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
                        label: this.$t('revenue'),
                        field: 'revenue',
                        sort: true,
                        data: (revenue) => this.getFormattedCurrency(revenue.revenue)
                    },
                    {
                        label: this.$t('order_id'),
                        field: 'order_id',
                        sort: true,
                    },

                    {
                        label: this.$t('date_&_time'),
                        field: 'created_at',
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: () => this.getRevenues(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_revenue')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('revenues'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IAdminRevenue[]) {
            let list = [];
            data?.forEach((revenue) => {
                list.push({
                    'ID': revenue.id,
                    "Revenue": revenue.revenue,
                    "Order ID": revenue.order_id,
                    "Date & Time": revenue.created_at,
                });
            });
            return {
                data: list,
                fileName: 'Admin Revenues'
            };
        },
        async getRevenues(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IAdminRevenue[]>>(adminAPI.revenues.get, {forced: forced});
                this.revenues = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('admin_revenues'));
        this.getRevenues();
    }
});

</script>
