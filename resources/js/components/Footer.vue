<template>
    <footer class="pt-4 " id="footer">
        <div class="container">
            <div class="py-3">
                <Row gap="3">
                    <Col sm="6" xl="3">
                        <a class="logo-footer" href="#">
                            <img alt="" height="36" src="/assets/images/logo/light.svg">
                        </a>

                        <div class="mt-3">

                            <div class="d-flex gap-3 text-white mt-3">
                                <a :href="setup.facebook_url" class="rounded font-16" target="_blank">
                                    <svg fill="none" height="24" stroke="currentColor"
                                         stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                         viewBox="0 0 24 24"
                                         width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                                    </svg>
                                </a>
                                <a :href="setup.instagram_url" class="rounded font-16" target="_blank">
                                    <svg fill="none" height="24" stroke="currentColor"
                                         stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                         viewBox="0 0 24 24"
                                         width="24" xmlns="http://www.w3.org/2000/svg">
                                        <rect height="20" rx="5" ry="5" width="20" x="2" y="2"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                    </svg>
                                </a>
                                <a :href="setup.twitter_url" class="rounded font-16" target="_blank">
                                    <svg fill="none" height="24" stroke="currentColor"
                                         stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                         viewBox="0 0 24 24"
                                         width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                                    </svg>
                                </a>
                                <a :href="setup.linkedin_url" class="rounded font-16" target="_blank">
                                    <svg fill="none" height="24" stroke="currentColor"
                                         stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                         viewBox="0 0 24 24"
                                         width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                                        <rect height="12" width="4" x="2" y="9"></rect>
                                        <circle cx="4" cy="4" r="2"></circle>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </Col>

                    <Col sm="6" xl="3">
                        <div class="d-flex flex-column gap-3 text-white">
                            <div class="d-flex align-items-center gap-2">
                                <i class="material-symbols-rounded font-24">home</i>
                                <span>{{ setup.address }}</span>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <i class="material-symbols-rounded font-24">call</i>
                                <span>{{ setup.mobile_number }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="material-symbols-rounded font-24">mail</i>
                                <span>{{ setup.email }}</span>
                            </div>
                        </div>
                    </Col>

                    <Col sm="6" xl="3">
                        <div class="d-flex flex-column gap-2">
                            <router-link :to="{name: 'customer.profile'}" class="footer-link">My account
                            </router-link>
                            <router-link :to="{name: 'customer.orders.index'}" class="footer-link">Order
                                History
                            </router-link>
                            <router-link :to="{name: 'customer.favorites.index'}" class="footer-link">
                                Favorites
                            </router-link>
                            <router-link :to="{name: 'customer.carts.index'}" class="footer-link">My Cart
                            </router-link>
                            <router-link :to="{name: 'customer.wallets.index'}" class="footer-link">Wallet
                            </router-link>

                        </div>
                    </Col>

                    <Col sm="6" xl="3">
                        <div class="col-12">
                            <h3 class="text-white mb-3 mt-0">Newsletter</h3>
                            <p class="text-white">Subscribe and receive the latest tips via email.</p>
                        </div>
                        <Row>
                            <Col lg="12">
                                <div class="foot-subscribe mb-3">
                                    <input class="form-control border bg-transparent"
                                           v-model="email" name="email" placeholder="Email Address" type="email">
                                    <Button color="bluish-purple" @click="subscribeToNewsletter">
                                        <Icon icon="arrow_forward" size="18"></Icon>
                                    </Button>
                                </div>
                            </Col>
                        </Row>

                        <div class="d-flex flex-wrap gap-1 mt-3 justify-content-center">
                            <a :href="setup.customer_android_app_url" class="logo-footer" target="_blank">
                                <img alt="" height="44" src="/assets/images/landing/play-store.png">
                            </a>
                            <a :href="setup.customer_ios_app_url" class="logo-footer" target="_blank">
                                <img alt="" height="44" src="/assets/images/landing/app-store.png">
                            </a>
                        </div>

                    </Col>
                    <!--end col-->
                </Row>
                <!--end row-->
            </div>

            <div class="py-3">
                <div class="container text-center">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="text-sm-start text-white">
                                <p class="mb-0">©{{ full_year }}
                                    {{appName}} - Design & Develop by Getappui
                                </p>
                            </div>
                        </div>
                        <!--end col-->

                        <div class="col-sm-6 mt-4 mt-sm-0 pt-2 pt-sm-0">
                            <div class="list-unstyled text-sm-end mb-0">
                                <img alt="" class="avatar avatar-ex-sm"
                                     src="/assets/images/landing/payment-method.png" title="Payments">

                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end container-->
            </div>
        </div>
        <!--end container-->
    </footer>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import Icon from "@js/components/Icon.vue";
import {BusinessSetting, IBusinessSetting} from "@js/types/models/business_setting";
import Row from "@js/components/Row.vue";
import Col from "@js/components/Col.vue";
import Button from "@js/components/buttons/Button.vue";
import {ToastNotification} from "@js/services/toast_notification";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {handleException} from "@js/services/api/handle_exception";
import UtilMixin from "@js/shared/mixins/UtilMixin";


export default defineComponent({
    components: {Icon, Button, Col, Row},
    mixins: [UtilMixin],
    props: {},
    data() {
        return {
            setup: {} as IBusinessSetting,
            email: ''
        }
    },
    computed: {
        full_year() {
            return new Date().getFullYear();
        }
    },
    watch: {},

    methods: {
        async subscribeToNewsletter() {
            if (this.email.trim().length == 0) {
                ToastNotification.error('Enter a valid email');
            }
            try {
                const response = await Request.post(customerAPI.newsletter.subscribe, {
                    email: this.email
                });
                if (response.success()) {
                    ToastNotification.success("Email is subscribed")
                }
            } catch (e) {
                if (Response.is422(e)) {
                    let errors = e.response.data.errors;
                    if (errors['email']) {
                        ToastNotification.error(errors['email'][0])
                    }
                } else {
                    handleException(e);
                }
            }
        }
    },
    mounted() {
        this.setup = BusinessSetting.instance;
    }
});
</script>
