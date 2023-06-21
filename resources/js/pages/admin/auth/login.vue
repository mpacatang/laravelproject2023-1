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
                    <router-link :to="{name: 'seller.dashboard'}">
                        <Button class="px-2-5 fw-medium" color="bluish-purple" variant="soft">{{ $t('seller_panel') }}

                        </Button>
                    </router-link>
                </div>
                <div class="my-auto"
                     style="padding: 40px 60px">
                    <h2 class="text-center text-capitalize fw-semibold mt-0">{{ $t('login') }}</h2>
                    <p class="text-muted text-center mt-1">Admin need to enter a email and password to access.</p>

                    <div class="mt-4">
                        <FormInput v-if="using_email" v-model="email"
                                   :errors="errors"
                                   :label="$t('email_address')"
                                   name="email"
                                   required type="email">
                            <template #prefix>
                                <Icon icon="mail" size="18"/>
                            </template>
                            <template #label-suffix>
                            <span class="text-primary float-end cursor-pointer"
                                  @click="toggleUsingEmail">{{ $t('mobile_number') }}</span>
                            </template>
                        </FormInput>

                        <FormInput v-else v-model="mobile_number"
                                   :errors="errors"
                                   :label="$t('mobile_number')"
                                   name="mobile_number"
                                   required type="tel">
                            <template #prefix>
                                <Icon icon="phone" size="18"/>
                            </template>
                            <template #label-suffix>
                            <span class="text-primary float-end cursor-pointer"
                                  @click="toggleUsingEmail">{{ $t('email') }}</span>
                            </template>
                        </FormInput>


                        <FormPasswordInput v-model="password" :errors="errors" :label="$t('password')"
                                           name="password" required>
                            <template #prefix>
                                <Icon icon="lock" size="18"/>
                            </template>
                        </FormPasswordInput>

                        <div v-if="need_recaptcha" class="mt-4">
                            <VueRecaptcha :sitekey="getSiteKey"
                                          class="g-recaptcha"
                                          @error="onRecaptchaError"
                                          @expired="onRecaptchaExpired"
                                          @verify="onRecaptchaVerified"/>
                            <FormValidationError :errors="errors" name="recaptcha"></FormValidationError>

                        </div>

                        <div class="d-lg-flex d-grid gap-2 mt-3 ">
                            <div class="border p-1-5 cursor-pointer rounded d-flex align-items-center justify-content-center"
                                 @click="loginWithGoogle">
                                <img alt="google sign in" height="22" src="/assets/images/brand/google_login.svg">
                                <span class="fw-medium ms-1-5">Sign in with Google</span>
                            </div>
                            <div class="d-grid">
                                <LoadingButton :loading="loading" class="px-2-5 fw-medium py-1-5"
                                               flexed-icon icon="login"
                                               icon-size="14" @click="login">
                                    {{ $t('log_in') }}
                                </LoadingButton>
                            </div>
                        </div>

                        <div v-if="isDemo" class="border-dashed border rounded mt-3 p-2">
                            <div class="d-flex align-items-start cursor-pointer" @click="autoFill">

                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Admin</p>
                                    <p class="mb-0-5">
                                        <Icon icon="mail" size="19"/>
                                        <span class="ms-1-5">{{ dummy.email }}</span></p>
                                    <p class="mb-0">
                                        <Icon icon="key" size="19"/>
                                        <span class="ms-1-5">{{ dummy.password }}</span></p>
                                </div>
                                <Button class="py-1-5" color="teal" flexed-icon variant="soft" @click="autoFill">
                                    <Icon icon="drive_file_rename_outline" size="18"></Icon>
                                </Button>
                            </div>
                        </div>


                    </div>

                </div>
                <hr class="dashed">
                <div class="p-2-5 pb-3 text-center">
                <span>Looking for <router-link :to="{name: 'customer.home'}"
                                               class="fw-medium">Website</router-link> -></span>
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
import {adminAPI} from "@js/services/api/request_url";
import Loading from "@js/components/Loading.vue";
import Button from "@js/components/buttons/Button.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import FormInput from "@js/components/form/FormInput.vue";
import Icon from "@js/components/Icon.vue";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import {BusinessSetting} from "@js/types/models/business_setting";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {VueRecaptcha} from 'vue-recaptcha';
import FormValidationError from "@js/components/form/FormValidationError.vue";
import {useAdminDataStore} from "@js/services/state/states";
import {Firebase} from "@js/firebase";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {setLightTheme} from "@js/services/theme_service";


export default defineComponent({
    components: {
        FormValidationError,
        FormPasswordInput, Icon, FormInput, VueRecaptcha, LoadingButton, Button, Loading,
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            dummy: {
                email: 'super_admin@shopy.com',
                password: 'password'
            },

            email: null,
            password: null,
            showPassword: false,
            loading: false,
            need_recaptcha: true,
            mobile_number: '',
            fcm_token: null,
            using_email: true,
            recaptcha_verified: false,
        }
    },
    computed: {
        getSiteKey() {
            return BusinessSetting.getInstance().recaptcha_site_key;
        },
        isDemo(){
            return BusinessSetting.getInstance().demo;
        }
    },
    methods: {
        toggleUsingEmail() {
            this.using_email = !this.using_email;
        },
        async loginWithGoogle() {

            if (this.loading == true)
                return;
            this.loading = true;
            this.clearAllError();

            let user = await Firebase.googleSignIn();
            if (user == null) {
                ToastNotification.error();
                this.loading = false;

                return;
            }

            try {
                const response = await Request.post(adminAPI.auth.google_login, {
                    uid: user.uid,
                    fcm_token: this.fcm_token
                });

                const store = useAdminDataStore();
                store.updateUserData(response.data.admin);
                store.updateAuthToken(response.data.token);

                const redirectTo = this.$route.query?.redirectTo;
                if (redirectTo != null) {
                    this.$router.replace(redirectTo);
                } else {
                    this.$router.replace({name: 'admin.dashboard'});
                }

            } catch (error: any) {
                handleException(error);
            }
            this.loading = false;

        },
        async login() {
            if (this.loading == true)
                return;

            if (!this.recaptcha_verified) {
                this.addError('recaptcha', this.$t('please_check_recaptcha'))
                return;
            }

            this.loading = true;
            this.errors = null;


            try {
                const response = await Request.post(adminAPI.auth.login, {
                    email: this.using_email ? this.email : null,
                    mobile_number: !this.using_email ? this.mobile_number : null,
                    password: this.password,
                    fcm_token: this.fcm_token
                });
                const store = useAdminDataStore();
                store.updateUserData(response.data.admin);
                store.updateAuthToken(response.data.token);

                const redirectTo = this.$route.query?.redirectTo;
                if (redirectTo != null) {
                    this.$router.replace(redirectTo);
                } else {
                    this.$router.replace({name: 'admin.dashboard'});
                }

            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                }
            }

            this.loading = false;
        },

        autoFill() {
            this.using_email = true;
            this.email = this.dummy.email;
            this.password = this.dummy.password;
        },
        onRecaptchaVerified() {
            this.recaptcha_verified = true;
            this.clearAllError();
        },
        onRecaptchaError() {
            this.recaptcha_verified = true;
            this.clearAllError();
        },

        onRecaptchaExpired() {
            this.recaptcha_verified = false;
            this.addError('recaptcha', this.$t('please_check_recaptcha'));
        }

    },
    created() {
        setLightTheme();
        if (useAdminDataStore().isLoggedIn()) {
            const redirectRoute = this.$route.query?.redirectTo ?? 'admin.dashboard';
            this.$router.replace({name: redirectRoute});
            return;
        }
        Firebase.getFCMToken().then((e) => this.fcm_token = e);

        if (!BusinessSetting.instance.recaptcha_enable) {
            this.need_recaptcha = false;
            this.recaptcha_verified = true;
        }


    }

})


</script>

<style scoped>

</style>
