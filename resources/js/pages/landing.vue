<template>
    <div id="landing">
        <!--Navbar Start-->
        <nav id="navbar" ref="navbar" class="navbar navbar-expand-lg fixed-top sticky">
            <div class="container">
                <a class="navbar-brand text-dark fw-bold me-auto">
                    <div class="logo-box">
                        <img alt="" height="30" src="/assets/images/logo/dark.svg">
                    </div>
                </a>

                <div id="navbarCollapse" class="collapse navbar-collapse">
                    <ul class="navbar-nav mx-auto navbar-center">
                    </ul>


                    <Button color="teal" flexed-icon variant="soft" @click="browseCustomerWeb">
                        <Icon class="me-1-5" icon="language" size="18"></Icon>
                        Customer
                    </Button>
                    <Button class="ms-2" color="bluish-purple" flexed-icon @click="goToAdminPanel">
                        <Icon class="me-1-5" icon="computer" size="18"></Icon>
                        Admin Panel
                    </Button>
                </div>

            </div>

        </nav>



        <div class="hero-container pb-5">
            <div class="d-flex justify-content-center" style="margin-top: 70px">
                <img alt="" class="img-fluid" src="/assets/images/landing/hero_1.png">
            </div>
            <div class="row justify-content-center">

                <div class="col-lg-7 col-md-7 text-center">
                    <div class="title-heading">
                        <h1 class="fw-medium">{{appName}} - One Stop Ecommerce Solution</h1>
                        <p class="para-desc text-muted">More than just online ecommerce platform</p>

                    </div>
                </div>

            </div><!--end row-->

            <div class="d-flex align-items-center justify-content-center mt-3">
                <a class="scroll-down-mouse" data-scroll-offset="20" href="#customer">
                    <div class="mouse"></div>
                </a>

            </div>
        </div>


        <section id="customer" class="">
            <div class="container">
                <h4 class="section-title mb-0 customer-title">Customer</h4>
                <hr class="mx-auto bg-soft-teal my-2-5" style="height: 3px; width: 200px">

                <div class="row align-items-center justify-content-between mt-5">
                    <div class="col-lg-5">
                        <div class="d-flex">
                            <div class="border rounded-lg d-flex align-items-center p-1-5 ps-2 pe-2-5">
                                <VItem align-items="center" class="p-0-5 px-1-5 rounded-pill" color="primary"
                                       display="flex">
                                    <span class="font-11">New</span>
                                </VItem>
                                <span class="ms-2 fw-medium">Our First Release</span>
                            </div>
                            <Button class="fw-semibold text-uppercase  ms-2 font-13" variant="soft">
                                <span class="ls-0-5">more  &nbsp;</span>->
                            </Button>

                        </div>
                        <h2 class="fw-medium mt-4">We build best apps that customers love</h2>
                        <p class="text-muted mb-0">Download latest version of customer app from app store and play
                            store or use customer web to access same</p>

                        <div class="d-flex flex-wrap gap-1 mt-4">

                            <Button color="teal" flexed-icon @click="showCustomerAppModal">
                                <Icon icon="vertical_align_bottom" size="18"></Icon>
                                <span class="ms-1-5 fw-medium"> Download Apps</span>
                            </Button>
                            <Button class="ms-1-5" color="bluish-purple" flexed-icon variant="outline"
                                    @click="browseCustomerWeb">
                                <Icon icon="language" size="18"></Icon>
                                <span class="ms-1-5 fw-medium">Browse Web&nbsp; -></span>
                            </Button>
                        </div>


                    </div>


                    <div class="col-lg-5 offset-lg-1">
                        <img alt="" class="d-block mx-auto ms-lg-auto" width="500"
                             src="/assets/images/landing/customer.png"/>
                    </div>
                </div>

                <Row class="mt-5">
                    <Col lg="3">
                        <div class="features feature-primary text-center">
                            <div class="image position-relative d-inline-block">
                                <img alt="" src="/assets/images/module/food.png" width="50">
                            </div>

                            <div class="content mt-3">
                                <h5 class="fw-medium">Food</h5>
                                <p class="text-muted mb-0">Get food at your doorstep in snap</p>
                            </div>
                        </div>
                    </Col>
                    <Col lg="3">
                        <div class="features feature-primary text-center">
                            <div class="image position-relative d-inline-block">
                                <img alt="" src="/assets/images/module/grocery.png" width="50">
                            </div>

                            <div class="content mt-3">
                                <h5 class="fw-medium">Grocery</h5>
                                <p class="text-muted mb-0">We deliver grocery everyday as your routine</p>
                            </div>
                        </div>
                    </Col>
                    <Col lg="3">
                        <div class="features feature-primary text-center">
                            <div class="image position-relative d-inline-block">
                                <img alt="" src="/assets/images/module/ecommerce.png" width="50">
                            </div>

                            <div class="content mt-3">
                                <h5 class="fw-medium">Ecommerce</h5>
                                <p class="text-muted mb-0">Find the best deals near your home</p>
                            </div>
                        </div>
                    </Col>
                    <Col lg="3">
                        <div class="features feature-primary text-center">
                            <div class="image position-relative d-inline-block">
                                <img alt="" src="/assets/images/module/pharmacy.png" width="50">
                            </div>

                            <div class="content mt-3">
                                <h5 class="fw-medium">Pharmacy</h5>
                                <p class="text-muted mb-0">If you sick, don't worry we are for you</p>
                            </div>
                        </div>
                    </Col>


                </Row>


            </div>
        </section>


        <section class="odd">
            <div class="container">
                <h4 class="section-title mb-0 delivery-boy-title">Delivery</h4>
                <hr class="mx-auto bg-soft-green my-2-5" style="height: 3px; width: 200px">

                <div class="row align-items-center justify-content-between mt-5">

                    <div class="col-lg-5">
                        <img alt="" class="d-block mx-auto ms-lg-auto"
                             height="500" src="/assets/images/landing/delivery_boy.png"/>
                    </div>

                    <div class="col-lg-5 offset-lg-1">
                        <div class="d-flex">
                            <div class="border rounded-lg d-flex align-items-center p-1-5 ps-2 pe-2-5">
                                <VItem align-items="center" class="p-0-5 px-1-5 rounded-pill" color="primary"
                                       display="flex">
                                    <span class="font-11">New</span>
                                </VItem>
                                <span class="ms-2 fw-medium">Our First Release</span>
                            </div>
                            <Button class="fw-semibold text-uppercase  ms-2 font-13" variant="soft">
                                <span class="ls-0-5">more  &nbsp;</span>->
                            </Button>

                        </div>

                        <h2 class="fw-medium mt-4">We provide best app for our smart delivery partner</h2>
                        <p class="text-muted mb-0">Download latest version of delivery app from app store and play
                            store</p>

                        <div class="d-flex flex-wrap gap-1 mt-4">

                            <Button color="teal" flexed-icon @click="showDeliveryAppModal">
                                <Icon icon="vertical_align_bottom" size="18"></Icon>
                                <span class="ms-1-5 fw-medium"> Download Apps</span>
                            </Button>
                            <Button class="ms-1-5" color="bluish-purple" flexed-icon variant="outline"
                                    @click="goToDeliveryRegistration">
                                <Icon icon="local_shipping" size="18"></Icon>
                                <span class="ms-1-5 fw-medium">Self Registration&nbsp; -></span>
                            </Button>
                            <!--                            <a :href="setup.customer_android_app_url" class="logo-footer" target="_blank">-->
                            <!--                                <img alt="" height="44" src="/assets/images/landing/play-store.png">-->
                            <!--                            </a>-->
                            <!--                            <a :href="setup.customer_ios_app_url" class="logo-footer" target="_blank">-->
                            <!--                                <img alt="" height="44" src="/assets/images/landing/app-store.png">-->
                            <!--                            </a>-->
                        </div>


                    </div>


                </div>

            </div>


        </section>


        <section class="">
            <div class="container">
                <h4 class="section-title mb-0 seller-title">Seller</h4>
                <hr class="mx-auto bg-soft-purple my-2-5" style="height: 3px; width: 200px">

                <div class="row align-items-center justify-content-between mt-5">
                    <div class="col-lg-5">
                        <div class="d-flex">
                            <div class="border rounded-lg d-flex align-items-center p-1-5 ps-2 pe-2-5">
                                <VItem align-items="center" class="p-0-5 px-1-5 rounded-pill" color="primary"
                                       display="flex">
                                    <span class="font-11">New</span>
                                </VItem>
                                <span class="ms-2 fw-medium">Our First Release</span>
                            </div>
                            <Button class="fw-semibold text-uppercase  ms-2 font-13" variant="soft">
                                <span class="ls-0-5">more  &nbsp;</span>->
                            </Button>

                        </div>
                        <h2 class="fw-medium mt-4">Meet new way to the manage your business</h2>
                        <p class="text-muted mb-0">Download latest version of seller app from app store and play
                            store<br>No shop yet, no worry just register new shop</p>

                        <div class="d-flex flex-wrap gap-1 mt-4">

                            <Button color="teal" flexed-icon @click="showSellerAppModal">
                                <Icon icon="vertical_align_bottom" size="18"></Icon>
                                <span class="ms-1-5 fw-medium"> Download Apps</span>
                            </Button>
                            <Button class="ms-1-5" color="bluish-purple" flexed-icon variant="outline"
                                    @click="goToSellerRegistration">
                                <Icon icon="storefront" size="18"></Icon>
                                <span class="ms-1-5 fw-medium">Shop Registration&nbsp; -></span>
                            </Button>
                        </div>


                    </div>


                    <div class="col-lg-5 offset-lg-1">
                        <img alt="" class="d-block mx-auto ms-lg-auto"
                             width="500" src="/assets/images/landing/seller.png"/>
                    </div>
                </div>


                <h5 class="section-title mb-0 mt-5">Pricing (Monthly)</h5>

                <p class="text-center mt-3 text-muted ">Choose plan according your usage, You can also <span
                    class="text-primary  fw-medium">Upgrade plan</span>
                    as your need</p>


                <Row class="mt-5" gap="3">
                    <Col v-for="plan in shop_plans" :key="plan.id" lg="3">
                        <div class="shop-plan">
                            <h4 class="title">{{ plan.title }}</h4>
                            <h3 class="price">
                                {{ getFormattedCurrency(plan.price) }}
                                <span class="">/mo</span>
                            </h3>

                            <ul class="list-unstyled mb-0 ps-0 mt-4">
                                <li class="feature">
                                    <Icon class="me-2" icon="check_circle" size="19"></Icon>
                                    Access Seller Panel
                                </li>
                                <li class="feature">
                                    <Icon class="me-2" icon="local_convenience_store" size="19"></Icon>
                                    POS Enabled
                                </li>
                                <li class="feature">
                                    <Icon class="me-2" icon="dns" size="19"></Icon>
                                    {{ plan.products }} Products
                                </li>
                                <li class="feature">
                                    <Icon class="me-2" icon="percent" size="19"></Icon>
                                    <template v-if="plan.admin_commission===0">
                                        <span class="text-success">No commission</span>
                                    </template>
                                    <template v-else>
                                        <span class="text-danger">{{
                                                plan.admin_commission_type === 'percent' ? getPreciseCurrency(plan.admin_commission) + "%" : getFormattedCurrency(plan.admin_commission)
                                            }}</span> &nbsp; Commission
                                    </template>
                                </li>
                            </ul>
                            <div class="mt-4 text-center">
                                <Button class="fw-medium" color="primary" variant="soft" @click="goToSellerRegistration">
                                    Register ->
                                </Button>
                            </div>
                        </div>
                    </Col>
                </Row>

            </div>

        </section>


        <section class="odd">
            <div class="container">
                <Row align-items="center" gap="3">
                    <Col xl="7">
                        <div class="">
                            <h2 class="fw-medium mb-3">See everything about <span
                                class="text-primary fw-semibold">{{ setup.business_name }}</span>
                                business</h2>
                            <p class=" text-muted ">Start working with <span
                                class="text-primary fw-semibold">{{ setup.business_name }}</span> that can provide
                                everything you need to for you shop</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-1">
                                    <p>
                                        <Icon class="me-1-5" color="success" icon="check_circle" size="18"/>
                                        Marketing your shop to make it visible worldwide
                                    </p>
                                </li>
                                <li class="mb-1">
                                    <p>
                                        <Icon class="me-1-5" color="success" icon="check_circle" size="18"/>
                                        It's provide point of sales (POS) system to manage you shop offline
                                    </p>
                                </li>
                                <li class="mb-1">
                                    <p>
                                        <Icon class="me-1-5" color="success" icon="check_circle" size="18"/>
                                        Best plan with upmost priority to every sellers
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </Col><!--end col-->

                    <Col xl="5">
                        <Row class="counter" gap="3">
                            <Col md="6" sm="12">
                                <VItem class="py-5 px-5 text-center customer" color="primary" rounded xsoft>
                                    <h2 class="fw-semibold">1500+</h2>
                                    <h4 class="fw-medium">Happy Customer</h4>
                                </VItem>
                            </Col>
                            <Col md="6" sm="12">
                                <VItem class="py-5 px-5 mt-lg-5 ms-lg-3 text-center order" color="green" rounded xsoft>
                                    <h2 class="fw-semibold">3000+</h2>
                                    <h4 class="fw-medium">Orders Placed</h4>
                                </VItem>
                            </Col>
                            <Col md="6" sm="12">
                                <VItem class="py-5 px-5 text-center shop" color="teal" rounded xsoft>
                                    <h2 class="fw-semibold">120+</h2>
                                    <h4 class="fw-medium">Verified<br> Shops</h4>
                                </VItem>
                            </Col>
                            <Col md="6" sm="12">
                                <VItem class="py-5 px-5 mt-lg-5 ms-lg-3 text-center delivery-boy" color="rose" rounded xsoft>
                                    <h2 class="fw-semibold">300+</h2>
                                    <h4 class="fw-medium">Delivery</h4>
                                </VItem>
                            </Col>
                        </Row>
                    </Col><!--end col-->
                </Row>
            </div>
        </section>

        <Footer/>


        <VModal v-model="show_modal" lg>
            <Card class="mb-0">
                <CardHeader icon="vertical_align_bottom" title="Scan to Download"></CardHeader>
                <CardBody>
                    <div class="d-flex justify-content-around">
                        <div class="text-center">
                            <img :src="getQRImageLink(app_links.play_store)" alt="" class="img-fluid d-block"
                                 width="250"/>
                            <img alt="" class="mt-4 cursor-pointer" height="44"
                                 src="/assets/images/landing/play-store.png" @click="onPlayStore">
                        </div>
                        <div class="text-center">
                            <img :src="getQRImageLink(app_links.ios_store)" alt="" class="img-fluid d-block"
                                 width="250"/>
                            <img alt="" class="mt-4 cursor-pointer" height="44"
                                 src="/assets/images/landing/app-store.png" @click="onIosStore">
                        </div>
                    </div>
                </CardBody>
            </Card>
        </VModal>

        <div ref="back_to_top" class="back-to-top fade" @click="scrollTop">
            <Icon icon="arrow_upward" size="20"/>
        </div>

    </div>

</template>

<script lang="ts">

import {defineComponent} from "vue";
import {BusinessSetting, IBusinessSetting} from "@js/types/models/business_setting";
import Button from "@js/components/buttons/Button.vue";
import Icon from "@js/components/Icon.vue";
import Row from "@js/components/Row.vue";
import Col from "@js/components/Col.vue";
import Footer from "@js/components/Footer.vue";
import Card from "@js/components/Card.vue";
import VItem from "@js/components/VItem.vue";
import VModal from "@js/components/VModal.vue";
import CardHeader from "@js/components/CardHeader.vue";
import CardBody from "@js/components/CardBody.vue";
import {IShopPlan} from "@js/types/models/shop_plan";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {setLightTheme} from "@js/services/theme_service";

export default defineComponent({
    components: {CardBody, CardHeader, VModal, VItem, Card, Footer, Col, Row, Icon, Button},
    mixins: [UtilMixin],
    data() {
        return {
            setup: {} as IBusinessSetting,
            email: '',
            show_modal: false,
            shop_plans: [] as IShopPlan[],
            app_links: {
                play_store: '',
                ios_store: ''
            } as { play_store: string, ios_store: string }
        }
    },
    methods: {
        scrollTop() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        },
        onPlayStore() {
            window.open(this.app_links.play_store, '_blank');
        },
        onIosStore() {
            window.open(this.app_links.ios_store, '_blank');
        },
        getQRImageLink(url: string) {
            return "https://api.qrserver.com/v1/create-qr-code/?data=" + url;
        },
        browseCustomerWeb() {
            this.$router.push({name: 'customer.home'});
        },
        goToDeliveryRegistration() {
            this.$router.push({name: 'delivery_boy.registration'});
        },
        goToSellerRegistration() {
            this.$router.push({name: 'seller.shops.self_registration'});
        },
        goToAdminPanel() {
            this.$router.push({name: 'admin.dashboard'});
        },
        showCustomerAppModal() {
            this.app_links = {
                play_store: this.setup.customer_android_app_url,
                ios_store: this.setup.customer_ios_app_url
            }
            this.show_modal = true;
        },
        showDeliveryAppModal() {
            this.app_links = {
                play_store: this.setup.delivery_boy_android_app_url,
                ios_store: this.setup.delivery_boy_ios_app_url
            }
            this.show_modal = true;
        },
        showSellerAppModal() {
            this.app_links = {
                play_store: this.setup.seller_android_app_url,
                ios_store: this.setup.seller_ios_app_url
            }
            this.show_modal = true;
        },
        initScroll() {
            const navbar = this.$refs.navbar;
            const back_to_top = this.$refs.back_to_top;

            function windowScroll() {
                if (navbar != null) {
                    if (
                        document.body.scrollTop >= 50 ||
                        document.documentElement.scrollTop >= 50
                    ) {
                        navbar.classList.add("nav-sticky");
                        back_to_top?.classList.add("show");
                    } else {
                        navbar.classList.remove("nav-sticky");
                        back_to_top?.classList.remove("show");
                    }
                }
            }

            window.addEventListener('scroll', (ev) => {
                ev.preventDefault();
                windowScroll();
            })

        },
        async getPlans() {
            try {
                const response = await Request.get<IData<IShopPlan[]>>(sellerAPI.shop_plans.get, null, {forced: true});
                if (response.success()) {
                    this.shop_plans = response.data.data;
                }
                this.page_loading = false;

            } catch (error) {
                handleException(error);
            } finally {
            }
        }
    },
    mounted() {
        this.initScroll();
        this.getPlans();
    },
    created() {
        setLightTheme();
        this.setup = BusinessSetting.instance;
    },
});

</script>

<style scoped>

</style>
