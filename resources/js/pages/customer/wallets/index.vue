<template>
    <Layout>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="5">
                    <Card>
                        <CardHeader :title="$t('my_wallet')" icon="account_balance_wallet" type="msr"/>
                        <CardBody class="text-center">
                            <p class="fw-medium text-muted">{{ $t('current_balance') }}</p>
                            <h1 class="fw-semibold">{{ getFormattedCurrency(wallet.balance) }}</h1>
                        </CardBody>
                    </Card>
                    <Card>
                        <CardHeader :title="$t('add_money')" icon="payments" type="msr"/>
                        <CardBody>
                            <FormInput v-model="amount" :errors="errors" :label="$t('amount')" :min="100" name="amount"
                                       show-currency type="number">

                            </FormInput>
                            <div class="text-center">
                                <Button flexed-icon color="bluish-purple" @click="addMoney">
                                    <Icon class="me-1-5" icon="add"/>
                                    {{ $t('add_money') }}
                                </Button>
                            </div>
                        </CardBody>
                    </Card>

                </Col>
                <Col lg="7">
                    <Card>
                        <CardHeader :title="$t('transactions')" icon="timeline" type="msr"/>
                        <div class="py-3">
                            <SimpleBar style="max-height: 440px" class="px-3">
                                <VItem v-for="transaction in wallet.transactions" align-items="center"
                                       border class="p-2 mb-2" display="flex" rounded >
                                    <VItem :color="transaction.added?'primary':'danger'" class="p-1" display="flex"
                                           circular soft>
                                        <Icon :icon="transaction.added?'arrow_upward':'arrow_downward'"></Icon>
                                    </VItem>
                                    <div class="flex-grow-1">
                                        <p v-if="transaction.order_id" class="mb-0 ms-2 fw-medium">{{ $t('order') }}
                                            #{{ transaction.order_id }}</p>
                                        <p v-else class="mb-0 ms-2 fw-medium">
                                            {{ transaction.payment_method }}</p>
                                    </div>
                                    <VItem align-items="end" display="flex" flex="column">
                                        <p v-if="transaction.added" class="text-primary fw-semibold mb-0">
                                            {{ getFormattedCurrency(transaction.amount) }}
                                        </p>
                                        <p v-else class="text-danger fw-semibold mb-0">
                                            {{ getFormattedCurrency(-transaction.amount) }}
                                        </p>
                                        <p class="mb-0 text-muted font-13">
                                            {{ getFormattedDateTime(transaction.created_at) }}</p>
                                    </VItem>

                                </VItem>
                            </SimpleBar>
                        </div>
                    </Card>
                </Col>
            </Row>
        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from "vue";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import {ICustomerWallet, ICustomerWalletTransaction} from "@js/types/models/customer";
import Button from "@js/components/buttons/Button.vue";
import {ToastNotification} from "@js/services/toast_notification";
import {SimpleBar} from 'simplebar-vue3';
import {useCustomerDataStore} from "@js/services/state/states";

export default defineComponent({
    components: {SimpleBar, Button, Layout, ...Components},
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {
        return {
            page_loading: true,
            loading: false,
            wallet: {} as ICustomerWallet,
            amount: 100,
        };
    },
    computed: {},
    methods: {
        async getWallet() {
            try {
                this.page_loading = true;
                const response = await Request.getAuth<IData<ICustomerWallet>>(customerAPI.wallets.get, {forced: true});
                this.wallet = response.data.data;
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        addMoney() {
            if (this.amount < 100) {
                this.addError('amount', this.$t('add_at_least') + ` ${this.getFormattedCurrency(100)}`)
                return;
            }
            this.goToPayment('customer.add_money.index', this.amount);

            // if (this.selected_payment_type === 'razorpay') {
            //
            // } else {
            //     ToastNotification.error(this.$t('this_payment_method_is_only_available_for_mobile_app'))
            // }
        },
        goToPayment(payment: string, amount: number) {
            const self = this;
            let customer = useCustomerDataStore().getUserData();
            let paymentWindow = window.open(this.$router.resolve({
                name: payment,
                query: {amount: amount, customer_id: customer.data.id, token: customer.auth_token}
            }).href, '_blank');
            window.onmessage = function (e) {
                console.info(e);
                if (e.data) {
                    let success = e.data['payment_success'] != null ? e.data['payment_success'] : null;

                    if (success != null) {
                        if (success) {
                            ToastNotification.success(self.$t('money_added_to_wallet...\n refresh_in_5_second'));
                            setTimeout(function (){
                                self.getWallet()
                            }, 5000)
                        } else {
                            ToastNotification.error(self.$t('something_went_wrong'));
                        }
                    }
                }
            }
        }

    },
    mounted() {
        this.setTitle(this.$t('wallet'));
        this.getWallet();
    }
});

</script>

<style scoped>

</style>
