<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('manual_notifications')"/>
        <Card>
            <Table :data="notifications" :loading="table_loading" :option="table_header">
                <template #body="data">
                    <span class="max-lines-2"  v-html="data.value"/>
                </template>
                <template #all_customers="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #all_sellers="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #all_delivery_boys="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.manual_notifications.edit',  params: { id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.manual_notifications.create' }">
                        <CreateButton/>
                    </router-link>
                </template>
            </Table>

        </Card>

    </Layout>
</template>


<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Badge from "@js/components/Badge.vue";
import PageLoading from "@js/components/PageLoading.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IManualNotification} from "@js/types/models/manual_notification";
import Table from "@js/components/Table.vue";
import {ITableOption} from "@js/types/models/models";
import {IData} from "@js/types/models/data";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/CreateButton.vue";

export default defineComponent({
    components: {
        CreateButton,
        EditActionButton,
        Table,
        PageLoading,
        Badge,
        CardBody,
        Card,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            table_loading: true,
            notifications: [] as IManualNotification[],

        }
    },
    computed: {
        table_header(): ITableOption<IManualNotification> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                    },
                    {
                        label: this.$t('title'),
                        field: 'title',

                    },
                    {
                        label: this.$t('body'),
                        field: 'body',
                    },

                    {
                        label: this.$t('all_customers'),
                        field: 'all_customers',
                        width: 160,
                    },
                    {
                        label: this.$t('all_sellers'),
                        field: 'all_sellers',
                        width: 160,
                    },

                    {
                        label: this.$t('all_delivery_boys'),
                        field: 'all_delivery_boys',
                        width: 160,
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
                onRefresh: ()=>this.getNotifications(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_notifications')
                }
            };
        },
        breadcrumb_items() {
            return [
                {
                    text: this.$t("manual_notification"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IManualNotification[]) {
            let list = [];
            data?.forEach((notification) => {
                list.push({
                    'ID': notification.id,
                    "Title": notification.title,
                    "Body": notification.body,
                    "All Customers": notification.all_customers,
                    "All Sellers": notification.all_sellers,
                    "All Delivery": notification.all_delivery_boys,
                    "Created At": this.getFormattedDateTime(notification.created_at),
                });
            });
            return {
                data: list,
                fileName: 'Manual Notifications'
            };
        },
        async getNotifications(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IManualNotification[]>>(adminAPI.manual_notifications.get, {forced: forced});
                this.notifications = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
        }
    },
    mounted() {
        this.setTitle(this.$t('manual_notifications'));
        this.getNotifications();
    }
});

</script>
