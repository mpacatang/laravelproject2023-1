<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('customers')"/>

        <Card>
            <Table :data="filter_customers" :loading="table_loading" :option="table_header">
                <template #avatar="data">
                   <span class="d-flex align-items-center">
                        <NetworkImage :src="data.value" circular size="36" show-full-image
                                      :from-text="data.row.first_name + ' ' + data.row.last_name" />
                    <span class="ms-2-5">{{ data.row.first_name }} {{ data.row.last_name }}</span>
                   </span>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.customers.edit', params: {id:  data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.customers.create' }">
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
                                <span v-if="getInactiveCustomerCount!==0" class="counter">{{
                                        getInactiveCustomerCount
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
import {ICustomer} from "@js/types/models/customer";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import Icon from "@js/components/Icon.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import NetworkImage from "@js/components/NetworkImage.vue";


export default defineComponent({
    components: {NetworkImage, CreateButton, EditActionButton, Icon, Card, CardBody, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            customers: [] as ICustomer[],
            filter_customers: [] as ICustomer[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveCustomerCount() {
            return this.customers.filter((customer) => !customer.active).length;
        },
        table_header(): ITableOption<ICustomer> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        labelStyle: {
                            fontWeight: "medium"
                        },
                        width: 100
                    },
                    {
                        label: this.$t('name'),
                        field: 'avatar',
                        sort: {
                            compare: (a, b) => (a.first_name + " " + a.last_name).localeCompare(b.first_name + " " + b.last_name,)
                        },
                    },

                    {
                        label: this.$t('email'),
                        field: 'email',
                        sort: true,
                        onCopy: (a) => a.email


                    },
                    {
                        label: this.$t('mobile_number'),
                        field: 'mobile_number',
                        sort: true,
                        onCopy: (a) => a.mobile_number
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
                onRefresh: () => this.getCustomers(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_customers')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('customers'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: ICustomer[]) {
            let list = [];
            data?.forEach((customer) => {
                list.push({
                    'ID': customer.id,
                    "Active": customer.active,
                    "First Name": customer.first_name,
                    "Last Name": customer.last_name,
                    "Email": customer.email,
                    "Mobile Number": customer.mobile_number,
                    "Referral": customer.referral,
                });
            });
            return {
                data: list,
                fileName: 'Customers'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_customers = this.customers.filter((customer) => customer.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_customers = this.customers.filter((customer) => !customer.active);
            }
        },
        async getCustomers(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<ICustomer>>(adminAPI.customers.get, {forced: forced});
                this.customers = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('customers'));
        this.getCustomers();
    }
});

</script>
