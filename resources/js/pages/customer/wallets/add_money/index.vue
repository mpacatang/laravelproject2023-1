<template>
    <PageLoading :loading="page_loading">

        <Row align-items="center" class="mt-5" justify-content="center">
            <Col lg="6" md="8" sm="10" xl="5" xxl="4">
                <Card>
                    <CardHeader :title="$t('select_payment')" icon="credit_card"></CardHeader>
                    <div>
                        <div v-if="payment_data.razorpay?.enable" class="cursor-pointer nav-link">
                            <div class="p-2-5 px-3 d-flex align-items-center justify-content-between"
                                 @click="payByRazorpay">
                                <img alt="Razorpay" height="30" src="/assets/images/payments/razorpay.png">
                                <Icon class="" icon="chevron_right" type="msr"></Icon>
                            </div>
                            <hr class="dashed m-0"/>
                        </div>
                    </div>
                    <div>
                        <div v-if="payment_data.stripe?.enable" class="cursor-pointer nav-link">
                            <div class="p-2-5 px-3 d-flex align-items-center justify-content-between"
                                 @click="payByStripe">
                                <img alt="Stripe" height="30" src="/assets/images/payments/stripe.png">
                                <Icon class="" icon="chevron_right" type="msr"></Icon>
                            </div>
                            <hr class="dashed m-0"/>
                        </div>
                    </div>
                    <div>
                        <div v-if="payment_data.paypal?.enable" class="cursor-pointer nav-link">
                            <div class="p-2-5 px-3 d-flex align-items-center justify-content-between"
                                 @click="payByPaypal">
                                <img alt="Paypal" height="30" src="/assets/images/payments/paypal.png">
                                <Icon class="" icon="chevron_right" type="msr"></Icon>
                            </div>
                            <hr class="dashed m-0"/>
                        </div>
                    </div>

                        <div v-if="payment_data.flutterwave?.enable" class="cursor-pointer nav-link">
                            <div class="p-2-5 px-3 d-flex align-items-center justify-content-between"
                                 @click="payByFlutterwave">
                                <img alt="Paypal" height="30" src="/assets/images/payments/flutterwave.png">
                                <Icon class="" icon="chevron_right" type="msr"></Icon>
                            </div>
                            <hr class="dashed m-0"/>
                        </div>

                    <div v-if="payment_data.paystack?.enable" class="cursor-pointer nav-link">
                        <div class="p-2-5 px-3 d-flex align-items-center justify-content-between"
                             @click="payByPaystack">
                            <img alt="Paystack" height="30" src="/assets/images/payments/paystack.png">
                            <Icon class="" icon="chevron_right" type="msr"></Icon>
                        </div>
                        <hr class="dashed m-0"/>
                    </div>


                    <div v-if="payment_data.cashfree?.enable" class="cursor-pointer nav-link">
                        <div class="p-2-5 px-3 d-flex align-items-center justify-content-between"
                             @click="payByCashfree">
                            <img alt="Cashfree" height="30" src="/assets/images/payments/cashfree.png">
                            <Icon class="" icon="chevron_right" type="msr"></Icon>
                        </div>
                        <hr class="dashed m-0"/>
                    </div>


                    <div v-if="payment_data.mercado?.enable" class="cursor-pointer nav-link">
                        <div class="p-2-5 px-3 d-flex align-items-center justify-content-between"
                             @click="payByMercado">
                            <img alt="Mercado" height="30" src="/assets/images/payments/cashfree.png">
                            <Icon class="" icon="chevron_right" type="msr"></Icon>
                        </div>
                        <hr class="dashed m-0"/>
                    </div>

                </Card>
            </Col>
        </Row>


        <VModal v-model="show_mercado">
            <Mercado_payment v-if="show_mercado" :amount="amount" :customer_id="customer_id" :payment_data="payment_data"
                             :token="token"></Mercado_payment>
        </VModal>

    </PageLoading>
</template>
<script lang="ts">
import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import Button from "@js/components/buttons/Button.vue";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IPaymentData} from "@js/types/models/payment";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {ToastNotification} from "@js/services/toast_notification";
import Mercado_payment from "@js/pages/customer/wallets/add_money/mercado_payment.vue";

export default defineComponent({
    components: {Mercado_payment, VModal, Button, Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            loading: false,
            customer_id: null,
            token: null,
            amount: null,
            payment_data: null as IPaymentData,
            show_mercado: false
        };
    },
    computed: {},
    methods: {
        async getData() {
            try {
                const response = await Request.getAuth<IPaymentData>(customerAPI.payments.data + "?token=" + this.token, {forced: true});
                this.payment_data = response.data;
                this.page_loading = false;
            } catch (error) {
                const handled = handleException(error);
                if (!handled) {
                    // this.$router.push({name: 'customer.home'});
                    // return;
                }
            }
        },
        async payByRazorpay() {
            this.page_loading = true;
            try {
                const response = await Request.getAuth<any>(customerAPI.payments.create_razorpay_checkout + "?token=" + this.token + "&amount=" + this.amount, {forced: true});
                if (response.success()) {
                    let checkout_url = response.data.checkout_url;
                    if (checkout_url) {
                        window.location = checkout_url;

                    }
                } else {
                    ToastNotification.unknownError();
                }

            } catch (error) {
                const handled = handleException(error);
            }
            this.page_loading = false;
        },
        async payByCashfree() {
            this.page_loading = true;

            try {
                const response = await Request.getAuth<any>(customerAPI.payments.create_cashfree_checkout + "?token=" + this.token + "&amount=" + this.amount, {forced: true});
                if (response.success()) {
                    let checkout_url = response.data.checkout_url;
                    if (checkout_url) {
                        window.location = checkout_url;
                    }
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                const handled = handleException(error);
            }
            this.page_loading = false;

        },
        async payByStripe() {
            this.page_loading = true;

            try {
                const response = await Request.getAuth<any>(customerAPI.payments.create_stripe_checkout + "?token=" + this.token + "&amount=" + this.amount, {forced: true});
                if (response.success()) {
                    let checkout_url = response.data.checkout_url;
                    if (checkout_url) {
                        window.location = checkout_url;
                    }
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                const handled = handleException(error);
            }
            this.page_loading = false;

        },
        async payByPaypal() {
            this.page_loading = true;

            try {
                const response = await Request.getAuth<any>(customerAPI.payments.create_paypal_checkout + "?token=" + this.token + "&amount=" + this.amount, {forced: true});
                if (response.success()) {
                    let checkout_url = response.data.checkout_url;
                    if (checkout_url) {
                        window.location = checkout_url;
                    }
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                const handled = handleException(error);
            }
            this.page_loading = false;

        },

        async payByFlutterwave() {
            this.page_loading = true;

            try {
                const response = await Request.getAuth<any>(customerAPI.payments.create_flutterwave_checkout + "?token=" + this.token + "&amount=" + this.amount, {forced: true});
                if (response.success()) {
                    let checkout_url = response.data.checkout_url;
                    if (checkout_url) {
                        window.location = checkout_url;
                    }
                } else {
                    ToastNotification.unknownError();
                }
                this.page_loading = false;
            } catch (error) {
                const handled = handleException(error);
            }
            this.page_loading = false;

        },

        async payByMercado() {
            this.show_mercado = true;
        },
        async payByPaystack() {
            this.page_loading = true;

            try {
                const response = await Request.getAuth<any>(customerAPI.payments.create_paystack_checkout + "?token=" + this.token + "&amount=" + this.amount, {forced: true});
                if (response.success()) {
                    let checkout_url = response.data.checkout_url;
                    if (checkout_url) {
                        window.location = checkout_url;
                    }
                } else {
                    ToastNotification.unknownError();
                }

            } catch (error) {
                const handled = handleException(error);
            }
            this.page_loading = false;
        },
    },
    mounted() {
        this.setTitle(this.$t('add_money'));
    },
    created() {
        const query = this.$route.query;
        this.customer_id = query['customer_id'];
        this.amount = query['amount'];
        this.token = query['token'];
        if (this.token == null || this.amount == null) {
            ToastNotification.error(this.$t('something_wrong'));
            this.$router.push({name: 'customer.home'});
            return;
        }
        this.getData();
    }
});
</script>
<style scoped>
</style>
