<template>
    <Layout>


        <div id="search-page" class="d-flex gap-3 align-items-start">
            <div :class="[{'d-block': show_filter_wrapper}]" class="search-filter-wrapper">
                <Card style="min-width: 300px;">
                    <CardHeader :title=" $t('Filters')" class="py-2 ps-3 pe-2" icon="tune" icon-weight="500" size="sm"
                                type="msr">
                        <div class="d-flex justify-content-end">
                            <Button class="px-2 py-1 font-14" color="primary" flexed-icon variant="soft"
                                    @click="applyFilter">
                                <Icon class="me-1" icon="filter_alt" size="16"></Icon>

                                {{ $t('apply') }}
                            </Button>
                            <Button class="px-2 py-1 ms-1-5 font-14" color="secondary" flexed-icon
                                    variant="soft" @click="clearFilter">
                                {{ $t('clear') }}
                            </Button>

                        </div>
                    </CardHeader>

                    <SimpleBar class="pb-3" style="max-height: 85vh">
                        <div id="accordionPanelsStayOpenExample" class="accordion accordion-flush">

                            <div class="accordion-item">
                                <h2 class="accordion-header m-0">
                                    <button class="accordion-button  shadow-none"
                                            data-bs-target="#category_filter" data-bs-toggle="collapse"
                                            type="button">
                                        <span class="fs-12 fw-medium">{{ $t('categories') }}</span>
                                    </button>
                                </h2>
                                <div id="category_filter"
                                     class="accordion-collapse collapse show">
                                    <div class="accordion-body pt-1">
                                        <template v-for="category in categories">
                                            <div class="form-check mb-1">
                                                <input :id="'category_'+category.id"
                                                       :checked="isSelectedCategory(category)"
                                                       class="form-check-input cursor-pointer"
                                                       type="checkbox" @change.prevent="toggleCategory(category)">
                                                <label :for="'category_'+category.id"
                                                       class="form-check-label ms-1 fw-medium"
                                                       style="cursor:pointer;">{{ category.name }}</label>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header m-0">
                                    <button class="accordion-button shadow-none"
                                            data-bs-target="#rating_filter" data-bs-toggle="collapse"
                                            type="button">
                                        <span class="fs-12 fw-medium">{{ $t('ratings') }}</span>
                                    </button>
                                </h2>
                                <div id="rating_filter"
                                     class="accordion-collapse collapse show">
                                    <div class="accordion-body pt-1">
                                        <template v-for="rating in [4,3,2,1]">
                                            <div class="form-check mb-1">
                                                <input :id="'rating_'+rating" v-model="rating_model[rating]"
                                                       class="form-check-input cursor-pointer" type="checkbox"
                                                       @change="changeMinRating(rating)">
                                                <label :for="'rating_'+rating" class="form-check-label ms-1"
                                                       style="cursor:pointer;">
                                                    <StarRating :rating="rating" :star-spacing="2" size="12"/>
                                                    {{ rating }} & {{ $t('above') }}
                                                </label>
                                            </div>
                                        </template>
                                        <div class="form-check mb-1">
                                            <input :id="'rating_all'" v-model="rating_model[0]"
                                                   class="form-check-input cursor-pointer" type="checkbox"
                                                   @change="changeMinRating(0)">
                                            <label :for="'rating_all'" class="form-check-label ms-1"
                                                   style="cursor:pointer;">
                                                {{ $t('show_all') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="accordion-item">
                                <h2 class="accordion-header m-0">
                                    <button class="accordion-button shadow-none"
                                            data-bs-target="#price_filter" data-bs-toggle="collapse"
                                            type="button">
                                        <span class="fs-12 fw-medium">{{ $t('pricing') }}</span>
                                    </button>
                                </h2>
                                <div id="price_filter"
                                     class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <Slider v-model="filter.price_range" :max="200" :min="1" showTooltip="focus"/>
                                        <div class="d-flex align-items-center mt-3">
                                            <div class="flex-grow-1">
                                                <FormInput v-model="filter.price_range[0]" :max="filter.price_range[1]"
                                                           :min="1"
                                                           :step="1"
                                                           name="min"
                                                           no-label
                                                           no-spacing show-currency type="number">

                                                </FormInput>
                                            </div>
                                            <div class="mx-1-5">
                                                <span class="text-muted fw-medium">{{ $t('to') }}</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <FormInput v-model="filter.price_range[1]" :max="200"
                                                           :min="filter.price_range[0]"
                                                           :step="1"
                                                           name="min"
                                                           no-label
                                                           no-spacing show-currency type="number">

                                                </FormInput>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </SimpleBar>

                </Card>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="fw-medium font-16 mb-0 d-none d-md-block">{{ $t('your_searched_items') }}</p>
                    <div :class="[{'bg-soft-secondary':show_filter_wrapper, 'bg-soft-purple':!show_filter_wrapper}]"
                         class="p-2 bg-soft-secondary d-block d-md-none rounded d-flex align-items-center"
                         @click="show_filter_wrapper=!show_filter_wrapper">
                        <Icon :icon="show_filter_wrapper?'close':'tune'" size="20"/>
                    </div>

                    <ul class="nav nav-tabs nav-tabs-custom-icons color-teal border-none" role="tablist">
                        <li class="nav-item active" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#filtered_products">
                                <Icon class="icon" icon="dns" size="16"></Icon>
                                <span class="ms-1-5 title">{{ $t('products') }}</span>
                            </a>
                        </li>
                        <li class="nav-item active" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#filtered_shops">
                                <Icon class="icon" icon="storefront" size="16"></Icon>
                                <span class="ms-1-5 title">{{ $t('shops') }}</span>
                            </a>
                        </li>

                    </ul>
                </div>
                <div class="tab-content mt-2">
                    <div id="filtered_products"
                         class="tab-pane active" role="tabpanel">
                        <PageLoading :loading="item_loading">
                            <Row v-if="products.length>0"
                                 class="g-lg-3 g-md-2-5 g-2 row-cols-2 row-cols-lg-3 row-cols-xxl-4 row-cols-xxxl-5">
                                <Col v-for="product in products" :key="product.id">
                                    <router-link
                                        :to="{name:'customer.products.show', params: {id: product.id}}"
                                        class="nav-link">
                                        <Card class="mb-0">
                                            <div class="text-center p-2-5">
                                                <NetworkImage
                                                    :placeholder-src="productLandscapePlaceholder"
                                                    :src="getProductImage(product)"
                                                    class="img-fluid"
                                                    height="120"
                                                    object-fit="contain" rounded/>
                                            </div>


                                            <hr class="m-0 p-0 hr-muted">
                                            <div class="p-1-5 px-2-5">
                                                <p
                                                    class="max-lines mb-0 fw-medium mt-0 font-13 font-md-15">
                                                    {{ product.name }}
                                                </p>
                                                <div
                                                    class="d-flex align-items-center justify-content-between mt-0">
                                                    <div>
                                                        <i class="fas fa-star text-star-5 font-13"/>
                                                        <span class="font-12">
                                                                    ({{ getRatingText(product) }})</span>
                                                    </div>
                                                    <span class="font-14 fw-medium">
                                                        {{ getFormattedCurrency(getMinPrice(product)) }}
                                                        </span>
                                                </div>

                                            </div>
                                        </Card>
                                    </router-link>


                                </Col>
                            </Row>
                            <div v-else class="text-center mt-5">
                                <p class="fw-medium text-muted">{{ $t('there_is_no_product_in_this_filter') }}
                                    <span class="cursor-pointer text-primary ms-1"
                                          @click="clearFilter">{{ $t('clear_filter') }}</span>
                                </p>
                            </div>
                        </PageLoading>

                    </div>

                    <div id="filtered_shops"
                         class="tab-pane" role="tabpanel">
                        <PageLoading :loading="item_loading">
                            <Row v-if="shops.length>0">
                                <Col v-for="shop in shops" :key="shop.id" lg="4" md="6" sm="6" xl="3">
                                    <router-link :to="{name:'customer.shops.show', params: {id: shop.id}}"
                                                 class="nav-link">
                                        <Card class="overflow-hidden">
                                            <NetworkImage
                                                :placeholder-src="shopLandscapePlaceholder"
                                                :src="shop.cover_image"
                                                class="img-fluid"
                                                height="220"/>
                                            <CardBody class="p-2-5">
                                                <h5 class="fw-medium mt-0 mb-0-5">{{ shop.name }}</h5>
                                                <div>
                                                    <StarRating :rating="shop.rating" :size="14" star-spacing="1"/>
                                                    <span class="ms-1 font-13">
                                                        ({{ getRatingText(shop) }} -
                                                        {{ shop.ratings_count }})
                                                    </span>
                                                </div>
                                            </CardBody>
                                        </Card>
                                    </router-link>
                                </Col>
                            </Row>
                            <div v-else class="text-center mt-5">
                                <p class="fw-medium text-muted">{{ $t('there_is_no_shop_in_this_filter') }}
                                    <span class="cursor-pointer text-primary ms-1"
                                          @click="clearFilter">{{ $t('clear_filter') }}</span>
                                </p>
                            </div>
                        </PageLoading>

                    </div>
                </div>
            </div>
        </div>

    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from "vue";
import Request from "@js/services/api/request";
import {IData} from "@js/types/models/data";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {HomeLayout, IHomeLayout} from "@js/types/models/home_layout";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {ICategory} from "@js/types/models/category";
import Product, {IProduct} from "@js/types/models/product";
import {IShop} from "@js/types/models/shop";
import StarRating from "@js/components/shared/StarRating.vue";
import Slider from '@vueform/slider'
import {array_remove} from "@js/shared/array_utils";
import {deepCompare, deepCopy} from "@js/shared/utils";
import {getCustomerSelectedModuleId} from "@js/services/state/state_helper";
import {SimpleBar} from 'simplebar-vue3';

export default defineComponent({
    components: {StarRating, Layout, SimpleBar, Slider, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            item_loading: true,
            show_filter_wrapper: false,
            products: [] as IProduct[],
            shops: [] as IShop[],
            categories: [] as ICategory[],

            filter: {
                price_range: [5, 100],
                min_rating: 0,
                selected_categories: [] as number[],
                q: null,
            },
            old_filter: {},
            rating_model: {
                4: false,
                3: false,
                2: false,
                1: false,
                0: true
            }
        }
    },
    methods: {
        async getCategories() {
            const categoryResponse = await Request.getAuth<IData<ICategory[]>>(Request.addParameters(customerAPI.categories.get, {addModule: true}),);
            this.categories = categoryResponse.data.data;
        },
        getMinPrice(product: IProduct) {
            return Product.getMinPrice(product);
        },
        getTitleFromLayout(layout: IHomeLayout) {
            return this.$t(HomeLayout.getTextFromType(layout.type));
        },
        isShopLayout(layout: IHomeLayout) {
            return HomeLayout.isShop(layout.type);
        },
        isProductLayout(layout: IHomeLayout) {
            return HomeLayout.isProduct(layout.type);
        },
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },

        getRatingText(item: IProduct | IShop): string {
            return item.rating.toFixed(2);
        },
        isSelectedCategory(category: ICategory) {
            return this.filter.selected_categories.includes(category.id);
        },
        toggleCategory(category: ICategory) {

            if (this.filter.selected_categories.includes(category.id)) {
                this.filter.selected_categories = array_remove(this.filter.selected_categories, category.id)
            } else {
                this.filter.selected_categories.push(category.id)
            }
        },
        changeMinRating(rating: number) {
            if (rating == 0 || (this.filter.min_rating === rating && rating === 4)) {
                this.filter.min_rating = 0;
                for (let i = 1; i <= 4; i++) {
                    this.rating_model[i] = false;
                }
                this.rating_model[0] = true;
            } else {
                this.filter.min_rating = rating;
                this.rating_model[0] = false;
                for (let i = rating; i <= 4; i++) {
                    this.rating_model[i] = true;
                }
                for (let i = 1; i < rating; i++) {
                    this.rating_model[i] = false;
                }
            }
        },
        applyFilter() {
            this.filter = deepCopy(this.filter);

            this.getData();
            this.show_filter_wrapper = false;
        },
        clearFilter() {
            this.filter = {
                q: '',
                min_rating: 0,
                price_range: [1, 100],
                selected_categories: []
            }
            this.changeMinRating(0);
            this.getData();
            this.show_filter_wrapper = false;

        },
        async getData() {
            if (!deepCompare(this.old_filter, this.filter)) {
                this.item_loading = true;
                try {
                    let url = customerAPI.search.get;
                    let params = "?";
                    let moduleId = getCustomerSelectedModuleId();
                    if (moduleId != null) {
                        params += ("module_id=" + moduleId + "&&");
                    }
                    let query = {
                        q: this.filter.q,
                        categories: this.filter.selected_categories.join(","),
                        min_rating: this.filter.min_rating,
                        min_price: this.filter.price_range[0],
                        max_price: this.filter.price_range[1]
                    };
                    if (this.filter.q != null) {
                        params += ("q=" + query.q + "&&");
                    }
                    if (this.filter.selected_categories.length > 0) {
                        params += ('categories=' + query.categories + "&&")
                    }
                    if (this.filter.min_rating != 0) {
                        params += ('min_rating=' + query.min_rating + "&&")
                    }
                    if (this.filter.price_range) {
                        params += ('min_price=' + query.min_price + "&&max_price=" + query.max_price + "&&")
                    }

                    const response = await Request.getAuth<{
                        products: Product[],
                        shops: IShop[]
                    }>(url + params);
                    this.products = response.data.products;
                    this.shops = response.data.shops;
                    this.$router.replace({query: query, silent: true})
                } catch (error) {
                    handleException(error);
                } finally {
                    this.page_loading = false;
                    this.item_loading = false;
                }
            }
            this.old_filter = deepCopy(this.filter)
        },

    },
    watch: {
        filter: {
            handler() {

            },
            deep: true
        },
        '$route.query': {
            // immediate: true,
            handler(newVal) {
                this.filter.q = newVal['q'];
                this.applyFilter();
            }
        },
    },
    computed: {},
    async mounted() {

        this.setTitle(this.$t('search'));
        this.getCategories().then();
        const query = this.$route.query;
        this.filter.q = query['q'];
        this.filter.price_range = [query['min_price'] ?? 1, query['max_price'] ?? 100];
        this.changeMinRating(query['min_rating'] ?? 0);
        if (query['categories'] && query['categories'].trim().length > 0)
            this.filter.selected_categories = (query['categories']?.trim().split(",").map(Number) ?? [])

        await this.getData();
        this.page_loading = false;
        this.old_filter = deepCopy(this.filter)
    }
});

</script>
<style scoped>
</style>
