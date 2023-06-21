<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('delivery_boys')"/>

        <Card>
            <Table :data="filter_delivery_boys" :loading="table_loading" :option="table_header">
                <template #avatar="data">
                    <NetworkImage :from-text="data.row.first_name +' '+data.row.last_name" :src="data.row.avatar" circular
                                  size="44"/>
                </template>
                <template #active_for_delivery="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #shop="data">
                    <template v-if="data.value">
                        <router-link :to="{name: 'admin.shops.edit', params: {id: data.row.shop.id}}">
                            {{ data.value.name }}
                        </router-link>
                    </template>
                    <span v-else>{{ $t('global') }}</span>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.delivery_boys.edit',  params: { id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>

                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.delivery_boys.create', }">
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
                                <span v-if="getInactiveDeliveryBoyCount!==0" class="counter">{{
                                        getInactiveDeliveryBoyCount
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

import Layout from "@js/pages/admin/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";
import {defineComponent} from "vue";

export default defineComponent({
    components: {CreateButton, EditActionButton, Table, Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            delivery_boys: [] as IDeliveryBoy[],
            filter_delivery_boys: [] as IDeliveryBoy[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveDeliveryBoyCount() {
            return this.delivery_boys.filter((delivery_boy) => !delivery_boy.approved && !delivery_boy.archived).length;
        },
        table_header(): ITableOption<IDeliveryBoy> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        width: 140,
                        labelStyle: {
                            fontWeight: "medium"
                        },
                    },
                    {
                        label: this.$t('avatar'),
                        field: 'avatar',
                        width: 140,
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: {
                            compare: (first: IDeliveryBoy, second: IDeliveryBoy) => {
                                return (first.first_name + first.last_name).localeCompare(second.first_name + second.last_name)
                            }
                        },
                        data: (a) => a.first_name + " " + a.last_name,
                        onCopy: (a) => a.first_name + " " + a.last_name,
                    },

                    {
                        label: this.$t('shop'),
                        field: 'shop',
                        sort: {
                            compare: (first: IDeliveryBoy, second: IDeliveryBoy) => (first.shop.name.localeCompare(second.shop.name))
                        },
                    },
                    {
                        label: this.$t('active_for_delivery'),
                        field: 'active_for_delivery',
                        sort: true,
                        width: 200,
                    }, {
                        label: this.$t('mobile_number'),
                        field: 'mobile_number',
                        sort: true,
                        width: 200,
                        onCopy: (a) => a.mobile_number

                    },

                    {
                        label: this.$t('earning'),
                        field: 'salary_based',
                        sort: true,
                        data: (e) => e.salary_based ? this.$t('salary_based') : this.$t('delivery_based'),
                        width: 200,
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 140,
                        printNone: true
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: () => this.getDeliveryBoys(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_delivery_boy')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('delivery_boy'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IDeliveryBoy[]) {
            let list = [];
            data?.forEach((delivery) => {
                list.push({
                    'ID': delivery.id,
                    "Active": delivery.active,
                    "First Name": delivery.first_name,
                    "Last Name": delivery.last_name,
                    "Shop": delivery.shop?.name ?? 'Global',
                    "Mobile Number": delivery.mobile_number,
                });
            });
            return {
                data: list,
                fileName: 'Delivery'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_delivery_boys = this.delivery_boys.filter((delivery_boy) => delivery_boy.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_delivery_boys = this.delivery_boys.filter((delivery_boy) => !delivery_boy.approved && !delivery_boy.archived);
            }
        },
        async getDeliveryBoys(forced=false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IDeliveryBoy>>(adminAPI.delivery_boys.get, {forced: forced});
                this.delivery_boys = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;

        },

    },
    mounted() {
        this.setTitle(this.$t('delivery_boys'))
        this.getDeliveryBoys();
    }
});

</script>
