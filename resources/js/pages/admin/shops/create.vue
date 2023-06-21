<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('shop_registration')"/>

        <PageLoading :loading="page_loading">

            <FormSwitch v-model="create_seller"
                        :label="$t('create_owner')"
                        name="create_seller"/>

            <Row v-if="create_seller">


                <Col lg="6">
                    <Card>

                        <CardHeader :title="$t('info')" icon="manage_accounts" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="seller.active" :errors="errors"
                                            :label="$t('active')"
                                            name="seller.active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="seller.first_name" :errors="errors"
                                               :label="$t('first_name')" name="seller.first_name"
                                               required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="seller.last_name" :errors="errors"
                                               :label="$t('last_name')" name="seller.last_name"
                                               required/>
                                </Col>
                            </Row>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="seller.mobile_number" :errors="errors"
                                               :label="$t('mobile_number')"
                                               name="seller.mobile_number" required
                                               type="tel">
                                        <template #prefix>
                                            <Icon icon="call" size="18"></Icon>
                                        </template>
                                    </FormInput>

                                </Col>
                                <Col lg="6">

                                    <FormInput v-model="seller.email" :errors="errors" :label="$t('email')"
                                               name="seller.email" required type="email">
                                        <template #prefix>
                                            <Icon icon="mail" size="18"></Icon>
                                        </template>
                                    </FormInput>

                                </Col>
                            </Row>


                            <FormInput v-model="seller.password" :errors="errors" :label="$t('password')"
                                       :placeholder="$t('password_(if_you_need_to_change)')"
                                       name="seller.password" no-spacing required type="password"/>


                        </CardBody>
                    </Card>
                </Col>


                <Col lg="6">
                    <Card>

                        <CardHeader :title="$t('identity')" icon="badge" type="msr"/>

                        <CardBody class="pb-0">

                            <FileUpload
                                container-height="126" max-files="1"
                                preview-as-list
                                :errors="errors"
                                :label="$t('avatar')"
                                name="avatar"
                                show-file-manager
                                v-on:file-added="avatar_helper.onFileUpload"
                                v-on:file-removed="avatar_helper.onFileRemoved"
                            />


                        </CardBody>
                    </Card>

                </Col>

            </Row>

            <Row>
                <Col lg="5">
                    <Card>
                        <CardHeader :title="$t('general')" icon="storefront">
                            <div class="float-end">
                                <FormSwitch v-model="shop.active"
                                            :errors="errors" :label="$t('active')"
                                            name="shop.active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">


                            <FormInput v-model="shop.name" :errors="errors"
                                       :label="$t('name')" name="shop.name"/>
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="shop.mobile_number" :errors="errors"
                                               :label="$t('mobile_number')"
                                               name="shop.mobile_number" type="tel">
                                        <template #prefix>
                                            <Icon icon="call" size="18"></Icon>
                                        </template>
                                    </FormInput>

                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop.email" :errors="errors"
                                               :label="$t('email')"
                                               name="shop.email"
                                               type="email"
                                    >
                                        <template #prefix>
                                            <Icon icon="mail" size="18"></Icon>
                                        </template>
                                    </FormInput>

                                </Col>

                            </Row>
                            <FormInput v-model="shop.license_number" :errors="errors"
                                       :label="$t('license_number')"
                                       name="shop.license_number">
                                <template #label-suffix>
                                    <InfoTooltip :tooltip="$t('you_can_set_fssai_number_etc')"/>
                                </template>
                            </FormInput>

                            <Row>
                                <Col lg="6">

                                    <FormSelect :data="modules" :helper="module_helper"
                                                :label="$t('module')"
                                                :onSelected="(e)=>shop.module_id=e"
                                                :placeholder="$t('select_module')"
                                                :selected="shop.module_id"
                                                :errors="errors"
                                                name="shop.module_id">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="Module can't be change after shop is created. So be careful"/>
                                        </template>
                                    </FormSelect>
                                </Col>

                                <Col lg="6">
                                    <FormSelect :data="shop_plans" :helper="shop_plan_helper"
                                                :label="$t('shop_plan')"
                                                :onSelected="(e)=>shop.shop_plan_id=e"
                                                :placeholder="$t('shop_plan')"
                                                :errors="errors"
                                                :selected="shop.shop_plan_id"
                                                name="shop.shop_plan_id"/>

                                </Col>
                            </Row>

                        </CardBody>
                    </Card>
                </Col>

                <Col lg="7">

                    <Card>
                        <CardHeader :title="$t('meta')" icon="edit_note" type="msr"/>

                        <CardBody class="pb-0">


                            <TextEditor v-model="shop.description" :label="$t('description')"/>

                            <Row>
                                <Col lg="6">

                                    <FileUpload :default-files="logo_helper.defaultFiles" :max-files="1"
                                                :errors="errors" :label="$t('logo')"
                                                :placeholder="$t('logo')+' - '+$t('square')"
                                                :validator="logoValidator"
                                                preview-as-list
                                                height="82"
                                                name="logo"
                                                show-file-manager
                                                v-on:file-added="logo_helper.onFileUpload"
                                                v-on:file-removed="logo_helper.onFileRemoved"/>
                                </Col>
                                <Col lg="6">
                                    <FileUpload :default-files="cover_image_helper.defaultFiles" :max-files="1"
                                                :errors="errors"
                                                :label="$t('cover_image')"
                                                :placeholder="$t('cover')+' - '+$t('landscape')"
                                                height="82"
                                                :validator="coverImageValidator"
                                                name="cover_image"
                                                show-file-manager
                                                preview-as-list
                                                v-on:file-added="cover_image_helper.onFileUpload"
                                                v-on:file-removed="cover_image_helper.onFileRemoved"/>
                                </Col>
                            </Row>

                        </CardBody>
                    </Card>
                </Col>

                <Col lg="6">
                    <Card>

                        <CardHeader :title="$t('pick_from_map')" icon="home_pin" type="msr"/>

                        <CardBody class="p-2">

                            <GoogleMap :height=366 :location="location"
                                       pick-enabled
                                       search-enabled
                                       :polygon="getSelectedZone?.coordinates.coordinates[0]"
                                       @change-location="onLocationChange"/>

                        </CardBody>
                    </Card>
                </Col>

                <Col lg="6">
                    <Card>
                        <CardHeader :title="$t('location')" icon="near_me" type="msr"/>

                        <CardBody class="pb-0">

                            <Row>
                                <Col lg="5">
                                    <FormSelect :data="zones" :helper="zone_helper"
                                                :label="$t('zone')"
                                                :onSelected="(e)=>shop.zone_id=e"
                                                :placeholder="$t('select_zone')"
                                                :selected="shop.zone_id"
                                                name="shop.zone_id">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="If you set zone then only that zone's customer can only see your shop and order"/>
                                        </template>
                                    </FormSelect>
                                </Col>
                                <Col lg="7">
                                    <FormInput v-model="shop.address" :errors="errors"
                                               :label="$t('address')"
                                               name="shop.address" required/>
                                </Col>
                            </Row>


                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="shop.city" :errors="errors"
                                               :label="$t('city')"
                                               name="shop.city" required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop.state" :errors="errors"
                                               :label="$t('state')"
                                               name="shop.state" required/>
                                </Col>
                            </Row>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="shop.country" :errors="errors"
                                               :label="$t('country')"
                                               name="shop.country" required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop.pincode" :errors="errors"
                                               :label="$t('pincode')"
                                               name="shop.pincode" required type="number"/>
                                </Col>

                            </Row>

                            <Row>
                                <Col lg="6">

                                    <FormInput v-model="location.latitude" :errors="errors" :label="$t('latitude')"
                                               :placeholder="$t('enter_a_latitude_or_pick_from_map')"
                                               name="shop.latitude" required
                                               step="0.00001" type="number" v-on:onchange="onLatitudeChange"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="location.longitude" :errors="errors"
                                               :label="$t('longitude')"
                                               :placeholder="$t('enter_a_longitude_or_pick_from_map')"
                                               name="shop.longitude"
                                               required
                                               step="0.00001" type="number" v-on:onchange="onLongitudeChange"/>
                                </Col>
                            </Row>


                        </CardBody>
                    </Card>
                </Col>


            </Row>

            <Card>
                <CardHeader :title="$t('setups')" icon="tune" type="msr">
                </CardHeader>
                <CardBody>
                    <Row>

                        <Col lg="3">
                            <div class="p-2 border border-dashed  rounded">
                                <FormSwitch v-model="shop.open"
                                            :errors="errors" :label="$t('currently_open')"
                                            name="shop.currently_open"
                                            no-spacing/>
                            </div>
                        </Col>
                        <Col lg="3">
                            <div class="p-2 border border-dashed  rounded">
                                <FormSwitch v-model="shop.self_delivery"
                                            :errors="errors" :label="$t('self_delivery')"
                                            name="shop.self_delivery" no-spacing/>
                            </div>
                        </Col>
                        <Col lg="3">
                            <div class="p-2 border border-dashed  rounded">
                                <FormSwitch v-model="shop.take_away"
                                            :errors="errors" :label="$t('take_away')"
                                            name="shop.take_away" no-spacing/>

                            </div>
                        </Col>
                        <Col lg="3">
                            <div class="p-2 border border-dashed  rounded">

                                <FormSwitch v-model="shop.open_for_delivery"
                                            :errors="errors" :label="$t('open_for_delivery')"
                                            name="shop.open_for_delivery" no-spacing/>
                            </div>
                        </Col>
                    </Row>
                </CardBody>
            </Card>

            <Row>
                <Col lg="4">
                    <Card>
                        <CardHeader :title="$t('order_options')" icon="local_mall" type="msr">
                        </CardHeader>

                        <CardBody class="pb-0">
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="shop.min_delivery_time" :errors="errors"
                                               :label="$t('min_delivery_time')"
                                               name="shop.min_delivery_time"
                                               type="number">
                                        <template #suffix>
                                            <Icon icon="schedule" size="xs"/>
                                        </template>
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="You can set approx. minimum delivery time, which includes preparing, packaging and delivery"/>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop.max_delivery_time" :errors="errors"
                                               :label="$t('max_delivery_time')"
                                               min="0"
                                               name="shop.max_delivery_time"
                                               type="number">
                                        <template #suffix>
                                            <Icon icon="schedule" size="xs"/>
                                        </template>
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="You can set approx. maximum delivery time, which includes preparing, packaging and delivery"/>
                                        </template>
                                    </FormInput>
                                </Col>

                            </Row>
                            <Row>
                                <Col lg="6">
                                    <FormSelect :data="delivery_time_types" :helper="delivery_time_type_helper"
                                                :label="$t('delivery_time_type')" :onSelected="onChangeDeliveryTypeType"
                                                :placeholder="$t('time_type')"
                                                :selected="selected_delivery_time_type"
                                                name="shop.delivery_time_type"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop.min_order_amount" :errors="errors"
                                               :label="$t('min_order_amount')"
                                               :placeholder="$t('min_order_amount')"
                                               min="0"
                                               name="shop.min_order_amount"
                                               show-currency type="number">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="You can set minimum order amount to make online order for customer"/>
                                        </template>
                                    </FormInput>
                                </Col>
                            </Row>


                        </CardBody>
                    </Card>
                </Col>
                <Col lg="4">
                    <Card>
                        <CardHeader :title="$t('delivery_options')" icon="local_shipping" type="msr">

                        </CardHeader>
                        <CardBody class="pb-0">


                            <FormInput v-model="shop.delivery_range" :errors="errors"
                                       :label="$t('delivery_range')+' ['+$t('in_meter') +']'"
                                       :placeholder="$t('delivery_range_in_meter')"
                                       min="0"
                                       name="shop.delivery_range"
                                       type="number">
                                <template #label-suffix>
                                    <InfoTooltip
                                        tooltip="If it blank, then customer can order from any distance [infinite]"/>
                                </template>
                            </FormInput>
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="shop.minimum_delivery_charge" :errors="errors"
                                               :label="$t('minimum_delivery_charge')"
                                               :placeholder="$t('minimum_delivery_charge')" min="0"
                                               name="shop.minimum_delivery_charge"
                                               show-currency type="number">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="When order is delivery type then delivery charge applied. It's a minimum charge"/>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="shop.delivery_charge_multiplier" :errors="errors"
                                               :label="$t('delivery_charge_multiplier')"
                                               min="0"
                                               name="shop.delivery_charge_multiplier"
                                               show-currency type="number">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="Extra delivery charge, which is based on shop to delivery location distance.
                                                 Charge is calculate per KM = (1000 meter)"/>
                                        </template>
                                    </FormInput>
                                </Col>
                            </Row>


                        </CardBody>
                    </Card>

                </Col>
                <Col lg="4">
                    <Card>
                        <CardHeader :title="$t('tax_info')" icon="post_add"></CardHeader>
                        <CardBody class="pb-0">
                            <FormInput v-model="shop.packaging_charge" :errors="errors"
                                       :label="$t('packaging_charge')"
                                       :placeholder="$t('packaging_charge')" name="shop.packaging_charge" show-currency>

                                <template #label-suffix>
                                    <InfoTooltip
                                        tooltip="Includes packaging charges, extra cutlery, etc."/>
                                </template>

                            </FormInput>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="shop.tax" :errors="errors"
                                               :label="$t('tax')"
                                               :placeholder="$t('enter_a_tax')" name="shop.tax" :prefix-or-suffix="shop.tax_type">
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="Includes SGST, CGST, and any other taxes"/>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">

                                    <FormSelect :data="charge_types" :helper="charge_type_helper"
                                                :onSelected="(value)=>shop.tax_type=value"
                                                :errors="errors"
                                                :label="$t('tax_type')"
                                                :placeholder="$t('tax_type')" :selected="shop.tax_type"
                                                name="shop.tax_type"/>
                                </Col>

                            </Row>


                        </CardBody>
                    </Card>
                </Col>
            </Row>

            <div class="text-end mb-3">
                <CreateButton :loading="loading" @click="create"/>
            </div>

        </PageLoading>
    </Layout>
</template>


<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IShop} from "@js/types/models/shop";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {ISeller, Seller} from "@js/types/models/seller";
import {IBreadcrumb, IFormSelectOption} from "@js/types/models/models";
import GoogleMap from "@js/components/maps/GoogleMap.vue";
import DeliveryTimeTypeMixin from "@js/shared/mixins/DeliveryTimeTypeMixin";
import {IData} from "@js/types/models/data";
import {IModule, Module} from "@js/types/models/module";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {IZone, Zone} from "@js/types/models/zone";
import {IShopPlan} from "@js/types/models/shop_plan";
import Tooltip from "@js/components/Tooltip.vue";
import InfoTooltip from "@js/components/InfoTooltip.vue";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import {array_get_from_id} from "@js/shared/array_utils";
import {RequestCache} from "@js/services/api/cache";
import NavigatorService from "@js/services/navigator_service";

export default defineComponent({
    components: {
        CreateButton,
        InfoTooltip,
        Tooltip,
        GoogleMap,
        Layout, ...Components
    },
    mixins: [FormMixin, UtilMixin, DeliveryTimeTypeMixin, DiscountTypeMixin],
    data() {

        return {
            loading: false,
            page_loading: true,

            create_seller: true,
            //Seller
            seller: {is_owner: true, active: true} as ISeller,
            avatar_helper: new FileUploadHelper(),

            //Shop
            shop: {active: true} as IShop,
            logo_helper: new FileUploadHelper({max: 1}),
            cover_image_helper: new FileUploadHelper({max: 1}),


            location: {
                latitude: null,
                longitude: null
            },
            modules: [] as IModule[],
            zones: [] as IZone[],
            shop_plans: [] as IShopPlan[],
        }
    },
    computed: {
        getSelectedZone() {
            if (this.shop.zone_id != null && this.zones != null) {
                return array_get_from_id(this.zones, this.shop.zone_id);
            }
        },
        seller_helper: Seller.select_helper,
        module_helper: Module.select_helper,
        zone_helper: Zone.select_helper,
        shop_plan_helper(): IFormSelectOption<IShopPlan> {
            return {
                option: {
                    label: (plan) => plan.title + " " + this.getFormattedCurrency(plan.price),
                    value: 'id'
                },
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("shop"),
                    route: 'admin.shops.index'
                },
                {
                    text: this.$t('create'),
                    active: true,
                },
            ];
        }
    },

    methods: {
        onSellerChange(sellers) {
            this.selected_sellers = sellers;
        },
        logoValidator(file): boolean {
            this.errors = {};
            console.info(file);
            if (file.height === file.width) {
                return true;
            }
            this.addError('logo', "Please upload square (1:1) image");
        },
        coverImageValidator(file): boolean {
            this.errors = {};
            if (file.height < file.width) {
                return true;
            }
            this.addError('cover_image', "Please upload landscape image");
        },
        onLocationChange(location) {
            this.location = location;
        },
        onLatitudeChange(e) {
            e.preventDefault();
            this.location = {...this.location, latitude: Number.parseFloat(e.target.value)}
        },
        onLongitudeChange(e) {
            e.preventDefault();
            this.location = {...this.location, longitude: Number.parseFloat(e.target.value)}
        },
        async create() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.shops.create, {
                    shop: {
                        ...this.shop, ...this.location,
                        logo: this.logo_helper.getImageDataFile(),
                        cover_image: this.cover_image_helper.getImageDataFile()
                    },
                    seller: this.create_seller ? {
                        ...this.seller,
                        avatar: this.avatar_helper.getImageDataFile()
                    } : null
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('shop_registered'));
                    RequestCache.removeSimilar(adminAPI.shops.get);
                    NavigatorService.goBackOrRoute({name: 'admin.shops.index'});
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        }
    },
    async mounted() {
        const query = this.$route.query;

        this.location = {
            latitude: query['lat'],
            longitude: query['lng']
        }

        this.setTitle(this.$t('create_shop'));
        try {
            const sellerResponse = await Request.getAuth<IData<ISeller[]>>(adminAPI.sellers.available_owners);
            if (sellerResponse.success()) {
                this.sellers = sellerResponse.data.data;
            }
            const moduleResponse = await Request.getAuth<IData<IModule[]>>(adminAPI.modules.get);
            if (moduleResponse.success()) {
                this.modules = moduleResponse.data.data;
            }
            const zoneResponse = await Request.getAuth<IData<IZone[]>>(adminAPI.zones.get);
            if (zoneResponse.success()) {
                this.zones = zoneResponse.data.data;
            }
            const shopPlanResponse = await Request.getAuth<IData<IShopPlan[]>>(adminAPI.shop_plans.get);
            if (shopPlanResponse.success()) {
                this.shop_plans = shopPlanResponse.data.data;
            }
        } catch (error) {
            handleException(error);
        }
        this.page_loading = false;
    }
});

</script>
