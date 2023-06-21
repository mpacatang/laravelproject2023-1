<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('shop_plan')"/>
        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6" lg="9">
                    <Card>
                        <CardHeader :title="$t('upgrade_plan')" icon="workspace_premium"></CardHeader>
                        <CardBody>
                            <Row>
                                <Col lg="6">
                                    <p class="text-muted fw-medium mb-1">{{ $t('current_plan') }}</p>
                                    <p v-if="current_plan" class="fw-medium">{{ current_plan.title }}</p>
                                    <p v-else class="fw-medium">{{ $t('not_activate_any_plan') }}</p>
                                </Col>
                                <Col lg="6">
                                    <FormSelect :data="plans" :helper="shop_plan_helper"
                                                :onSelected="onChangePlan"
                                                :errors="errors"
                                                :placeholder="$t('select_plan')"
                                                :label="$t('upgrade_to')" name="shop_plan_id"
                                                :selected="upgrade_to"
                                    />
                                </Col>

                                <div class="text-end">
                                    <UpdateButton :disabled="!canUpgrade" :loading="loading" @click="upgrade"/>
                                </div>

                            </Row>

                        </CardBody>
                    </Card>
                </Col>
                <Col lg="12">
                    <Card>
                        <Table :data="plan_histories" :option="table_header">
                            <template #ended_at="data">
                                <span v-if="data.value">{{ getFormattedDateTime(data.value) }}</span>
                                <span v-else class="text-primary fw-medium">{{ $t('current_plan') }}</span>
                            </template>
                            <template #order_id="data">
                                <router-link :to="{name: 'seller.orders.edit', params: {id:data.value}}">#{{
                                        data.value
                                    }}
                                </router-link>
                            </template>
                        </Table>
                    </Card>
                </Col>


            </Row>
        </PageLoading>
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
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import {IBreadcrumb, IFormSelectOption, ITableOption} from "@js/types/models/models";
import {IShopPlan, IShopPlanHistory} from "@js/types/models/shop_plan";
import PageLoading from "@js/components/PageLoading.vue";
import Row from "@js/components/Row.vue";
import Col from "@js/components/Col.vue";
import CardHeader from "@js/components/CardHeader.vue";
import FormSelect from "@js/components/form/FormSelect.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import {ToastNotification} from "@js/services/toast_notification";
import Response from "@js/services/api/response";
import FormSwitch from "@js/components/form/FormSwitch.vue";
import FormCheckbox from "@js/components/form/FormCheckbox.vue";
import FormValidationError from "@js/components/form/FormValidationError.vue";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";


export default defineComponent({
    components: {
        UpdateButton,
        FormValidationError,
        FormCheckbox,
        FormSwitch,
        LoadingButton,
        FormSelect, CardHeader, Col, Row, PageLoading, CardBody, Card, Icon, Table, PageHeader, Layout,
    },
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {
        return {
            page_loading: true,
            loading: false,
            plans: [] as IShopPlan[],
            plan_histories: [] as IShopPlanHistory[],
            current_plan: null as IShopPlan | null,
            upgrade_to: null,

        }
    },
    computed: {
        table_header(): ITableOption<IShopPlanHistory> {
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
                        label: this.$t('plan'),
                        field: 'plan',
                        data: (history) => history.shop_plan.title
                    },
                    {
                        label: this.$t('price'),
                        field: 'price',
                        data: (history) => this.getFormattedCurrency(history.price)
                    },
                    {
                        label: this.$t('started_at'),
                        field: 'started_at',
                        data: (history) => this.getFormattedDateTime(history.started_at)
                    },

                    {
                        label: this.$t('ended_at'),
                        field: 'ended_at',
                    },
                    {
                        label: this.$t('duration'),
                        field: 'duration',
                        data: (history) => history.duration == null ? "-" : this.getDurationInText(history.duration)
                    },
                ],
                exports: {
                    print: {
                        enable: true,
                        auto: true
                    }
                },
                onRefresh: () => this.getData(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_revenue')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('shop_plan'),
                    active: true,
                },
            ];
        },
        shop_plan_helper(): IFormSelectOption<IShopPlan> {
            return {
                option: {
                    value: 'id',
                    label: (plan) => plan.title + (plan.id == this.current_plan.id ? ` - ${this.$t('current_plan')}` : "")
                },
            };
        },
        canUpgrade() {
            return this.upgrade_to != this.current_plan?.id;
        }
    },
    methods: {
        async upgrade() {
            this.clearAllError();
            if(this.upgrade_to==null){
                this.addError('shop_plan_id', this.$t('select_plan_to_upgrade'));
                return;
            }
            this.loading = true;
            try {
                const response = await Request.postAuth(sellerAPI.shop_plans.upgrade, {
                    shop_plan_id: this.upgrade_to
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('plan_is_upgraded'));
                    await this.getData();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        },
        onChangePlan(id) {
            this.upgrade_to = id;
        },
        async getData(forced = false) {
            this.page_loading = true;
            try {
                const planResponse = await Request.getAuth<IData<IShopPlan>>(sellerAPI.shop_plans.get, {forced: forced});
                this.plans = planResponse.data.data;
                const planHistoryResponse = await Request.getAuth<IData<IShopPlanHistory>>(sellerAPI.shop_plan_histories.get, {forced: forced});
                this.plan_histories = planHistoryResponse.data.data;
                if (this.plan_histories.length > 0 && this.plan_histories[0].end_at == null) {
                    this.current_plan = this.plan_histories[0].shop_plan;
                    this.upgrade_to = this.current_plan.id;
                }
            } catch (error) {
                handleException(error);
            } finally {
                this.page_loading = false;
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('shop_plan'))
        this.getData();
    }
});

</script>
