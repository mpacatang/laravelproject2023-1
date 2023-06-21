<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('payment_settings')"/>
        <PageLoading :loading="page_loading">
            <Row>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('razorpay')" icon="credit_card" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="payment_setting.payment_razorpay_enable" :errors="errors"
                                            :label="$t('enable')"
                                            name="payment_razorpay_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInputArea v-model="payment_setting.payment_razorpay_id" :errors="errors"
                                           :label="$t('id')"
                                           name="payment_razorpay_id"
                                           rows="2" type="text"/>

                            <FormInputArea v-model="payment_setting.payment_razorpay__secret_key" :errors="errors"
                                           :label="$t('secret')"
                                           name="payment_razorpay__secret_key"
                                           rows="2" type="text"/>


                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('stripe')" icon="credit_card" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="payment_setting.payment_stripe_enable" :errors="errors"
                                            :label="$t('enable')"
                                            name="payment_stripe_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInputArea v-model="payment_setting.payment_stripe_publishable_key" :errors="errors"
                                           :label="$t('publishable_key')"
                                           name="payment_stripe_publishable_key"
                                           rows="2" type="text"/>

                            <FormInputArea v-model="payment_setting.payment_stripe__secret_key" :errors="errors"
                                           :label="$t('secret_key')"
                                           name="payment_stripe__secret_key"
                                           rows="2" type="text"/>


                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('paypal')" icon="credit_card" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="payment_setting.payment_paypal_enable" :errors="errors"
                                            :label="$t('enable')"
                                            rows="2"
                                            name="payment_paypal_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInputArea v-model="payment_setting.payment_paypal_client_id" :errors="errors"
                                           :label="$t('client_id')"
                                           name="payment_paypal_client_id"
                                           rows="2" type="text"/>

                            <FormInputArea v-model="payment_setting.payment_paypal__secret_key" :errors="errors"
                                           :label="$t('secret_key')"
                                           name="payment_paypal__secret_key"
                                           rows="2" type="text"/>

                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('paystack')" icon="credit_card" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="payment_setting.payment_paystack_enable" :errors="errors"
                                            :label="$t('enable')"
                                            name="payment_paystack_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInputArea v-model="payment_setting.payment_paystack_public_key" :errors="errors"
                                           :label="$t('public_key')"
                                           name="payment_paystack_public_key"
                                           rows="2" type="text"/>

                            <FormInputArea v-model="payment_setting.payment_paystack__secret_key" :errors="errors"
                                           :label="$t('secret_key')"
                                           name="payment_paystack__secret_key"
                                           rows="2" type="text"/>


                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('flutterwave')" icon="credit_card" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="payment_setting.payment_flutterwave_enable" :errors="errors"
                                            :label="$t('enable')"
                                            name="payment_flutterwave_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInputArea v-model="payment_setting.payment_flutterwave_public_key" :errors="errors"
                                           :label="$t('public_key')"
                                           name="payment_flutterwave_public_key"
                                           rows="2" type="text"/>

                            <FormInputArea v-model="payment_setting.payment_flutterwave__secret_key" :errors="errors"
                                           :label="$t('secret_key')"
                                           name="payment_flutterwave__secret_key"
                                           rows="2" type="text"/>


                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('cashfree')" icon="credit_card" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="payment_setting.payment_cashfree_enable" :errors="errors"
                                            :label="$t('enable')"
                                            name="payment_cashfree_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInputArea v-model="payment_setting.payment_cashfree_app_id" :errors="errors"
                                           :label="$t('app_id')"
                                           name="payment_cashfree_app_id"
                                           rows="2" type="text"/>

                            <FormInputArea v-model="payment_setting.payment_cashfree__secret_key" :errors="errors"
                                           :label="$t('secret_key')"
                                           name="payment_cashfree__secret_key"
                                           rows="2" type="text"/>


                        </CardBody>
                    </Card>
                </Col>


                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-medium cursor-pointer">Need any help? <span class="text-primary"
                                                                                @click="goToRazorpayDocs">Check our payment documentation</span></span>
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
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import {DocsNavigation} from "@js/services/navigator_service";
import VModal from "@js/components/VModal.vue";
import FormInputArea from "@js/components/form/FormInputArea.vue";
import {IPaymentSetting} from "@js/types/models/business_setting";

export default defineComponent({
    components: {
        FormInputArea,
        VModal,
        UpdateButton,

        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            payment_setting: {} as IPaymentSetting,
        }
    },
    computed: {

        breadcrumb_items(): IBreadcrumb[] {
            return [

                {
                    text: this.$t("payment_setting"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        goToRazorpayDocs() {
            DocsNavigation.goToRazorpay();
        },

        async update() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.business_settings.payment_setting, {
                    ...this.payment_setting
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('payment_settings_is_updated'));
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
        this.setTitle(this.$t('payment_setting'));
        try {
            const response = await Request.getAuth<IData<IPaymentSetting>>(adminAPI.business_settings.get_data);
            this.payment_setting = response.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
