<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('units')"/>
        <Card>
            <Table :data="filter_units" :loading="table_loading" :option="table_header">
                <template #active="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>

                <template #actions="data">
                    <router-link :to="{name: 'admin.units.edit', params: {id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.units.create' }">
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
                                <span v-if="getInactiveUnitCount!==0" class="counter">
                                    {{ getInactiveUnitCount }}
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
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import NetworkImage from "@js/components/NetworkImage.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import Button from "@js/components/buttons/Button.vue";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import {IUnit} from "@js/types/models/unit";
import CreateButton from "@js/components/buttons/AddNewButton.vue";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, Card, NetworkImage, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            units: [] as IUnit[],
            filter_units: [] as IUnit[],
            filter_by: 'active',
        }
    },
    computed: {
        getInactiveUnitCount() {
            return this.units.filter((unit) => !unit.active).length;
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("units"),
                    active: true,
                },
            ];
        },
        table_header(): ITableOption<IUnit> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        labelStyle: {
                            fontWeight: "medium"
                        },
                        width: 150,
                        sort: true
                    },

                    {
                        label: this.$t('active'),
                        field: 'active',
                        sort: true,
                        width: 150
                    },
                    {
                        label: this.$t('type'),
                        field: 'type',
                        sort: true

                    },
                    {
                        label: this.$t('title'),
                        field: 'title',
                        sort: true

                    }, {
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
                onRefresh: () => this.getUnits(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_units')
                }
            };
        }
    },
    methods: {
        generateData(data: IUnit[]) {
            let list = [];
            data?.forEach((unit) => {
                list.push({
                    "ID": unit.id,
                    "Active": unit.active,
                    "Type": unit.type,
                    "Title": unit.title,
                    "Description": unit.description,
                });
            });
            return {
                data: list,
                fileName: 'Units'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_units = this.units.filter((unit) => unit.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_units = this.units.filter((unit) => !unit.active);
            }
        },
        async getUnits(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IUnit>>(adminAPI.units.get, {forced: forced});
                this.units = response.data.data;
                this.applyFilter();
            } catch (error) {
                console.info(error);
                handleException(error);
            }
            this.table_loading = false;

        }
    },
    mounted() {
        this.setTitle(this.$t('units'));
        this.getUnits();
    }
});

</script>
