<template>
    <Layout>
        <PageHeader :title="$t('my_cart')"/>

        <PageLoading :loading="page_loading">
            <Row v-if="shop_carts.length>0">
                <Col v-for="shop_cart in shop_carts" lg="6" xl="4">
                    <Card class="shadow-none">
                        <CardBody>
                            <div class="d-flex justify-content-between mb-1-5">
                                <router-link :to="{name: 'customer.shops.show', params: {id: shop_cart.shop.id}}">
                                    <h5 class="fw-medium my-0 text-teal">{{ shop_cart.shop.name }}</h5>
                                </router-link>
                                <Button class="p-0" color="danger" variant="text" @click="deleteShopCart(shop_cart)">
                                    <span class="font-13">{{ $t('clear_cart') }}</span>
                                </Button>
                            </div>
                            <div v-for="cart in shop_cart.carts"
                                 class="border border-dashed d-flex p-2 rounded-lg mb-2 align-items-center">
                                <NetworkImage :src="getProductImage(cart.product)" object-fit="contain" rounded
                                              :placeholder-src="productPortraitPlaceholder"
                                              size="64"/>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between ">
                                        <router-link :to="{name:'customer.products.show', params:{id: cart.product.id}}"
                                                     class="nav-link">
                                            <h5 class="fw-medium flex-grow-1 my-0  max-lines">
                                                {{ cart.product.name }}
                                            </h5>
                                        </router-link>
                                        <h5 class="fw-semibold ms-2 my-0 text-nowrap">
                                            {{ getFormattedCurrency(calculateCartTotal(cart)) }}
                                        </h5>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1-5 align-items-center">
                                        <Button class="py-0-5 px-1" color="success" variant="soft"
                                                @click="editCart(cart)">
                                            <Icon icon="edit" size="14"></Icon>
                                        </Button>
                                        <p class="mb-0" v-if="cart.product_option.option_value">{{ cart.product.unit_title }} :
                                            {{ cart.product_option.option_value }}</p>
                                        <QuantityButton v-model="cart.quantity" :on-change="(qty)=>updateQty(cart, qty)"
                                                        size="sm"
                                                        name="quantity"/>
                                    </div>
                                </div>
                            </div>

                            <template v-if="needOrderAmount(shop_cart)>0">
                                <span
                                    class="text-danger font-13">* {{ $t('you_need_more') }} {{ getFormattedCurrency(needOrderAmount(shop_cart)) }} {{ $t('products_to_checkout') }}</span>
                                <div class="text-center mt-2">
                                    <router-link :to="{name: 'customer.shops.show', params: {id: shop_cart.shop.id}}">
                                        <Button color="secondary">
                                            <Icon class="me-1-5" icon="add_shopping_cart" size="18"></Icon>
                                            {{
                                                $t('shopping_more')
                                            }}
                                        </Button>
                                    </router-link>
                                </div>
                            </template>
                            <div v-else class="text-center">

                                <Button @click="checkout(shop_cart)">
                                    <Icon class="me-1-5" icon="shopping_cart_checkout" size="18"></Icon>
                                    {{
                                        $t('checkout')
                                    }}
                                </Button>
                            </div>
                        </CardBody>
                    </Card>
                </Col>
            </Row>
            <div v-else class="text-center">
                <h3 class="fw-medium mt-5 mb-3">{{ $t('your_cart_is_empty') }}</h3>

                <Button color="teal" variant="soft" @click="goToShopping" >
                    <Icon icon="production_quantity_limits" class="me-1-5" size="20"></Icon>
                    {{ $t('go_to_shopping') }}
                </Button>
            </div>


            <VModal v-model="show_cart_edit_modal" close-btn lg overflow-hidden>
                <EditCart :cart="selected_cart" :on-delete-cart="onDeleteCart"></EditCart>
            </VModal>
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
import {Cart, ICart, IShopCart, ShopCart} from "@js/types/models/cart";
import QuantityButton from "@js/components/buttons/QuantityButton.vue";
import Button from "@js/components/buttons/Button.vue";
import {replaceCartToState, updateCartToState} from "@js/services/state/state_helper";
import Product, {IProduct} from "@js/types/models/product";
import {array_update_unique} from "@js/shared/array_utils";
import EditCart from "@js/pages/customer/carts/EditCart.vue";
import VModal from "@js/components/VModal.vue";
import {ToastNotification} from "@js/services/toast_notification";
import {useCustomerDataStore} from "@js/services/state/states";

export default defineComponent({
    components: {VModal, EditCart, Button, QuantityButton, Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            shop_carts: [] as IShopCart[],
            carts: [] as ICart[],
            show_cart_edit_modal: false,
            selected_cart: null
        };
    },
    methods: {
        needOrderAmount(shopCart: IShopCart) {
            let total = 0;
            for (const cart of shopCart.carts) {
                total += Cart.calculateTotal(cart);
            }
            console.info(shopCart.shop.min_order_amount - total)
            return shopCart.shop.min_order_amount - total;
        },
        async getCarts() {
            try {
                const response = await Request.getAuth<IData<ICart[]>>(customerAPI.carts.get, {forced: true});
                this.carts = response.data.data;
                this.shop_carts = ShopCart.fromCarts(this.carts);
                replaceCartToState(this.carts);
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        editCart(cart: ICart) {
            this.selected_cart = cart;
            this.show_cart_edit_modal = true;
        },
        onDeleteCart() {
            ToastNotification.success(this.$t('cart_removed'));
            const store = useCustomerDataStore();
            store.removeCart(this.selected_cart.id);
            this.show_cart_edit_modal = false;
            this.shop_carts = ShopCart.fromCarts(store.carts);

        },
        async deleteShopCart(shop_cart: IShopCart) {
            if (this.loading)
                return;
            const store = useCustomerDataStore();

            this.loading = true;
            for (let cart of shop_cart.carts) {
                try {
                    const response = await Request.deleteAuth(customerAPI.carts.delete(cart.id));
                    if (response.success()) {
                        store.removeCart(cart.id);
                    }
                } catch (e) {
                    handleException(e);
                } finally {

                }
            }
            this.loading = false;
            ToastNotification.success(this.$t('carts_removed'));
            this.shop_carts = ShopCart.fromCarts(store.carts);

        },
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        calculateCartTotal(cart: ICart) {
            return Cart.calculateTotal(cart);
        },
        checkout(shop_cart: IShopCart) {
            const store = useCustomerDataStore();
            store.updateCheckoutShopId(shop_cart.shop.id);
            this.$router.push({name: 'customer.checkout'});
        },
        async updateQty(cart: ICart, qty: number) {
            const response = await Request.patchAuth<IData<ICart>>(customerAPI.carts.update(cart.id), {
                quantity: qty
            });
            this.carts = array_update_unique(this.carts, response.data.data);
            this.shop_carts = ShopCart.fromCarts(this.carts);
            updateCartToState(response.data.data);
        },
        goToShopping() {
            this.$router.push({name: 'customer.search'});
        }
    },
    mounted() {
        this.setTitle(this.$t('my_cart'));
        this.getCarts();
    }
});

</script>

<style scoped>

</style>
