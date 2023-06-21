<template>

    <div>
        <LinearLoading :loading="loading"></LinearLoading>
        <div v-if="cart" class="d-lg-flex p-4 align-items-start">

            <NetworkImage :placeholder-src="productPortraitPlaceholder" :src="getProductImageUrl()" object-fit="contain" rounded
                          size="300"/>
            <div class="ms-lg-4 ms-0 mt-3 mt-lg-0 flex-grow-1">
                <VItem v-if="cart.product_option.discount!==0"
                       as="badge"
                       color="success"
                       display="inline-block"
                       soft>
                            <span class="font-13">
                                {{
                                    cart.product_option.discount_type !== 'percent' ? getFormattedCurrency(cart.product_option.discount) : cart.product_option.discount + "%"
                                }}
                                 OFF
                            </span>
                </VItem>
                <VItem v-else as="badge" color="danger" display="inline" soft>
                        <span
                            class="font-13">{{ $t('no_offer') }}</span>
                </VItem>

                <h4 class="fw-semibold mt-1">{{ cart.product.name }}</h4>
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <StarRating :rating="cart.product.rating" size="16"/>
                        <span class="text-muted ms-1">
                        ({{ getRatingText() }} - {{ cart.product.ratings_count }} {{ $t('reviews') }})
                    </span>
                    </div>
                    <div>
                      <span
                          class="font-17 fw-semibold">{{
                              getFormattedCurrency(cart.product_option.calculated_price)
                          }}</span>
                        <span v-if="cart.product_option.discount!==0"
                              class="ms-1 font-13 text-muted fw-medium text-decoration-line-through">{{
                                getFormattedCurrency(cart.product_option.price)
                            }}</span>
                    </div>
                </div>
                <p class="mt-2 mb-1 fw-medium" v-if="cart.product.unit_title">{{ cart.product.unit_title ?? $t('options') }} : <span class="font-13">{{cart.product_option.option_value??"-"}}</span></p>


                <div class=" mt-2-5 d-flex align-items-center">
                    <QuantityButton v-model="cart.quantity"
                                    :on-change="(quan)=>onChangeCartQuantity(quan)"
                                    name="quantity"></QuantityButton>

                    <Button class="ms-2-5 p-1" color="red" variant="soft" @click="deleteCart()">
                        <Icon icon="delete"></Icon>
                    </Button>
                </div>

                <div v-if="cart.product.addons!=null && cart.product.addons.length>0" class="mt-2-5">
                    <p class="fw-medium">{{ $t('addons') }}</p>
                    <VItem v-for="addon in cart.product.addons" border class="p-2 mb-1-5" rounded>
                        <div class="d-flex align-items-center">
                            <NetworkImage :src="addon.image" size="40" object-fit="contain" :placeholder-src="productPortraitPlaceholder"/>
                            <div class="mx-2 flex-grow-1">
                                <p class="mb-0 fw-medium">{{ addon.name }}</p>
                                <span>{{ getFormattedCurrency(addon.price) }}</span>
                            </div>
                            <Button v-if="!hasAddonAddedInCart(addon)" class="p-1-5" color="teal" flexed-icon
                                    variant="soft" @click="()=>addAddonToCart(addon)">
                                <Icon icon="exposure_plus_1" size="18"></Icon>
                            </Button>
                            <div v-else class="d-flex">
                                <Button class="me-2-5 p-1" color="orange" flexed-icon variant="soft"
                                        @click="deleteAddonFromCart(getAddonFromCart(addon))">
                                    <Icon icon="delete" size="18"></Icon>
                                </Button>
                                <QuantityButton v-model="getAddonFromCart(addon).quantity"
                                                :on-change="(qty)=>onCartAddonQuantityChange(getAddonFromCart(addon), qty)"
                                                name="addon_quantity" size="sm"/>
                            </div>

                        </div>
                    </VItem>
                </div>
            </div>
        </div>
    </div>


</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent, PropType} from "vue";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {ICart} from "@js/types/models/cart";
import QuantityButton from "@js/components/buttons/QuantityButton.vue";
import Button from "@js/components/buttons/Button.vue";
import Product from "@js/types/models/product";
import StarRating from "@js/components/shared/StarRating.vue";
import {IAddon} from "@js/types/models/addon";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import LinearLoading from "@js/components/LinearLoading.vue";
import {ICartAddon} from "@js/types/models/cart_addon";
import {IData} from "@js/types/models/data";

export default defineComponent({
    components: {LinearLoading, StarRating, Button, QuantityButton, Layout, ...Components},
    mixins: [UtilMixin],
    props: {
        cart: {
            type: null as PropType<ICart>,
            required: true
        },
        onDeleteCart: Function,
    },
    data() {
        return {
            loading: false,
        };
    },
    methods: {
        getProductImageUrl(): string | null {
            return Product.getImageUrl(this.cart.product);
        },
        getRatingText(): string {
            return (this.cart.product.rating).toFixed(1);
        },
        async onChangeCartQuantity(quantity: number) {
            if(this.loading)
                return;
            if (quantity > 0) {
                this.loading = true;
                try {
                    const response = await Request.patchAuth(customerAPI.carts.update(this.cart.id), {
                        quantity: quantity
                    });
                    if (response.success()) {
                        this.cart.quantity = quantity;
                    }
                } catch (e) {
                    handleException(e);
                } finally {
                    this.loading = false;
                }
            }
        },
        async deleteCart() {
            if(this.loading)
                return;
            this.loading = true;
            try {
                const response = await Request.deleteAuth(customerAPI.carts.delete(this.cart.id));
                if (response.success()) {
                    if(this.onDeleteCart){
                        this.onDeleteCart();
                    }
                }
            } catch (e) {
                handleException(e);
            } finally {

                this.loading = false;
            }

        },
        getAddonFromCart(addon: IAddon): ICartAddon|null {

            return this.cart.addons?.find((s_a)=>{

                return s_a.addon.id == addon.id;
            });
        },
        hasAddonAddedInCart(addon: IAddon) {
            return this.getAddonFromCart(addon) != undefined
        },
        async addAddonToCart(addon: IAddon) {
            if (this.loading)
                return;
            this.loading = true;
            try {
                const response = await Request.postAuth<IData<ICartAddon>>(customerAPI.cart_addons.create, {
                    cart_id: this.cart.id,
                    addon_id: addon.id
                });
                if (response.success()) {
                    if (this.cart.addons == null) {
                        this.cart.addons = [];
                    }
                    this.cart.addons.push(response.data.data)
                }
            } catch (e) {
                handleException(e);
            } finally {
                this.loading = false;
            }

        },
        async deleteAddonFromCart(addon: ICartAddon) {
            if (this.loading)
                return;
            this.loading = true;
            try {
                const response = await Request.deleteAuth(customerAPI.cart_addons.delete(addon.id));
                if (response.success()) {
                    this.cart.addons = this.cart.addons?.filter((s_a) => {
                        return addon.id != s_a.id;
                    });
                }
            } catch (e) {
                handleException(e);
            } finally {
                this.loading = false;
            }


        },
        async onCartAddonQuantityChange(addon: ICartAddon, qty: number) {

            // return;
            if (this.loading)
                return;
            this.loading = true;
            try {
                const response = await Request.patchAuth(customerAPI.cart_addons.update(addon.id), {
                    quantity: qty
                });
                if (response.success()) {
                    addon.quantity = qty;
                }
            } catch (e) {
                handleException(e);
            } finally {
                this.loading = false;
            }

        },
    },
    mounted() {

    }
});

</script>

<style scoped>

</style>
