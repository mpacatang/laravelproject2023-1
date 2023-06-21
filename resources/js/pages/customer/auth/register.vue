<template>
    <div class="auth-wrapper">
        <div class="auth-image-wrapper">
            <img alt="" class="img-fluid" src="/assets/images/landing/hero.png"/>
        </div>

        <div class="auth-content-wrapper">
            <div class="auth-content">
                <div class="d-flex justify-content-between align-items-center logo"
                     style="padding: 40px 40px 0 40px">
                    <img alt="" class="dark" height="30" src="/assets/images/logo/light.svg">
                    <img alt="" class="light" height="30" src="/assets/images/logo/dark.svg">
                    <router-link :to="{name: 'customer.login'}">
                        <Button class="px-2-5 fw-medium" color="bluish-purple" variant="soft">{{ $t('login') }}
                        </Button>
                    </router-link>

                </div>
                <div class="flex-grow-1 d-flex flex-column justify-content-center"
                     style="padding: 32px 60px">
                    <h3 class="text-center text-capitalize fw-semibold mt-0">{{ $t('create_an_account') }}</h3>
                    <p class="text-muted text-center mt-1">Customer need to needful details to create a new account</p>

                    <div class="mt-4">

                        <Row>
                            <Col>
                                <FormInput v-model="first_name"
                                           :errors="errors"
                                           :label="$t('first_name')"
                                           name="first_name"
                                           required type="text"/>
                            </Col>
                            <Col>
                                <FormInput v-model="last_name"
                                           :errors="errors"
                                           :label="$t('last_name')"
                                           name="last_name"
                                           required type="text"/>
                            </Col>
                        </Row>

                        <FormInput v-model="email"
                                   :errors="errors"
                                   :label="$t('email_address')"
                                   name="email" type="email">
                            <template #prefix>
                                <Icon icon="mail" size="18"/>
                            </template>
                        </FormInput>

                        <FormInput v-model="mobile_number"
                                   :errors="errors"
                                   :label="$t('mobile_number')"
                                   name="mobile_number"
                                   required type="tel">
                            <template #prefix>
                                <Icon icon="phone" size="18"/>
                            </template>
                        </FormInput>


                        <FormPasswordInput v-model="password" :errors="errors" :label="$t('password')"
                                           name="password" required>
                            <template #prefix>
                                <Icon icon="lock" size="18"/>
                            </template>
                        </FormPasswordInput>


                        <FormInput v-model="from_referral"
                                   :errors="errors"
                                   :label="$t('referral')"
                                   name="from_referral"
                                   type="text">
                        </FormInput>

                        <div class="mt-4">
                            <VueRecaptcha :sitekey="getSiteKey" class="g-recaptcha"
                                          @error="onRecaptchaError"
                                          @expired="onRecaptchaExpired"
                                          @verify="onRecaptchaVerified"/>
                            <FormValidationError :errors="errors" name="recaptcha"></FormValidationError>
                        </div>

                        <div class="mt-4 d-grid">
                            <LoadingButton :loading="loading" class="px-2-5 fw-medium py-1-5"
                                           flexed-icon icon="add"
                                           icon-size="20" @click="register">
                                {{ $t('create_an_account') }}
                            </LoadingButton>
                        </div>


                    </div>

                </div>
                <hr class="dashed">
                <div class="p-2-5 pb-3 text-center">
                    <Button class="fw-medium" color="primary" flexed-icon variant="text" @click="showing_terms=true">
                        <Icon class="me-1-5" icon="shield" size="20"/>
                        {{ $t('privacy') + " & " + $t('privacy') }}
                    </Button>
                </div>
            </div>


        </div>
        <PrivacyTerms v-model="showing_terms"/>
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
import {BusinessSetting} from "@js/types/models/business_setting";
import {useCustomerDataStore} from "@js/services/state/states";
import {Firebase} from "@js/firebase";
import {setLightTheme} from "@js/services/theme_service";
import PrivacyTerms from "@js/components/PrivacyTerms.vue";


export default defineComponent({
        components: {
            PrivacyTerms,
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
                showing_terms: false,

                first_name: '',
                last_name: '',
                email: '',
                mobile_number: '',
                from_referral: '',
                password: '',
                fcm_token: null,
                recaptcha_verified: false,

            }
        },
        computed: {
            getSiteKey() {
                return BusinessSetting.getInstance().recaptcha_site_key;
            }
        },
        methods: {
            async register() {
                if (this.loading == true)
                    return;

                if (!this.recaptcha_verified) {
                    this.addError('recaptcha', this.$t('please_check_recaptcha'))
                    return;
                }

                this.loading = true;
                this.errors = null;

                try {
                    const response = await Request.post(customerAPI.auth.register, {
                        first_name: this.first_name,
                        last_name: this.last_name,
                        email: this.email,
                        mobile_number: this.mobile_number,
                        password: this.password,
                        from_referral: this.from_referral,
                        fcm_token: this.fcm_token
                    });

                    const store = useCustomerDataStore();
                    store.updateUserData(response.data.customer);
                    store.updateAuthToken(response.data.token);

                    this.$router.push({name: 'customer.home'});
                } catch (error: any) {
                    if (Response.is422(error)) {
                        this.errors = error.response.data.errors;
                    }
                }

                this.loading = false;
            },

            autoFill() {
                this.email = this.dummy.email;
                this.password = this.dummy.password;
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
            }

        },
        created() {
            setLightTheme();

            Firebase.getFCMToken().then((e) => this.fcm_token = e);
        }

    }
)

</script>

<style scoped>

</style>
