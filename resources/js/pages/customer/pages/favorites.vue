<template>
    <Layout>
        <PageHeader :title="$t('favorites')">
            <ul class="nav nav-tabs nav-tabs-custom-icons color-teal border-none" role="tablist">
                <li class="nav-item active" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#favorite_products">
                        <Icon class="icon" icon="dns" size="16"></Icon>
                        <span class="ms-1-5 title">{{ $t('products') }}</span>
                    </a>
                </li>
                <li class="nav-item active" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#favorite_shops">
                        <Icon class="icon" icon="storefront" size="16"></Icon>
                        <span class="ms-1-5 title">{{ $t('shops') }}</span>
                    </a>
                </li>
            </ul>
        </PageHeader>

        <PageLoading :loading="page_loading">
            <div class="tab-content mt-2">
                <div id="favorite_products"
                     class="tab-pane active" role="tabpanel">
                    <Row v-if="products.length>0" >
                        <Col v-for="product in products" :key="product.id" xxl="2" xl="3" lg="4" sm="6">
                            <router-link
                                :to="{name:'customer.products.show', params: {id: product.id}}"
                                class="nav-link">
                                <Card>
                                    <CardBody>
                                        <div class="text-center">
                                            <NetworkImage
                                                :src="getProductImage(product)"
                                                class="img-fluid"
                                                height="120"
                                                :placeholder-src="productLandscapePlaceholder"
                                                object-fit="contain" rounded/>
                                        </div>


                                        <h6
                                            class="text-muted mt-2-5 mb-1 fw-medium">
                                            {{ product.sub_category?.name }}
                                        </h6>
                                        <h5
                                            class="max-lines mb-0 fw-medium mt-0">{{ product.name }}</h5>
                                        <div class="d-flex align-items-center justify-content-between mt-0-5">
                                            <div>
                                                <StarRating :rating="product.rating" :size="12"/>
                                                <span class="font-13"> ({{ getRatingText(product) }})</span>
                                            </div>
                                            <span class="font-15 fw-medium">
                                                {{ getFormattedCurrency(getMinPrice(product)) }}
                                            </span>

                                        </div>


                                    </CardBody>
                                </Card>
                            </router-link>


                        </Col>
                    </Row>
                    <div v-else class="text-center mt-5">
                        <h4 class="fw-medium mb-3">{{ $t('there_is_no_product_in_favorites') }}</h4>
                        <Button color="teal" variant="soft" @click="goToShopping">
                            <Icon class="me-1-5" icon="explore" size="20"></Icon>
                            {{ $t('explore_more') }}
                        </Button>
                    </div>

                </div>

                <div id="favorite_shops"
                     class="tab-pane" role="tabpanel">
                    <Row v-if="shops.length>0" >
                        <Col v-for="shop in shops" :key="shop.id" xxl="2"  xl="3" lg="4" sm="6">
                            <router-link :to="{name:'customer.shops.show', params: {id: shop.id}}"
                                         class="nav-link">
                                <Card class="overflow-hidden">
                                    <NetworkImage
                                        :src="shop.cover_image"
                                        class="img-fluid"
                                        :placeholder-src="shopLandscapePlaceholder"
                                        height="220"/>
                                    <CardBody class="p-2-5">
                                        <h5 class="fw-medium mt-0 mb-0-5">{{ shop.name }}</h5>
                                        <div>
                                            <StarRating :rating="shop.rating" :size="14" star-spacing="1"/>
                                            <span class="ms-1 font-13">
                                                        ({{ getRatingText(shop) }} -
                                                        {{ shop.ratings_count }} {{ this.$t('reviews') }})
                                                    </span>
                                        </div>
                                    </CardBody>
                                </Card>
                            </router-link>
                        </Col>
                    </Row>
                    <div v-else class="text-center mt-5">
                        <h4 class="fw-medium mb-3">{{ $t('there_is_no_shop_in_favorites') }}</h4>
                        <Button color="teal" variant="soft" @click="goToShopping">
                            <Icon class="me-1-5" icon="explore" size="20"></Icon>
                            {{ $t('explore_more') }}
                        </Button>
                    </div>
                </div>
            </div>


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
import Product, {IProduct} from "@js/types/models/product";
import {IShop} from "@js/types/models/shop";
import StarRating from "@js/components/shared/StarRating.vue";

export default defineComponent({
    components: {StarRating, Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,

            products: [] as IProduct[],
            shops: [] as IShop[],
        };
    },
    computed: {},
    methods: {
        async getFavorites() {
            this.page_loading = true;
            try {
                const response = await Request.getAuth<IData<any>>(customerAPI.favorites.get, {forced: true});
                this.products = response.data['products'];
                this.shops = response.data['shops'];
            } catch (error) {
                handleException(error);
            }
            this.page_loading = false;
        },
        getMinPrice(product: IProduct) {
            return Product.getMinPrice(product);
        },
        getRatingText(item: IProduct | IShop): string {
            return item.rating.toFixed(2);
        },
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        goToShopping() {
            this.$router.push({name: 'customer.search'});
        }
    },
    mounted() {
        this.setTitle(this.$t('favorites'));
    },
    created() {
        this.getFavorites();
    }
});

</script>

<style scoped>

</style>
