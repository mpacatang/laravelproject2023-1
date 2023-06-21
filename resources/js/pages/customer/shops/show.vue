<template>
    <Layout>

        <PageLoading :loading="page_loading">
            <Row>

                <Col lg="4" xl="3">
                    <Card style="position: sticky; top: 6rem; overflow: hidden">

                        <NetworkImage :src="shop.cover_image" height="250" :placeholder-src="shopLandscapePlaceholder"/>

                        <VItem :color="shop.open?'primary':'danger'"
                               class="px-2 py-0-5 font-14 fw-medium position-absolute top-0 end-0 m-2 shadow-lg"
                               rounded>
                            {{ shop.open ? $t('open') : $t('closed') }}
                        </VItem>
                        <div class="p-3 py-2-5">
                            <div class="d-flex align-items-start">
                                <NetworkImage :src="shop.logo" circular height="48" :placeholder-src="shopPortraitPlaceholder"/>
                                <div class="ms-2 flex-grow-1">
                                    <h4 class="fw-medium mt-0 mb-1">{{ shop.name }}</h4>
                                    <div>
                                        <StarRating :rating="shop.rating"></StarRating>
                                        <span> ({{ getRatingText(shop) }} - {{
                                                shop.ratings_count
                                            }} {{ this.$t('reviews') }})</span>
                                    </div>
                                </div>
                                <Icon :color="isFavorite?'danger':null" :fill="isFavorite"
                                      class="cursor-pointer"
                                      icon="favorite" @click="onFavoriteToggle"
                                ></Icon>

                            </div>


                            <div class="d-flex mt-2-5 justify-content-end align-items-center">


                                <Button class="py-1 px-2" color="teal" flexed-icon variant="soft"
                                        @click="showTimeModal">
                                    <Icon class="me-1-5" icon="schedule" size="18"/>
                                    {{ $t('time') }}
                                </Button>
                                <Button class="py-1-5 px-2 ms-2" color="success" flexed-icon variant="soft"
                                        @click="showReviewModal">
                                    <Icon icon="hotel_class" size="20"/>
                                </Button>

                            </div>
                        </div>

                        <hr class="dashed m-0"/>
                        <div class="p-3 py-2-5">
                            <HTMLViewer :text="shop.description" viewer-id="p_description"></HTMLViewer>
                        </div>

                        <hr class="dashed m-0"/>
                        <div class="p-3 py-2-5">

                            <div class="d-flex align-items-center">
                                <Icon icon="location_on" msr weight="300"></Icon>
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0">{{ shop.address }}</p>
                                    <span class="text-muted">{{ shop.city }} - {{ shop.pincode }}</span>
                                </div>
                            </div>
                            <div v-if="shop.delivery_time_type?.length>0" class="d-flex mt-3 align-items-center">
                                <Icon icon="alarm_on" msr weight="300"></Icon>
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 fw-medium">{{ shop.min_delivery_time }} -
                                        {{ shop.max_delivery_time }} {{ $t(shop.delivery_time_type) }}</p>
                                    <span class="text-muted">{{ $t('delivery_time') }}</span>
                                </div>
                            </div>
                        </div>



                    </Card>


                </Col>


                <Col lg="8" xl="9">
                    <div class="d-flex flex-wrap gap-2">
                        <VItem :border="selected_categories.length!==0"
                               :color="selected_categories.length===0?'teal':null"
                               align-items="center"
                               class="px-3 py-1 cursor-pointer" display="inline-flex"
                               radius="40" @click="selectAllCategory">
                            <span class="font-14" style="margin-top: 1px">{{ $t('all') }}</span>
                        </VItem>
                        <VItem v-for="sub_category in sub_categories"
                               :class="[{'border-teal': isSelectedCategory(sub_category)}]"
                               :text-color="isSelectedCategory(sub_category)?'teal':null"
                               :soft="isSelectedCategory(sub_category)" align-items="center"
                               class="px-2-5 py-1 cursor-pointer"
                               border
                               display="inline-flex" radius="40" @click="selectCategory(sub_category)">
                            <Icon v-if="isSelectedCategory(sub_category)" class="me-1" fill icon="fiber_manual_record"
                                  size="10"/>
                            <span :class="[{'': isSelectedCategory(sub_category)}]" class="font-14"
                                  style="margin-top: 1px">{{ sub_category.name }}</span>
                        </VItem>
                    </div>

                    <template v-if="filterProducts.length===0">
                        <h3 class="text-center mt-5 fw-medium">{{ $t('there_is_no_product') }}</h3>
                    </template>

                    <Row v-else class="mt-3">
                        <Col v-for="product in filterProducts" :key="product.id" xl="3" lg="6" sm="4">
                            <router-link
                                :to="{name:'customer.products.show', params: {id: product.id, slug: product.slug}}"
                                class="nav-link">
                                <Card>
                                    <CardBody>
                                        <div class="text-center">
                                            <NetworkImage
                                                :src="getProductImage(product)"
                                                class="img-fluid"
                                                :placeholder-src="productLandscapePlaceholder"
                                                height="80" rounded/>
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

                                    </CardBody>
                                </Card>
                            </router-link>
                        </Col>
                    </Row>
                </Col>


            </Row>

            <VModal v-model="show_time_modal" close-btn lg>
                <Card class="mb-0">
                    <CardHeader :title="$t('shop_timing')">
                    </CardHeader>

                    <CardBody>
                        <table class="table table-bordered table-centered mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width: 120px">{{ $t('day') }}</th>
                                <th>{{ $t('time') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="day in allDays">
                                <td>
                                    <span class="fw-semibold">{{ $t(day) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <VItem v-for="time in getDayTimings(day)" text-color="success" flexed-icon
                                                border class="p-1" rounded>
                                                <span class="font-15">
                                                    {{ getFormattedTimeFromTime(time.open_at, {second: false}) }} -
                                                {{ getFormattedTimeFromTime(time.close_at, {second: false}) }}
                                                </span>
                                        </VItem>
                                    </div>
                                </td>
                            </tr>
                            </tbody>

                        </table>

                    </CardBody>
                </Card>
            </VModal>

            <VModal v-model="show_review_modal" close-btn>
                <Card class="shadow-none mb-0">

                    <CardHeader :title="$t(`reviews`)" icon="hotel_class"></CardHeader>
                    <PageLoading :loading="review_loading">
                        <template v-if="shop_reviews==null || shop_reviews.length===0">
                            <p class="text-center fw-medium p-2 mb-0">{{ $t('there_is_no_review_yet') }}</p>
                        </template>
                        <template v-else>
                            <template v-for="(review, index) in shop_reviews">
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
import QuantityButton from "@js/components/buttons/QuantityButton.vue";
import {Swiper, SwiperSlide} from 'swiper/vue';
import 'swiper/css';
import "swiper/css/free-mode"
// import "swiper/css/bundle"
import "swiper/css/thumbs"
import "swiper/css/zoom"
import {IShop, IShopTime, Shop} from "@js/types/models/shop";
import {ISubCategory} from "@js/types/models/category";
import StarRating from "@js/components/shared/StarRating.vue";
import Badge from "@js/components/Badge.vue";
import {array_add_unique, array_contains_unique, array_toggle_unique} from "@js/shared/array_utils";
import VModal from "@js/components/VModal.vue";
import HTMLViewer from "@js/components/HTMLViewer.vue";
import {SimpleBar} from 'simplebar-vue3';
import {IProductReview, IShopReview} from "@js/types/models/review";
import {ToastNotification} from "@js/services/toast_notification";


export default defineComponent({

    components: {
        HTMLViewer,
        VModal,
        Badge,
        StarRating,
        Swiper,
        SwiperSlide,
        QuantityButton,
        Layout,
        SimpleBar,
        ...Components
    },
    mixins: [UtilMixin,],
    data() {
        return {
            id: this.$route.params.id,
            products: [] as IProduct[],
            page_loading: true,
            shop: {} as IShop,
            sub_categories: [] as ISubCategory[],
            selected_categories: [] as ISubCategory[],
            show_time_modal: false,
            show_review_modal: false,
            shop_reviews: null as IShopReview[],
            review_loading: false
        };
    },
    methods: {
        getDayTimings(day: string): IShopTime[] {
            let timings = [];
            if (this.shop.timings != null) {
                this.shop.timings.forEach(function (time) {
                    if (time.day === day) {
                        timings.push(time);
                    }
                });
            }
            return timings;
        },
        getMinPrice(product: IProduct) {
            return Product.getMinPrice(product);
        },
        showTimeModal() {
            this.show_time_modal = true;
        },

        showReviewModal() {
            if (this.shop_reviews == null) {
                this.getReviews();
            }
            this.show_review_modal = true;
        },
        async getReviews() {
            this.review_loading = true;
            try {
                const response = await Request.getAuth<IData<IProductReview[]>>(customerAPI.shops.reviews(this.id), {forced: true});
                this.shop_reviews = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.review_loading = false;
            }

        },
        async getShop() {
            try {
                const response = await Request.getAuth<IData<IShop>>(customerAPI.shops.show(this.id), {forced: true});
                this.shop = response.data.data;
                this.setTitle(this.shop.name);
                this.products = this.shop.products;
                this.extractSubCategories(this.products);
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        async onFavoriteToggle() {
            try {
                const response = await Request.postAuth<any>(customerAPI.favorite_shops.create,
                    {'shop_id': this.id});
                if (response.success()) {
                    this.shop = Shop.toggleFavorite(this.shop);
                    if (this.isFavorite) {
                        ToastNotification.success(this.$t('shop_added_to_favorite'));
                    } else {
                        ToastNotification.success(this.$t('shop_removed_from_favorite'));
                    }
                }
            } catch (error) {
                handleException(error);
            }
        },
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        getFloorRating(product: IProduct) {
            return Math.floor(product.ratings_total / product.ratings_count);
        },
        getRatingText(item: IProduct | IShop) {
            return item.rating.toFixed(1);
        },
        getRating(item: IProduct | IShop) {
            if (item.ratings_count == 0)
                return 0;
            return (item.ratings_total / item.ratings_count);
        },
        extractSubCategories(products: IProduct[]) {
            let categories: ISubCategory[] = [];
            for (const product of products) {
                categories = array_add_unique(categories, product.sub_category);
            }
            this.sub_categories = categories;
        },
        isSelectedCategory(sub_category: ISubCategory) {
            return array_contains_unique(this.selected_categories, sub_category);
        },
        selectCategory(sub_category: ISubCategory) {
            this.selected_categories = array_toggle_unique(this.selected_categories, sub_category);
        },
        selectAllCategory() {
            this.selected_categories = [];
        },

    },
    computed: {
        isFavorite() {
            return this.shop?.customer_favorite_shops != null && this.shop?.customer_favorite_shops.length > 0;
        },
        allDays(): string[] {
            return Shop.getDays();
        },
        filterProducts() {
            if (this.selected_categories.length == 0) {
                return this.products;
            } else {
                const self = this;
                return this.products.filter(function (product) {
                    return array_contains_unique(self.selected_categories, product.sub_category);
                })
            }
        }
    },
    mounted() {

        this.getShop();
    }
});

</script>

<style scoped>
.swiper-slide-thumb-active div {
    border: 1px solid green;
}


</style>
