<template>
    <Layout>


        <PageLoading :loading="page_loading">

            <Row justify-content="center">
                <Col xl="9">
                    <div>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <span class="text-primary fw-medium cursor-pointer"
                                      @click="goToCategory(product.category)">{{ product.category.name }}</span>
                            </li>
                            <li class="breadcrumb-item">
                                <span>{{ product.sub_category.name }}</span>
                            </li>
                        </ol>
                    </div>
                    <Row class="mt-4">
                        <Col lg="6" class="mb-3">
                            <div class="d-flex">
                                <SimpleBar style="max-height: 450px; width: 100px">
                                    <div class="d-flex flex-column gap-2">
                                        <div v-for="product_image in product.images">
                                            <div :class="[{'bg-card': selected_image?.id===product_image.id}]"
                                                 class="p-2 rounded-lg cursor-pointer border text-center transition-2"
                                                 @click="selected_image=product_image">
                                                <NetworkImage :placeholder-src="productPortraitPlaceholder"
                                                              :src="product_image.image" height="80"
                                                              object-fit="contain"
                                                              rounded
                                                              width="80"/>
                                            </div>
                                        </div>
                                    </div>
                                </SimpleBar>
                                <div class="ms-3">
                                    <NetworkImage :placeholder-src="productPortraitPlaceholder"
                                                  :src="selected_image?.image" object-fit="contain"
                                                  rounded
                                                  size="400"
                                                  style="max-height: 450px; max-width: 400px;"/>

                                </div>

                            </div>

                        </Col>

                        <Col lg="6" class="px-lg-4 mb-3">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <VItem v-if="selected_option.discount!==0"
                                           as="badge"
                                           color="success"
                                           display="inline-block"
                                           soft><span
                                        class="font-13">{{
                                            selected_option.discount_type === 'percent' ? getPreciseCurrency(selected_option.discount) + '%' : getFormattedCurrency(selected_option.discount)
                                        }} OFF</span>
                                    </VItem>
                                    <VItem v-else as="badge" color="danger" display="inline" soft>
                                        <span
                                            class="font-13 text-uppercase">{{ $t('no_offer') }}</span>
                                    </VItem>
                                    <h3 class="fw-semibold mt-1 mb-0">{{ product.name }}</h3>
                                </div>
                                <div>
                                    <template v-if="product.food_type">
                                        <NetworkImage :src="'/assets/images/food/'+product.food_type+'.png'"
                                                      size="24"></NetworkImage>
                                    </template>
                                    <template v-else-if="product.need_prescription">
                                        <VItem as="badge" class="px-1 fw-medium" color="purple" rounded soft>
                                            <span class="font-13">{{ $t('need_prescription') }}</span>
                                        </VItem>
                                    </template>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-1">


                                <router-link v-if="shop" :to="{name:'customer.shops.show', params:{id: shop.id}}">
                                    <Button color="teal" flexed-icon variant="text">
                                        <Icon class="me-1-5" icon="storefront" size="20"></Icon>
                                        <span class="fw-medium">{{ shop.name }}</span>
                                    </Button>
                                </router-link>

                                <div class="d-flex align-items-baseline">
                                     <span
                                         class="font-20 fw-semibold mb-0">{{
                                             getFormattedCurrency(selected_option.calculated_price)
                                         }}</span>
                                    <span v-if="selected_option.discount!==0"
                                          class="ms-1 font-13 text-muted fw-medium text-decoration-line-through mb-0">{{
                                            getFormattedCurrency(selected_option.price, {currencySpace: false})
                                        }}</span>
                                </div>
                            </div>

                            <div v-if="product.options.length>1 ||
                            (product.options.length===1 && (product.unit_title!=null || product.options[0].option_value!=null))"
                                 class="mt-2">

                                <p class="fw-medium mb-1">{{ product.unit_title ?? $t('options') }}</p>
                                <div class="btn-group">
                                    <Button v-for="option in product.options"
                                            :variant="selected_option && selected_option.id===option.id?'fill':'soft'"
                                            class="fw-medium"
                                            color="teal"
                                            @click="onSelectProductOption(option)">
                                        {{ option.option_value }}
                                    </Button>
                                </div>

                            </div>


                            <div class="mt-3">
                                <div v-if="getCartProduct==null" class="d-flex">
                                    <QuantityButton v-model="quantity" automatic name="quantity"
                                                    size="sm"></QuantityButton>
                                    <Button class="ms-3" color="primary"
                                            flexed-icon @click="addToCart">
                                        <Icon icon="add_shopping_cart" size="18" type="msr"/>
                                        <span class="d-inline-block mb-0 ms-1"
                                              style="min-width: 60px">
                                        {{ getFormattedCurrency(selected_option.calculated_price * quantity) }}
                                        </span>
                                    </Button>
                                </div>

                                <Button v-else color="primary"
                                        flexed-icon @click="editCart">
                                    <Icon icon="shopping_cart_checkout" size="18" type="msr"/>
                                    <span class="ms-2">
                                        <span class="d-inline-block mb-0">
                                        {{ $t('edit_in_cart') }}</span></span>
                                </Button>

                            </div>

                            <template v-if="variant.products!=null && variant.products.length>1">
                                <div class="d-flex mt-4">
                                    <div class="pre-header"></div>
                                    <p class="fw-medium d-inline">{{ $t('variants') }}</p>
                                </div>


                                <div class="d-flex gap-2-5 flex-wrap">

                                    <Card v-for="v_product in variant.products"
                                          :class="[{'border-teal  border ': v_product.id===product.id}]"
                                          class="border rounded p-2-5 mb-0 cursor-pointer shadow-none"
                                          @click="onSelectProduct(v_product)">
                                        <div class="">
                                            <p :class="[{'text-teal': v_product.id===product.id}]"
                                               class="fw-medium mb-0">{{
                                                    v_product.name
                                                }}</p>
                                            <div>
                                            <span class="text-muted font-13 ">{{
                                                    product.unit_title ?? $t('options')
                                                }} - </span>
                                                <span
                                                    class="font-12 text-muted">{{
                                                        v_product.options?.map((op) => op.option_value).join(", ")
                                                    }}</span>
                                            </div>
                                        </div>
                                    </Card>

                                </div>


                            </template>
                        </Col>
                    </Row>


                </Col>

                <Col xl="3" class="mb-3">
                    <PageLoading :loading="review_loading">
                        <div class="d-flex ">

                            <Icon :color="isFavorite?'danger':null" :fill="isFavorite"
                                  class="mt-1-5 me-2 cursor-pointer"
                                  icon="favorite" @click="onFavoriteToggle"
                            ></Icon>
                            <div class="flex-grow-1">
                                <ul class="nav nav-tabs nav-tabs-custom-icons border-none" role="tablist">

                                    <li class="nav-item" role="presentation">
                                        <a aria-expanded="false" aria-selected="false" class="nav-link px-3"
                                           data-bs-toggle="tab" href="#description-content" role="tab" tabindex="-1">
                                            <Icon class="icon" icon="description" size="15"></Icon>
                                            <span class="ms-1-5">{{ $t('description') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item " role="presentation">
                                        <a aria-expanded="false" aria-selected="false" class="nav-link px-3 active"
                                           data-bs-toggle="tab" href="#review-content" role="tab" tabindex="-1">
                                            <Icon class="icon" icon="star" size="15"></Icon>
                                            <span class="ms-1-5">{{ $t('reviews') }}</span>
                                        </a>
                                    </li>
                                </ul>

                            </div>
                        </div>

                        <div class="tab-content mt-2-5">
                            <div id="description-content"
                                 class="tab-pane" role="tabpanel">
                                <HTMLViewer :text="product.description" class="mt-3"
                                            viewer-id="p_description"></HTMLViewer>

                            </div>
                            <div id="review-content"
                                 class="tab-pane active" role="tabpanel">
                                <Card class="border shadow-none">
                                    <CardBody>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex flex-column px-3 align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-medium font-20">
                                                        {{ getPrecise(product.rating) }}
                                                    </span>
                                                    <Icon class="ms-0-5" icon="star" size="16"
                                                          type="feather"></Icon>
                                                </div>
                                                <span class="text-muted mt-0-5">{{ product.ratings_count }} {{
                                                        $t('ratings')
                                                    }}</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <template v-for="key in  Object.keys(this.rating_counts).reverse()">
                                                    <div class="d-flex align-items-center">
                                                        <p class="mb-0 lh-1" style="margin-top: 2px; width: 14px">
                                                            {{ key }}</p>
                                                        <Icon icon="star" size="12"
                                                              type="feather"></Icon>
                                                        <div class="flex-grow-1 mx-1-5">
                                                            <div class=" progress progress-sm progress-rounded">
                                                                <div :class="['bg-star-'+key]"
                                                                     :style="{width: (this.rating_counts[key]*100/this.max_rating)+'%'}"
                                                                     aria-valuemax="100"
                                                                     aria-valuemin="0" aria-valuenow="30"
                                                                     class="progress-bar"
                                                                     role="progressbar"
                                                                ></div>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 text-muted" style=" width: 36px">
                                                            {{ this.rating_counts[key] }}</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </CardBody>

                                </Card>
                                <Card class="shadow-none border">

                                    <CardHeader :title="$t('reviews')" icon="hotel_class"></CardHeader>
                                    <PageLoading :loading="review_loading">
                                        <template v-if="product_reviews==null || product_reviews.length===0">
                                            <p
                                                class="text-center fw-medium p-2 mb-0">{{
                                                    $t('there_is_no_review_yet')
                                                }}</p>
                                        </template>
                                        <template v-else>
                                            <template v-for="(review, index) in product_reviews.slice(0,3)">
                                                <div class="d-flex p-2-5">
                                                    <NetworkImage :src="review.customer?.avatar" circular
                                                                  object-fit="contain" size="42"></NetworkImage>
                                                    <div class="ms-2 flex-grow-1">
                                                        <div class="d-flex justify-content-between">
                                                            <p class="mb-0 fw-medium mt-0">
                                                                {{
                                                                    review.customer?.first_name + " " + review.customer?.last_name
                                                                }}</p>
                                                            <StarRating :rating="review.rating" size="14"></StarRating>
                                                        </div>

                                                        <p v-if="review.review" class="my-0 font-14">{{
                                                                review.review
                                                            }}</p>
                                                    </div>
                                                </div>
                                                <hr v-if="index!==3 && product_reviews.length!==3" class="dashed m-0">
                                            </template>
                                            <div class="text-center py-2">
                                                <Button color="teal" flexed-icon variant="text" @click="showAllReviews">
                                                    {{ $t('view_all') }}
                                                    <Icon class="ms-1" icon="arrow_right_alt" size="18"/>
                                                </Button>
                                            </div>
                                        </template>
                                    </PageLoading>
                                </Card>
                            </div>
                        </div>
                    </PageLoading>
                </Col>

            </Row>

            <Card class="mt-3 shadow-none">
                <CardBody class="mb-0">
                    <PageHeader :title="$t('similar_products')"></PageHeader>


                    <Row v-if="similar_products.length>0">
                        <Col v-for="product in similar_products" :key="product.id" xxl=2  lg="4" sm="6" xs="12">
                            <router-link
                                :to="{name:'customer.products.show', params: {id: product.id, slug: product.slug}}"
                                class="nav-link rounded border border-dashed p-2-5 mb-3">


                                <div class="text-center">
                                    <NetworkImage
                                        :placeholder-src="productLandscapePlaceholder"
                                        :src="getProductImage(product)"
                                        class="img-fluid"
                                        height="120"
                                        object-fit="contain"
                                        rounded/>
                                </div>

                                <h6
                                    class="text-muted mt-2-5 mb-1 fw-medium">{{
                                        product.sub_category?.name
                                    }}</h6>
                                <h5
                                    class="max-lines mb-0 fw-medium mt-0">{{ product.name }}</h5>
                                <div class="d-flex align-items-center justify-content-between mt-0-5">
                                    <div>
                                        <StarRating :rating="product.rating" :size="12"/>
                                        <span class="font-13"> ({{ getRatingText(product) }})</span>
                                    </div>
                                    <span class="font-15 fw-medium"> {{
                                            getFormattedCurrency(getMinPrice(product))
                                        }}</span>

                                </div>
                            </router-link>
                        </Col>
                    </Row>
                </CardBody>
            </Card>


            <VModal v-model="show_cart_edit_modal" close-btn lg overflow-hidden>
                <EditCart :cart="selected_cart" :on-delete-cart="onDeleteCart"></EditCart>
            </VModal>

            <VModal v-model="show_review_modal" close-btn>
                <Card class="shadow-none mb-0">

                    <CardHeader :title="$t(`reviews`)" icon="hotel_class"></CardHeader>
                    <PageLoading :loading="review_loading">
                        <template v-if="product_reviews==null || product_reviews.length===0">
                            <p
                                class="text-center fw-medium p-2 mb-0">{{ $t('there_is_no_review_yet') }}</p>
                        </template>
                        <template v-else>
                            <template v-for="(review, index) in product_reviews">
                                <hr v-if="index!==0" class="dashed m-0">
                                <div class="d-flex p-2-5">
                                    <NetworkImage :src="review.customer?.avatar" circular
                                                  object-fit="contain" size="42"></NetworkImage>
                                    <div class="ms-2 flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <p class="mb-0 mt-0 fw-medium">
                                                {{ review.customer?.first_name + " " + review.customer?.last_name }}
                                            </p>
                                            <StarRating :rating="review.rating"></StarRating>
                                        </div>

                                        <p v-if="review.review" class="my-0 font-14">
                                            {{ review.review }}
                                        </p>
                                    </div>
                                </div>

                            </template>

                        </template>
                    </PageLoading>
                </Card>
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
import Product, {IProduct} from "@js/types/models/product";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IProductVariant} from "@js/types/models/product_variant";
import {IProductOption} from "@js/types/models/product_option";
import QuantityButton from "@js/components/buttons/QuantityButton.vue";

import {IProductImage} from "@js/types/models/product_image";
import {Swiper, SwiperSlide} from 'swiper/vue';
import 'swiper/css';
import "swiper/css/free-mode"
import "swiper/css/zoom"
// import "swiper/css/thumbs"
import {Controller, FreeMode, Navigation, Thumbs, Zoom} from 'swiper';
import {ICart} from "@js/types/models/cart";
import {ToastNotification} from "@js/services/toast_notification";
import {addCartToState} from "@js/services/state/state_helper";
import StarRating from "@js/components/shared/StarRating.vue";
import {IShop} from "@js/types/models/shop";

import HTMLViewer from "@js/components/HTMLViewer.vue";
import {ICategory} from "@js/types/models/category";
import VModal from "@js/components/VModal.vue";
import EditCart from "@js/pages/customer/carts/EditCart.vue";
import {IProductReview} from "@js/types/models/review";
import {SimpleBar} from 'simplebar-vue3';
import {useCustomerDataStore} from "@js/services/state/states";

export default defineComponent({

    components: {
        EditCart,
        VModal,
        SimpleBar,
        HTMLViewer,
        StarRating,
        Swiper,
        SwiperSlide,
        QuantityButton,
        Layout, ...Components
    },
    mixins: [UtilMixin,],
    data() {
        return {
            id: this.$route.params.id,
            slug: this.$route.params.slug,
            page_loading: true,
            loading_similar: true,
            product: {} as IProduct,
            variant: {} as IProductVariant,
            selected_option: {} as IProductOption,
            selected_image: {} as IProductImage,
            quantity: 1,
            modules: [Zoom, FreeMode, Navigation, Controller, Thumbs],
            thumbs_swiper: null,
            image_swiper: null,
            similar_products: [] as IProduct[],
            show_cart_edit_modal: false,
            selected_cart: null as ICart,

            shop: null as IShop,

            review_loading: true,
            show_review_modal: false,
            product_reviews: null as IProductReview[],
            rating_counts: {},
            max_rating: 1,
        };
    },
    watch: {
        '$route.params': {
            // immediate: true,
            handler() {
                if (this.$route?.name === 'customer.products.show') {
                    this.id = this.$route.params.id;
                    if (this.id != null) {
                        this.thumbs_swiper = null;
                        if (this.product.id !== this.id) {
                            this.getProduct();
                            this.getSimilarProducts();
                        }
                    }
                }
            }
        },
    },
    computed: {
        isFavorite() {
            return this.product?.customer_favorite_products != null && this.product?.customer_favorite_products.length > 0;
        },
        getCartProduct() {
            let store = useCustomerDataStore();
            for (let cart of (store.carts ?? [])) {
                if (cart.product_option.id == this.selected_option.id) {
                    return cart;
                }
            }
            return null;
        },
    },
    methods: {
        showAllReviews() {
            this.show_review_modal = true;
        }, getMinPrice(product: IProduct) {
            return Product.getMinPrice(product);
        },
        async getProduct() {
            this.page_loading = true;
            try {
                const response = await Request.getAuth<IData<IProduct>>(customerAPI.products.show(this.id));
                this.product = response.data.data;
                this.shop = this.product.shop;
                this.setTitle(this.product.name);

                this.variant = this.product.variant;
                this.selected_option = this.product.options[0];
                this.selected_image = Product.getImage(this.product);
                this.onSelectProduct(this.product);
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            } finally {

            }

            //TODO: If you need to make slug more proper
            // history.replaceState({
            //     id: 'JavaScript Tutorials',
            //     source: 'web'
            // }, 'All JavaScript Tutorials', 'http://192.168.1.72:8000/user/products/jockey-mens-regular/100001/');
        },
        async getProductReview() {
            this.review_loading = true;
            try {
                const response = await Request.getAuth<IData<IProductReview[]>>(customerAPI.products.reviews((this.product ?? this).id));
                this.product_reviews = response.data.data;
                this.rating_counts = {
                    5: 0,
                    4: 0,
                    3: 0,
                    2: 0,
                    1: 0
                };
                if (this.product_reviews != null && this.product_reviews.length > 0) {
                    this.max_rating = 1;
                    for (let productReview of this.product_reviews) {
                        this.rating_counts[productReview.rating] += 1;
                    }
                    for (let key of Object.keys(this.rating_counts).reverse()) {
                        if (this.rating_counts[key] > this.max_rating) {
                            this.max_rating = this.rating_counts[key];
                        }
                    }
                }
            } catch (error) {
                handleException(error);
            } finally {
                this.review_loading = false;
            }

        },
        async getSimilarProducts() {
            this.loading_similar = true;
            try {
                const response = await Request.getAuth<IData<IProduct[]>>(Request.addParameters(customerAPI.products.similar(this.id), {addModule: true}), {forced: true});
                this.similar_products = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.loading_similar = false;
            }
        },
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        getFloorRating() {
            return Math.floor(this.product.ratings_total / this.product.ratings_count);
        },
        getRating(item: IShop | IProduct) {
            return item.rating;
        },
        goToCategory(category: ICategory) {
            this.$router.push({name: 'customer.search', query: {categories: category.id}});
        },
        editCart() {
            let cart = this.getCartProduct;
            if (cart != null) {
                this.selected_cart = cart;
                this.show_cart_edit_modal = true;
            }
        },
        onDeleteCart() {
            ToastNotification.success(this.$t('product_removed_from_cart'));
            const store = useCustomerDataStore();
            store.removeCart(this.selected_cart.id);
            this.show_cart_edit_modal = false;
        },
        async addToCart() {
            try {
                const response = await Request.postAuth<IData<ICart>>(customerAPI.carts.create, {
                    quantity: this.quantity,
                    product_option_id: this.selected_option.id
                });
                if (response.success()) {
                    let cart = response.data.data;
                    addCartToState(response.data.data);
                    if (this.product.addons != null && this.product.addons.length > 0) {
                        this.selected_cart = cart;
                        this.show_cart_edit_modal = true;
                    }
                    ToastNotification.show({
                        message: this.$t('added_to_cart'),
                        icon: 'shopping_cart_checkout',
                        action: {
                            text: this.$t('show_cart'),
                            callback: () => this.$router.push({name: 'customer.carts.index'})
                        }

                    });
                }
            } catch (e) {
                handleException(e);
            }
        },
        onSelectProduct(product: IProduct) {
            this.product = product;

            this.selected_option = this.product.options[0];
            this.selected_image = Product.getImage(this.product);
            this.getProductReview();
            // this.thumbs_swiper.destroy(true);
            // this.thumbs_swiper.init();
            // this.setThumbsSwiper(this.thumbs_swiper);
            // if(this.product.images && this.product.images.length>3){
            //
            // }else{
            //
            // }

            this.quantity = 1;
        },
        onSelectProductOption(option: IProductOption) {
            this.selected_option = option;
        },
        getRatingText(item: IProduct | IShop): string {
            return this.getRating(item).toFixed(1);
        },
        async onFavoriteToggle() {
            try {
                const response = await Request.postAuth<any>(customerAPI.favorite_products.create, {
                    'product_id': this.product.id
                });
                if (response.success()) {
                    this.product = Product.toggleFavorite(this.product);
                    if (this.isFavorite) {
                        ToastNotification.success(this.$t('product_added_to_favorite'));
                    } else {
                        ToastNotification.success(this.$t('product_removed_from_favorite'));
                    }
                }
            } catch (error) {
                handleException(error);
            }
        },


    },
    async mounted() {
        // this.getProductReview().then();

        await this.getProduct();
        await this.getSimilarProducts();

    }
});

</script>

<style scoped>

.pre-header {
    margin-bottom: 0;
    display: inline-block !important;
    width: 6px;
    margin-right: 12px;
    border-radius: 1px;
    height: 20px;
    background: #fd7e144d;
}

</style>
