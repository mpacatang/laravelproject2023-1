<template>
    <Layout id="admin-dashboard">
        <PageLoading :loading="page_loading">
            <PageHeader :title="$t('dashboard')"></PageHeader>
            <Row>
                <Col lg="6" xxl="3">
                    <router-link :to="{name: 'admin.revenues.index'}" class="nav-link">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center">
                                    <p class="text-uppercase fw-semibold text-rose text-truncate mb-0 ls-0-5">
                                        {{ $t('revenues') }}</p>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2-5">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-2-5 mt-0">
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
                    <router-link :to="{name: 'admin.orders.index'}" class="nav-link">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center">
                                    <p class="text-uppercase fw-semibold text-primary text-truncate mb-0 ls-0-5">
                                        {{ $t('orders') }}</p>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2-5">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-2-5 mt-0">
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
                    <router-link :to="{name: 'admin.shops.index'}" class="nav-link">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center">
                                    <p class="text-uppercase fw-semibold text-teal text-truncate mb-0 ls-0-5">
                                        {{ $t('total_shops') }}</p>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2-5">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-2-5 mt-0">
                                        <span :data-target="dashboard_data.shop_count"
                                              class="counter-value">{{ dashboard_data.shop_count }}</span>
                                        </h4>
                                        <span>
                                    <span class="action-text">{{ $t('all_shops') }}
                                    </span>
                                    <Icon class="arrow" icon="arrow_forward" size="12"></Icon>
                                </span>
                                    </div>
                                    <VItem class="p-2" color="teal" radius="4" soft>
                                        <Icon class="data-icon" icon="storefront" size="26" type="msr"/>
                                    </VItem>
                                </div>
                            </CardBody><!-- end card body -->
                        </Card>
                    </router-link>

                </Col>

                <Col lg="6" xxl="3">
                    <router-link :to="{name: 'admin.customers.index'}" class="nav-link">
                        <Card class="data-card">
                            <CardBody>
                                <div class="d-flex align-items-center">
                                    <p class="text-uppercase fw-semibold text-success text-truncate mb-0 ls-0-5">
                                        {{ $t('customers') }}</p>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2-5">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-2-5 mt-0">
                                        <span :data-target="dashboard_data.customer_count"
                                              class="counter-value">{{ dashboard_data.customer_count }}</span>
                                        </h4>
                                        <span>
                                    <span class="action-text">{{ $t('manage_customers') }}
                                    </span>
                                    <Icon class="arrow" icon="arrow_forward" size="12"></Icon>
                                </span>
                                    </div>
                                    <VItem class="p-2" color="success" radius="4" soft>
                                        <Icon class="data-icon" icon="groups" size="26" type="msr"/>
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
                    <Card :loading="category_product_loading">
                        <CardHeader :title="$t('category')" class="py-2 text-nowrap" icon="trending_up">
                            <div class="ms-5">
                                <FormSelect :data="categories" :helper="category_select_helper"
                                            :onSelected="onChangeCategory"
                                            :placeholder="$t('all_category')" :selected="selected_category"
                                            name="category_id" no-label no-spacing/>
                            </div>
                        </CardHeader>
                        <div class="my-2-5">
                            <template v-for="(product, index) in dashboard_data.category_product_popular_data">
                                <hr v-if="index!==0" class="dashed my-2-5"/>
                                <router-link
                                    :to="{name:'admin.products.edit', params: {id: product.id}}"
                                    class="nav-link">
                                    <div class="d-flex px-3 align-items-center">

                                        <NetworkImage
                                            :src="getProductImage(product)"
                                            class="img-fluid"
                                            height="48"
                                            object-fit="contain"
                                            rounded
                                            :placeholder-src="productPlaceholder"
                                            width="48"/>


                                        <div class="ms-2 flex-grow-1">
                                            <h5
                                                class="max-lines mb-1-5 fw-medium mt-0">{{ product.name }}</h5>
                                            <div class="d-flex align-items-center">
                                                <StarRating :rating="product.rating" :size="14"/>
                                                <span class="text-muted font-13 ms-1"> ({{ getRatingText(product) }} - {{
                                                        product.ratings_count
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
                    <Card>
                        <CardHeader :title="$t('most_rated')" icon="hotel_class">

                        </CardHeader>
                        <div class="my-2-5">
                            <template v-for="(product, index) in dashboard_data.most_rated_product_data">
                                <hr v-if="index!==0" class="dashed my-2-5"/>
                                <router-link
                                    :to="{name:'admin.products.edit', params: {id: product.id}}"
                                    class="nav-link">
                                    <div class="d-flex px-3 align-items-center">

                                        <NetworkImage
                                            :src="getProductImage(product)"
                                            class="img-fluid"
                                            height="48"
                                            object-fit="contain"
                                            rounded
                                            :placeholder-src="productPlaceholder"
                                            width="48"/>


                                        <div class="ms-2 flex-grow-1">
                                            <h5
                                                class="max-lines mb-1-5 fw-medium mt-0">{{ product.name }}</h5>
                                            <div class="d-flex align-items-center">
                                                <StarRating :rating="product.rating" :size="14"/>
                                                <span class="text-muted font-13 ms-1"> ({{ getRatingText(product) }} - {{
                                                        product.ratings_count
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
                    <Card>
                        <CardHeader :title="$t('shop_plan_stats')" icon="donut_small"></CardHeader>
                        <CardBody>
                            <vue-apex-charts :options="shop_plan_chart.options" :series="shop_plan_chart.series"
                                             height="325"
                                             type="donut"></vue-apex-charts>
                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import PageHeader from "../../components/PageHeader.vue";
import Layout from "./layouts/Layout.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Row from "@js/components/Row.vue";
import Col from "@js/components/Col.vue";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import VItem from "@js/components/VItem.vue";
import Icon from "@js/components/Icon.vue";
import VueApexCharts from "vue3-apexcharts";
import CardHeader from "@js/components/CardHeader.vue";
import {IAdminDashboard} from "@js/types/models/dashboard";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import PageLoading from "@js/components/PageLoading.vue";
import Button from "@js/components/buttons/Button.vue";
import {IData} from "@js/types/models/data";
import {Category, ICategory} from "@js/types/models/category";
import NetworkImage from "@js/components/NetworkImage.vue";
import Product, {IProduct} from "@js/types/models/product";
import StarRating from "@js/components/shared/StarRating.vue";
import {IShop} from "@js/types/models/shop";
import FormSelect from "@js/components/form/FormSelect.vue";
import {chartColors} from "@js/shared/utils";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";
import {defineComponent} from 'vue';
import {Images} from "@js/shared/constant";

export default defineComponent({
    components: {
        FormSelect,
        StarRating,
        NetworkImage,
        Button,
        PageLoading, CardHeader, Icon, VItem, CardBody, Card, Col, Row, PageHeader, VueApexCharts, Layout
    },
    mixins: [UtilMixin, AdminModuleSelectorMixin],

    data() {
        const productPlaceholder = Images.productPortraitPlaceholder;

        return {
            productPlaceholder,
            page_loading: true,
            revenue_loading: false,
            category_product_loading: false,
            dashboard_data: null as IAdminDashboard,
            categories: [] as ICategory[],
            selected_category: null,
            shop_plan_chart: {
                options: null,
                series: null
            },
            dashboard_setup: {
                revenue_interval: 'monthly'
            } as {
                revenue_interval: 'monthly' | 'daily',
            },
            revenue_chart: {
                options: null,
                series: null
            }

        }
    },
    computed: {
        category_select_helper: Category.select_helper,
    },
    methods: {
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        getRatingText(item: IProduct | IShop): string {
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

            this.revenue_chart = {

                chart: {
                    fontFamily: 'Inter, Arial, sans-serif',

                },


                options: {
                    plotOptions: {

                        bar: {
                            borderRadius: 4,
                            columnWidth: '50%',
                            colors: {
                                ranges: [{
                                    from: 0,
                                    to: 1000,
                                    color: '#567AFA'
                                }],
                            }
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
        initShopPlanChart() {
            let x = [];
            let y = [];
            this.dashboard_data.shop_plan_data?.forEach((shop_plan) => {
                x.push(shop_plan.shop_plan.title);
                y.push(shop_plan.count);
            });
            this.shop_plan_chart = {
                chart: {
                    fontFamily: 'Inter, Arial, sans-serif'
                },

                options: {
                    legend: {
                        show: true,
                        position: 'right'
                    },
                    labels: x,
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        showAlways: true,
                                        show: true
                                    }
                                }
                            }
                        }
                    },
                    fill: {
                        colors: chartColors()
                    },
                },

                series: y,


            }

        },
        async changeRevenueInterval(interval: 'daily' | 'monthly') {
            if (this.revenue_loading || this.dashboard_setup.revenue_interval === interval)
                return;

            this.dashboard_setup.revenue_interval = interval;
            this.revenue_loading = true;
            try {
                const url = Request.addAdminModule(adminAPI.dashboard.revenues) + "revenue_interval=" + this.dashboard_setup.revenue_interval;
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
        async onChangeCategory(e) {
            if (this.selected_category === e || this.category_product_loading)
                return;

            this.selected_category = e;
            this.category_product_loading = true;
            try {
                const url = adminAPI.dashboard.category_products + (this.selected_category != null ? "?category_id=" + e : "");
                const response = await Request.getAuth<any>(url);
                if (response.success()) {
                    this.dashboard_data.category_product_popular_data = response.data.data;
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                handleException(error);
            } finally {
            }

            this.category_product_loading = false;
            // const response = await Request.getAuth<IData<ISubCategory>>(sellerAPI.sub_categories.get);
            // this.sub_categories = response.data.data;
        },
        async getData(forced = false) {
            this.page_loading = true;
            try {
                const cResponse = await Request.getAuth<IData<ICategory>>(Request.addAdminModule(adminAPI.categories.get), {forced: forced});
                this.categories = cResponse.data.data;

                const response = await Request.getAuth<IAdminDashboard>(Request.addAdminModule(adminAPI.dashboard.get), {forced: forced});
                if (response.success()) {
                    this.dashboard_data = response.data;

                } else {
                    ToastNotification.unknownError();
                }
                this.page_loading = false;

            } catch (error) {
                handleException(error);
            } finally {
            }
            const self = this;
            this.initRevenueChart();
            this.initShopPlanChart();
            setTimeout(self.initCounter, 2);
        },
        onChangeAdminModule(module_id) {
            this.getData();
        }
    },
    mounted() {
        this.setTitle(this.$t('dashboard'));
        this.getData(true);
    },
    created() {
        window.Apex.chart = {fontFamily: "Inter, Arial, sans-serif"};

    }
});

</script>
