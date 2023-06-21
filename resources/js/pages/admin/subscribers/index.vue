<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('email')"/>
        <Card>
            <Table :data="subscribers" :loading="table_loading" :option="table_header">
                <template #order_id="data">
                    <router-link :to="{name: 'admin.orders.edit', params: {id:data.value}}">#{{ data.value }}
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
import {IData} from "@js/types/models/data";
import {IAdminRevenue} from "@js/types/models/revenue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import {ISubscriber} from "@js/types/models/subscriber";


export default defineComponent({
    components: {Card, CardBody, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            subscribers: [] as ISubscriber[],
        }
    },
    computed: {
        table_header(): ITableOption<ISubscriber> {
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
                        label: this.$t('email'),
                        field: 'email',
                        sort: true,
                    },
                    {
                        label: this.$t('date_&_time'),
                        field: 'created_at',
                        data: (subscriber) => this.getFormattedDateTime(subscriber.created_at,)
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: () => this.getSubscribers(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_subscriber')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('subscribers'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: ISubscriber[]) {
            let list = [];
            data?.forEach((subscriber) => {
                list.push({
                    'ID': subscriber.id,
                    "Email": subscriber.email,
                    "Date & Time": subscriber.created_at,
                });
            });
            return {
                data: list,
                fileName: 'Subscribers'
            };
        },
        async getSubscribers(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IAdminRevenue[]>>(adminAPI.subscribers.get, {forced: forced});
                this.subscribers = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('subscribers'));
        this.getSubscribers();
    }
});

</script>
