<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="order==null?$t('edit_order'):$t('order')+ ' #'+order.id"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="8">
                    <Card>

                        <CardHeader :title="$t('order_details')" icon="shopping_bag" type="msr" class="py-2">
                            <div class="d-flex float-end">
                                <Button class="me-2-5 p-1 px-2" variant="soft" color="secondary"
                                        @click="toggleShowingProducts">
                                    <Icon :icon="show_all_products ? 'visibility' : 'visibility_off'" size="20"
                                          type="msr"/>
                                    <span class="ms-1-5">{{
                                            show_all_products ? $t('hide_products') : $t('show_products')
                                        }}</span></Button>

                                <router-link :to="{name: 'seller.orders.invoice', params: {'id': id} }">
                                    <Button variant="soft" color="teal" class=" p-1 px-2">
                                        <Icon icon="print" type="msr" size="20"/>
                                        <span class="ms-1-5">{{ $t('invoice') }}</span>
                                    </Button>
                                </router-link>


                            </div>
                        </CardHeader>

                        <CardBody>
                            <div class="table-responsive">
                                <table class="table table-bordered table-centered mb-0 table-nowrap">
                                    <thead class="table-light">
                                    <tr>
                                        <th>{{ $t('image') }}</th>
                                        <th>{{ $t('name') }}</th>
                                        <th>{{ $t('quantity') }}</th>
                                        <th>{{ $t('effected_price') }}</th>
                                        <th>{{ $t('total') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <template v-for="item in order.items" v-if="show_all_products">
                                        <tr :class="[{'border-dashed': item.addons!=null && item.addons.length>0}]"
                                            class="border">
                                            <td>
                                                <NetworkImage :placeholder-src="productPortraitPlaceholder"
                                                              :src="item.product.images[0]?.image"
                                                              :width="48"
                                                              rounded/>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ item.product.name }}</span>
                                            <p>[{{ item.product.unit_title }} :
                                                {{ item.product_option.option_value }}]</p>
                                        </td>

                                        <td>{{ item.quantity }}</td>
                                        <td>{{ getFormattedCurrency(item.product_option.calculated_price) }}</td>
                                        <td>{{ getFormattedCurrency(getItemTotal(item)) }}</td>

                                    </tr>
                                    <template v-if="item.addons">
                                        <tr v-for="addon in item.addons" class="border-dashed"
                                            style="padding: 0.5rem 0.5rem !important;">
                                            <td>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ addon.addon.name }}</span>
                                            </td>
                                            <td>{{ addon.quantity }}</td>
                                            <td>{{ getFormattedCurrency(addon.price) }}</td>
                                            <td>{{ getFormattedCurrency(getAddonTotal(addon)) }}</td>
                                        </tr>
                                    </template>
                                </template>

                                <template v-else>
                                    <tr>
                                        <td class="text-center align-items-center" colspan="5">
                                            {{ $t('all_products_are_hidden') }}
                                            <span class="text-primary cursor-pointer ms-1" @click="toggleShowingProducts">{{ $t('show_products') }}
                                            </span>
                                        </td>
                                    </tr>
                                </template>


                                <tr>
                                    <td class="text-end fw-semibold" colspan="4">{{ $t('sub_total') }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ getFormattedCurrency(order.order_amount) }}</div>
                                    </td>
                                </tr>
                                <tr v-if="order.packaging_charge!==0">
                                    <td class="text-end fw-semibold" colspan="4">{{ $t('packaging_charge') }}</td>
                                    <td>{{ getFormattedCurrency(order.packaging_charge) }}</td>
                                </tr>
                                <tr v-if="order.delivery_charge!==0">
                                    <td class="text-end fw-semibold" colspan="4">{{ $t('delivery_charge') }}</td>
                                    <td>{{ getFormattedCurrency(order.delivery_charge) }}</td>
                                </tr>
                                <tr v-if="order.coupon_discount!==0">
                                    <td class="text-end fw-semibold" colspan="4">{{ $t('coupon_discount') }}</td>
                                    <td>- {{ getFormattedCurrency(order.coupon_discount) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold" colspan="4">{{ $t('extra_charges') }}</td>
                                    <td>{{ getFormattedCurrency(getOrderCharges) }}</td>
                                </tr>
                                    <tr>
                                        <td class="text-end fw-bold" colspan="4">{{ $t('total') }}</td>
                                        <td>
                                            <div class="fw-bold">{{ getFormattedCurrency(order.total) }}</div>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </CardBody>

                    </Card>
                    <Row>
                        <Col lg="6">
                            <Card>

                                <CardHeader :title="$t('customer_detail')" icon="person">
                                    <div class="float-end">
                                        <span class="text-muted fw-medium">{{ $t('OTP') }} - {{ order.otp }}</span>
                                    </div>
                                </CardHeader>

                                <CardBody>
                                    <div class="d-flex align-items-center" v-if="order.customer!=null">
                                        <NetworkImage :src="order.customer.avatar" :size="48" class="rounded-circle"
                                                      :from-text="order.customer.first_name+' '+order.customer.last_name"
                                                      circular/>

                                        <div class="ms-2 flex-grow-1">
                                            <div class="fw-medium">
                                                {{ order.customer.first_name }} {{ order.customer.last_name }}
                                            </div>
                                            <span class="text-primary cursor-pointer" @click="mailToCustomer">
                                                {{ order.customer.email }}</span>
                                        </div>

                                        <Button class="p-1-5" color="teal" flexed-icon variant="soft"
                                                @click="chatWithCustomer">
                                            <Icon icon="chat_bubble"></Icon>
                                        </Button>

                                        <Button class="p-1-5 ms-2" color="orange" flexed-icon variant="soft"
                                                @click="callToCustomer">
                                            <Icon icon="call"></Icon>
                                        </Button>

                                    </div>

                                    <hr class="dashed my-2-5">
                                    <p class="fw-medium text-muted">{{ $t('payment_details') }}</p>
                                    <div class="d-flex">
                                        <div class="text-end">
                                            <p class="mb-1 fw-medium">{{ $t('type') }}:</p>
                                            <p class="mb-1 fw-medium">{{ $t('status') }}:</p>
                                        </div>
                                        <div class="ms-2">
                                            <p class="mb-1"> {{ getPaymentType() }}</p>
                                            <p class="mb-0">
                                                {{ getPaymentStatus() }}
                                                <Button v-if="isUnpaid" class="ms-2" variant="text" @click="setAsPaid">
                                                    {{ $t('set_paid') }}
                                                </Button>
                                            </p>

                                        </div>
                                    </div>
                                </CardBody>

                            </Card>

                        </Col>
                        <Col lg="6">

                            <Card v-if="order.address">

                                <CardHeader :title="$t('delivery')" icon="pin_drop" type="msr"/>

                                <CardBody>

                                    <template v-if="order.assign_delivery_boy!=null">
                                        <div class="d-flex align-items-center">
                                            <NetworkImage :src="order.assign_delivery_boy.avatar" :size="48"
                                                          class="rounded-circle"
                                                          :from-text="order.assign_delivery_boy.first_name+ ' '+order.assign_delivery_boy.last_name"
                                                          circular/>

                                            <div class="ms-2 flex-grow-1">
                                                <div class="fw-medium">
                                                    {{ order.assign_delivery_boy.first_name }}
                                                    {{ order.assign_delivery_boy.last_name }}
                                                </div>
                                                <span class="text-primary cursor-pointer" @click="mailToDelivery">
                                                    {{ order.assign_delivery_boy.email }}</span>
                                            </div>

                                            <Button class="p-1-5" color="orange" flexed-icon variant="soft"
                                                    @click="chatWithDeliveryBoy">
                                                <Icon icon="chat_bubble"></Icon>
                                            </Button>

                                            <Button class="p-1-5 ms-2" color="primary" flexed-icon variant="soft"
                                                    @click="goToDeliveryBoyOnMap">
                                                <Icon icon="location_on"></Icon>
                                            </Button>
                                            <Button class="p-1-5 ms-2" color="teal" flexed-icon variant="soft"
                                                    @click="callToDelivery">
                                                <Icon icon="call"></Icon>
                                            </Button>

                                        </div>
                                        <hr class="dashed my-2">
                                    </template>

                                    <div class="d-flex align-items-end">
                                        <div class="flex-grow-1">
                                            <p class="text-muted mb-0 fw-semibold">{{ order.address.type }}</p>
                                            <p class="mb-1"><span class="fw-medium">{{ $t('address') }}:</span>
                                                {{ order.address.address }}
                                            </p>
                                            <p class="mb-1"><span class="fw-medium">{{ $t('city') }}:   </span>
                                                {{ order.address.city }}</p>
                                            <p class="mb-0"><span class="fw-medium">{{ $t('pincode') }}:</span>
                                                {{ order.address.pincode }}
                                            </p>
                                        </div>
                                        <Button class="p-1-5" color="success" flexed-icon variant="soft"
                                                @click="goToCustomerAddressOnMap">
                                            <Icon icon="route"></Icon>
                                        </Button>

                                    </div>


                                </CardBody>

                            </Card>
                            <Card v-else>

                                <CardHeader :title="$t('delivery_type')" icon="pin_drop" type="msr"/>

                                <CardBody>
                                    <div class="d-flex">

                                        <div class="text-end">
                                            <p class="mb-1 fw-medium">{{ $t('type') }}:</p>

                                        </div>
                                        <div class="ms-2">
                                            <p class="mb-1"> {{ getOrderType() }}</p>
                                        </div>
                                    </div>

                                </CardBody>

                            </Card>
                        </Col>
                    </Row>
                </Col>


                <Col xl="4">
                    <Card>

                        <CardHeader :title="$t('track_order')" icon="route">
                            <div :class="[{'bg-primary':!isCancelled}, {'bg-danger':isCancelled}]" class="badge ms-2">
                                {{ getStatusFromText(lastOrderStatus.status) }}
                            </div>
                        </CardHeader>

                        <div class="track-order-list">

                            <ul id="order_status" class="list-unstyled p-4 pb-0"
                                style="height: 450px">
                                <li v-for="(order_status, index) in order.statuses"
                                    :class="[{'completed' : index< order.statuses.length-1}]">
                                    <span v-if="index===order.statuses.length-1" class="active-dot dot"></span>
                                    <h5 class="mt-0 mb-1 text-capitalize">
                                        {{ getStatusFromText(order_status.status) }}</h5>
                                    <p v-if="order_status.description" class="mb-0">Note:
                                        {{ order_status.description }}</p>
                                    <p class="text-muted">{{ getFormattedDate(order_status.created_at) }} -
                                        {{ getFormattedTime(order_status.created_at) }}</p>


                                </li>

                                <li v-if="canAccept">
                                    <h5 class="mt-0 mb-1">{{ $t('accept_order') }}</h5>
                                    <p class="text-muted">{{ $t('you_can_accept_or_reject_this_order') }}</p>
                                </li>

                                <li v-if="canReadyForDelivery">
                                    <h5 class="mt-0 mb-1">{{ $t('ready_for_deliver') }}</h5>
                                    <p class="text-muted">{{ $t('set_for_delivery_whenever_order_is_ready') }}</p>
                                </li>

                                <li v-if="canWaitForPayment" class="error">
                                    <h5 class="mt-0 mb-1 text-danger">{{ $t('wait_for_payment') }}</h5>
                                    <p class="text-muted">{{ $t('wait_until_customer_confirm_payment_process') }}</p>
                                </li>


                                <!--                                Todo: Need to translation-->
                                <li v-if="canWaitForDeliveryConfirmation">
                                    <h5 class="mt-0 mb-1">{{ $t('wait_for_delivery_boy') }}</h5>
                                    <p class="text-muted mb-0-5"> - {{ $t('wait_until_delivery_boy_accept_order') }}</p>
                                    <p class="text-muted"> - {{ $t('or_assign_to_other_delivery_boy') }}</p>
                                </li>
                                <li v-if="canWaitForDeliveryBoy">
                                    <h5 class="mt-0 mb-1">Wait for Delivery Partner</h5>
                                    <p class="text-muted">Please wait until delivery arrives</p>
                                </li>
                                <li v-if="canWaitForDeliverOrder">
                                    <h5 class="mt-0 mb-1">Wait for Delivery</h5>
                                    <p class="text-muted">Order will deliver too soon</p>
                                </li>

                            </ul>

                        </div>

                    </Card>

                    <Card>
                        <CardHeader :title="$t('actions')" icon="shopping_cart_checkout" type="msr">
                            <div v-if="order.ready_at"
                                 class="ms-2 d-flex align-items-center flex-grow-1 justify-content-end text-muted">
                                <Icon icon="alarm_on" size="18"/>
                                <span class="ms-1-5">
                                    {{ getFormattedDate(order.ready_at) }} -
                                {{ getFormattedTime(order.ready_at) }}</span>
                            </div>
                        </CardHeader>

                        <CardBody>
                            <p v-if="canWaitResubmit" class="text-primary">
                                {{ $t('please_wait_until_customer_resubmit') }}
                            </p>
                            <div v-if="order.medias!=null && order.medias.length>0" class="mb-3">
                                <div v-for="media in order.medias" class="border-1 d-inline-flex">

                                    <div class="d-inline me-1">
                                        <NetworkImage :src="media.media" height="50"/>
                                    </div>

                                </div>
                            </div>
                            <FormInput v-if="canAccept" v-model="description" :label="$t('description')"
                                       :placeholder="$t('describe_if_you_reject_or_cancel_order')" name="description" noLabel
                                       required
                                       type="text"/>
                            <div v-if="canSetEstimateTime" class="mb-3">
                                <div class="row g-3">
                                    <div class="col">
                                        <flatPicker
                                            v-model="ready_at"
                                            :config="{
                                                enableTime: true,
                                                 dateFormat: 'Y-m-d h:i K',
                                                 minDate: getToday,

                                             }"
                                            :placeholder="$t('set_estimate_time')"
                                            class="form-control"
                                        ></flatPicker>
                                        <FormValidationError :errors="errors" name="est_time"></FormValidationError>
                                    </div>
                                    <div class="col-auto">
                                        <LoadingButton :loading="loading" color="brown" variant="soft" icon="alarm_on"
                                                       @click="updateReadyTime" flexed-icon>{{ $t('update_est_time') }}
                                        </LoadingButton>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end" v-if="!isCancelled">
                                <div v-if="canAccept">
                                    <LoadingButton :loading="loading" class="ms-2" color="teal" icon="restore_page"
                                                   variant="soft"
                                                   @click="rejectOrder">
                                        {{ $t('reject') }}
                                    </LoadingButton>
                                    <LoadingButton :loading="loading" class="ms-2 btn-danger" icon="close"
                                                   @click="cancelOrder">
                                        {{ $t('cancel') }}
                                    </LoadingButton>

                                    <LoadingButton :loading="loading" class="ms-2" @click="acceptOrder">
                                        {{ $t('accept') }}
                                    </LoadingButton>

                                </div>
                                <div class="d-flex justify-content-end">
                                    <div v-if="canAssignForDelivery" class="ms-2">
                                        <router-link :to="{name: 'seller.orders.assign_delivery',  params: { id: id}}">
                                            <Button color="teal" flexed-icon>
                                                <Icon icon="local_shipping" size="18"></Icon>
                                                <span class="ms-1">{{ $t('assign_delivery_boy') }}</span>
                                            </Button>
                                        </router-link>

                                    </div>
                                    <div v-else-if="canAssignForOtherDelivery" class="ms-2">
                                        <router-link :to="{name: 'seller.orders.assign_delivery',  params: { id: id}}">
                                            <Button color="teal" flexed-icon>
                                                <Icon icon="local_shipping" size="18"></Icon>
                                                <span class="ms-1">{{ $t('change_delivery_boy') }}</span>
                                            </Button>

                                        </router-link>

                                    </div>
                                    <div v-if="canOrderReady" class="ms-2">
                                        <LoadingButton :loading="loading" @click="readyOrder">
                                            {{ $t('order_ready') }}
                                        </LoadingButton>


                                    </div>
                                    <div v-if="canOrderDeliver" class="ms-2">
                                        <LoadingButton :loading="loading" @click="deliverOrder">
                                            {{ $t('order_deliver') }}
                                        </LoadingButton>


                                    </div>

<!--                                    <div v-if="canReadyForDelivery" class="ms-2">-->
<!--                                        <LoadingButton :loading="loading" @click="onTheWayOrder">-->
<!--                                            {{ $t('on_the_way') }}-->
<!--                                        </LoadingButton>-->
<!--                                    </div>-->
                                </div>
                            </div>

                        </CardBody>
                    </Card>

                </Col>


            </Row>
        </PageLoading>


    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/seller/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import OrderItem from "@js/types/models/order_item";
import {
    getLocationDirectionFromMyLocationURL,
    getLocationDirectionURL,
    getMobileNumberCallURL,
    getSendEmailURl
} from "@js/services/api/location_api";
import OrderStatus from "@js/types/models/order_status";
import {handleException} from "@js/services/api/handle_exception";
import Order, {IOrder} from "@js/types/models/order";
import SimpleBar from 'simplebar';
import flatPicker from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import {defineComponent} from "vue";
import OrderItemAddon from "@js/types/models/order_item_addon";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import {FcmNotification} from "@js/services/fcm_notification";


export default defineComponent({
    components: {
        ...Components, Layout,
        flatPicker
    },
    mixins: [FormMixin, DiscountTypeMixin, UtilMixin],
    data() {
        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            order: null as IOrder,
            description: '',
            ready_at: Date.now(),
            show_all_products: true,
            simplebar: null
        }
    },
    computed: {
        getToday() {
            return Date.now();
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('orders'),
                    route: 'seller.orders.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        },
        lastOrderStatus() {
            return Order.getLastStatus(this.order);
        },
        isUnpaid(): boolean {
            return Order.isUnpaid(this.order);
        },
        getOrderCharges() {
            return Order.getTotalCharges(this.order);
        },
        canAccept() {
            return Order.canAccept(this.order);
        },
        canWaitResubmit() {
            return Order.canWaitResubmit(this.order);
        },
        canReadyForDelivery() {
            return Order.canReadyForDelivery(this.order);
        },
        canWaitForPayment() {
            return Order.canWaitForPayment(this.order);
        },
        canAssignForDelivery() {
            return Order.canAssignForDelivery(this.order);
        },
        canAssignForOtherDelivery() {
            return Order.canAssignForOtherDelivery(this.order);
        },
        canOrderReady() {
            return Order.canOrderReady(this.order);
            // return (Order.isPOS(this.order) || Order.isSelfPickup(this.order)) && OrderStatus.canAssignForDelivery(this.lastOrderStatus.status);
        },
        canOrderDeliver() {
            return Order.canOrderDeliver(this.order);
        },
        canWaitForDeliveryConfirmation() {
            return Order.canWaitForDeliveryConfirmation(this.order);
        },
        canWaitForDeliveryBoy() {
            return Order.canWaitForDeliveryBoy(this.order);
        },
        canSetEstimateTime() {
            return Order.canSetEstimateTime(this.order);
        },
        canWaitForDeliverOrder() {
            return Order.canWaitForDeliverOrder(this.order);
        },
        isCancelled() {
            return Order.isCancelled(this.order)
        },
        getStatusHeight() {
            return 275 + (this.order.items.length) * 76;
            // let isBtn = this.canAccept || this.canReadyForDelivery || this.canWaitForDeliveryBoy;
            // return 250 + (this.order.items.length) * 76 - (isBtn ? 36.53 : 0);
        }

    },
    methods: {
        toggleShowingProducts() {
            this.show_all_products = !this.show_all_products;
        },
        ///================= Order Actions ===============================================//
        async cancelOrder() {
            try {
                const response = await Request.patchAuth(sellerAPI.orders.cancel(this.id),);
                if (response.success()) {
                    ToastNotification.success(this.$t('order_cancelled_by_you'));
                    await this.getOrder();
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },
        async rejectOrder() {
            try {
                const response = await Request.patchAuth(sellerAPI.orders.reject(this.id), {
                    description: this.description
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('order_rejected'));
                    await this.getOrder();
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },
        async acceptOrder() {
            try {
                const response = await Request.patchAuth(sellerAPI.orders.accept(this.id),);
                if (response.success()) {
                    ToastNotification.success(this.$t('order_accepted'));
                    await this.getOrder();
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },
        async updateReadyTime() {
            this.clearAllError();
            try {
                if (this.ready_at == null) {
                    this.addError('est_time', this.$t('please_provide_est_time'))
                    return;
                }
                if (this.ready_at === this.order.ready_at) {
                    this.addError('est_time', this.$t('already_updated_time'))
                    return;
                }
                const date = new Date(this.ready_at);

                const response = await Request.patchAuth(sellerAPI.orders.set_ready_at(this.id), Request.getClearBody({
                    'ready_at': date.toUTCString()
                }));
                if (response.success()) {
                    ToastNotification.success(this.$t('estimate_time_set'));
                    await this.getOrder();
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },

        async setAsPaid() {
            try {
                const response = await Request.patchAuth(sellerAPI.orders.set_as_paid(this.id),);
                if (response.success()) {
                    ToastNotification.success('Order is now paid');
                    await this.getOrder();
                    // this.$router.go(-1);
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },
        async deliverOrder() {
            try {
                const response = await Request.patchAuth(sellerAPI.orders.deliver(this.id),);
                if (response.success()) {
                    ToastNotification.success(this.$t('order_delivered'));
                    await this.getOrder();
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },
        async readyOrder() {
            try {
                const response = await Request.patchAuth(sellerAPI.orders.ready(this.id),);
                if (response.success()) {
                    ToastNotification.success(this.$t('set_order_to_ready'));
                    await this.getOrder();
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },
        ///======= Order Functions ====================================//
        getItemTotal(orderItem) {
            return OrderItem.getItemTotal(orderItem);
        },
        getAddonTotal(addon) {
            return OrderItemAddon.getAddonTotal(addon);
        },
        goToDeliveryBoyOnMap() {
            let end = {
                latitude: (this.order.delivery_boy ?? this.order.assign_delivery_boy)?.latitude,
                longitude: (this.order.delivery_boy ?? this.order.assign_delivery_boy)?.longitude
            }
            let start = {
                latitude: this.order.shop.latitude,
                longitude: this.order.shop.longitude,

            }
            return window.open(getLocationDirectionURL(start, end), "_blank")

        },
        goToCustomerAddressOnMap() {
            return window.open(getLocationDirectionFromMyLocationURL(this.order.address.latitude, this.order.address.longitude), "_blank")
        },
        callToDelivery() {
            let number = this.order.delivery_boy?.mobile_number ?? this.order.assign_delivery_boy?.mobile_number;
            if (number != null) {
                window.open(getMobileNumberCallURL(number), "_blank")
            }
        },
        callToCustomer() {
            let number = this.order.customer.mobile_number;
            if (number != null) {
                window.open(getMobileNumberCallURL(number), "_blank")
            }
        },
        chatWithCustomer() {
            this.$router.push({name: 'seller.chats.index', query: {user_type: 'customer', id: this.order.customer.id}})
        },
        chatWithDeliveryBoy() {
            this.$router.push({
                name: 'seller.chats.index',
                query: {user_type: 'delivery_boy', id: (this.order.delivery_boy ?? this.order.assign_delivery_boy)?.id}
            })
        },
        mailToDelivery() {
            let address = this.order.delivery_boy?.email ?? this.order.assign_delivery_boy?.email;
            if (address != null) {
                window.open(getSendEmailURl(address), "_blank")
            }
        },
        mailToCustomer() {
            let address = this.order.customer?.email;
            if (address != null) {
                window.open(getSendEmailURl(address), "_blank")
            }
        },
        getStatusFromText(status) {
            return OrderStatus.getStatusText(status);
        },
        getPaymentType() {
            return Order.getPaymentTextFromOrder(this.order);
        },

        getOrderType() {
            return Order.getTypeText(this.order);
        },

        getPaymentStatus() {
            return Order.getPaymentStatusTextFromOrder(this.order);
        },
        async getOrder() {
            try {
                const response = await Request.getAuth<IData<IOrder>>(sellerAPI.orders.show(this.id), {forced: true});
                if (response.success()) {
                    this.order = response.data.data;
                    if (this.order.ready_at)
                        this.ready_at = this.order.ready_at;

                    this.page_loading = false;

                } else {
                    ToastNotification.unknownError();
                }

            } catch (error) {
                handleException(error);
            }
            this.updateState();
        },
        updateState() {
            const self = this;
            setTimeout(function () {
                let orderStatusContainer = document.getElementById('order_status');
                if (self.simplebar == null) {
                    self.simplebar = new SimpleBar(orderStatusContainer);
                }
                let simplebar = self.simplebar;

                if (simplebar.getContentElement()) {
                    const maxPosition = simplebar.getContentElement().scrollHeight;
                    const time = 12;

                    let scroll_position = simplebar.getScrollElement().scrollTop;
                    const interval = setInterval(function () {
                        scroll_position += 2;
                        simplebar.getScrollElement().scrollTop = scroll_position;
                        if (scroll_position > maxPosition) clearInterval(interval);
                    }, time);
                }
            }, 0)
        },
        orderChangeLister(order_id: number) {
            if (order_id === this.id) {
                this.getOrder();
            }
        }
    },
    async mounted() {
        FcmNotification.subscribeOrderChangeListener(this.orderChangeLister);
        this.setTitle(this.$t('edit_order'));
        await this.getOrder();

    },
    beforeUnmount() {
        this.simplebar = null;
        FcmNotification.unsubscribeOrderChangeListener(this.orderChangeLister);
    },


});

</script>
