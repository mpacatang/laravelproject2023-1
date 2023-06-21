<template>
    <Layout>
        <PageHeader :title="$t('orders')">
            <ul class="nav nav-tabs nav-tabs-custom-icons color-teal border-none" role="tablist">
                <li class="nav-item active" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#active_orders">
                        <Icon class="icon" icon="dns" size="16"></Icon>
                        <span class="ms-1-5 title">{{ $t('active') }}</span>
                    </a>
                </li>
                <li class="nav-item active" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#past_orders">
                        <Icon class="icon" icon="priority" size="16"></Icon>
                        <span class="ms-1-5 title">{{ $t('past') }}</span>
                    </a>
                </li>

            </ul>
        </PageHeader>

        <PageLoading :loading="page_loading">
            <template v-if="orders==null || orders.length===0">
                <div class="text-center mt-5">
                    <h3 class="fw-medium mb-3">{{ $t('their_is_no_any_orders') }}</h3>
                    <Button color="teal" variant="soft" @click="goToShopping">
                        <Icon class="me-1-5" icon="production_quantity_limits" size="20"></Icon>
                        {{ $t('go_to_shopping') }}
                    </Button>
                </div>
            </template>
            <template v-else>
                <div class="tab-content">
                    <div id="active_orders"
                         class="tab-pane active" role="tabpanel">

                        <template v-if="activeOrders.length>0">
                            <Row>
                                <Col v-for="order in activeOrders" lg="6">
                                    <Card>
                                        <CardHeader :title="'# '+order.id">
                                        </CardHeader>
                                        <CardBody>
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="mt-0 fw-semibold">
                                                        {{ getFormattedCurrency(order.total) }}
                                                    </h5>
                                                    <h5 class="text-muted">
                                                        {{ getFormattedDateTime(new Date(order.created_at)) }}
                                                    </h5>
                                                </div>
                                                <div>
                                                    <router-link
                                                        :to="{name: 'customer.orders.show', params: {id: order.id}}">
                                                        <Button color="primary" flexed-icon variant="soft">
                                                            <Icon class="me-1-5" icon="route" size="18"></Icon>
                                                            {{ $t('track') }}
                                                        </Button>
                                                    </router-link>
                                                    <router-link
                                                        :to="{name: 'customer.orders.invoice', params: {id: order.id}}">
                                                        <Button class="ms-2 py-1-5" color="secondary" flexed-icon
                                                                variant="soft">
                                                            <Icon icon="receipt" size="20"/>
                                                        </Button>
                                                    </router-link>
                                                </div>
                                            </div>

                                            <template v-for="item in order.items">
                                                <div class="d-inline me-2-5">
                                                    <NetworkImage :src="getProductImage(item.product)"
                                                                  object-fit="contain"
                                                                  :placeholder-src="productPortraitPlaceholder"
                                                                  rounded
                                                                  show-full-image size="100"/>
                                                </div>
                                            </template>
                                        </CardBody>
                                    </Card>
                                </Col>
                            </Row>
                        </template>
                        <div v-else class="text-center">
                            <h4 class="fw-medium mt-5 mb-3">{{ $t('there_is_no_any_active_order') }}</h4>

                            <Button color="teal" variant="soft" @click="goToShopping">
                                <Icon class="me-1-5" icon="production_quantity_limits" size="20"></Icon>
                                {{ $t('go_to_shopping') }}
                            </Button>
                        </div>

                    </div>

                    <div id="past_orders"
                         class="tab-pane" role="tabpanel">
                        <template v-if="pastOrders.length>0">
                            <Row>
                                <Col v-for="order in pastOrders" lg="6">
                                    <Card>
                                        <CardHeader :title="'# '+order.id">
                                        </CardHeader>
                                        <CardBody>
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="mt-0 fw-semibold">
                                                        {{ getFormattedCurrency(order.total) }}
                                                    </h5>
                                                    <h5 class="text-muted">
                                                        {{ getFormattedDateTime(new Date(order.created_at)) }}
                                                    </h5>
                                                </div>
                                            <div>
                                            <router-link
                                                :to="{name: 'customer.orders.show', params: {id: order.id}}">
                                                <Button color="primary" flexed-icon variant="soft">
                                                    <Icon class="me-1-5" icon="visibility" size="18"></Icon>
                                                    {{ $t('show') }}
                                                </Button>
                                            </router-link>
                                            <router-link
                                                :to="{name: 'customer.orders.invoice', params: {id: order.id}}">
                                                <Button class="ms-2 py-1-5" color="secondary" flexed-icon variant="soft">
                                                    <Icon icon="receipt" size="20"/>
                                                </Button>
                                            </router-link>
                                        </div>
                                        </div>

                                            <template v-for="item in order.items">
                                                <div class="d-inline me-2-5">
                                                    <NetworkImage :src="getProductImage(item.product)"
                                                                  object-fit="contain"
                                                                  :placeholder-src="productPortraitPlaceholder"
                                                                  rounded
                                                                  show-full-image size="100"/>
                                                </div>
                                            </template>
                                        </CardBody>
                                    </Card>
                                </Col>
                            </Row>
                        </template>

                        <div v-else class="text-center">
                            <h4 class="fw-medium mt-5 mb-3">{{ $t('there_is_no_any_active_order') }}</h4>

                            <Button color="teal" variant="soft" @click="goToShopping">
                                <Icon class="me-1-5" icon="production_quantity_limits" size="20"></Icon>
                                {{ $t('go_to_shopping') }}
                            </Button>
                        </div>

                    </div>
                </div>

            </template>

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
import {IShopCart} from "@js/types/models/cart";
import QuantityButton from "@js/components/buttons/QuantityButton.vue";
import Button from "@js/components/buttons/Button.vue";
import {IOrder} from "@js/types/models/order";
import Product, {IProduct} from "@js/types/models/product";
import {useCustomerDataStore} from "@js/services/state/states";

export default defineComponent({
    components: {Button, QuantityButton, Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            orders: [] as IOrder[],

        };
    },
    computed: {
        activeOrders() {
            return this.orders.filter((o) => !o.complete);
        },
        pastOrders() {
            return this.orders.filter((o) => o.complete);
        }
    },
    methods: {
        async getCarts() {
            try {
                const response = await Request.getAuth<IData<IOrder[]>>(customerAPI.orders.get, {forced: true});
                this.orders = response.data.data;
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },


        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        checkout(shop_cart: IShopCart) {
            const store = useCustomerDataStore();
            store.updateCheckoutShopId(shop_cart.shop.id);
            this.$router.push({name: 'customer.checkout'});
        },
        goToShopping() {
            this.$router.push({name: 'customer.search'});
        }
    },
    mounted() {
        this.setTitle(this.$t('orders'));
        this.getCarts();
    }
});

</script>

<style scoped>

</style>
