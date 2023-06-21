<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('sellers')"/>
        <Card>

            <Table :data="filter_sellers" :loading="table_loading" :option="table_header">
                <template #role="data">
                    <span v-if="data.row.is_owner" class="fw-medium">{{$t('owner')}}</span>
                    <span v-else>{{data.row.role?.title??"-"}}</span>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.sellers.edit',  params: { id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.sellers.create' }">
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
                                <span v-if="getInactiveSellerCount!==0" class="counter">
                                    {{ getInactiveSellerCount }}
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
import {ISeller} from "@js/types/models/seller";
import {ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Button from "@js/components/buttons/Button.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin, AdminModuleSelectorMixin],
    data() {
        return {
            table_loading: true,
            sellers: [] as ISeller[],
            filter_sellers: [] as ISeller[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveSellerCount() {
            return this.sellers.filter((seller) => !seller.active).length;
        },
        table_header(): ITableOption<ISeller> {
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
                        label: this.$t('name'),
                        field: 'name',
                        sort: true,
                        data: (seller) => seller.first_name + " " + seller.last_name,
                        onCopy: (seller) => seller.first_name + " " + seller.last_name,
                    },
                    {
                        label: this.$t('shop'),
                        field: 'shop',
                        sort: true,
                        data: (seller) => seller.shop?.name ?? this.$t('not_assigned'),
                        onClick: (seller) => this.$router.push({name: 'admin.shops.edit', params: {id: seller.shop_id}})
                    },

                    {
                        label: this.$t('email'),
                        field: 'email',
                        sort: true,
                        onCopy: (seller) => seller.email

                    },
                    {
                        label: this.$t('mobile_number'),
                        field: 'mobile_number',
                        onCopy: (seller) => seller.mobile_number
                    },
                    {
                        label: this.$t('role'),
                        field: 'role',

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
                    message: this.$t('there_is_no_any_sellers')
                }
            };
        },
        breadcrumb_items() {
            return [
                {
                    text: this.$t('sellers'),
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
                    "Shop": seller.shop?.name ?? '-',
                    "Email": seller.email,
                    "Mobile Number": seller.mobile_number,
                    "Role": seller.role?.title ?? 'Main Seller',
                });
            });
            return {
                data: list,
                fileName: 'Sellers'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_sellers = this.sellers.filter((seller) => seller.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_sellers = this.sellers.filter((seller) => !seller.active);
            }
        },
        async getSellers(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<ISeller[]>>(Request.addAdminModule(adminAPI.sellers.get), {forced: forced});
                this.sellers = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;
        },
        onChangeAdminModule(module_id){
            this.getSellers();
        }
    },
    mounted() {
        this.setTitle(this.$t('sellers'));
        this.getSellers();
    }
});

</script>
