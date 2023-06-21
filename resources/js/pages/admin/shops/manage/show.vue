<template>
    <Layout id="admin-shops-show" ref="admin-shops-show">

            <div class="d-lg-flex d-grid gap-3 justify-content-between">
                <div class="d-flex flex-grow-1 align-items-center" v-if="shop">
                    <NetworkImage
                        :src="shop.logo"
                        circular
                        :placeholder-src="shopPlaceholder"
                        size="36"
                    />
                    <span class="ms-2 fw-medium text-nowrap">{{ shop.name }}</span>
                </div>

                <ul class="nav nav-tabs nav-tabs-custom-icons color-teal border-none flex-nowrap transparent-scrollbar overflow-scroll " role="tablist" id="shop-menu">
                    <li class="nav-item" role="presentation">
                        <a aria-expanded="false" aria-selected="false"
                           class="nav-link active"
                           data-bs-toggle="tab" data-page="description" role="tab" tabindex="-1">
                            <Icon class="icon" icon="storefront" size="16"></Icon>
                            <span class="ms-2">{{ $t('description') }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-expanded="false" aria-selected="false"
                           ref="edit"
                           class="nav-link"
                           data-bs-toggle="tab" data-page="edit" role="tab" tabindex="-1">
                            <Icon class="icon" icon="edit" size="16"></Icon>
                            <span class="ms-2">{{ $t('edit') }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-expanded="false" aria-selected="false"
                           class="nav-link"
                           ref="settings"
                           data-bs-toggle="tab" data-page="settings" role="tab" tabindex="-1">
                            <Icon class="icon" icon="settings" size="16"></Icon>
                            <span class="ms-2">{{ $t('settings') }}</span>
                        </a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a aria-expanded="false" aria-selected="false"
                           class="nav-link"
                           ref="orders"
                           data-bs-toggle="tab" data-page="orders" role="tab" tabindex="-1">
                            <Icon class="icon" icon="receipt_long" size="16"></Icon>
                            <span class="ms-2">{{ $t('orders') }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-expanded="false" aria-selected="false"
                           class="nav-link"
                           ref="products"
                           data-bs-toggle="tab" data-page="products" role="tab" tabindex="-1">
                            <Icon class="icon" icon="inventory_2" size="16"></Icon>
                            <span class="ms-2">{{ $t('products') }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-expanded="false" aria-selected="false"
                           class="nav-link"
                           ref="revenues"
                           data-bs-toggle="tab" data-page="revenues" role="tab" tabindex="-1">
                            <Icon class="icon" icon="account_balance_wallet" size="16"></Icon>
                            <span class="ms-2">{{ $t('revenues') }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-expanded="false" aria-selected="false"
                           class="nav-link"
                           ref="reviews"

                           data-bs-toggle="tab" data-page="reviews" role="tab" tabindex="-1">
                            <Icon class="icon" icon="star" size="16"></Icon>
                            <span class="ms-2">{{ $t('reviews') }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-expanded="false" aria-selected="false"
                           class="nav-link"
                           ref="plans"

                           data-bs-toggle="tab" data-page="plans" role="tab" tabindex="-1">
                            <Icon class="icon" icon="view_carousel" size="16"></Icon>
                            <span class="ms-2">{{ $t('plans') }}</span>
                        </a>
                    </li>
                </ul>


            </div>

        <PageLoading :loading="page_loading">
            <div class="tab-content mt-3">
                <template v-if="page==='description'">

                    <div class="mb-3" v-if="!shop.approved">
                        <VItem align-items="center" border class="mt-2 card shadow-none" radius="4" soft
                               style="width: 460px">
                            <div class="d-flex align-items-center p-2 px-3">
                                <Icon class="text-primary" icon="warning" size="20"></Icon>
                                <span class="mx-2 fw-medium text-primary">{{ $t('take_a_action') }}</span>
                            </div>
                            <hr class="dashed m-0">
                            <div class="p-2 px-3">
                                <ul>
                                    <li>New shop registered</li>
                                    <li>Take look every entities and make approve.
                                    </li>
                                    <li>Delete is also remove shop from database</li>
                                </ul>
                                <div class=" text-end mt-2">
                                    <Button class="fw-medium" color="success" variant="text" @click="approveShop">{{
                                            $t('approve')
                                        }}
                                    </Button>
                                    <Button class="fw-medium ms-3" color="danger" variant="text" @click="deleteShop">{{
                                            $t('delete')
                                        }}
                                    </Button>
                                </div>
                            </div>

                        </VItem>
                    </div>
                    <Card>
                        <CardHeader :title="$t('shop_information')" icon="storefront">

                        </CardHeader>
                        <CardBody>
                            <Row>
                                <Col lg="6">
                                    <div class="d-flex align-items-center mb-2">
                                        <Icon icon="mail" size="20"></Icon>
                                        <span class="ms-2">{{ shop.email }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <Icon icon="call" size="20"></Icon>
                                        <span class="ms-2">{{ shop.mobile_number }}</span>
                                    </div>

                                    <div class="d-flex align-items-start">
                                        <Icon icon="location_on" size="20"></Icon>
                                        <div class="ms-2">
                                            <p class="fw-medium font-15 mb-1">{{ $t('address') }}</p>
                                            <p class="font-14 mb-0-5">{{ shop.address }}</p>
                                            <p class="font-14 mb-0-5">{{ shop.city }} - {{ shop.pincode }}</p>
                                            <p class="font-14">{{ shop.country }}</p>
                                        </div>

                                    </div>
                                </Col>
                                <Col lg="6">
                                    <GoogleMap
                                        :location="{latitude: shop.latitude, longitude: shop.longitude}"></GoogleMap>
                                </Col>
                            </Row>
                        </CardBody>
                    </Card>

                    <Row>
                        <Col xl="6">
                            <Card>
                                <CardHeader :title="$t('owner_information')" icon="admin_panel_settings">
                                </CardHeader>
                                <template v-if="!owner">
                                    <div class="p-3">
                                        {{ $t('there_is_no_owner_allocated') }}
                                    </div>
                                    <div class="text-end mb-3 me-3">
                                        <router-link :to="{name:'admin.sellers.create'}">
                                            <Button color="bluish-purple" variant="soft">
                                                <Icon class="me-1" icon="add" size="20"></Icon>
                                                {{ $t('create_owner') }}
                                            </Button>
                                        </router-link>
                                    </div>
                                </template>
                                <template v-else>
                                    <CardBody>
                                        <div class="d-flex align-items-start">
                                            <NetworkImage :src="owner.avatar" circular size="60" :from-text="owner.first_name+' '+owner.last_name"/>
                                            <div class="ms-3 flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <Icon icon="person" size="20"></Icon>
                                                    <span
                                                        class="ms-2">{{ owner.first_name }} {{ owner.last_name }}</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <Icon icon="mail" size="20"></Icon>
                                                    <span class="ms-2">{{ owner.email }}</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <Icon icon="call" size="20"></Icon>
                                                    <span class="ms-2">{{ owner.mobile_number }}</span>
                                                </div>
                                            </div>
                                            <router-link :to="{name: 'admin.sellers.edit', params: {id:owner.id}}">
                                                <Button color="primary" flexed-icon radius="md" variant="soft">
                                                    <Icon icon="edit" size="16"></Icon>
                                                    <span class="ms-1-5 d-md-block d-none">{{ $t('edit_owner') }}</span>
                                                </Button>
                                            </router-link>
                                        </div>
                                    </CardBody>
                                    <hr class="dashed m-0">
                                    <CardBody>
                                        <div class="d-flex align-items-center mb-2">
                                            <Icon icon="account_balance" size="20"></Icon>
                                            <span class="ms-2 fw-semibold">{{ $t('bank_details') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="fw-medium">{{ $t('bank_name') }}:</span>
                                            <span class="ms-2">{{ owner.bank_name ?? "-" }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="fw-medium">{{ $t('account') }}:</span>
                                            <span class="ms-2">{{ owner.account_number ?? "-" }}</span>
                                        </div>
                                    </CardBody>
                                </template>

                            </Card>
                        </Col>
                    </Row>

                </template>
                <EditShop v-if="page==='edit'" :id="id"/>
                <ShopSetting v-if="page==='settings'" :id="id"/>
                <ShopOrder v-if="page==='orders'" :id="id"/>
                <ShopProduct v-if="page==='products'" :id="id"/>
                <ShopRevenue v-if="page==='revenues'" :id="id"/>
                <ShopReview v-if="page==='reviews'" :id="id"/>
                <ShopPlan v-if="page==='plans'" :id="id"/>
            </div>

        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IShop, Shop} from "@js/types/models/shop";
import {IBreadcrumb} from "@js/types/models/models";
import GoogleMap from "@js/components/maps/GoogleMap.vue";
import DeliveryTimeTypeMixin from "@js/shared/mixins/DeliveryTimeTypeMixin";
import {IData} from "@js/types/models/data";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import EditShop from "@js/pages/admin/shops/manage/EditShop.vue";
import ShopOrder from "@js/pages/admin/shops/manage/ShopOrder.vue";
import ShopProduct from "@js/pages/admin/shops/manage/ShopProduct.vue";
import ShopSetting from "@js/pages/admin/shops/manage/ShopSetting.vue";
import ShopRevenue from "@js/pages/admin/shops/manage/ShopRevenue.vue";
import ShopReview from "@js/pages/admin/shops/manage/ShopReview.vue";
import ShopPlan from "@js/pages/admin/shops/manage/ShopPlan.vue";
import {ISeller} from "@js/types/models/seller";
import {Tab} from "bootstrap";
import {Images} from "@js/shared/constant";

export default defineComponent({
    components: {
        ShopPlan,
        ShopReview,
        ShopRevenue,
        ShopSetting,
        ShopProduct,
        ShopOrder,
        EditShop,
        GoogleMap,
        Layout, ...Components
    },
    mixins: [FormMixin, UtilMixin, DeliveryTimeTypeMixin, DiscountTypeMixin],
    data() {
        const shopPlaceholder = Images.shopPortraitPlaceholder;
        return {
            shopPlaceholder,
            id: this.$route.params.id,
            page_loading: true,
            shop: {} as IShop,
            page: null,
            owner: null as ISeller,
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("shops"),
                    route: 'admin.shops.index'
                },
                {
                    text: this.$t('show'),
                    active: true,
                },
            ];
        }
    },

    methods: {
        async approveShop() {
            try {
                const response = await Request.patchAuth<IData<any>>(adminAPI.shops.approve(this.id));
                if (response.success()) {
                    this.shop.approved = true;
                    ToastNotification.success(this.$t('shop_approved'))
                } else {
                    ToastNotification.unknownError();
                }

            } catch (error) {
                handleException(error);
            }
        },
        async deleteShop() {
            try {
                const response = await Request.deleteAuth<IData<any>>(adminAPI.shops.delete(this.id));
                if (response.success()) {
                    this.$router.go(-1);
                    ToastNotification.success(this.$t('shop_archived'))
                } else {
                    ToastNotification.unknownError();
                }

            } catch (error) {
                handleException(error);
            }
        },

    },

    async mounted() {
        this.setTitle(this.$t('manage_shop'));
        const page = this.$route.query?.tab;
        if (page != null) {
            let nav = this.$refs[page];
            if (nav != null) {
                new Tab(nav).show();
            }
        }
        this.page = page??'description';
        try {
            const response = await Request.getAuth<IData<IShop>>(adminAPI.shops.show(this.id));
            if (response.success()) {
                this.shop = response.data.data;
                if (this.shop.sellers != null) {
                    this.owner = Shop.getOwner(this.shop);
                }
                this.setTitle(this.shop.name);
            } else {
                ToastNotification.unknownError();
            }

        } catch (error) {
            handleException(error);
        }
        this.page_loading = false;
        const self = this;
        setTimeout(() => {
            const tabEl = document.querySelectorAll('[data-bs-toggle="tab"]')
            tabEl.forEach((element) => {
                element.addEventListener('shown.bs.tab', event => {
                    self.page = element.getAttribute('data-page');
                    self.$router.replace({query: {tab: self.page}, silent: true})
                })
            })
        }, 10);

    },


});

</script>
