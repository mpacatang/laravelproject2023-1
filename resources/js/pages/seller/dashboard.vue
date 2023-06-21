<template>
    <Layout id="seller-dashboard">
        <PageLoading :loading="page_loading">
            <PageHeader :title="$t('dashboard')"></PageHeader>

            <Row>
                <Col lg="6" xxl="3">
                    <router-link :to="{name: 'seller.shops.plan'}" class="nav-link">
                        <Card class="data-card overflow-hidden">
                            <VItem class="p-2 px-3" color="bluish-purple" display="flex" soft>
                                {{ $t('current_plan') }} -
                                <span class="ms-1 fw-semibold">{{ dashboard_data.shop_plan.title }}</span>
                            </VItem>
                            <div class="d-flex align-items-end justify-content-between p-3">
                                <div>
                                    <h5 class="fs-22 fw-medium mb-1-5 mt-0-5">
                                        {{ $t('upgrade_to_best_plans') }}
                                    </h5>
                                    <span>
                                    <span class="action-text">{{ $t('go_to_premium') }}
                                    </span>
                                    <Icon class="arrow" icon="arrow_forward" size="12"></Icon>
                                </span>
                                </div>
                                <VItem class="p-1-5" color="teal" radius="4" soft>
                                    <Icon class="data-icon" icon="upgrade" size="26" type="msr"/>
                                </VItem>
                            </div>
                        </Card>
                    </router-link>


                </Col>

                <Col lg="6" xxl="3">
                    <router-link :to="{name: 'seller.shop_revenues.index'}" class="nav-link">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center">
                                    <p class="text-uppercase fw-semibold text-rose text-truncate mb-0 ls-0-5">
                                        {{ $t('revenues') }}</p>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2-5">
                                    <div>
                                        <h4 class="fs-22 fw-semibold mb-2-5 mt-0">
                                        <span :data-target="dashboard_data.total_revenue"
                                              class="counter-value">{{
                                                getFormattedCurrency(dashboard_data.total_revenue)
                                            }}</span></h4>
                                        <span>
                                    <span class="action-text">{{ $t('view_all_transactions') }}
                                    </span>
                                    <Icon class="arrow" icon="arrow_forward" size="12"></Icon>
                                </span>
                                    </div>
                                    <VItem class="p-2" color="rose" radius="4" soft>
                                        <Icon class="data-icon" icon="account_balance_wallet" size="26" type="msr"/>
                                    </VItem>
                                </div>
                            </CardBody><!-- end card body -->
                        </Card>
                    </router-link>


                </Col>


                <Col lg="6" xxl="3">
                    <router-link :to="{name: 'seller.orders.index'}" class="nav-link">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center">
                                    <p class="text-uppercase fw-semibold text-primary text-truncate mb-0 ls-0-5">
                                        {{ $t('orders') }}</p>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2-5">
                                    <div>
                                        <h4 class="fs-22 fw-semibold mb-2-5 mt-0">
                                        <span :data-target="dashboard_data.order_count"
                                              class="counter-value">{{ dashboard_data.order_count }}</span>
                                        </h4>
                                        <span>
                                    <span class="action-text">{{ $t('view_orders') }}
                                    </span>
                                    <Icon class="arrow" icon="arrow_forward" size="12"></Icon>
                                </span>
                                    </div>
                                    <VItem class="p-2" color="primary" radius="4" soft>
                                        <Icon class="data-icon" icon="shopping_bag" size="26" type="msr"/>
                                    </VItem>
                                </div>
                            </CardBody><!-- end card body -->
                        </Card>
                    </router-link>


                </Col>


                <Col lg="6" xxl="3">
                    <router-link :to="{name: 'seller.products.index'}" class="nav-link">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center">
                                    <p class="text-uppercase fw-semibold text-success text-truncate mb-0 ls-0-5">
                                        {{ $t('products') }}</p>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2-5">
                                    <div>
                                        <h4 class="fs-22 fw-semibold mb-2-5 mt-0">
                                        <span :data-target="dashboard_data.product_count"
                                              class="counter-value">{{ dashboard_data.product_count }}</span>
                                        </h4>
                                        <span>
                                    <span class="action-text">{{ $t('manage_inventory') }}
                                    </span>
                                    <Icon class="arrow" icon="arrow_forward" size="12"></Icon>
                                </span>
                                    </div>
                                    <VItem class="p-2" color="success" radius="4" soft>
                                        <Icon class="data-icon" icon="inventory_2" size="26" type="msr"/>
                                    </VItem>
                                </div>
                            </CardBody><!-- end card body -->
                        </Card>
                    </router-link>


                </Col>


            </Row>

            <Card :loading="revenue_loading">
                <CardHeader :title="$t('revenue_stats')" class="py-2" icon="monitoring" type="mso">
                    <div class="d-flex float-end gap-1">
                        <Button :color="dashboard_setup.revenue_interval==='daily'?'primary':'secondary'"
                                :variant="dashboard_setup.revenue_interval==='daily'?'fill':'soft'"
                                size="sm"
                                @click="changeRevenueInterval('daily')">
                            <span class="font-13">{{ $t('daily') }}</span>
                        </Button>
                        <Button :color="dashboard_setup.revenue_interval==='monthly'?'primary':'secondary'"
                                :variant="dashboard_setup.revenue_interval==='monthly'?'fill':'soft'"
                                size="sm" @click="changeRevenueInterval('monthly')">
                            <span class="font-13">{{ $t('monthly') }}</span>
                        </Button>
                    </div>
                </CardHeader>
                <CardBody>
                    <vue-apex-charts :options="revenue_chart.options" :series="revenue_chart.series"
                                     height="350"
                                     type="bar"></vue-apex-charts>

                </CardBody>
            </Card>
            <Row>
                <Col lg="6" xxl="4">
                    <Card :loading="selling_loading">
                        <CardHeader :title="$t('product_selling')" class="p-2  px-2-5" icon="trending_up">
                            <div class="float-end">
                                <Button :color="dashboard_setup.selling_order==='low'?'danger':'primary'"
                                        class="p-1 px-2"
                                        rounded
                                        flexed-icon
                                        variant="soft" @click="onChangeSellingOrder">
                                    <Icon :class="[{'down':dashboard_setup.selling_order==='low'}]" class="order-icon"
                                          icon="north"
                                          size="18"
                                          type="msr"></Icon>
                                    <span class="ms-1 font-13 fw-medium">{{
                                            dashboard_setup.selling_order === 'low' ? $t('lowest') : $t('highest')
                                        }}</span>
                                </Button>
                            </div>
                        </CardHeader>
                        <div class="my-2-5">
                            <template v-for="(product, index) in dashboard_data.product_selling_data">
                                <hr v-if="index!==0" class="dashed my-2-5"/>
                                <router-link
                                    :to="{name:'seller.products.edit', params: {id: product.id}}"
                                    class="nav-link">
                                    <div class="d-flex px-3 align-items-center">

                                        <NetworkImage
                                            :src="getProductImage(product)"
                                            class="img-fluid"
                                            height="48"
                                            object-fit="contain"
                                            rounded
                                            :placeholder-src="productPortraitPlaceholder"
                                            width="48"/>


                                        <div class="ms-2 flex-grow-1">
                                            <h5
                                                class="max-lines mb-1-5 fw-medium mt-0">{{ product.name }}</h5>
                                            <div class="d-flex">
                                                <StarRating :rating="product.rating" :size="14"/>
                                                <span class="text-muted font-13 ms-1"> ({{ getRatingText(product) }} - {{
                                                        product.ratings_count
                                                    }} {{
                                                        this.$t('reviews')
                                                    }})</span>
                                                <span class="ms-auto">
                                                    <span class="fw-medium">{{ product.selling_count }}</span>
                                                    <Icon class="text-muted ms-1" icon="shopping_cart" size="16"></Icon>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </router-link>
                            </template>
                        </div>
                    </Card>
                </Col>
                <Col lg="6" xxl="4">
                    <Card :loading="rating_product_loading">
                        <CardHeader :title="$t('product_rating')" class="p-2 px-2-5" icon="hotel_class">
                            <div class="float-end">
                                <Button :color="dashboard_setup.rating_order==='low'?'danger':'primary'"
                                        class="p-1 px-2"
                                        rounded
                                        flexed-icon
                                        variant="soft" @click="onChangeRatedOrder">
                                    <Icon :class="[{'down':dashboard_setup.rating_order==='low'}]"
                                          class="order-icon"
                                          icon="north" size="16"
                                          type="msr"></Icon>
                                    <span class="ms-1 font-13 fw-medium">{{
                                            dashboard_setup.rating_order === 'low' ? $t('lowest') : $t('highest')
                                        }}</span>
                                </Button>
                            </div>
                        </CardHeader>
                        <div class="my-2-5">
                            <template v-for="(product, index) in dashboard_data.product_rated_data">
                                <hr v-if="index!==0" class="dashed my-2-5"/>
                                <router-link
                                    :to="{name:'seller.products.edit', params: {id: product.id}}"
                                    class="nav-link">
                                    <div class="d-flex px-3 align-items-center">

                                        <NetworkImage
                                            :src="getProductImage(product)"
                                            class="img-fluid"
                                            height="48"
                                            object-fit="contain"
                                            :placeholder-src="productPortraitPlaceholder"
                                            rounded
                                            width="48"/>


                                        <div class="ms-2 flex-grow-1">
                                            <h5
                                                class="max-lines mb-1-5 fw-medium mt-0">{{ product.name }}</h5>
                                            <div class="d-flex">
                                                <StarRating :rating="product.rating" :size="14"/>
                                                <span class="text-muted font-13 ms-1">
                                                    ({{ getRatingText(product) }} - {{ product.ratings_count }}
                                                    {{ this.$t('reviews') }})</span>
                                                <span class="ms-auto">
                                                    <span class="fw-medium">{{ product.selling_count }}</span>
                                                    <Icon class="text-muted ms-1" icon="shopping_cart" size="16"></Icon>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </router-link>
                            </template>
                        </div>
                    </Card>
                </Col>
                <Col lg="6" xxl="4">
                    <Card>
                        <CardHeader :title="$t('latest_review')" icon="reviews" size="18" type="msr">
                        </CardHeader>
                        <div class="my-2-5">
                            <template v-for="(review, index) in dashboard_data.review_data">
                                <hr v-if="index!==0" class="dashed my-2-5"/>

                                <div class="d-flex px-3 align-items-center">

                                    <NetworkImage
                                        :src="review.customer.avatar"
                                        circular
                                        :from-text="review.customer.first_name + ' '+review.customer.last_name"
                                        class="img-fluid"
                                        height="48"
                                        width="48"/>



                                    <div class="ms-2 flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h5
                                                class="max-lines my-0 fw-medium">
                                                {{ review.customer.first_name + " " + review.customer.last_name }}</h5>
                                            <StarRating :rating="review.rating" :size="14"/>
                                        </div>
                                        <p class="font-14 mt-1 mb-0">{{ review.review }}</p>

                                    </div>

                                </div>

                            </template>
                        </div>
                    </Card>
                </Col>
            </Row>
        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import Layout from "./layouts/Layout.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VueApexCharts from "vue3-apexcharts";
import {ISellerDashboard} from "@js/types/models/dashboard";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {ICategory} from "@js/types/models/category";
import Product, {IProduct} from "@js/types/models/product";
import StarRating from "@js/components/shared/StarRating.vue";
import {Components} from "@js/components/components";
import {IShopReview} from "@js/types/models/review";
import {defineComponent} from "vue";
import {chartColors} from "@js/shared/utils";

export default defineComponent({
    components: {
        StarRating,
        VueApexCharts, Layout,
        ...Components
    },
    mixins: [UtilMixin],

    data() {
        return {
            page_loading: true,
            revenue_loading: false,
            selling_loading: false,
            rating_product_loading: false,
            dashboard_data: null as ISellerDashboard,
            categories: [] as ICategory[],
            selected_category: null,
            shop_plan_chart: {
                options: null,
                series: null
            },
            dashboard_setup: {
                revenue_interval: 'monthly',
                selling_order: 'high',
                rating_order: 'high',
            } as {
                revenue_interval: 'monthly' | 'daily',
                selling_order: 'high' | 'low',
                rating_order: 'high' | 'low',
            },
            revenue_chart: {
                options: null,
                series: null
            }

        }
    },
    computed: {},
    methods: {
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        getRatingText(item: IProduct | IShopReview): string {
            return item.rating.toFixed(2);
        },
        initCounter() {
            const numberWithCommas = (x) => {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            };
            const counters = document.querySelectorAll<HTMLElement>(".counter-value");
            counters.forEach(function (counter) {
                const innerValue = counter.innerText;
                const max = parseFloat(counter.getAttribute('data-target'));
                const speed = max / 100;
                let value = 0;

                function updateCount() {
                    value += speed;
                    if (value < max) {
                        counter.innerText = numberWithCommas(value.toFixed(0));
                        setTimeout(updateCount, 12);
                    } else {
                        counter.innerText = innerValue;
                    }
                }

                updateCount();
            });

        },
        initRevenueChart() {
            let x = [];
            let y = [];
            this.dashboard_data.revenues?.forEach((revenue) => {
                x.push(new Date(revenue.date));
                y.push(this.getPrecise(revenue.revenue));
            })
            // xaxis.categories = x;


            this.revenue_chart = {
                chart: {
                    fontFamily: 'Inter, Arial, sans-serif'
                },
                options: {
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                        },
                    },
                    chart: {
                        id: 'revenue-chart'
                    },

                    fill: {
                        colors: chartColors()
                    },
                    xaxis: {
                        categories: x,
                        labels: {
                            formatter: (e) => {

                                if (this.dashboard_setup.revenue_interval == 'monthly') {
                                    return this.getFormattedDate(e, {date: false, short: true});
                                } else {
                                    return this.getFormattedDate(e, {
                                        date: true,
                                        month: false,
                                        year: false,
                                        short: true
                                    });
                                }
                            },
                        },
                        title: {
                            text: this.getFormattedDate(new Date(), {
                                date: false,
                                month: this.dashboard_setup.revenue_interval === 'daily'
                            })

                        }
                    },
                    yaxis: {
                        show: true,
                        labels: {
                            show: true,
                            formatter: (value) => {
                                return this.getFormattedCurrency(value);
                            },
                        },
                    },
                },

                series: [{
                    name: this.$t('revenues'),
                    data: y
                }]
            }
            // this.revenue_chart.options.xaxis.range = x.length;

        },
        async changeRevenueInterval(interval: 'daily' | 'monthly') {
            if (this.revenue_loading || this.dashboard_setup.revenue_interval === interval)
                return;

            this.dashboard_setup.revenue_interval = interval;
            this.revenue_loading = true;
            try {
                const url = sellerAPI.dashboard.revenues + "?revenue_interval=" + this.dashboard_setup.revenue_interval;
                const response = await Request.getAuth<any>(url);
                if (response.success()) {
                    this.dashboard_data.revenues = response.data;
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                handleException(error);
            } finally {
            }
            this.initRevenueChart();
            this.revenue_loading = false;

        },
        async onChangeSellingOrder() {
            if (this.selling_loading) {
                return;
            }
            const order = this.dashboard_setup.selling_order == 'high' ? 'low' : 'high';
            this.selling_loading = true;
            try {
                const url = sellerAPI.dashboard.selling_products + "?selling_order=" + order;
                const response = await Request.getAuth<any>(url);
                if (response.success()) {
                    this.dashboard_setup.selling_order = order;
                    this.dashboard_data.product_selling_data = response.data.data;
                }
            } catch (error) {
                handleException(error);
            } finally {
            }

            this.selling_loading = false;
            // const response = await Request.getAuth<IData<ISubCategory>>(sellerAPI.sub_categories.get);
            // this.sub_categories = response.data.data;
        },
        async onChangeRatedOrder() {
            if (this.rating_product_loading) {
                return;
            }
            const order = this.dashboard_setup.rating_order == 'high' ? 'low' : 'high';
            this.rating_product_loading = true;
            try {
                const url = sellerAPI.dashboard.rated_products + "?rating_order=" + order;
                const response = await Request.getAuth<any>(url);
                if (response.success()) {
                    this.dashboard_setup.rating_order = order;
                    this.dashboard_data.product_rated_data = response.data.data;
                }
            } catch (error) {
                handleException(error);
            } finally {
            }

            this.rating_product_loading = false;
        }
    },
    async mounted() {
        this.setTitle(this.$t('edit_home_layout'));
        try {
            const response = await Request.getAuth<ISellerDashboard>(sellerAPI.dashboard.get);
            if (response.success()) {
                this.dashboard_data = response.data;
            }
            this.page_loading = false;

        } catch (error) {
            handleException(error);
        } finally {
        }
        const self = this;
        this.initRevenueChart();
        setTimeout(self.initCounter, 2);
    },
    created() {
        window.Apex.chart = {fontFamily: "Inter, Arial, sans-serif"};

    }
});

</script>
