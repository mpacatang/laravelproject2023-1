<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('POS')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xxl="9">
                    <Card>
                        <CardBody>
                            <Row align-items="center" justify-content="between">
                                <Col xl="3" xs="6">
                                    <form class="app-search border rounded" v-on:submit.prevent="onSearchDone">
                                        <div class="app-search-box dropdown">
                                            <FormInput v-model="search" :placeholder="$t('search')"
                                                       class="bg-transparent"
                                                       name="search"
                                                       no-spacing noLabel>
                                            </FormInput>
                                            <Icon class="search-icon" color="muted" icon="search" size="xs"></Icon>
                                            <Icon :class="[{'d-none': !show_search_cancel}]"
                                                  class="search-widget-icon-close"
                                                  color="muted" enterkeyhint="done" icon="cancel" size="xs"
                                                  @click="onSearchCancel"></Icon>
                                        </div>
                                    </form>
                                </Col>
                                <Col auto>
                                    <Button v-if="show_clear_filter" class="p-1-5" color="orange" flexed-icon
                                            radius="100" variant="soft" @click="clearFilter">
                                        <Icon icon="filter_alt_off" size="20"></Icon>
                                    </Button>
                                </Col>

                                <Col auto class="text-end">

                                    <div class="btn-group">
                                        <Button
                                            :color="!show_product_image?'cyan':'light'"
                                            @click="show_product_image=false">
                                            <Icon icon="image_not_supported"></Icon>
                                        </Button>

                                        <Button
                                            :color="show_product_image?'cyan':'light'"
                                            @click="show_product_image=true">
                                            <Icon icon="image"></Icon>
                                        </Button>
                                    </div>
                                </Col>
                            </Row>
                            <Row class="mt-3">
                                <Col lg="12">
                                    <div class="d-flex transparent-scrollbar justify-content-xl-center" style=" overflow-x: scroll;">

                                        <template v-for="category in categories">
                                            <VItem align-items="center" class="cursor-pointer" display="flex"
                                                   flex="column"
                                                   style="min-width: 100px" @click="onSelectCategory(category)">
                                                <div :class="[{'show-check': selected_category===category}]"
                                                     class="category-check">
                                                    <NetworkImage :size="60" :src="category.image" circular :placeholder-src="categoryPlaceholder"/>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold text-center">{{ category.name }}</p>
                                                </div>
                                            </VItem>
                                        </template>
                                    </div>
                                </Col>


                            </Row>


                        </CardBody>
                    </Card>

                    <template v-if="products.length>0">
                        <Row>
                            <Col v-for="product in filter_products"  lg="4" sm="6" xl="3" xs="12">
                                <Card class="shadow-none border cursor-pointer" @click="onSelectProduct(product)">
                                    <CardBody class="p-0">
                                        <div class="rounded" style="overflow: hidden">
                                            <div v-if="show_product_image" class="text-center mt-1-5">
                                                <NetworkImage :height="200"
                                                              :src="getProductImageUrl(product)"
                                                              :placeholder-src="productLandscapePlaceholder"
                                                              class="w-100" object-fit="contain"
                                                              rounded/>
                                            </div>
                                            <div class="p-2">
                                                <div class="d-flex align-items-end">
                                                    <div class="flex-grow-1">
                                                        <p class="fw-semibold font-14 mb-0 max-lines">
                                                            {{ product.name }}</p>
                                                        <p class="text-muted font-13 mb-0 fw-medium">
                                                            {{ (product.options?.length ?? 1) }}
                                                            {{ $t('option_available') }}</p>
                                                    </div>
                                                    <Button class="p-1" color="orange" flexed-icon variant="soft">
                                                        <!--                                                            <span class="fw-medium font-13" style="letter-spacing: 0.5px">ADD</span>-->
                                                        <Icon icon="add" size="16"></Icon>
                                                    </Button>
                                                </div>

                                            </div>
                                        </div>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </template>
                    <template v-else>
                        <p class="text-center fw-medium text-danger">
                            {{ $t('there_is_no_product_in_this_filter') }}</p>
                    </template>
                </Col>
                <Col xxl="3">


                    <Card>

                        <CardHeader :title="$t('items')" icon="category"/>

                        <div id="cart_items" style="max-height: 380px">
                            <div v-for="(cart, index) in carts">

                                <div class="d-flex align-items-center p-2-5">
                                    <NetworkImage :src="getProductImageUrl(cart.product)"
                                                  object-fit="contain" rounded size="60"/>
                                    <div class="flex-grow-1 ms-2">
                                        <div class="d-flex">
                                            <div class="flex-grow-1 ">
                                                <span class="fw-medium max-lines">{{ cart.product.name }}</span>

                                            </div>
                                            <p v-if="cart.product_option.option_value"
                                               class="ms-1 text-nowrap mb-0">{{ cart.product.unit_title }} :
                                                {{ cart.product_option.option_value }}</p>
                                        </div>
                                        <div class="d-flex align-items-center mt-1-5 justify-content-between">
                                            <div class="d-flex  align-items-center">
                                                <Icon class="cursor-pointer me-3" color="success" icon="edit"
                                                      size="19" @click="onSelectCart(cart)"></Icon>


                                                <QuantityButton v-model="cart.quantity"
                                                                :on-change="(quan)=>onChangeCartQuantity(cart, quan)"
                                                                name="item_quantity"
                                                                size="sm"></QuantityButton>

                                                <p class="fw-medium ms-2 mb-0">
                                                    {{
                                                        getFormattedCurrency(cart.product_option.calculated_price * cart.quantity)
                                                    }}
                                                </p>
                                            </div>
                                            <Icon class="cursor-pointer" color="red" icon="delete" size="20"
                                                  @click="deleteCart(cart)"></Icon>
                                        </div>
                                    </div>
                                </div>
                                <hr v-if="index!==carts.length-1" class="dashed m-0">
                            </div>

                        </div>

                    </Card>

                    <Card>

                        <CardHeader :title="$t('order')" class="py-2" icon="shopping_bag">
                            <div class="float-end">
                                <Button class="px-2 py-1" color="brown" flexed-icon variant="soft"
                                        @click="showing_order_information_modal=true">
                                    <Icon icon="edit_note" size="19"></Icon>
                                    <span class="ms-1-5">{{ $t('edit') }}</span>
                                </Button>
                            </div>
                        </CardHeader>


                        <CardBody>

                            <div class="d-flex justify-content-between">
                                <span class="font-14 fw-medium">{{ $t('order_amount') }}</span>
                                <span class="font-14">{{ getFormattedCurrency(getItemTotal) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="font-14 fw-medium">{{ $t('addon_amount') }}</span>
                                <span class="font-14">+ {{ getFormattedCurrency(getAddonTotal) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="font-14 fw-medium">{{ $t('tax') }}</span>
                                <span class="font-14">+ {{ getFormattedCurrency(getTax) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mt-1">
                                <span class="font-14 fw-medium">{{ $t('discount') }}</span>
                                <span class="font-14">
                                            - {{ getFormattedCurrency(getDiscount) }}</span>
                            </div>


                            <hr class="w-50 ms-auto my-2 dashed">

                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold font-15">{{ $t('total') }}</span>
                                <span class="fw-semibold font-15">{{ getFormattedCurrency(getTotal) }}</span>
                            </div>

                        </CardBody>
                    </Card>

                    <Card>
                        <CardHeader :title="$t('customer')" class="py-2"
                                    icon="shopping_cart_checkout">
                            <div class="float-end">
                                <Button class="px-2 py-1 ms-2" color="indigo" flexed-icon variant="soft"
                                        @click="showing_customer_modal=true">
                                    <Icon icon="person_add" size="19"></Icon>
                                    <span class="ms-1-5">{{ $t('create_new') }}</span>
                                </Button>
                            </div>
                        </CardHeader>

                        <CardBody>
                            <FormSelect :data="customers"
                                        :errors="errors"
                                        :helper="customer_select_helper"
                                        :onSelected="onSelectCustomer"
                                        :placeholder="$t('select_customer')"
                                        :selected="selected_customer"
                                        name="customer_id"
                                        no-label/>


                            <div class="d-flex align-items-center justify-content-between">
                                <FormSwitch v-model="is_paid" :label="$t('paid')"
                                            name="is_paid" no-spacing></FormSwitch>

                                <div class="btn-group ms-3">
                                    <button
                                        :class="[{'btn-soft-teal': payment_type!=='cash'}, {'btn-teal': payment_type==='cash'}]"
                                        class="btn fw-medium" type="button"
                                        @click="payment_type='cash'">
                                        {{ $t('cash') }}
                                    </button>
                                    <button
                                        :class="[{'btn-soft-teal': payment_type!=='card'}, {'btn-teal': payment_type==='card'}, ]"
                                        class="btn fw-medium" type="button"
                                        @click="payment_type='card'">
                                        {{ $t('card') }}
                                    </button>
                                </div>
                            </div>

                            <template v-if="payment_type==='cash'">
                                <Row class="d-flex mt-3 align-items-center">
                                    <Col class="fw-medium" lg="6">{{ $t('paid_amount') }}</Col>
                                    <Col lg="6">
                                        <FormInput v-model="paid_amount" :errors="errors" name="paid_amount"
                                                   no-label no-spacing show-currency>

                                        </FormInput>
                                    </Col>
                                </Row>

                                <div class="d-flex mt-2 align-items-center">
                                        <span class="text-nowrap flex-grow-1  fw-medium">{{
                                                $t('change_amount')
                                            }}</span>
                                    <div class="ms-2">
                                        <span>{{ getFormattedCurrency(getChangeAmount) }}</span>
                                    </div>
                                </div>
                            </template>


                        </CardBody>
                    </Card>

                    <div class="text-center mt-3">
                        <LoadingButton :loading="loading" @click="placeOrder">
                            {{ $t('place_order') }}
                        </LoadingButton>
                    </div>

                </Col>

            </Row>

            <VModal v-model="showing_product_modal" close-btn lg>
                <div v-if="selected_product" class="d-lg-flex d-grid p-4 align-items-start">
                    <div >
                        <NetworkImage :src="getProductImageUrl(selected_product)" object-fit="contain" rounded
                                      size="280"/>
                        <div class="mt-2 text-center">
                            <template v-if="selected_product.food_type">
                                <NetworkImage :src="'/assets/images/food/'+selected_product.food_type+'.png'"
                                              rounded
                                              size="24"></NetworkImage>
                            </template>
                            <template v-else-if="selected_product.need_prescription">
                                <VItem as="badge" class="px-1 fw-medium" color="purple" display="inline" rounded
                                       soft>
                                    <span class="font-13">{{ $t('need_prescription') }}</span>
                                </VItem>
                            </template>
                        </div>
                    </div>

                    <div class="ms-lg-4 ms-0 flex-grow-1">
                        <VItem v-if="selected_option.discount!==0"
                               as="badge"
                               color="success"
                               display="inline-block"
                               soft>
                            <span class="font-13">
                               <template v-if="selected_option.discount_type === 'amount'">
                                    {{ getFormattedCurrency(selected_option.discount) }}
                                </template>
                                <template v-else>
                                    {{ selected_option.discount }} %
                                </template> OFF
                            </span>
                        </VItem>
                        <VItem v-else as="badge" color="danger" display="inline" soft>
                        <span
                            class="font-13">{{ $t('no_offer') }}</span>
                        </VItem>

                        <h4 class="fw-semibold mt-1">{{ selected_product.name }}</h4>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <StarRating :rating="selected_product.rating" size="16"/>
                                <span class="text-muted ms-1">
                                        ({{ getRatingText(selected_product) }} -
                                        {{ selected_product.ratings_count }} {{ $t('reviews') }})</span>
                            </div>
                            <div>
                                  <span
                                      class="font-20 fw-semibold">
                                      {{ getFormattedCurrency(selected_option.calculated_price) }}
                                  </span>
                                <span v-if="selected_option.discount!==0"
                                      class="ms-1 font-16 text-muted fw-medium text-decoration-line-through">
                                      {{ getFormattedCurrency(selected_option.price) }}
                                  </span>
                            </div>
                        </div>
                        <p class="mt-2 mb-1 fw-medium">{{ selected_product.unit_title ?? $t('options') }}</p>
                        <div class="btn-group">
                            <Button v-for="option in selected_product.options"
                                    :variant="selected_option && selected_option.id===option.id?'fill':'soft'"
                                    class="fw-medium"
                                    color="teal"
                                    @click="onSelectProductOption(option)">
                                {{ option.option_value ?? '-' }}
                            </Button>
                        </div>


                        <div class="mt-3 d-flex align-items-center">
                            <QuantityButton v-model="quantity" :on-change="onQuantityChange"
                                            name="quantity" size="sm"></QuantityButton>
                            <Button class="ms-3" flexed-icon
                                    radius="md" @click="addToCart">
                                <Icon icon="add_shopping_cart" size="18" type="msr"/>
                                <span class="ms-2">
                                        {{ $t('add_to_cart') }} -
                                        {{ getFormattedCurrency(selected_option.calculated_price * quantity) }}</span>
                            </Button>
                        </div>

                        <div v-if="selected_product.addons!=null && selected_product.addons.length>0" class="mt-2-5">
                            <p class="fw-medium">{{ $t('addons') }}</p>
                            <VItem v-for="addon in selected_product.addons" border class="px-2 py-1 mb-1-5" rounded>
                                <div class="d-flex align-items-center">
                                    <NetworkImage :src="addon.image" size="36"/>
                                    <div class="mx-2-5 flex-grow-1">
                                        <p class="mb-0 fw-medium">{{ addon.name }}</p>
                                        <span>{{ getFormattedCurrency(addon.price) }}</span>
                                    </div>
                                    <Button v-if="!hasAddonAdded(addon)" class="p-1-5" color="teal" flexed-icon
                                            variant="soft" @click="()=>addAddon(addon)">
                                        <Icon icon="exposure_plus_1" size="18"></Icon>
                                    </Button>
                                    <div v-else class="d-flex align-items-center">
                                        <Icon class="me-1-5 cursor-pointer" color="red" icon="delete"
                                              size="22" @click="deleteAddon(addon)"></Icon>
                                        <QuantityButton v-model="getAddonFromSelected(addon).quantity"
                                                        :on-change="(qty)=>onAddonQuantityChange(addon, qty)"
                                                        name="addon_quantity" size="sm"/>
                                    </div>

                                </div>
                            </VItem>
                        </div>

                    </div>
                </div>
            </VModal>

            <VModal v-model="showing_cart_modal" close-btn lg>
                <div v-if="selected_cart" class="d-lg-flex d-grid  p-4 align-items-start">
                    <div>
                        <NetworkImage :src="getProductImageUrl(selected_product)" object-fit="contain" rounded
                                      size="300"/>
                        <div class="mt-2">
                            <template v-if="selected_product.food_type">
                                <NetworkImage :src="'/assets/images/food/'+selected_product.food_type+'.png'"
                                              size="24"></NetworkImage>
                            </template>
                            <template v-else-if="selected_product.need_prescription">
                                <VItem as="badge" class="px-1 fw-medium" color="purple" display="inline" rounded
                                       soft>
                                    <span class="font-13">{{ $t('need_prescription') }}</span>
                                </VItem>
                            </template>
                        </div>
                    </div>
                    <div class="ms-lg-4 ms-0 flex-grow-1">
                        <VItem v-if="selected_cart.product_option.discount!==0"
                               as="badge"
                               color="success"
                               display="inline-block"
                               soft>
                            <span class="font-13">
                                <template v-if="selected_cart.product_option.discount_type === 'amount'">
                                    {{ getFormattedCurrency(selected_cart.product_option.discount) }}
                                </template>
                                <template v-else>
                                    {{ selected_cart.product_option.discount }} %
                                </template> OFF
                            </span>
                        </VItem>
                        <VItem v-else as="badge" color="danger" display="inline" soft>
                        <span
                            class="font-13">{{ $t('no_offer') }}</span>
                        </VItem>

                        <h4 class="fw-semibold mt-1">{{ selected_cart.product.name }}</h4>
                        <div>
                            <StarRating :rating="selected_cart.product.rating" size="16"/>
                            <span class="text-muted">&nbsp; ({{
                                    getRatingText(selected_cart.product)
                                }} - {{ selected_cart.product.ratings_count }} {{
                                    $t('reviews')
                                }})</span>
                        </div>
                        <div class="mt-2-5">
                            <span
                                class="font-20 fw-semibold">{{
                                    getFormattedCurrency(selected_cart.product_option.calculated_price)
                                }}</span>
                            <span v-if="selected_option.discount!==0"
                                  class="ms-1 font-16 text-muted fw-medium text-decoration-line-through">{{
                                    getFormattedCurrency(selected_cart.product_option.price)
                                }}</span>
                        </div>

                        <div class="mt-2-5">
                            <span class="me-2">{{
                                    selected_cart.product.unit_title ?? $t('options')
                                }} : {{ selected_cart.product_option.option_value ?? "-" }}</span>
                        </div>


                        <div class=" mt-2-5 d-flex align-items-center">
                            <QuantityButton v-model="selected_cart.quantity"
                                            :on-change="(quan)=>onChangeCartQuantity(selected_cart, quan)"
                                            name="quantity" size="sm"></QuantityButton>

                            <Button class="ms-2-5 p-1" color="red" variant="soft" @click="deleteCart(selected_cart)">
                                <Icon icon="delete"></Icon>
                            </Button>
                        </div>

                        <div v-if="selected_cart.product.addons!=null && selected_cart.product.addons.length>0"
                             class="mt-2-5">
                            <p class="fw-medium">{{ $t('addons') }}</p>
                            <VItem v-for="addon in selected_cart.product.addons" border class="p-2 mb-1-5" rounded>
                                <div class="d-flex align-items-center">
                                    <NetworkImage :src="addon.image" size="40"/>
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
                                                @click="deleteAddonFromCart(addon)">
                                            <Icon icon="delete" size="18"></Icon>
                                        </Button>
                                        <QuantityButton v-model="getAddonFromCart(addon).quantity"
                                                        :on-change="(qty)=>onCartAddonQuantityChange(addon, qty)"
                                                        name="addon_quantity" size="sm"/>
                                    </div>

                                </div>
                            </VItem>
                        </div>

                    </div>
                </div>
            </VModal>

            <VModal v-model="showing_order_information_modal" close-btn>
                <Card class="mb-0">
                    <CardHeader :title="$t('order_customize')" icon="tune"></CardHeader>
                    <CardBody class="pb-0">
                        <Row>
                            <Col lg="6">
                                <FormInput v-model="shop.tax" :errors="errors"
                                           :label="$t('tax')"
                                           min="0" type="number"
                                           :placeholder="$t('enter_a_tax')" :prefix-or-suffix="shop.tax_type"
                                           name="tax"></FormInput>
                            </Col>

                            <Col lg="6">
                                <FormSelect :data="charge_types" :helper="charge_type_helper"
                                            :label="$t('tax_type')"
                                            :onSelected="(value)=>shop.tax_type=value"
                                            :placeholder="$t('tax_type')"
                                            :selected="shop.tax_type"/>

                            </Col>

                        </Row>
                        <Row>
                            <Col lg="6">
                                <FormInput v-model="custom_discount.discount" :errors="errors"
                                           :label="$t('discount')"
                                           min="0" type="number"
                                           :placeholder="$t('enter_a_discount')"
                                           :prefix-or-suffix="custom_discount.discount_type"
                                           name="discount"></FormInput>
                            </Col>

                            <Col lg="6">
                                <FormSelect :data="charge_types" :helper="charge_type_helper"
                                            :label="$t('discount_type')"
                                            :onSelected="(value)=>custom_discount.discount_type=value"
                                            :selected="custom_discount.discount_type"/>

                            </Col>

                        </Row>
                    </CardBody>

                </Card>
            </VModal>

            <VModal v-model="showing_customer_modal" close-btn lg>
                <Card class="mb-0">
                    <CardHeader :title="$t('create_customer')" icon="person_add">

                    </CardHeader>
                    <CardBody>
                        <Row>
                            <Col lg="6">
                                <FormInput v-model="new_customer.first_name" :errors="errors"
                                           :label="$t('first_name')" name="first_name"
                                           required/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="new_customer.last_name" :errors="errors"
                                           :label="$t('last_name')" name="last_name"
                                           required/>
                            </Col>

                            <Col lg="6">
                                <FormInput v-model="new_customer.mobile_number" :errors="errors"
                                           :label="$t('mobile_number')"
                                           name="mobile_number" required
                                           type="tel">
                                    <template #prefix>
                                        <Icon icon="phone" size="18"></Icon>
                                    </template>
                                </FormInput>

                            </Col>
                            <Col lg="6">
                                <FormInput v-model="new_customer.email" :errors="errors" :label="$t('email')"
                                           name="email" required type="email">
                                    <template #prefix>
                                        <Icon icon="mail" size="18"></Icon>
                                    </template>
                                </FormInput>
                            </Col>

                            <div class="text-end">
                                <CreateButton :loading="loading" @click="createCustomer"/>
                            </div>
                        </Row>


                    </CardBody>

                </Card>
            </VModal>

        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/seller/layouts/Layout.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IProductVariant} from "@js/types/models/product_variant";
import {IProductOption} from "@js/types/models/product_option";
import {ICart} from "@js/types/models/cart";
import SimpleBar from 'simplebar';
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {Customer, ICustomer} from "@js/types/models/customer";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import {ToastNotification} from "@js/services/toast_notification";
import {IOrder} from "@js/types/models/order";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import VModal from "@js/components/VModal.vue";
import Product, {IProduct} from "@js/types/models/product";
import {IBreadcrumb} from "@js/types/models/models";
import StarRating from "@js/components/shared/StarRating.vue";
import {IShop} from "@js/types/models/shop";
import QuantityButton from "@js/components/buttons/QuantityButton.vue";
import {BusinessSetting} from "@js/types/models/business_setting";
import {ChargesTypes} from "@js/types/custom_types";
import Response from "@js/services/api/response";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {IOrderPaymentTypes} from "@js/types/models/order_payment";
import {IAddon} from "@js/types/models/addon";
import {deepCopy} from "@js/shared/utils";
import {ICartAddon} from "@js/types/models/cart_addon";
import {ICategory} from "@js/types/models/category";
import {SimpleBar as SComp} from 'simplebar-vue3';
import CreateButton from "@js/components/buttons/CreateButton.vue";
import {defineComponent} from 'vue';

export default defineComponent({
    components: {
        CreateButton,
        QuantityButton,
        StarRating,
        VModal,
        SComp,
        ...Components, Layout,
    },
    mixins: [FormMixin, UtilMixin, DiscountTypeMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            search: '',
            show_search_cancel: false,
            show_clear_filter: false,
            cart_item_simplebar: null as SimpleBar,

            selected_category: null as ICategory,
            categories: [] as ICategory[],
            products: [] as IProduct[],
            filter_products: [] as IProduct[],
            customers: [] as ICustomer[],

            showing_product_modal: false,
            showing_cart_modal: false,
            showing_customer_modal: false,
            showing_order_information_modal: false,


            selected_product: null as IProduct,
            selected_option: {} as IProductOption,
            quantity: 1,
            selected_addons: [] as {
                addon: IAddon,
                quantity: number
            }[],

            selected_cart: null as ICart,


            shop: null as IShop,
            coupon_discount: 0,
            custom_discount: {
                discount: 0,
                discount_type: 'percent' as ChargesTypes
            },
            new_customer: {} as ICustomer,


            carts: [] as ICart[],
            selected_customer: null,
            is_paid: false,
            paid_amount: 0,
            payment_type: 'cash' as IOrderPaymentTypes,
            show_product_image: true
        }
    },
    watch: {
        search(newVal: string) {
            this.show_search_cancel = newVal != null && newVal?.length != 0;
            this.filter();
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('pos'),
                    active: true,
                },
            ];
        },
        customer_select_helper: Customer.select_helper,
        getItemTotal() {
            let total = 0;
            this.carts.forEach(function (cart) {
                total += cart.quantity * (cart.product_option.calculated_price)
            });
            return this.getPrecise(total);
        },
        getAddonTotal() {
            let total = 0;
            this.carts.forEach(function (cart) {
                if (cart.addons != null) {
                    for (let c_addon of cart.addons) {
                        total += c_addon.addon.price * c_addon.quantity
                    }
                }
            });
            return this.getPrecise(total);
        },
        getTax() {
            let amount = this.getItemTotal + this.getAddonTotal;
            if (this.shop != null) {
                return BusinessSetting.calculateChargeFromAmountAndType(amount, this.shop.tax, this.shop.tax_type);
            }
            return 0;

        },
        getDiscount() {
            let amount = this.getItemTotal + this.getAddonTotal + this.getTax;
            return BusinessSetting.calculateChargeFromAmountAndType(amount, this.custom_discount.discount, this.custom_discount.discount_type);
        },
        getTotal() {
            let total = this.getItemTotal + this.getAddonTotal + this.getTax - this.getDiscount;
            this.paid_amount = this.getPrecise(total);
            return total;
        },
        getChangeAmount() {
            if (this.carts.length == 0)
                return 0;
            return this.paid_amount - this.getTotal
        }

    },
    methods: {
        onSearchDone() {
            this.filter();
        },
        onSearchCancel() {
            this.search = '';
            this.onSearchDone();
        },
        onSelectCategory(category: ICategory) {
            this.selected_category = this.selected_category?.id != category.id ? category : null;
            this.filter();
        },
        clearFilter() {
            this.search = '';
            this.selected_category = null;
            this.filter();
            this.show_clear_filter = false;
        },
        filter() {
            let products = [];
            let search = this.search.toLowerCase().trim();
            for (let product of this.products) {
                if (search.length > 0 && !product.name.toLowerCase().includes(search)) {
                    continue;
                }
                if (this.selected_category != null && product.category_id != this.selected_category.id) {
                    continue;
                }
                products.push(product);
            }
            this.filter_products = products;
            this.show_clear_filter = products.length != this.products.length;
        },
        getRatingText(item: IProduct | IShop): string {
            return (item.rating).toFixed(1);
        },
        onSelectCart(cart: ICart) {
            this.selected_cart = cart;
            this.showing_cart_modal = true;
        },
        onSelectProduct(product: IProduct) {
            this.selected_product = product;
            this.selected_option = this.selected_product.options[0];
            this.showing_product_modal = true;
            this.quantity = 1;
        },

        onSelectProductOption(option: IProductOption) {
            this.selected_option = option;
            this.selected_addons = [];
        },

        getAddonFromSelected(addon: IAddon) {
            return this.selected_addons.find((s_a) => s_a.addon.id == addon.id);
        },
        hasAddonAdded(addon: IAddon) {
            return this.getAddonFromSelected(addon) != undefined
        },
        getAddonFromCart(addon: IAddon) {
            return this.selected_cart?.addons?.find((s_a) => s_a.addon.id == addon.id);
        },
        hasAddonAddedInCart(addon: IAddon) {
            return this.getAddonFromCart(addon) != undefined
        },
        addAddon(addon: IAddon) {
            this.selected_addons.push({
                addon: addon,
                quantity: 1
            })
        },
        deleteAddon(addon: IAddon) {
            this.selected_addons = this.selected_addons.filter((s_a) => {
                return addon.id != s_a.addon.id;
            });
        },
        addAddonToCart(addon: IAddon) {
            if (this.selected_cart.addons == null) {
                this.selected_cart.addons = [];
            }
            this.selected_cart.addons.push({
                addon: addon,
                quantity: 1
            })
        },
        deleteAddonFromCart(addon: IAddon) {
            this.selected_cart.addons = this.selected_cart.addons?.filter((s_a) => {
                return addon.id != s_a.addon.id;
            });
        },
        onAddonQuantityChange(addon: IAddon, qty: number) {
            this.selected_addons.find((s_a) => s_a.addon.id == addon.id).quantity = qty;
        },
        onCartAddonQuantityChange(addon: IAddon, qty: number) {
            this.selected_cart.addons.find((s_a) => s_a.addon.id == addon.id).quantity = qty;
        },
        onSelectCustomer(id: number) {
            this.selected_customer = id;
        },
        getProductImageUrl(product): string | null {
            return Product.getImageUrl(product);
        },
        onQuantityChange(quan) {
            if (quan > 0) {
                this.quantity = quan;
            }
        },
        onChangeCartQuantity(cart: ICart, quantity: number) {
            if (quantity > 0) {
                cart.quantity = quantity;
            }
        },

        addToCart() {
            const self = this;
            this.showing_product_modal = false;
            for (const cart of this.carts) {
                if (cart.product_option.id === self.selected_option.id) {
                    ToastNotification.error(this.$t('already_added'));
                    return;
                }
            }

            let cartAddons: ICartAddon[] = [];
            for (let selectedAddon of this.selected_addons) {
                cartAddons.push({
                    addon: selectedAddon.addon, addon_id: 0, cart_id: 0, id: 0,
                    quantity: selectedAddon.quantity
                });
            }

            this.carts.push({
                product: this.selected_product,
                product_option: this.selected_option,
                quantity: this.quantity,
                addons: deepCopy(cartAddons)
            });

            this.selected_addons = [];

            if (this.cart_item_simplebar == null) {
                let cartItemContainer = document.getElementById('cart_items');
                this.cart_item_simplebar = new SimpleBar(cartItemContainer);
            }
            this.cart_item_simplebar.recalculate();
            this.simplebar_scroll(this.cart_item_simplebar, true);
        },
        deleteCart(cart: ICart) {
            this.carts = this.carts.filter(function (item) {
                return item !== cart
            })
        },
        async createCustomer() {
            this.loading = true;
            this.clearAllError();
            try {
                const response = await Request.postAuth<IData<ICustomer>>(sellerAPI.customers.create, {
                    ...this.new_customer
                });
                if (response.success()) {
                    let customer = response.data.data;
                    ToastNotification.success(this.$t('customer_created'));
                    this.customers.push(customer);
                    this.selected_customer = customer.id;
                    this.showing_customer_modal = false;
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            } finally {
                this.loading = false;
            }
        },
        async placeOrder() {
            if (this.carts.length == 0) {
                ToastNotification.error(this.$t('add_item_to_cart'));
                return;
            }
            let items = [];
            this.carts.forEach(function (cart) {
                let addons = null;
                if (cart.addons != null) {
                    addons = [];
                    for (let addon of cart.addons) {
                        addons.push({
                            addon_id: addon.addon.id,
                            quantity: addon.quantity
                        })
                    }
                }
                items.push({
                    product_option_id: cart.product_option.id,
                    quantity: cart.quantity,
                    addons: addons
                })
            });
            let body = {
                'items': items,
                'customer_id': this.selected_customer,
                'paid': this.is_paid,
                'order_amount': this.getItemTotal + this.getAddonTotal,
                'tax': this.getTax,
                'discount_amount': this.getDiscount,
                'total': this.getTotal,
                'payment_type': this.payment_type,
                'paid_amount': this.paid_amount,
                'change_amount': this.getChangeAmount
            };
            try {
                const response = await Request.postAuth<IData<IOrder>>(sellerAPI.pos.create, Request.getClearBody(body));
                if (response.success()) {
                    let order: IOrder = response.data.data;
                    ToastNotification.success(this.$t('order_placed'));
                    this.$router.push({name: 'seller.orders.edit', params: {id: order.id}});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },
        async getData() {
            try {
                const categoryResponse = await Request.getAuth<IData<ICategory[]>>(sellerAPI.categories.get);
                this.categories = categoryResponse.data.data;
                const response = await Request.getAuth<IData<IProductVariant>>(sellerAPI.products.get);
                this.products = response.data.data;
                this.filter_products = response.data.data;
                const shopResponse = await Request.getAuth<IData<ICustomer>>(sellerAPI.shops.show);
                this.shop = shopResponse.data.data;
            } catch (error) {
                handleException(error);
            }
        },
        async getCustomers() {
            try {
                const response = await Request.getAuth<IData<ICustomer>>(sellerAPI.customers.get);
                this.customers = response.data.data;

            } catch (error) {
                handleException(error);
            }
        },
    },

    async mounted() {
        this.setTitle(this.$t('POS'));
        await this.getCustomers();
        await this.getData();
        this.page_loading = false;

    },

});

</script>
<style>


</style>
