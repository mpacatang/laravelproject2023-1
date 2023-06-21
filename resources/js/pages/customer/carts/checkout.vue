<template>
    <Layout>
        <PageLoading :loading="page_loading">
            <div v-if="carts.length>0">
                <Row>
                    <Col lg="8">
                        <Card>
                            <CardHeader :title="$t('delivery_information')" icon="local_shipping" size="sm" type="msr"
                                        weight="300"/>
                            <CardBody>
                                <div class="d-flex">
                                    <div class="form-check">
                                        <input id="delivery_radio" v-model="order_type"
                                               class="form-check-input cursor-pointer"
                                               name="order_type" type="radio" value="delivery">
                                        <label class="form-check-label cursor-pointer" for="delivery_radio">
                                            {{ $t('delivery') }}
                                        </label>
                                    </div>
                                    <div class="form-check ms-4">
                                        <input id="self_pickup_radio" v-model="order_type"
                                               class="form-check-input cursor-pointer"
                                               name="order_type" type="radio" value="self_pickup">
                                        <label class="form-check-label cursor-pointer" for="self_pickup_radio">
                                            {{ $t('self_pickup') }}
                                        </label>
                                    </div>
                                </div>

                                <div v-if="order_type==='delivery'" class="mt-2-5">
                                    <Row class="g-3">
                                        <Col v-for="address in addresses" lg="4">
                                            <div class="border p-2 rounded position-relative cursor-pointer"
                                                 @click="onChangeAddress(address)">
                                                <div class="d-flex">
                                                    <Icon icon="location_on" size="sm" type="msr"/>
                                                    <p class="ms-1 font-14 text-muted fw-semibold text-success mb-1">
                                                        {{ address.type }}</p>
                                                </div>

                                                <p class="fw-medium ms-1 mb-1">{{ address.address }}</p>
                                                <p class="ms-1 mb-0">{{ address.city }} - {{ address.pincode }}</p>
                                                <span :class="[{'active': selected_address?.id===address.id}]"
                                                      class="custom-check address-check">
                                                    <Icon icon="check" size="xxs"/>
                                                </span>
                                            </div>
                                        </Col>
                                    </Row>
                                </div>


                            </CardBody>
                        </Card>
                        <Row>
                            <Col lg="6">
                                <Card>
                                    <CardHeader :title="$t('coupon')" icon="confirmation_number" size="sm" type="msr"
                                                weight="300"></CardHeader>
                                    <CardBody>
                                        <div v-if="selected_coupon">
                                            <p class="fw-semibold mb-0">{{ selected_coupon.code }}</p>
                                            <p class="mb-1">{{ $t('you_will_save') }}
                                                {{ getFormattedCurrency(couponDiscount(selected_coupon)) }}
                                                {{ $t('with_this_code') }}</p>
                                        </div>
                                        <div v-else>
                                            <p class="fw-medium text-muted">
                                                {{ $t('there_is_no_coupon_selected') }}</p>
                                        </div>
                                        <div>
                                            <Button v-if="selected_coupon" color="danger" variant="text"
                                                    @click="onSelectCoupon(null)">{{ $t('remove?') }}
                                            </Button>
                                            <div class="float-end">
                                                <Button class="rounded" color="primary" flexed-icon
                                                        variant="soft"
                                                        @click="show_coupon_modal=true">
                                                    <Icon icon="confirmation_number" size="sm" type="msr"/>
                                                    <span class="ms-2">{{
                                                            selected_coupon != null ? $t('change_coupon') : $t('choose_coupon')
                                                        }}</span>
                                                </Button>
                                            </div>
                                        </div>
                                    </CardBody>
                                </Card>
                                <Card>
                                    <CardHeader :title="$t('payment')" icon="credit_card" size="sm" type="msr"
                                                weight="300"></CardHeader>
                                    <CardBody>
                                        <div class="form-check">
                                            <input id="cash_on_delivery_radio" v-model="payment_type"
                                                   class="form-check-input cursor-pointer"
                                                   name="payment_type" type="radio" value="cash_on_delivery">
                                            <label class="form-check-label cursor-pointer" for="cash_on_delivery_radio">
                                                {{ $t('cash_on_delivery') }}
                                            </label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input id="wallet_radio" v-model="payment_type"
                                                   class="form-check-input cursor-pointer"
                                                   name="payment_type" type="radio" value="wallet">
                                            <label class="form-check-label cursor-pointer" for="wallet_radio">
                                                {{ $t('wallet') }}
                                            </label>
                                        </div>

                                        <div v-if="payment_type==='wallet'" class="mt-3 text-center">
                                            <PageLoading :loading="wallet_loading">
                                                <p class="fw-medium text-muted mb-1">{{ $t('current_balance') }}</p>
                                                <h2 class="fw-medium mt-0">{{ getFormattedCurrency(wallet.balance) }}</h2>
                                            </PageLoading>
                                        </div>
                                    </CardBody>
                                </Card>
                            </Col>
                            <Col lg="6">
                                <Card>
                                    <CardHeader :title="$t('other')" icon="article" size="sm" type="msr"
                                                weight="300"></CardHeader>
                                    <CardBody class="pb-0">
                                        <FormInputArea v-model="notes" :errors="errors"
                                                       :label="$t('notes')" name="notes"/>

                                        <template v-if="needPrescription">
                                            <FileUpload :default-files="medias_helper.defaultFiles"
                                                        :placeholder="$t('upload_prescription')"
                                                        :label="$t('prescription')"
                                                        preview-as-list
                                                        v-on:file-added="medias_helper.onFileUpload"
                                                        height="68"
                                                        v-on:file-removed="medias_helper.onFileRemoved"
                                            />
                                        </template>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>

                    </Col>
                    <Col lg="4">
                        <Card>
                            <CardHeader :title="$t('products')" icon="dns" size="sm" type="msr" weight="300">
                                <Badge class="ms-1" primary>{{ carts.length }}</Badge>
                            </CardHeader>
                            <CardBody>
                                <SimpleBar style="max-height: 300px">
                                    <div class="d-flex flex-column gap-2-5">
                                        <div v-for="cart in carts" :key="cart.id"
                                             class="border d-flex p-2 rounded align-items-center">
                                            <NetworkImage :src="getProductImage(cart.product)" size="54" rounded :placeholder-src="productPortraitPlaceholder"/>
                                            <div class="flex-grow-1 ms-2">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-medium max-lines">{{ cart.product.name }}</span>
                                                    <span class="fw-medium text-nowrap ms-2">{{
                                                            getFormattedCurrency(calculateCartTotal(cart))
                                                        }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span class="text-muted">{{
                                                            cart.product.unit_title
                                                        }}: {{ cart.product_option.option_value }}</span>
                                                    <span class="text-muted">{{ $t('Qty') }}: {{ cart.quantity }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </SimpleBar>
                            </CardBody>
                        </Card>
                        <Card>
                            <CardHeader :title="$t('order_information')" icon="local_mall" size="sm" type="msr"
                                        weight="300"></CardHeader>
                            <PageLoading :loading="order_loading" class="mb-3">
                                <CardBody class="pb-0">
                                    <div class="d-flex justify-content-between">
                                        <p class="fw-medium">{{ $t('order') }}</p>
                                        <p class="fw-semibold">{{ getFormattedCurrency(order_amount) }}</p>
                                    </div>
                                    <div v-if="shop_charges!==0" class="d-flex cursor-pointer"
                                         @click="show_shop_charge_modal=true">
                                        <div class="flex-grow-1">
                                            <span class="fw-medium me-0-5">{{ $t('govt._tax') }}</span>
                                            <InfoTooltip tooltip="It include all taxes, packaging charges from seller"/>
                                        </div>

                                        <p class="fw-semibold">{{ getFormattedCurrency(shop_charges) }}</p>
                                    </div>
                                    <div v-if="delivery_charge!==0" class="d-flex cursor-pointer"
                                         @click="show_delivery_explain_modal=true">
                                        <div class="flex-grow-1">
                                            <span class="fw-medium me-0-5">{{ $t('delivery_charge') }}</span>
                                            <InfoTooltip tooltip="Delivery charges are based on minimum charge + distance"/>
                                        </div>
                                        <p class="fw-semibold">{{ getFormattedCurrency(delivery_charge) }}</p>
                                    </div>
                                    <div v-if="admin_charge!==0" class="d-flex justify-content-between cursor-pointer"
                                         @click="show_admin_charge_modal=true">
                                        <div class="flex-grow-1">
                                            <span class="fw-medium me-0-5">{{ $t('admin_charge') }}</span>
                                            <InfoTooltip tooltip="Admin take commission for platform and payment charges"/>
                                        </div>
                                        <p class="fw-semibold">{{ getFormattedCurrency(admin_charge) }}</p>
                                    </div>
                                    <div v-if="coupon_discount!==0" class="d-flex justify-content-between">
                                        <p class="fw-medium">{{ $t('coupon_discount') }}</p>
                                        <p class="fw-semibold">- {{ getFormattedCurrency(coupon_discount) }}</p>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <hr class="dashed" style="width: 50%;"/>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="fw-semibold">{{ $t('total') }}</p>
                                        <p class="fw-bold">{{ getFormattedCurrency(total) }}</p>
                                    </div>
                                </CardBody>
                            </PageLoading>
                        </Card>
                        <div class="text-center">
                            <LoadingButton primary @click="placeOrder">{{ $t('place_order') }}</LoadingButton>
                        </div>
                    </Col>
                </Row>

                <VModal v-if="coupons" v-model="show_coupon_modal" close-btn>
                    <Card class="mb-0">
                        <CardHeader :title="$t('select_coupon')" icon="confirmation_number" msr></CardHeader>

                        <template v-if="coupons.length!==0">
                            <p class="text-center mt-2 fw-medium">
                                {{ $t('there_is_no_any_coupon_available') }}
                            </p>
                        </template>
                        <template v-else>
                            <div v-if="selected_coupon" class="border m-3 mb-0 p-2 rounded">
                                <p class="fw-semibold mb-1">{{ selected_coupon.code }}</p>
                                <p class="mb-1">{{ selected_coupon.description }}</p>
                                <div class="d-flex justify-content-between">
                            <span
                                class="text-muted">{{
                                    $t('expired_at')
                                }} {{ getFormattedDate(new Date(selected_coupon.expired_at)) }}</span>
                                    <div class="text-success fw-medium">
                                        {{ $t('applied') }}
                                    </div>
                                </div>
                                <hr class="dashed">
                                <span v-if="selected_coupon.delivery_free" class="text-success">
                                {{ $t('delivery_free') }}
                               </span>
                                <span v-else class="text-primary">
                            {{ $t('you_will_save') }} {{ getFormattedCurrency(couponDiscount(selected_coupon)) }}
                            {{ $t('with_this_code') }}
                        </span>
                            </div>

                            <SimpleBar style="max-height: 600px">
                                <CardBody>

                                    <div v-for="coupon in remaining_coupons" class="border mb-2 p-2 rounded">
                                        <p class="fw-semibold mb-1">{{ coupon.code }}</p>
                                        <p class="mb-1">{{ coupon.description }}</p>
                                        <div class="d-flex justify-content-between">
                            <span
                                class="text-muted">{{
                                    $t('expired_at')
                                }} {{ getFormattedDate(new Date(coupon.expired_at)) }}</span>
                                            <div v-if="couponNeedMore(coupon)" class="text-muted">{{
                                                    $t('not_applicable')
                                                }}
                                            </div>
                                            <div v-else-if="this.selected_coupon?.id === coupon.id" class="text-muted">
                                                {{ $t('applied') }}
                                            </div>
                                            <div v-else class="text-primary cursor-pointer"
                                                 @click="onSelectCoupon(coupon)">
                                                {{ $t('apply') }}
                                            </div>
                                        </div>
                                        <hr class="dashed">
                                        <span v-if="coupon.delivery_free" class="text-success">
                            {{ $t('delivery_free') }}
                        </span>
                                        <span v-else-if="couponNeedMore(coupon)" class="text-danger">
                            {{ $t('add_items_worth') }} {{
                                                getFormattedCurrency(couponHowMuchNeed(coupon))
                                            }} {{ $t('more_to_apply_this_offer') }}
                            </span>
                                        <span v-else class="text-primary">
                            {{ $t('you_will_save') }} {{ getFormattedCurrency(couponDiscount(coupon)) }}
                            {{ $t('with_this_code') }}
                            </span>
                                    </div>

                                </CardBody>
                            </SimpleBar>
                        </template>

                    </Card>
                </VModal>

                <VModal v-model="show_admin_charge_modal" close-btn>
                    <Card class="mb-0">
                        <CardHeader :title="$t('admin_charges')" icon="paid"></CardHeader>
                        <CardBody>
                            <div class="d-flex justify-content-between">
                                <p class="">{{ $t('order_commission') }}</p>
                                <p class="fw-semibold">{{ getFormattedCurrency(order_commission) }}</p>
                            </div>

                            <div class="d-flex justify-content-between">
                                <p class="">{{ $t('delivery_commission') }}</p>
                                <p class="fw-semibold">{{ getFormattedCurrency(delivery_commission) }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="">{{ $t('payment_charge') }}</p>
                                <p class="fw-semibold">{{ getFormattedCurrency(payment_charge) }}</p>
                            </div>
                            <div class="d-flex justify-content-end mb-1">
                                <hr class="dashed" style="width: 50%;"/>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="fw-medium">{{ $t('total') }}</p>
                                <p class="fw-semibold">{{
                                        getFormattedCurrency(order_commission + delivery_commission + payment_charge)
                                    }}</p>
                            </div>
                        </CardBody>
                    </Card>
                </VModal>

                <VModal v-model="show_delivery_explain_modal" close-btn>
                    <Card class="mb-0">
                        <CardHeader :title="$t('delivery_charges')" icon="paid"></CardHeader>
                        <CardBody>
                            <div class="d-flex justify-content-between">
                                <p class="">{{ $t('base_charge') }}</p>
                                <p class="fw-semibold">{{ getFormattedCurrency(this.minDeliveryCharge) }}</p>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <p class="mb-0">{{ $t('extra_charge') }}
                                        ({{ this.getPreciseCurrency(delivery_distance_meter / 1000) + " " + $t('KM') }})</p>
                                    <p class="mb-0 text-muted font-12">{{ $t('charge_per_km') }} =
                                        {{ this.deliveryChargeMultiplier }}</p>
                                </div>
                                <p class="fw-semibold">{{
                                        getFormattedCurrency(this.deliveryChargeMultiplier * (delivery_distance_meter / 1000))
                                    }}</p>
                            </div>

                            <div class="d-flex justify-content-end mb-1">
                                <hr class="dashed" style="width: 50%;"/>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="fw-medium">{{ $t('total') }}</p>
                                <p class="fw-semibold">{{
                                        getFormattedCurrency(this.delivery_charge)
                                    }}</p>
                            </div>
                        </CardBody>
                    </Card>
                </VModal>

                <VModal v-model="show_shop_charge_modal" close-btn>
                    <Card class="mb-0">
                        <CardHeader :title="$t('tax')" icon="paid"></CardHeader>
                        <CardBody>
                            <div class="d-flex justify-content-between">
                                <p class="">{{ $t('taxes') }}</p>
                                <p class="fw-semibold">{{ getFormattedCurrency(tax) }}</p>
                            </div>

                            <div class="d-flex justify-content-between">
                                <p class="">{{ $t('packaging_charge') }}</p>
                                <p class="fw-semibold">{{ getFormattedCurrency(packaging_charge) }}</p>
                            </div>

                            <div class="d-flex justify-content-end mb-1">
                                <hr class="dashed" style="width: 50%;"/>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="fw-medium">{{ $t('total') }}</p>
                                <p class="fw-semibold">{{
                                        getFormattedCurrency(tax + packaging_charge)
                                    }}</p>
                            </div>
                        </CardBody>
                    </Card>
                </VModal>

            </div>
            <div v-else>
                <div class="text-center">
                    <img alt="Empty cart" src="/assets/images/placeholder/empty-cart.png" width="450"/>
                    <p class="fw-medium mt-5">{{ $t('your_cart_is_empty') }}</p>

                    <Button color="primary" variant="fill" @click="goToShopping">{{ $t('go_to_shopping') }}</Button>
                </div>
            </div>
        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {Cart, ICart} from "@js/types/models/cart";
import QuantityButton from "@js/components/buttons/QuantityButton.vue";
import Button from "@js/components/buttons/Button.vue";
import Request from "@js/services/api/request";
import {IData} from "@js/types/models/data";
import {ICustomerAddress} from "@js/types/models/customer_address";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import FormInputArea from "@js/components/form/FormInputArea.vue";
import {Coupon, ICoupon} from "@js/types/models/coupon";
import {IShop, Shop} from "@js/types/models/shop";
import VModal from "@js/components/VModal.vue";
import {SimpleBar} from 'simplebar-vue3';
import {BusinessSetting} from "@js/types/models/business_setting";
import {INavigation, INavigationRoute, MapNavigation} from "@js/types/models/navigation";
import {ToastNotification} from "@js/services/toast_notification";
import Response from "@js/services/api/response";
import Badge from "@js/components/Badge.vue";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import Product, {IProduct} from "@js/types/models/product";
import {IOrderTypes} from "@js/types/models/order";
import {getAddressWithSubscribeState} from "@js/services/state/state_helper";
import {useCustomerDataStore} from "@js/services/state/states";
import {ICustomerWallet} from "@js/types/models/customer";
import {IOrderPaymentTypes} from "@js/types/models/order_payment";


export default defineComponent({
    components: {Badge, VModal, FormInputArea, Button, QuantityButton, Layout, ...Components, SimpleBar},
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {
        return {
            page_loading: true,
            order_loading: false,
            loading: false,
            wallet_loading: true,

            wallet: null as ICustomerWallet,

            shop_id: null,
            carts: [] as ICart[],
            shop: {} as IShop,
            medias_helper: new FileUploadHelper(),
            order_type: 'delivery' as IOrderTypes,
            payment_type: 'cash_on_delivery' as IOrderPaymentTypes,
            addresses: [] as ICustomerAddress[],
            selected_address: null as ICustomerAddress,
            previous_address: null as ICustomerAddress,
            coupons: [] as ICoupon[],
            selected_coupon: null as ICoupon,
            business_setting: BusinessSetting.getInstance(),
            show_coupon_modal: false,
            notes: null,

            show_delivery_explain_modal: false,
            show_shop_charge_modal: false,
            show_admin_charge_modal: false,
            delivery_distance_meter: 0,

            order_amount: 0,
            tax: 0,
            packaging_charge: 0,
            order_commission: 0,
            shop_charges: 0,
            delivery_charge: 0,
            delivery_commission: 0,
            coupon_discount: 0,
            payment_charge: 0,
            admin_charge: 0,
            total: 0
        };
    },
    watch: {
        payment_type() {
            this.calculateOrderPrices();
        },
        order_type(newVal: IOrderTypes) {
            if (newVal === 'self_pickup') {
                this.selected_address = null;
            } else if (newVal === 'delivery') {
                this.selected_address = this.getSelectedAddress();
            }
            this.calculateOrderPrices();
        }
    },
    methods: {

        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        calculateCartTotal(cart: ICart) {
            return Cart.calculateTotal(cart);
        },
        getSelectedAddress(): ICustomerAddress | null {
            if (this.addresses == null)
                return null;
            return this.addresses.find(function (address) {
                return address.selected;
            })
        },
        async getData() {
            try {
                this.page_loading = true;
                const self = this;
                const cartResponse = await Request.getAuth<IData<ICart[]>>(customerAPI.shops.carts(this.shop_id), {forced: true});
                this.carts = cartResponse.data.data;
                if (this.carts.length > 0) {
                    this.shop = this.carts[0].shop;
                } else {

                }
                getAddressWithSubscribeState((addresses: ICustomerAddress[]) => {
                    self.addresses = addresses;
                    self.selected_address = self.getSelectedAddress();
                })


                const couponsResponse = await Request.getAuth<IData<ICoupon[]>>(customerAPI.shops.coupons(this.shop.id), {forced: true});
                this.coupons = couponsResponse.data.data;


                this.page_loading = false;
                this.calculateOrderPrices().then();
            } catch (error) {
                handleException(error);
            }
        },
        async getWallet() {
            try {
                this.wallet_loading = true;
                const response = await Request.getAuth<IData<ICustomerWallet>>(customerAPI.wallets.get, {forced: true});
                if (response.success()) {
                    this.wallet = response.data.data;
                }

                this.wallet_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        async onChangeAddress(address: ICustomerAddress) {
            this.selected_address = address;
            await this.calculateOrderPrices();
        },
        async calculateDeliveryPrices() {
            if (this.order_type === 'delivery') {
                if (this.selected_address != null && this.selected_address!=this.previous_address) {
                    this.order_loading = true;
                    try {
                        let navigation: INavigation | null = await MapNavigation.getNavigation(
                            {
                                latitude: this.selected_address.latitude,
                                longitude: this.selected_address.longitude
                            },
                            {
                                latitude: this.shop.latitude,
                                longitude: this.shop.longitude
                            })
                        let route: INavigationRoute = null;
                        if (navigation != null) {
                            route = MapNavigation.findBestRoute(navigation)
                            if (route != null) {
                                this.delivery_distance_meter = route.distance;
                                this.delivery_charge = this.minDeliveryCharge + (this.deliveryChargeMultiplier * route.distance / 1000);
                            }
                        }

                        if (navigation == null || route == null) {
                            this.delivery_charge = 0;
                            this.selected_address = null;
                            ToastNotification.error(this.$t('this_address_is_not_valid'));
                        }
                        this.previous_address = this.selected_address;
                    } catch (e) {
                        this.delivery_charge = 0;
                        this.selected_address = null;
                        ToastNotification.error(this.$t('this_address_is_not_valid'));
                    }
                    this.order_loading = false;
                }
            } else {
                this.previous_address = null;
                this.delivery_charge = 0;
                this.delivery_distance_meter = 0;
            }
        },
        async calculateOrderPrices() {
            if(this.business_setting ==0){
                return;
            }
            if (this.carts.length == 0)
                return;
            this.order_amount = 0;
            for (const cart of this.carts) {
                this.order_amount += Cart.calculateTotal(cart);
            }
            this.packaging_charge = this.shop.packaging_charge;
            this.tax = Shop.calculateTaxFromOrder(this.shop, this.order_amount);
            this.shop_charges = this.tax + this.packaging_charge;
            await this.calculateDeliveryPrices();
            this.order_commission = Shop.calculateAdminCommissionFromOrder(this.shop, (this.order_amount + this.tax + this.packaging_charge));
            this.delivery_commission = BusinessSetting.calculateDeliveryCommissionFromOrder(this.business_setting, this.delivery_charge);
            if (this.selected_coupon == null) {
                this.coupon_discount = 0;
            } else {
                if (this.selected_coupon.delivery_free) {
                    this.coupon_discount = this.delivery_charge;
                } else {
                    this.coupon_discount = this.couponDiscount(this.selected_coupon);
                }
            }

            let total = this.order_amount + this.tax + this.packaging_charge + this.delivery_charge + this.order_commission + this.delivery_commission - this.coupon_discount;
            if (this.payment_type === 'wallet') {
                this.payment_charge = BusinessSetting.calculatePaymentChargeFromOrder(this.business_setting, total);
            } else {
                this.payment_charge = 0;
            }

            this.admin_charge = this.order_commission + this.delivery_commission + this.payment_charge;
            this.total = total + this.payment_charge;
        },
        couponNeedMore(coupon: ICoupon) {
            return coupon.min_order > this.order_amount;
        },
        couponHowMuchNeed(coupon: ICoupon) {
            return coupon.min_order - this.order_amount;
        },
        couponDiscount(coupon: ICoupon) {
            if (coupon.delivery_free) {
                return this.delivery_charge;
            }
            return Coupon.getDiscountFromOrder(coupon, this.order_amount);
        },
        onSelectCoupon(coupon: ICoupon) {
            this.selected_coupon = coupon;
            this.show_coupon_modal = false;
            this.calculateOrderPrices();
        },
        async placeOrder() {
            try {
                const response = await Request.postAuth<IData<any>>(customerAPI.orders.create, {
                    shop_id: this.shop.id,
                    order_type: this.order_type,
                    order_amount: this.order_amount,
                    tax: this.tax,
                    packaging_charge: this.packaging_charge,
                    delivery_charge: this.delivery_charge,
                    coupon_discount: this.coupon_discount,
                    total: this.total,
                    payment_type: this.payment_type,
                    customer_address_id: this.selected_address?.id,
                    notes: this.notes,
                    coupon_id: this.selected_coupon?.id,
                    medias: this.medias_helper.getImageDataFiles(),


                });
                if (response.success()) {
                    ToastNotification.success(this.$t('order_placed'));
                    this.$router.push({name: 'customer.orders.index', replace: true});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                    let humanReadableError = Response.getHumanReadableError(this.errors);
                    ToastNotification.error(humanReadableError)
                } else {
                    handleException(error);
                }
            }
        },
        goToShopping() {
            this.$router.push({name: 'customer.search'});
        }


    },
    computed: {
        remaining_coupons(): ICoupon[] {
            const self = this;
            return this.coupons.filter(function (item) {
                return item.id !== self.selected_coupon?.id;
            })
        },
        needPrescription() {
            for (const cart of this.carts) {
                if (cart.product.need_prescription)
                    return true;
            }
            return false;
        },
        minDeliveryCharge() {
            return this.shop.self_delivery ? this.shop.minimum_delivery_charge : BusinessSetting.getInstance().minimum_delivery_charge;
        },
        deliveryChargeMultiplier() {

            return this.shop.self_delivery ? this.shop.delivery_charge_multiplier : BusinessSetting.getInstance().delivery_charge_multiplier;
        }
    },
    mounted() {
        this.setTitle(this.$t('checkout'));
        const checkoutStore = useCustomerDataStore();
        this.shop_id = checkoutStore.checkout_shop_id;
        if(this.shop_id==null){
            ToastNotification.error(this.$t('do_shopping_first'))
            this.$router.push({name: 'customer.home'});
            return;
        }

        this.getData();
        this.getWallet();
    },

});

</script>

<style scoped>
.address-check {
    top: 18px;
    right: -2px;
}
</style>
