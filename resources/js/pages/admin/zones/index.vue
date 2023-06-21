<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('zones')"/>
        <Card>
            <Table :data="filter_zones" :loading="table_loading" :option="table_header">
                <template #active="data">
                    <Icon v-if="data.value" color="success" icon="check_circle"/>
                    <Icon v-else color="danger" icon="cancel"/>
                </template>
                <template #actions="data">
                    <router-link :to="{name: 'admin.zones.edit', params: {id: data.row.id}}">
                        <EditActionButton/>
                    </router-link>
                </template>
                <template v-slot:table-actions>
                    <router-link :to="{name: 'admin.zones.create' }">
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
                                <span v-if="getInactiveZoneCount!==0" class="counter">{{ getInactiveZoneCount }}</span>
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
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import {ITableOption} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Button from "@js/components/buttons/Button.vue";
import {IZone} from "@js/types/models/zone";
import EditActionButton from "@js/components/buttons/EditActionButton.vue";
import CreateButton from "@js/components/buttons/AddNewButton.vue";


export default defineComponent({
    components: {CreateButton, EditActionButton, Button, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],

    data() {
        return {
            zones: [] as IZone[],
            filter_zones: [] as IZone[],
            table_loading: true,
            filter_by: 'active',
        }
    },
    computed: {
        table_header(): ITableOption<IZone> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        labelStyle: {
                            fontWeight: 'semibold'
                        }
                    },
                    {
                        label: this.$t('active'),
                        field: 'active',
                        sort: true

                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: true,

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
                onRefresh: ()=>this.getZones(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_zone')
                }
            };
        },
        getInactiveZoneCount() {
            return this.zones.filter((zone) => !zone.active).length;
        },
        breadcrumb_items() {
            return [
                {
                    text: this.$t('zones'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IZone[]) {
            let list = [];
            data?.forEach((zone) => {
                list.push({
                    'ID': zone.id,
                    "Active": zone.active,
                    "Name": zone.name,
                });
            });
            return {
                data: list,
                fileName: 'Addons'
            };
        },
        changeFilter(filter) {
            this.filter_by = filter;
            this.applyFilter();
        },
        applyFilter() {
            if (this.filter_by == 'active') {
                this.filter_zones = this.zones.filter((zone) => zone.active);
            } else if (this.filter_by == 'inactive') {
                this.filter_zones = this.zones.filter((zone) => !zone.active);
            }
        },
        async getZones(forced=false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IZone[]>>(adminAPI.zones.get, {forced: forced});
                this.zones = response.data.data;
                this.applyFilter();
            } catch (error) {
                handleException(error);
            }
            this.table_loading = false;

        }
    },
    mounted() {
        this.setTitle(this.$t('zones'));
        this.getZones();
    }
});

</script>
