<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('employee_roles')"/>
        <Card>
            <Table :data="roles" :loading="table_loading" :option="table_header">
                <template #actions="data">
                    <router-link :to="{name: 'admin.roles.edit', params: {id:data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.roles.create' }">
                        <CreateButton/>
                    </router-link>
                </template>
                <template v-slot:pre-actions>
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
                                <span v-if="getInactiveRoleCount!==0" class="counter">
                                    {{ getInactiveRoleCount }}
                                </span>
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
import {IData} from "@js/types/models/data";
import {IAdminRole} from "@js/types/models/admin";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import VItem from "@js/components/VItem.vue";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import Button from "@js/components/buttons/Button.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";


export default defineComponent({
    components: {EditActionButton, CreateButton, Button, VItem, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            roles: [] as IAdminRole[],
            filter_roles: [] as IDeliveryBoy[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveRoleCount() {
            return this.roles.filter((role) => !role.active).length;
        },
        table_header(): ITableOption<IAdminRole> {
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
                        label: this.$t('title'),
                        field: 'title',
                    },
                    {
                        label: this.$t('permissions'),
                        field: 'permissions',
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
                onRefresh: () => this.getRoles(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_roles')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('roles'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IAdminRole[]) {
            let list = [];
            data?.forEach((role) => {
                list.push({
                    'ID': role.id,
                    "Title": role.title,
                    "Permission": role.permissions,
                });
            });
            return {
                data: list,
                fileName: 'Admin Roles'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_roles = this.roles.filter((role) => role.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_roles = this.roles.filter((role) => !role.active);
            }
        },

        async getRoles(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IAdminRole[]>>(adminAPI.roles.get, {forced: forced});
                this.roles = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('roles'))
        this.getRoles();
    }
});

</script>
