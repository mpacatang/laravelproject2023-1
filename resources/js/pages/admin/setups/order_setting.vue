<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('order_settings')"/>
        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('delivery_setting')" icon="local_shipping" size="sm"
                                    type="msr"/>

                        <CardBody class="pb-0">
                            <Row>
                                <Col lg="6">
                                    <FormSwitch v-model="order_setting.can_delivery_boy_reject_order" :errors="errors"
                                                :label="$t('can_delivery_boy_reject_order')"
                                                name="can_delivery_boy_reject_order">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="If enable this, then delivery can accept or decline order"></InfoTooltip>
                                        </template>
                                    </FormSwitch>
                                </Col>
                                <Col lg="6">
                                    <FormSwitch
                                        v-model="order_setting.enable_delivery_otp_verification_for_delivery_boy"
                                        :errors="errors"
                                        :label="$t('enable_delivery_otp_verification')"
                                        name="enable_delivery_otp_verification_for_delivery_boy">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="If enable this, then delivery need otp to deliver order, it can provide by shop or customer"></InfoTooltip>
                                        </template>
                                    </FormSwitch>
                                </Col>
                                <Col lg="6">
                                    <FormSwitch
                                        v-model="order_setting.auto_assign_delivery_boy"
                                        :errors="errors"
                                        :label="$t('auto_assign_delivery_boy')"
                                        name="auto_assign_delivery_boy">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="If enable this, then nearest delivery automatic assign when order is ready"></InfoTooltip>
                                        </template>
                                    </FormSwitch>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="order_setting.minimum_delivery_charge" :errors="errors"
                                               :label="$t('minimum_delivery_charge')"
                                               :placeholder="$t('minimum_delivery_charge')" min="0"
                                               name="minimum_delivery_charge"
                                               show-currency type="number">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="It is global delivery charge (minimum)"></InfoTooltip>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="order_setting.delivery_charge_multiplier" :errors="errors"
                                               :label="$t('delivery_charge_multiplier')"
                                               min="0"
                                               name="delivery_charge_multiplier"
                                               show-currency type="number">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="It is global delivery charge (multiplier of every KM)"></InfoTooltip>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="order_setting.max_order_assign_to_delivery_boy" :errors="errors"
                                               :label="$t('max_order_assign_to_delivery_boy')"
                                               min="0"
                                               name="max_order_assign_to_delivery_boy"
                                               type="number">
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="How many order can delivery hold"></InfoTooltip>
                                        </template>
                                    </FormInput>
                                </Col>
                            </Row>

                        </CardBody>

                    </Card>
                </Col>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('commission_settings')" icon="monetization_on" type="msr"></CardHeader>

                        <CardBody class="pb-0">
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="order_setting.delivery_commission" :errors="errors"
                                               :label="$t('delivery_commission')"
                                               :prefix-or-suffix="order_setting.delivery_commission_type"
                                               min="0"
                                               name="delivery_commission"
                                               type="number"

                                    />
                                </Col>
                                <Col lg="6">

                                    <FormSelect :data="charge_types" :errors="errors"
                                                :helper="charge_type_helper"
                                                :label="$t('delivery_commission_type')"
                                                :onSelected="(value)=> order_setting.delivery_commission_type=value"
                                                :placeholder="$t('delivery_commission_type')"
                                                :selected="order_setting.delivery_commission_type"
                                                name="delivery_commission_type"/>

                                </Col>
                            </Row>


                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="order_setting.payment_charge" :errors="errors"
                                               :label="$t('payment_charge')"
                                               :prefix-or-suffix="order_setting.payment_charge_type"
                                               min="0"
                                               name="payment_charge"
                                               type="number"

                                    />
                                </Col>
                                <Col lg="6">
                                    <FormSelect :data="charge_types"
                                                :errors="errors"
                                                :helper="charge_type_helper"
                                                :label="$t('payment_charge_type')"
                                                :onSelected="(value)=> order_setting.payment_charge_type=value"
                                                :placeholder="$t('payment_charge_type')"
                                                :selected="order_setting.payment_charge_type"
                                                name="payment_charge_type"
                                    />

                                </Col>
                            </Row>


                        </CardBody>

                    </Card>


                </Col>
                <Col lg="12" xl="12" xxl="6">
                    <Card>
                        <CardHeader :title="$t('notification')" class="p-0 px-3 py-2-5" icon="edit_notifications"
                                    size="sm"
                                    type="msr">
                            <div class="float-end mb-n2-5">
                                <ul class="nav nav-tabs nav-tabs-custom-icons border-none" role="tablist">
                                    <li class="nav-item " role="presentation">
                                        <a aria-expanded="false" aria-selected="false"
                                           class="nav-link px-3 cursor-pointer d-flex align-items-center active "
                                           data-bs-toggle="tab" href="#customer-notification-content" role="tab"
                                           tabindex="-1">
                                            <Icon class="icon" icon="person" size="16"></Icon>
                                            <span class="ms-2">{{ $t('customer') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a aria-expanded="false" aria-selected="false"
                                           class="nav-link px-3 cursor-pointer d-flex align-items-center"
                                           data-bs-toggle="tab" href="#delivery-notification-content" role="tab"
                                           tabindex="-1">
                                            <Icon class="icon" icon="local_shipping" size="16"></Icon>
                                            <span class="ms-2">{{ $t('delivery_boy') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a aria-expanded="false" aria-selected="false"
                                           class="nav-link px-3 cursor-pointer d-flex align-items-center"
                                           data-bs-toggle="tab" href="#seller-notification-content" role="tab"
                                           tabindex="-1">
                                            <Icon class="icon" icon="storefront" size="16"></Icon>
                                            <span class="ms-2">{{ $t('seller') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a aria-expanded="false" aria-selected="false"
                                           class="nav-link px-3 cursor-pointer d-flex align-items-center"
                                           data-bs-toggle="tab" href="#admin-notification-content" role="tab"
                                           tabindex="-1">
                                            <Icon class="icon" icon="admin_panel_settings" size="16"></Icon>
                                            <span class="ms-2">{{ $t('admin') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </CardHeader>


                        <CardBody class="pb-0">

                            <div class="tab-content">
                                <div id="customer-notification-content"
                                     class="tab-pane active" role="tabpanel">
                                    <Row>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.cancel_by_seller_for_customer_notification"
                                                :errors="errors"
                                                :label="$t('cancel_by_seller')"
                                                name="cancel_by_seller_for_customer_notification"
                                                rows="2" type="text"/>
                                        </Col>

                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_reject_for_customer_notification"
                                                :errors="errors"
                                                :label="$t('order_reject')"
                                                name="order_reject_for_customer_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_accept_for_customer_notification"
                                                :errors="errors"
                                                :label="$t('order_accept')"
                                                name="order_accept_for_customer_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_processing_for_customer_notification"
                                                :errors="errors"
                                                :label="$t('order_processing')"
                                                name="order_processing_for_customer_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_estimate_time_change_for_customer_notification"
                                                :errors="errors"
                                                :label="$t('estimate_time_change')"
                                                name="order_estimate_time_change_for_customer_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.delivery_boy_accept_for_customer_notification"
                                                :errors="errors"
                                                :label="$t('delivery_boy_accept')"
                                                name="delivery_boy_accept_for_customer_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea v-model="order_setting.order_ready_for_customer_notification"
                                                           :errors="errors"
                                                           :label="$t('order_ready')"
                                                           name="order_ready_for_customer_notification"
                                                           rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea v-model="order_setting.out_for_delivery_notification"
                                                           :errors="errors"
                                                           :label="$t('out_for_delivery')"
                                                           name="out_for_delivery_notification"
                                                           rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_delivered_for_customer_notification"
                                                :errors="errors"
                                                :label="$t('order_deliver')"
                                                name="order_ready_for_customer_notification"
                                                rows="2" type="text"/>
                                        </Col>

                                    </Row>
                                </div>

                                <div id="delivery-notification-content"
                                     class="tab-pane" role="tabpanel">
                                    <Row>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_processing_for_delivery_boy_notification"
                                                :errors="errors"
                                                :label="$t('order_processing')"

                                                name="order_processing_for_delivery_boy_notification"
                                                rows="2"
                                                type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.assign_order_for_delivery_boy_notification"
                                                :errors="errors"
                                                :label="$t('assign_order')"
                                                name="assign_order_for_delivery_boy_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_ready_for_delivery_boy_notification"
                                                :errors="errors"
                                                :label="$t('order_ready')"
                                                name="order_ready_for_delivery_boy_notification"
                                                rows="2" type="text"/>
                                        </Col>


                                    </Row>
                                </div>
                                <div id="seller-notification-content"
                                     class="tab-pane" role="tabpanel">
                                    <Row>
                                        <Col lg="6">
                                            <FormInputArea v-model="order_setting.new_order_for_seller_notification"
                                                           :errors="errors"
                                                           :label="$t('new_order')"
                                                           name="new_order_for_seller_notification"
                                                           rows="2" type="text"/>
                                        </Col>

                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_payment_done_for_seller_notification"
                                                :errors="errors"
                                                :label="$t('order_payment_done')"
                                                name="order_payment_done_for_seller_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.cancel_by_customer_for_seller_notification"
                                                :errors="errors"
                                                :label="$t('cancel_by_customer')"
                                                name="cancel_by_customer_for_seller_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_resubmit_for_seller_notification"
                                                :errors="errors"
                                                :label="$t('order_resubmit')"
                                                name="order_resubmit_for_seller_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.delivery_boy_reject_for_seller_notification"
                                                :errors="errors"
                                                :label="$t('delivery_boy_reject')"
                                                name="delivery_boy_reject_for_seller_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.delivery_boy_accept_for_seller_notification"
                                                :errors="errors"
                                                :label="$t('delivery_boy_accept')"
                                                name="delivery_boy_accept_for_seller_notification"
                                                rows="2" type="text"/>
                                        </Col>
                                        <Col lg="6">
                                            <FormInputArea
                                                v-model="order_setting.order_delivered_for_seller_notification"
                                                :errors="errors"
                                                :label="$t('order_delivered')"
                                                name="order_delivered_for_seller_notification"
                                                rows="2" type="text"/>
                                        </Col>

                                    </Row>
                                </div>
                                <div id="admin-notification-content"
                                     class="tab-pane" role="tabpanel">
                                    <Row>
                                        <Col lg="6">
                                            <FormSwitch v-model="order_setting.enable_admin_order_notification"
                                                        :errors="errors"
                                                        :label="$t('enable_admin_order_notification')"
                                                        name="enable_admin_order_notification">
                                                <template #label-suffix>
                                                    <InfoTooltip
                                                        tooltip="Admin can received notification for new order"/>
                                                </template>
                                            </FormSwitch>
                                        </Col>

                                    </Row>
                                </div>

                            </div>


                        </CardBody>

                    </Card>
                </Col>
                <Col lg="12" xl="12" xxl="6">
                    <Card>
                        <CardHeader :title="$t('SMS')" class="p-0 px-3 py-2-5" icon="sms" size="sm" type="msr">
                            <div class="float-end mb-n2-5">
                                <ul class="nav nav-tabs nav-tabs-custom-icons border-none" role="tablist">
                                    <li class="nav-item " role="presentation">
                                        <a aria-expanded="false" aria-selected="false"
                                           class="nav-link px-3 cursor-pointer d-flex align-items-center active "
                                           data-bs-toggle="tab" href="#customer-notification-content" role="tab"
                                           tabindex="-1">
                                            <Icon class="icon" icon="person" size="16"></Icon>
                                            <span class="ms-2">{{ $t('customer') }}</span>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </CardHeader>


                        <CardBody class="pb-0">

                            <div class="tab-content">
                                <div id="customer-notification-content"
                                     class="tab-pane active" role="tabpanel">
                                    <Row>
                                        <Col lg="6">
                                            <FormInputArea v-model="order_setting.order_accept_for_customer_sms"
                                                           :errors="errors"
                                                           :label="$t('order_accept')"
                                                           name="order_accept_for_customer_sms"
                                                           rows="2" type="text"/>
                                        </Col>
                                    </Row>
                                </div>
                            </div>


                        </CardBody>

                    </Card>
                </Col>


                <div class="text-end mt-3">
                    <UpdateButton :loading="loading" @click="update"/>
                </div>

            </Row>
        </PageLoading>


    </Layout>
</template>


<script lang="ts">

import Layout from "../layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import FormInputArea from "@js/components/form/FormInputArea.vue";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import {IOrderSetting} from "@js/types/models/business_setting";

export default defineComponent({
    components: {
        UpdateButton,
        FormInputArea,
        ...Components, Layout
    },
    mixins: [FormMixin, DiscountTypeMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            order_setting: {} as IOrderSetting

        }
    },
    computed: {
        category_select_helper() {
            return {
                option: {
                    value: 'id',
                    label: 'name'
                }
            };
        },
        breadcrumb_items() {
            return [
                {
                    text: this.$t("app_setting"),
                    active: true,
                },
            ];
        }
    },
    methods: {

        async update() {
            this.loading = true;
            this.clearAllError();
            try {
                const response = await Request.postAuth(adminAPI.business_settings.order_setting, {
                    ...this.order_setting
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('order_settings_is_updated'));
                } else {
                    ToastNotification.unknownError();
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

    },
    async mounted() {
        this.setTitle(this.$t('order_setting'));
        try {
            const response = await Request.getAuth<IData<IOrderSetting>>(adminAPI.business_settings.get_data);
            this.order_setting = response.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
