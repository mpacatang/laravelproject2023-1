<template>
    <div class="auth-wrapper">
        <div class="auth-image-wrapper">
            <img alt="" class="img-fluid" src="/assets/images/landing/hero.png"/>
        </div>

        <div class="auth-content-wrapper">
            <div class="auth-content">
                <div class="d-flex justify-content-between align-items-center"
                     style="padding: 40px 40px 0 40px">
                    <router-link :to="{name: 'customer.home'}">
                        <img alt="" height="30" src="/assets/images/logo/dark.svg">
                    </router-link>
                    <Button class="px-2-5 fw-medium" color="danger" variant="soft" @click="logout">{{ $t('logout') }}
                    </Button>

                </div>
                <div class="flex-grow-1 d-flex flex-column justify-content-center"
                 style="padding: 32px 60px">
                <h3 class="text-center text-capitalize fw-semibold mt-0">{{ $t('customer_verification') }}</h3>

                <p class="text-muted mt-1 text-center">Need to verify your email account to access shopping</p>

                <div class="mt-4">

                    <FormInput v-model="email"
                               :errors="errors"
                               :label="$t('email_address')"
                               :disabled="email_disabled"
                               name="email" required type="email">
                        <template #prefix>
                            <Icon icon="mail" size="18"/>
                        </template>
                    </FormInput>


                    <div class="mt-4">
                        <VueRecaptcha :sitekey="getSiteKey" class="g-recaptcha"
                                      @expired="onRecaptchaExpired"
                                      @error="onRecaptchaError"
                                      @verify="onRecaptchaVerified"/>
                        <FormValidationError :errors="errors" name="recaptcha"></FormValidationError>
                    </div>

                    <div class="mt-4 d-grid">
                        <LoadingButton :loading="loading" class="px-2-5 fw-medium py-1-5" color="bluish-purple"
                                       flexed-icon icon="outgoing_mail"
                                       icon-size="20" @click="sendEmail">
                            {{ $t('send_verification_link') }}
                        </LoadingButton>
                    </div>


                </div>

                </div>
                <hr class="dashed">
                <div class="p-2-5 pb-3 text-center">
                <span>Looking for <router-link :to="{name: 'admin.dashboard'}"
                                               class="fw-medium">Admin panel</router-link> -></span>
                </div>
            </div>

        </div>
    </div>
</template>

<script lang="ts">


import {defineComponent} from "vue";
import Response from "@js/services/api/response";
import Request from "@js/services/api/request";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import {customerAPI} from "@js/services/api/request_url";
import Loading from "@js/components/Loading.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import FormInput from "@js/components/form/FormInput.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {VueRecaptcha} from 'vue-recaptcha';
import Logo from "@js/components/Logo.vue";
import Row from "@js/components/Row.vue";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import Button from "@js/components/buttons/Button.vue";
import Icon from "@js/components/Icon.vue";
import Col from "@js/components/Col.vue";
import FormValidationError from "@js/components/form/FormValidationError.vue";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {logoutCustomer} from "@js/services/state/state_helper";
import {BusinessSetting} from "@js/types/models/business_setting";
import {useCustomerDataStore} from "@js/services/state/states";
import {setLightTheme} from "@js/services/theme_service";


export default defineComponent({
        components: {
            FormValidationError,
            Col,
            Icon,
            Button,
            FormPasswordInput, Row, Logo, FormInput, LoadingButton, Loading, VueRecaptcha
        },
        mixins: [FormMixin, UtilMixin],
        data() {
            return {
                loading: false,
                fcm_token: null,
                recaptcha_verified: true,
                email: '',
                email_disabled: true,
            }
        },
        computed: {
            getSiteKey() {
                return BusinessSetting.getInstance().recaptcha_site_key;
            }
        },
        methods: {
            async sendEmail() {
                if (this.loading == true)
                    return;

                if (!this.recaptcha_verified) {
                    this.addError('recaptcha', this.$t('please_check_recaptcha'))
                    return;
                }

                this.loading = true;
                this.errors = null;
                try {
                    const response = await Request.postAuth(customerAPI.profile.send_verification_email, {
                        email: this.email
                    });
                    if (response.success()) {
                        ToastNotification.success(this.$t('email_sent') + " " + this.$t('check_your_inbox_and_verify'))
                    }
                } catch (error: any) {
                    if (Response.is422(error)) {
                        this.errors = error.response.data.errors;
                    }
                }

                this.loading = false;
            },


            onRecaptchaVerified(e) {
                this.recaptcha_verified = true;
                this.clearAllError();
            },
            onRecaptchaError(e) {
                this.recaptcha_verified = true;
                this.clearAllError();
            },
            onRecaptchaExpired(e) {
                this.recaptcha_verified = false;
                this.addError('recaptcha', this.$t('please_check_recaptcha'));
            },
            async logout() {
                try {
                    const response = await Request.postAuth(customerAPI.auth.logout, {

                    });
                } catch (error) {
                    handleException(error);
                } finally {
                }
                logoutCustomer();
                this.$router.push({'name': 'customer.login'});
            },

        },
        mounted() {

            const customer = useCustomerDataStore().getUserData();
            if (customer == null || customer.data == null) {
                this.$router.replace({name: 'customer.login'});
                return;
            }

            this.email = customer.data.email;
            if (customer.data.email == null || customer.data.email.toString().length == 0) {
                this.email_disabled = false
            }
            ToastNotification.success(this.$t('you_need_to_verify_account_first'))

        },
        created() {
            setLightTheme();
        }


    }
)

</script>

<style scoped>

</style>
