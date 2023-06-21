<template>
    <Layout>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="5">
                    <Card>
                        <CardHeader :title="$t('my_loyalty')" icon="card_membership" type="msr"/>
                        <CardBody class="text-center">
                            <p class="fw-medium text-muted">{{ $t('current_points') }}</p>
                            <h2 class="fw-medium">{{ getPrecise(wallet.point) }}</h2>
                        </CardBody>
                    </Card>
                    <Card>
                        <CardHeader :title="$t('convert_to_wallet')" icon="repeat" type="msr"/>
                        <CardBody>
                            <FormInput v-model="point" :errors="errors" :label="$t('point')" :min="100" name="point"
                                       type="number">
                            </FormInput>
                            <span class="text-teal">
                                {{ $t('you_get') + " " + getFormattedCurrency(getConverted) + " " + $t('into_wallet') }}
                            </span>
                            <div class="text-center mt-2">
                                <LoadingButton :loading="loading" color="bluish-purple" flexed-icon icon="repeat"
                                               @click="convertToWallet">
                                    {{ $t('convert') }}
                                </LoadingButton>
                            </div>
                        </CardBody>
                    </Card>

                </Col>
                <Col lg="7">
                    <Card>
                        <CardHeader :title="$t('transactions')" icon="timeline" type="msr"/>
                        <div class="py-3">
                            <SimpleBar class="px-3" style="max-height: 440px">
                                <VItem v-for="transaction in wallet.transactions" align-items="center"
                                       border class="p-2 mb-2" display="flex" rounded>
                                    <VItem :color="transaction.added?'primary':'danger'" circular class="p-1"
                                           display="flex" soft>
                                        <Icon :icon="transaction.added?'arrow_upward':'arrow_downward'"></Icon>
                                    </VItem>
                                    <div class="flex-grow-1">
                                        <p v-if="transaction.order_id" class="mb-0 ms-2 fw-medium">{{ $t('order') }}
                                            #{{ transaction.order_id }}</p>
                                        <p v-else class="mb-0 ms-2 fw-medium">
                                            {{ $t('convert_to_wallet') }}</p>
                                    </div>
                                    <VItem align-items="end" display="flex" flex="column">
                                        <p :class="transaction.added?'text-primary':'text-danger'"
                                           class="text-primary fw-semibold mb-0">
                                            {{ (transaction.added ? "+ " : "- ") + getPrecise(transaction.point) }}
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
import {ICustomerLoyaltyWallet} from "@js/types/models/customer";
import Button from "@js/components/buttons/Button.vue";
import {ToastNotification} from "@js/services/toast_notification";
import {SimpleBar} from 'simplebar-vue3';
import {BusinessSetting} from "@js/types/models/business_setting";

export default defineComponent({
    components: {SimpleBar, Button, Layout, ...Components},
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {
        return {
            page_loading: true,
            loading: false,
            wallet: {} as ICustomerLoyaltyWallet,
            point: 100,
        };
    },
    computed: {
        getConverted() {
            const multiplier = BusinessSetting.instance.customer_loyalty_to_wallet_multiplier;
            const point = parseInt(this.point);
            if (multiplier == 0 || isNaN(point)) {
                return 0;
            }

            return point / multiplier;

        }
    },
    methods: {
        async getWallet() {
            try {
                this.page_loading = true;
                const response = await Request.getAuth<IData<ICustomerLoyaltyWallet>>(customerAPI.loyalty_wallets.get, {forced: true});
                this.wallet = response.data.data;
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        async convertToWallet() {
            if (this.wallet == null)
                return;

            const bs = BusinessSetting.instance;
            const minAmount = bs.customer_minimum_loyalty_point_to_convert;
            if (this.point < minAmount) {
                this.addError('point', this.$t('you_need_minimum') + " " + this.getPrecise(minAmount) + " " + this.$t('points'))
                return;
            }
            if (this.point > this.wallet.point) {
                this.addError('point', this.$t('you_have_only') + " " + this.getPrecise(this.wallet.point) + " " + this.$t('points'))
                return;
            }
            try {
                this.loading = true;
                const data = {
                    point: this.point
                };
                const response = await Request.postAuth<IData<boolean>>(customerAPI.loyalty_wallets.convert_points, data);
                if (response.success()) {
                    ToastNotification.success(this.$t('converted_to_wallet'));
                    await this.getWallet();
                }
                this.loading = false;
            } catch (error) {
                handleException(error);
            }
        },

    },
    mounted() {
        this.setTitle(this.$t('wallet'));
        this.getWallet();
    }
});

</script>

<style scoped>

</style>
