<template>
    <Layout>
        <PageHeader :title="$t('order_review')"/>

        <PageLoading :loading="page_loading">
            <Row justify-content="center">
                <Col lg="5">
                    <Card>
                        <CardHeader :title="$t('shop_review')" icon="add_business" type="msr"></CardHeader>
                        <CardBody>
                            <div class="d-flex">
                                <NetworkImage :placeholder-src="shopPortraitPlaceholder" :src="order.shop.cover_image" height="146"
                                              rounded></NetworkImage>
                                <div class="ms-3 flex-grow-1">
                                    <p class="fw-medium font-15 mb-1-5">{{ order.shop.name }}</p>
                                    <StarRating :rating="shop_review.rating" interactive
                                                @rating:changed="(e)=>shop_review.rating=e"></StarRating>
                                    <div class="mb-2"></div>
                                    <FormInputArea v-model="shop_review.review" :label="$t('shop_review')" label=""
                                                   name="shop_review"
                                                   no-spacing rows="3" with-out-label/>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <Button v-if="shop_review.id != null" color="teal" flexed-icon variant="soft"
                                        @click="setShopReview">
                                    <Icon class="me-1-5" icon="upgrade" size="20"></Icon>
                                    {{ $t('update_review') }}
                                </Button>
                                <Button v-else flexed-icon @click="setShopReview">
                                    <Icon class="me-1-5" icon="add" size="20"></Icon>
                                    {{ $t('add_review') }}
                                </Button>
                            </div>
                        </CardBody>
                    </Card>

                    <Card v-if="order.delivery_boy">
                        <CardHeader :title="$t('delivery_review')" icon="local_shipping" type="msr"></CardHeader>
                        <CardBody>
                            <div class="d-flex">
                                <NetworkImage :from-text="order.delivery_boy.first_name + ' ' +order.delivery_boy.last_name" :src="order.delivery_boy.avatar"
                                              size="146"
                                              rounded></NetworkImage>
                                <div class="ms-3 flex-grow-1">
                                    <p class="fw-medium font-15 mb-1-5">
                                        {{ order.delivery_boy.first_name + " " + order.delivery_boy.last_name }}</p>
                                    <StarRating :rating="delivery_review.rating" interactive
                                                @rating:changed="(e)=>delivery_review.rating=e"></StarRating>
                                    <div class="mb-2"></div>
                                    <FormInputArea v-model="delivery_review.review" :label="$t('delivery_review')"
                                                   label=""
                                                   name="delivery_review"
                                                   no-spacing rows="3" with-out-label/>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <Button v-if="delivery_review.id != null" color="teal" flexed-icon variant="soft"
                                        @click="setDeliveryReview">
                                    <Icon class="me-1-5" icon="upgrade" size="20"></Icon>
                                    {{ $t('update_review') }}
                                </Button>
                                <Button v-else flexed-icon @click="setDeliveryReview">
                                    <Icon class="me-1-5" icon="add" size="20"></Icon>
                                    {{ $t('add_review') }}
                                </Button>
                            </div>
                        </CardBody>
                    </Card>
                </Col>
                <Col lg="5">

                    <Card>
                        <CardHeader :title="$t('product_reviews')" icon="hotel_class" type="msr"></CardHeader>

                        <template v-for="(p_review, index) in product_reviews">
                            <hr v-if="index!==0" class="dashed">
                            <CardBody>
                                <div class="d-flex">
                                    <NetworkImage :src="getProductImage(p_review.product)" height="146"
                                                  object-fit="contain" rounded :placeholder-src="productPortraitPlaceholder"/>
                                    <div class="ms-3 flex-grow-1">
                                        <p class="fw-medium font-15 mb-1-5">{{ p_review.product.name }}</p>
                                        <StarRating :rating="p_review.rating" interactive
                                                    @rating:changed="(e)=>p_review.rating=e"></StarRating>
                                        <div class="mb-2"></div>
                                        <FormInputArea v-model="p_review.review" :label="$t('review')" label=""
                                                       name="product_review"
                                                       no-spacing rows="3" with-out-label/>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <Button v-if="p_review.id != null" color="teal" flexed-icon variant="soft"
                                            @click="()=>setProductReview(p_review)">
                                        <Icon class="me-1-5" icon="upgrade" size="20"></Icon>
                                        {{ $t('update_review') }}
                                    </Button>
                                    <Button v-else flexed-icon @click="()=>setProductReview(p_review)">
                                        <Icon class="me-1-5" icon="add" size="20"></Icon>
                                        {{ $t('add_review') }}
                                    </Button>
                                </div>
                                <!--                                <div class="text-center mt-3" @click="setProductReview(p_review)">-->
                                <!--                                    <Button v-if="p_review.id != null" color="teal" variant="soft">-->
                                <!--                                        {{ $t('update_review') }}-->
                                <!--                                    </Button>-->
                                <!--                                    <Button v-else>{{ $t('review') }}</Button>-->
                                <!--                                </div>-->

                            </CardBody>
                        </template>

                    </Card>

                </Col>
            </Row>
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
import QuantityButton from "@js/components/buttons/QuantityButton.vue";
import Button from "@js/components/buttons/Button.vue";
import {IOrder} from "@js/types/models/order";
import Product, {IProduct} from "@js/types/models/product";
import {IDeliveryBoyReview, IProductReview, IShopReview} from "@js/types/models/review";
import StarRating from "@js/components/shared/StarRating.vue";
import FormInputArea from "@js/components/form/FormInputArea.vue";
import {ToastNotification} from "@js/services/toast_notification";
import {array_update_unique} from "@js/shared/array_utils";

export default defineComponent({
    components: {FormInputArea, StarRating, Button, QuantityButton, Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            id: this.$route.params.id,
            order: {} as IOrder,
            product_reviews: [] as IProductReview[],
            product_reviews_cache: [] as IProductReview[],
            delivery_review: {} as IDeliveryBoyReview,
            shop_review: {
                rating: 5
            } as IShopReview,
        };
    },
    methods: {
        async getData() {
            try {
                const response = await Request.getAuth<IData<IOrder>>(customerAPI.orders.show(this.id), {forced: true});
                this.order = response.data.data;

                const DeliveryResponse = await Request.getAuth<IData<IDeliveryBoyReview[]>>(customerAPI.orders.delivery_boy_reviews(this.id), {forced: true});
                if (DeliveryResponse.data.data != null && DeliveryResponse.data.data.length > 0) {
                    this.delivery_review = DeliveryResponse.data.data[0];
                }
                const pShopResponse = await Request.getAuth<IData<IShopReview[]>>(customerAPI.shops.myReview(this.order.shop.id), {forced: true});
                if (pShopResponse.data.data != null && pShopResponse.data.data.length > 0) {
                    this.shop_review = pShopResponse.data.data[0];
                }

                this.product_reviews_cache = (await Request.getAuth<IData<IProductReview[]>>(customerAPI.orders.product_reviews(this.id), {forced: true})).data.data;
                this.setProductReviewCache();
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },

        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },

        setProductReviewCache() {
            let pReviewList: IProductReview[] = [];
            for (let item of this.order.items) {
                let pr = this.product_reviews_cache.find((pr) => pr.product_option_id == item.product_option_id);
                if (pr != null) {
                    pReviewList.push({
                        ...pr,
                        product: item.product
                    });
                } else {
                    pReviewList.push({
                        updated_at: "",
                        id: null,
                        product: item.product,
                        product_id: item.product_id,
                        customer: null,
                        customer_id: -1,
                        order_id: item.order_id,
                        product_option_id: item.product_option_id,
                        rating: 5,
                        review: null,
                        shop_id: 0,
                        updatedAt: '0'

                    })
                }
            }
            this.product_reviews = pReviewList;
        },
        async setShopReview() {
            let already = this.shop_review.id != null;
            const pShopResponse = await Request.postAuth<IData<IShopReview>>(customerAPI.shops.reviews(this.order.shop.id), {
                rating: this.shop_review.rating,
                review: this.shop_review.review
            });
            this.shop_review = pShopResponse.data.data;
            ToastNotification.success(already ? this.$t('shop_review_updated') : this.$t('shop_review_added'));
        },
        async setDeliveryReview() {
            let already = this.delivery_review.id != null;
            const dResponse = await Request.postAuth<IData<IDeliveryBoyReview>>(customerAPI.orders.delivery_boy_reviews(this.order.id), {
                rating: this.delivery_review.rating,
                review: this.delivery_review.review,
                delivery_boy_id: this.order.delivery_boy.id
            });
            this.delivery_review = dResponse.data.data;
            ToastNotification.success(already ? this.$t('delivery_review_updated') : this.$t('delivery_review_added'));
        },
        async setProductReview(p_review: IProductReview) {
            let already = p_review.id != null;
            const dResponse = await Request.postAuth<IData<IDeliveryBoyReview>>(customerAPI.orders.product_reviews(this.order.id), {
                rating: p_review.rating,
                review: p_review.review,
                product_option_id: p_review.product_option_id
            });
            if (already) {
                this.product_reviews_cache = array_update_unique(this.product_reviews_cache, dResponse.data.data);
            } else {
                this.product_reviews_cache.push(dResponse.data.data)
            }
            this.setProductReviewCache();

            ToastNotification.success(already ? this.$t('product_review_updated') : this.$t('product_review_added'));
        }
    },
    mounted() {
        this.setTitle(this.$t('orders'));
        this.getData();
    }
});

</script>

<style scoped>

</style>
