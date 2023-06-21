<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('admins')"/>
        <Card>
            <Table :data="admins" :loading="table_loading" :option="table_header">
                <template #role="data">
                    <span v-if="data.row.is_super" class="fw-medium">{{$t('super_admin')}}</span>
                    <span v-else>{{data.row.role?.title??"-"}}</span>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.employees.edit',  params: { id: data.row.id}}">
                       <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.employees.create' }">
                        <CreateButton/>
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
import {adminAPI,} from "@js/services/api/request_url";
import Icon from "@js/components/Icon.vue";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Button from "@js/components/buttons/Button.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import {IAdmin} from "@js/types/models/admin";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            admins: [] as IAdmin[],
        }
    },
    computed: {
        table_header(): ITableOption<IAdmin> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        data: (admin) => admin.first_name + " " + admin.last_name
                    },
                    {
                        label: this.$t('email'),
                        field: 'email',
                    },

                    {
                        label: this.$t('mobile_number'),
                        field: 'mobile_number',
                    },

                    {
                        label: this.$t('role'),
                        field: 'role',
                        data: (admin: IAdmin) => {
                            return admin.is_super ? this.$t('super_admin') : admin.role?.title ?? "-";
                        }
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
                onRefresh: () => this.getAdmins(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_admin')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('admins'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IAdmin[]) {
            let list = [];
            data?.forEach((admin) => {
                list.push({
                    'ID': admin.id,
                    "Active": admin.active,
                    "Name": admin.first_name + " " + admin.last_name,
                    "Email": admin.email,
                    "Mobile Number": admin.mobile_number,
                    "Role": admin.role?.title ?? 'Main Admin',
                });
            });
            return {
                data: list,
                fileName: 'Admins'
            };
        },
        async getAdmins(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IAdmin[]>>(adminAPI.admins.get, {forced: forced});
                this.admins = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
        }
    },
    mounted() {
        this.setTitle(this.$t('admins'));
        this.getAdmins();
    }
});

</script>
