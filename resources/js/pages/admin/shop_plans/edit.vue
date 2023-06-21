<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_shop_plan')"/>

        <PageLoading :loading="page_loading">

            <Row class="mt-3">
                <Col lg="6">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="shop_plan.active" :checked="shop_plan.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody>



                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="shop_plan.title" :errors="errors"
                                               :label="$t('title')" :placeholder="$t('enter_addon_name')" name="title">
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="It should be unique, so that other plan can't conflict"/>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop_plan.sub_title" :errors="errors" :label="$t('sub_title')"
                                               name="sub_title">

                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop_plan.price" :errors="errors" :label="$t('price')"
                                               min="0" name="price" show-currency type="number">
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="This price is collect by admin every month from seller"/>
                                        </template>

                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop_plan.products" :errors="errors" :label="$t('products')"
                                               min="0" name="products" type="number">
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="How much products can create by shop with this plan"/>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop_plan.admin_commission" :errors="errors"
                                               :label="$t('admin_commission')"
                                               min="0"
                                               name="admin_commission" type="number"
                                               :prefix-or-suffix="shop_plan.admin_commission_type">
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="If admin need extra commission from shop order"/>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormSelect :data="charge_types" :helper="charge_type_helper"
                                                :onSelected="(value)=>shop_plan.admin_commission_type=value"
                                                :placeholder="$t('admin_commission_type')"
                                                :errors="errors" :label="$t('admin_commission_type')"
                                                name="admin_commission_type"
                                                :selected="shop_plan.admin_commission_type"/>
                                </Col>
                            </Row>

                            <div class="text-end">
                                <UpdateButton :loading="loading" @click="update" />
                            </div>

                        </CardBody>
                    </Card>
                </Col>

            </Row>

        </PageLoading>

    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import {IShopPlan} from "@js/types/models/shop_plan";
import ChargeTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";


export default defineComponent({
    components: {
        UpdateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin, ChargeTypeMixin],
    data() {

        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            shop_plan: {} as IShopPlan,
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('shop_plans'),
                    route: 'admin.shop_plans.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        }
    },
    methods: {

        async update() {
            this.loading = true;
            try {

                const response = await Request.patchAuth(adminAPI.shop_plans.update(this.id), {
                    ...this.shop_plan
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('shop_plan_updated'));
                    RequestCache.removeSimilar(adminAPI.shop_plans.get);
                    NavigatorService.goBackOrRoute({name: 'admin.shop_plans.index'});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error)
                }
            }
            this.loading = false;
        },

    },
    async mounted() {
        this.setTitle(this.$t('edit_shop_plan'))
        this.shop_plan = (await Request.getAuth<IData<IShopPlan>>(adminAPI.shop_plans.show(this.id))).data.data;

        this.page_loading = false;
    }
});

</script>
