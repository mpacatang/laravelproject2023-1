<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('system_settings')"/>
        <PageLoading :loading="page_loading">
            <Row>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('business_setup')" icon="edit_note" type="msr"></CardHeader>

                        <CardBody class="pb-0">
                            <Row>
                                <Col lg="6">
                                    <FormSwitch v-model="system_setting.can_shop_self_register" :errors="errors"
                                                :label="$t('can_shop_self_register')"
                                                name="can_shop_self_register">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="Publicly user can register their own shop and become seller"></InfoTooltip>
                                        </template>
                                    </FormSwitch>

                                </Col>
                                <Col lg="6">
                                    <FormSwitch v-model="system_setting.can_delivery_boy_self_register" :errors="errors"
                                                :label="$t('can_delivery_boy_self_register')"
                                                name="can_delivery_boy_self_register">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="Publicly user can register as delivery"></InfoTooltip>
                                        </template>
                                    </FormSwitch>

                                </Col>
                            </Row>
                            <Row>
                                <Col lg="6">
                                    <FormSelect :data="currencies"
                                                :helper="currency_select_helper"
                                                :label="$t('currency')"
                                                :onSelected="onSelectCurrency"
                                                :selected="system_setting.currency_code"
                                                name="currency_code"/>
                                </Col>
                                <Col lg="6">
                                    <FormSelect :data="currency_positions"
                                                :helper="currency_position_select_helper"
                                                :label="$t('currency_position')"
                                                :onSelected="(e)=>system_setting.currency_position=e"
                                                :selected="system_setting.currency_position"
                                                name="currency_position"
                                                no-search
                                    />
                                </Col>
                                <Col lg="6">
                                    <FormSelect :data="time_formats"
                                                :helper="time_format_select_helper"
                                                :label="$t('time_format')"
                                                :onSelected="(e)=>system_setting.time_format=e"
                                                :selected="system_setting.time_format"
                                                name="time_format"
                                                no-search
                                    />
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="system_setting.digit_after_decimal" :errors="errors"
                                               :label="$t('digit_after_decimal')"
                                               min="0" name="digit_after_decimal" type="number"/>
                                </Col>
                            </Row>


                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('customer_settings')" icon="group" type="msr"></CardHeader>

                        <CardBody class="pb-0">

                            <Row>
                                <Col lg="6">
                                    <FormSwitch v-model="system_setting.customer_email_verification"
                                                :errors="errors"
                                                :label="$t('email_verification')" name="email_verification"/>
                                </Col>

                                <Col lg="6">
                                    <FormSwitch v-model="system_setting.customer_mobile_number_verification"
                                                :errors="errors"
                                                :label="$t('mobile_number_verification')"
                                                name="mobile_number_verification"/>
                                </Col>

                                <Col lg="6">
                                    <FormInput v-model="system_setting.customer_referral_amount"
                                               :errors="errors"
                                               :label="$t('customer_referral_amount')"
                                               name="customer_referral_amount"
                                               show-currency type="number"/>
                                </Col>

                            </Row>
                        </CardBody>

                    </Card>

                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('customer_loyalty_program')" icon="card_membership" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="system_setting.customer_order_loyalty_enable" :errors="errors"
                                            :label="$t('customer_order_loyalty_enable')"
                                            name="customer_order_loyalty_enable" no-spacing>
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="Customer get loyalty points when order is completed"></InfoTooltip>
                                    </template>
                                </FormSwitch>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="system_setting.customer_order_loyalty_amount" :errors="errors"
                                               :label="$t('loyalty_amount')"
                                               :prefix-or-suffix="system_setting.customer_order_loyalty_amount_type"
                                               min="0"
                                               name="customer_order_loyalty_amount"
                                               type="number"

                                    />
                                </Col>
                                <Col lg="6">

                                    <FormSelect :data="charge_types"
                                                :errors="errors"
                                                :helper="charge_type_helper"
                                                :label="$t('loyalty_amount_type')"
                                                :onSelected="(value)=>system_setting.customer_order_loyalty_amount_type=value"
                                                :placeholder="$t('customer_order_loyalty_amount_type')"
                                                :selected="system_setting.customer_order_loyalty_amount_type"
                                                name="customer_order_loyalty_amount_type"
                                                no-search/>

                                </Col>
                                <Col lg="6">

                                    <FormInput v-model="system_setting.customer_minimum_loyalty_point_to_convert"
                                               :errors="errors"
                                               :label="$t('minimum_point_to_convert')"
                                               name="customer_loyalty_minimum_amount_to_convert"
                                               type="number"/>
                                </Col>
                                <Col lg="6">

                                    <FormInput v-model="system_setting.customer_loyalty_to_wallet_multiplier"
                                               :errors="errors"
                                               :label="$t('loyalty_to_wallet_multiplier')"
                                               name="customer_loyalty_to_wallet_multiplier"
                                               type="number">
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="How much loyalty point = 1 currency"/>
                                        </template>
                                    </FormInput>
                                </Col>

                            </Row>
                        </CardBody>

                    </Card>

                </Col>

                <div class="text-end mb-3">
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
import {IBreadcrumb, IFormSelectOption} from "@js/types/models/models";
import {CurrencyUtil} from "@js/shared/currency";
import {ICurrency, ISystemSetting} from "@js/types/models/business_setting";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import ChargeTypeMixin from "@js/shared/mixins/ChargeTypeMixin";

export default defineComponent({
    components: {
        UpdateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin, ChargeTypeMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            system_setting: {} as ISystemSetting,
            currencies: CurrencyUtil.getAll()
        }
    },
    computed: {
        currency_positions() {
            return [
                {
                    value: 'left',
                    title: this.$t('left')
                }, {
                    value: 'right',
                    title: this.$t('right')
                },
            ];
        },
        time_formats() {
            return [
                {
                    value: '12h',
                    title: this.$t('12h')
                }, {
                    value: '24h',
                    title: this.$t('24h')
                },
            ];
        },
        currency_select_helper(): IFormSelectOption<ICurrency> {
            return {
                option: {
                    value: 'code',
                    label: (e) => e.code + " (" + e.symbol + ")" + " - "  + e.name
                }
            };
        },
        currency_position_select_helper(): IFormSelectOption {
            return {
                option: {
                    value: 'value',
                    label: 'title'
                }
            };
        },
        time_format_select_helper(): IFormSelectOption {
            return {
                option: {
                    value: 'value',
                    label: 'title'
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("system_setting"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        onSelectCurrency(code) {
            this.system_setting.currency_code = code;
            this.system_setting.currency_symbol = CurrencyUtil.getAll().find((e) => e.code == code)?.symbol;
        },
        async update() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.business_settings.system_setting, {
                    ...this.system_setting
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('system_setting_is_updated'));
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
        this.setTitle(this.$t('system_setting'));

        try {
            const response = await Request.getAuth<IData<ISystemSetting>>(adminAPI.business_settings.get_data);
            this.system_setting = response.data.data;
            this.page_loading = false;

        } catch (error) {
            handleException(error);
        }
    }
});

</script>
