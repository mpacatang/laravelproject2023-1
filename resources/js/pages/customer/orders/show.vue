<template>
    <Layout>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="8">
                    <Card>

                        <CardHeader :title="$t('order_details')" icon="shopping_bag" type="msr" class="py-2">
                            <div class="d-flex float-end">
                                <Button class="me-2-5 p-1 px-2 d-flex align-items-center" color="purple" variant="soft"
                                        @click="chatWithShop">
                                    <Icon icon="chat_bubble" size="16"
                                          type="msr"/>
                                    <span class="ms-1-5 d-none d-lg-block">
                                        {{ $t('chat') }}
                                    </span>
                                </Button>

                                <Button class="me-2-5 p-1 px-2  d-flex align-items-center" color="secondary"
                                        variant="soft"
                                        @click="toggleShowingProducts">
                                    <Icon :icon="show_all_products ? 'visibility' : 'visibility_off'" size="20"
                                          type="msr"/>
                                    <span class="ms-1-5 d-none d-lg-block">
                                        {{ show_all_products ? $t('hide_products') : $t('show_products') }}
                                    </span>
                                </Button>

                                <router-link :to="{name: 'customer.orders.invoice', params: {'id': id} }">
                                    <Button class=" p-1 px-2  d-flex align-items-center" color="teal" variant="soft">
                                        <Icon icon="print" type="msr" size="20"/>
                                        <span class="ms-1-5 d-none d-lg-block">{{ $t('invoice') }}</span>
                                    </Button>
                                </router-link>


                            </div>
                        </CardHeader>

                        <CardBody>

                            <div class=" table-responsive">
                                <table class="table table-bordered table-centered table-nowrap mb-0">
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
                                                              :src=" item.product?.images[0]?.image"
                                                              :width="48"
                                                              rounded/>
                                            </td>
                                            <td>
                                                <span class="fw-semibold">{{ item.product.name }}</span>
                                                <p v-if="item.product_option.option_value">[{{ item.product.unit_title }} :
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
                                            <td class="text-center" colspan="5">{{ $t('all_products_are_hidden') }}
                                                <span
                                                    class="cursor-pointer text-primary"
                                                    @click="toggleShowingProducts">{{ $t('show_products') }}</span></td>
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

                                <CardHeader :title="$t('customer_detail')" icon="person" type="msr">
                                    <div class="float-end">
                                        <span class="text-muted fw-medium">{{ $t('OTP') }} - {{ order.otp }}</span>
                                    </div>
                                </CardHeader>

                                <CardBody>
                                    <div class="d-flex align-items-center" v-if="order.customer!=null">
                                        <NetworkImage :src="order.customer.avatar" :size="48" class="rounded-circle"
                                                      :from-text="order.customer.first_name+' '+order.customer.last_name"

                                                      circular/>

                                        <div class="ms-2">
                                            <div class="fw-medium">
                                                {{ order.customer.first_name }}
                                                {{ order.customer.last_name }}
                                            </div>
                                            <span>{{ order.customer.email }}</span>
                                        </div>
                                    </div>
                                    <hr class="dashed my-2-5"/>
                                    <p class="fw-medium text-muted">{{ $t('payment_details') }}</p>
                                    <div class="d-flex">
                                        <div class="text-end">
                                            <p class="mb-1 fw-medium">{{ $t('type') }}:</p>
                                            <p class="mb-1 fw-medium">{{ $t('status') }}:</p>
                                        </div>
                                        <div class="ms-2">
                                            <p class="mb-1"> {{ getPaymentType() }}</p>
                                            <p class="mb-1">
                                                {{ getPaymentStatus() }}</p>

                                        </div>
                                    </div>


                                </CardBody>

                            </Card>

                        </Col>
                        <Col v-if="order.address" lg="6">
                            <Card>

                                <CardHeader :title="$t('delivery')" icon="pin_drop" size="sm" type="msr"/>

                                <CardBody>
                                    <template v-if="order.delivery_boy!=null">
                                        <div class="d-flex align-items-center">
                                            <NetworkImage :src="order.delivery_boy.avatar" :size="48"
                                                          class="rounded-circle"
                                                          :from-text="order.delivery_boy.first_name+ ' '+order.delivery_boy.last_name"

                                                          circular/>

                                            <div class="ms-2 flex-grow-1">
                                                <div class="fw-medium">
                                                    {{ order.delivery_boy.first_name }}
                                                    {{ order.delivery_boy.last_name }}
                                                </div>
                                                <span class="text-primary" @click="mailToDelivery">
                                                    {{ order.delivery_boy.email }}</span>
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
                                    <p class="text-muted mb-0 fw-semibold">{{ order.address.type }}</p>
                                    <p class="mb-1"><span class="fw-medium">{{ $t('address') }}:</span> {{
                                            order.address.address
                                        }}
                                    </p>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <p class="mb-1"><span class="fw-medium">{{ $t('city') }}:</span>
                                                {{ order.address.city }}
                                            </p>
                                            <p class="mb-0"><span class="fw-medium">{{ $t('pincode') }}:</span> {{
                                                    order.address.pincode
                                                }}
                                            </p>
                                        </div>
                                        <a :href="getDirectionURL()"
                                           class="mt-2"
                                           target="_blank">
                                            <Button color="green" variant="soft">
                                                <Icon icon="route" size="sm" type="msr"></Icon>
                                            </Button>
                                        </a>
                                    </div>


                                </CardBody>

                            </Card>

                        </Col>
                        <Col v-else>
                            <div class="card">

                                <CardHeader icon="pin_drop" type="msr" :title="$t('delivery')"/>

                                <CardBody>
                                    <div class="d-flex">

                                        <div class="text-end">
                                            <p class="mb-1 fw-medium">{{ $t('type') }}:</p>

                                        </div>
                                        <div class="ms-2">
                                            <p class="mb-1"> {{ $t('self_pickup') }}</p>
                                        </div>
                                    </div>

                                </CardBody>

                            </div>
                        </Col>

                    </Row>
                </Col>


                <Col lg="4">
                    <Card>

                        <CardHeader :title="$t('track_order')" class="text-capitalize" icon="route" type="msr">
                            <div :class="[{'bg-primary':!isCancelled}, {'bg-danger':isCancelled}]"
                                 class="badge ms-2">
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


                            </ul>

                        </div>

                    </Card>

                    <Card>
                        <CardHeader :title="$t('actions')" icon="shopping_cart_checkout" size="sm" type="msr">
                            <div v-if="order.ready_at"
                                 class="ms-2 d-flex align-items-center flex-grow-1 justify-content-end text-muted">
                                <Icon icon="alarm_on" size="18"/>
                                <span class="ms-1-5">
                                    {{ getFormattedDate(order.ready_at) }} -
                                {{ getFormattedTime(order.ready_at) }}</span>
                            </div>
                        </CardHeader>

                        <CardBody>
                            <template v-if="canResubmit">
                                <FormInput v-model="description" :label="$t('description')"
                                           :placeholder="$t('describe_related_order')" name="description" no-label
                                           required
                                           type="text"/>
                            </template>

                            <PageLoading :loading="wallet_loading">
                                <div v-if="order.medias!=null && order.medias.length>0" class="mb-3">
                                    <div v-for="media in order.medias" class="border-1 d-inline-flex">

                                        <div class="d-inline me-1">
                                            <NetworkImage :src="media.media" height="50"/>
                                        </div>

                                    </div>
                                </div>

                                <div v-if="wallet && needToPay" class="text-center mb-3">
                                    <span class="text-muted fw-medium">{{ $t('current_balance') }}</span>
                                    <h3 class="fw-semibold mt-1">{{ getFormattedCurrency(wallet.balance) }}</h3>
                                </div>

                                <div class="d-flex flex-row-reverse">
                                    <LoadingButton v-if="canResubmit" :loading="loading" class="ms-2 " color="primary"
                                                   icon="playlist_add_check" @click="resubmitOrder">
                                        {{ $t('resubmit') }}
                                    </LoadingButton>
                                    <div v-if="canCancel">
                                        <LoadingButton :loading="loading" class="ms-2" color="danger"
                                                       icon="close" @click="cancelOrder">
                                            {{ $t('cancel') }}
                                        </LoadingButton>
                                    </div>
                                    <div v-if="needToPay">
                                        <LoadingButton :loading="loading" class="ms-2 " color="primary" icon="payments"
                                                       @click="payOrder">
                                            {{ $t('pay') }}
                                        </LoadingButton>
                                    </div>
                                    <div v-if="canReview">
                                        <router-link :to="{name:'customer.orders.reviews', params:{id: order.id}}">
                                            <LoadingButton class="ms-2" color="primary"
                                                           icon="hotel_class">
                                                {{ $t('review') }}
                                            </LoadingButton>
                                        </router-link>
                                    </div>
                                </div>

                            </PageLoading>

                        </CardBody>
                    </Card>

                </Col>


            </Row>

        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
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
import {ICustomerWallet} from "@js/types/models/customer";


export default defineComponent({

    components: {
        ...Components, Layout,
        flatPicker
    },
    mixins: [UtilMixin, FormMixin, DiscountTypeMixin,],
    data() {
        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            wallet_loading: false,
            order: {} as IOrder,
            description: '',
            show_all_products: true,
            wallet: null as ICustomerWallet,
            simplebar: null
        }
    },
    computed: {
        breadcrumb_items() {
            return [
                {
                    text: this.$t('orders'),
                    nameRoute: 'seller.orders.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        },
        lastOrderStatus() {
            if (this.order != null) {
                return this.order.statuses[this.order.statuses.length - 1];
            }
        },
        getOrderCharges() {
            return Order.getTotalCharges(this.order);
        },
        canCancel() {
            return Order.canCustomerCancel(this.order);
        },
        needToPay() {
            return Order.needToPay(this.order);
        },
        canReview() {
            return Order.canCustomerReview(this.order);
        },
        canAccept() {
            return Order.canAccept(this.order);
        },
        canResubmit() {

            return this.lastOrderStatus.status === 'reject';
        },
        canReadyForDelivery() {
            return OrderStatus.canAssignForDelivery(this.lastOrderStatus.status) && this.order.delivery_boy_id != null;
        },
        canWaitForPayment() {
            return Order.canWaitForPayment(this.order);
        },
        canAssignForDelivery() {
            return Order.isDelivery(this.order) && OrderStatus.canAssignForDelivery(this.lastOrderStatus.status) && this.order.assign_delivery_boy_id == null;
        },
        canOrderReady() {
            return (Order.isPOS(this.order) || Order.isSelfPickup(this.order)) && OrderStatus.canAssignForDelivery(this.lastOrderStatus.status);
        },
        canOrderDeliver() {
            return (Order.isPOS(this.order)) && OrderStatus.canDeliver(this.lastOrderStatus.status);
        },
        canWaitForDeliveryConfirmation() {
            return OrderStatus.canWaitForDeliveryConfirmation(this.lastOrderStatus.status);
        },
        canWaitForDeliveryBoy() {
            return OrderStatus.canWaitForDelivery(this.lastOrderStatus.status);
        },
        canSetEstimateTime() {
            return OrderStatus.canSetEstimateTime(this.lastOrderStatus.status);
        },
        canWaitForDeliverOrder() {
            return this.lastOrderStatus.status === 'on_the_way';
        },
        isCancelled() {
            return OrderStatus.isCancelled(this.lastOrderStatus.status)
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
        mailToDelivery() {
            let address = this.order.delivery_boy?.email;
            if (address != null) {
                window.open(getSendEmailURl(address), "_blank")
            }
        },
        callToDelivery() {
            let number = this.order.delivery_boy?.mobile_number;
            if (number != null) {
                window.open(getMobileNumberCallURL(number), "_blank")
            }
        },
        chatWithShop() {
            this.$router.push({name: 'customer.chats.index', query: {user_type: 'shop', id: this.order.shop_id}})
        },
        chatWithDeliveryBoy() {
            this.$router.push({
                name: 'customer.chats.index',
                query: {user_type: 'delivery_boy', id: this.order.delivery_boy.id}
            })
        },
        goToDeliveryBoyOnMap() {
            let end = {
                latitude: (this.order.delivery_boy)?.latitude,
                longitude: (this.order.delivery_boy)?.longitude
            }
            let start = {
                latitude: this.order.shop.latitude,
                longitude: this.order.shop.longitude,

            }
            return window.open(getLocationDirectionURL(start, end), "_blank")

        },
        async updateStatus(props: { url: string, }) {
            try {
                const response = await Request.postAuth(props.url, {});
                if (response.success()) {
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
        async cancelOrder() {
            await this.updateStatus({url: customerAPI.orders.cancel(this.id)});
            ToastNotification.success(this.$t('order_cancelled'));
        },
        async payOrder() {
            await this.updateStatus({url: customerAPI.orders.pay(this.id)});
            ToastNotification.success(this.$t('order_is_paid'));
        },
        async resubmitOrder() {
            await this.updateStatus({url: customerAPI.orders.resubmit(this.id)});
            ToastNotification.success(this.$t('order_resubmitted'));
        },
        getItemTotal(orderItem) {
            return OrderItem.getItemTotal(orderItem);
        },
        getAddonTotal(addon) {
            return OrderItemAddon.getAddonTotal(addon);
        },
        getDirectionURL() {
            return getLocationDirectionFromMyLocationURL(this.order.address.latitude, this.order.address.longitude)
        },
        getStatusFromText(status) {
            return OrderStatus.getStatusText(status);
        },
        getPaymentType() {
            return Order.getPaymentTextFromOrder(this.order);
        },
        getPaymentStatus() {
            return Order.getPaymentStatusTextFromOrder(this.order);
        },
        async getOrder() {
            try {
                const response = await Request.getAuth<IData<IOrder>>(customerAPI.orders.show(this.id));
                if (response.success()) {
                    this.order = response.data.data;
                    this.page_loading = false;
                    if (this.needToPay) {
                        this.wallet_loading = true;
                        const response = await Request.getAuth<IData<ICustomerWallet>>(customerAPI.wallets.get);
                        this.wallet = response.data.data;
                        this.wallet_loading = false;
                    }
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
        }
    },
    async mounted() {
        this.setTitle(this.$t('order') + " " +this.id);
        await this.getOrder();
    },
    beforeUnmount() {
        this.simplebar = null;
    }

});

</script>
