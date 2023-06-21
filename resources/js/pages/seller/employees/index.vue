<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('employees')"/>
        <Card>
            <Table :data="sellers" :loading="table_loading" :option="table_header">
                <template #actions="data">
                    <router-link :to="{name: 'seller.employees.edit',  params: { id: data.row.id}}">
                       <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'seller.employees.create' }">
                        <CreateButton/>
                    </router-link>
                </template>
            </Table>

        </Card>

    </Layout>
</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/seller/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Icon from "@js/components/Icon.vue";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {ISeller} from "@js/types/models/seller";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Button from "@js/components/buttons/Button.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            sellers: [] as ISeller[],
        }
    },
    computed: {
        table_header(): ITableOption<ISeller> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        data: (seller) => seller.first_name + " " + seller.last_name
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
                        data: (seller: ISeller) => {
                            return seller.is_owner ? this.$t('owner') : seller.role?.title ?? "-";
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
                onRefresh: () => this.getSellers(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_employee')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('employees'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: ISeller[]) {
            let list = [];
            data?.forEach((seller) => {
                list.push({
                    'ID': seller.id,
                    "Active": seller.active,
                    "Name": seller.first_name + " " + seller.last_name,
                    "Email": seller.email,
                    "Mobile Number": seller.mobile_number,
                    "Role": seller.role?.title ?? 'Main Seller',
                });
            });
            return {
                data: list,
                fileName: 'Employees'
            };
        },
        async getSellers(forced=false) {
            this.table_loading = true;

            try {
                const response = await Request.getAuth<IData<ISeller[]>>(sellerAPI.sellers.get, {forced: forced});
                this.sellers = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;

            }
        }
    },
    mounted() {
        this.setTitle(this.$t('employees'));
        this.getSellers();
    }
});

</script>
