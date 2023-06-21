<template>
    <PageLoading :loading="page_loading">
        <Row>

            <Col xxl="5">
                <Card>
                    <CardHeader :title="$t('general')" icon="storefront" type="msr">
                        <div class="float-end">
                            <FormSwitch v-model="shop.active"
                                        :errors="errors" :label="$t('active')"
                                        name="active" no-spacing/>
                        </div>
                    </CardHeader>

                    <CardBody class="pb-0">

                        <FormInput v-model="shop.name" :errors="errors"
                                   :label="$t('name')" name="name"/>

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
                                           type="email">
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
                                <FormLabel name="module">{{ $t('module') }}</FormLabel>
                                <h5 class="fw-medium mt-0">{{ shop.module?.title ?? "-" }}</h5>
                            </Col>
                            <Col lg="6">
                                <FormSelect :data="sellers" :helper="seller_helper"
                                            :onSelected="onOwnerChange"
                                            :placeholder="$t('select_owner')"
                                            :selected="selected_owner"
                                            :label="$t('select_owner')"
                                            name="owner"/>
                            </Col>
                        </Row>


                    </CardBody>
                </Card>
            </Col>

            <Col xxl="7">

                <Card>
                    <CardHeader :title="$t('meta')" icon="edit_note" type="msr"/>

                    <CardBody class="pb-0">


                        <TextEditor v-model="shop.description" :label="$t('description')"/>

                        <Row>
                            <Col lg="6">

                                <FileUpload :default-files="logo_helper.defaultFiles" :max-files="1"
                                            :placeholder="$t('logo')+' - '+$t('square')" :validator="logoValidator"
                                            preview-as-list
                                            height="82"
                                            show-file-manager
                                            :errors="errors"
                                            :label="$t('logo')" name="logo"
                                            v-on:file-added="logo_helper.onFileUpload"
                                            v-on:file-removed="logo_helper.onFileRemoved"/>

                            </Col>
                            <Col lg="6">

                                <FileUpload :default-files="cover_image_helper.defaultFiles" :max-files="1"
                                            :placeholder="$t('cover')+' - '+$t('landscape')"
                                            height="82"
                                            preview-as-list
                                            :validator="coverImageValidator"
                                            show-file-manager
                                            :errors="errors"
                                            :label="$t('cover_image')" name="cover_image"
                                            v-on:file-added="cover_image_helper.onFileUpload"
                                            v-on:file-removed="cover_image_helper.onFileRemoved"/>


                            </Col>
                        </Row>

                    </CardBody>
                </Card>
            </Col>

            <Col xxl="6">
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

            <Col xxl="6">
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
                                            :errors="errors"
                                            name="zone_id"/>
                            </Col>
                            <Col lg="7">
                                <FormInput v-model="shop.address" :errors="errors"
                                           :label="$t('address')"
                                           name="address" required/>
                            </Col>
                        </Row>

                        <Row>
                            <Col lg="6">
                                <FormInput v-model="shop.city" :errors="errors"
                                           :label="$t('city')"
                                           name="city" required/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="shop.state" :errors="errors"
                                           :label="$t('state')"
                                           name="state" required/>
                            </Col>
                        </Row>

                        <Row>
                            <Col lg="6">
                                <FormInput v-model="shop.country" :errors="errors"
                                           :label="$t('country')"
                                           name="country" required/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="shop.pincode" :errors="errors"
                                           :label="$t('pincode')"
                                           name="pincode" required type="number"/>
                            </Col>
                        </Row>

                        <Row>
                            <Col lg="6">

                                <FormInput v-model="location.latitude" :errors="errors" :label="$t('latitude')"
                                           :placeholder="$t('enter_a_latitude_or_pick_from_map')"
                                           name="latitude" required
                                           step="0.00001" type="number" v-on:onchange="onLatitudeChange"/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="location.longitude" :errors="errors"
                                           :label="$t('longitude')"
                                           :placeholder="$t('enter_a_longitude_or_pick_from_map')"
                                           name="longitude"
                                           required
                                           step="0.00001" type="number" v-on:onchange="onLongitudeChange"/>
                            </Col>
                        </Row>


                    </CardBody>
                </Card>
            </Col>

        </Row>
        <div class="text-end mb-3">
            <UpdateButton :loading="loading" @click="update"/>
        </div>

    </PageLoading>
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
import {IShop, Shop} from "@js/types/models/shop";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {ISeller} from "@js/types/models/seller";
import {IBreadcrumb, IFormSelectOption, ILocalFile} from "@js/types/models/models";
import GoogleMap from "@js/components/maps/GoogleMap.vue";
import DeliveryTimeTypeMixin from "@js/shared/mixins/DeliveryTimeTypeMixin";
import {IData} from "@js/types/models/data";
import {IModule} from "@js/types/models/module";
import {array_add_unique, array_get_from_id} from "@js/shared/array_utils";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {IZone} from "@js/types/models/zone";
import {IShopPlan} from "@js/types/models/shop_plan";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import {RequestCache} from "@js/services/api/cache";
import NavigatorService from "@js/services/navigator_service";

export default defineComponent({
    components: {
        UpdateButton,
        GoogleMap,
        Layout, ...Components
    },
    mixins: [FormMixin, UtilMixin, DeliveryTimeTypeMixin, DiscountTypeMixin],
    props: {
        id: {
            type: Number,
            required: true
        },
    },
    data() {
        return {
            loading: false,
            page_loading: true,
            shop: {} as IShop,
            logo_helper: new FileUploadHelper({max: 1}),
            cover_image_helper: new FileUploadHelper({max: 1}),
            location: {
                latitude: null,
                longitude: null
            },
            sellers: [] as ISeller[],
            zones: [] as IZone[],
            shop_plans: [] as IShopPlan[],
            selected_owner: null,
        }
    },
    computed: {
        getSelectedZone() {
            if (this.shop.zone_id != null && this.zones != null) {
                return array_get_from_id(this.zones, this.shop.zone_id);
            }
        },
        seller_helper(): IFormSelectOption {
            return {
                option: {
                    label: (item: ISeller) => item.first_name + " " + item.last_name,
                    value: 'id'
                },
            };
        },

        zone_helper(): IFormSelectOption<IModule> {
            return {
                option: {
                    label: 'name',
                    value: 'id'
                },
            };
        },
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
        async onLogoRemoved(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(adminAPI.shops.remove_logo(this.id));
                    if (response.success()) {
                        this.logo_helper.removeFile(file);
                        ToastNotification.success(this.$t('image_deleted'));
                    } else {
                        ToastNotification.error(this.$t('product_image_is_not_deleted'));
                    }
                } catch (error) {
                    handleException(error);
                }
            } else {
                this.logo_helper.removeFile(file);
            }
        },
        async onCoverRemoved(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(adminAPI.shops.remove_cover_image(this.id));
                    if (response.success()) {
                        this.cover_image_helper.removeFile(file);
                        ToastNotification.success(this.$t('image_deleted'));
                    } else {
                        ToastNotification.error(this.$t('product_image_is_not_deleted'));
                    }
                } catch (error) {
                    handleException(error);
                }
            } else {
                this.cover_image_helper.removeFile(file);
            }
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
        onOwnerChange(owner) {
            this.selected_owner = owner;
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
        async update() {
            this.loading = true;
            try {
                const response = await Request.patchAuth(adminAPI.shops.update(this.id), {
                    ...this.shop, ...this.location,
                    owner_id: this.selected_owner,
                    logo: this.logo_helper.getImageDataFile(),
                    cover_image: this.cover_image_helper.getImageDataFile()
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('shop_is_updated'));
                    RequestCache.removeSimilar(adminAPI.shops.get);
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
        this.setTitle(this.$t('edit_shop'));
        try {

            const sellerResponse = await Request.getAuth<IData<ISeller[]>>(adminAPI.sellers.available_owners);
            if (sellerResponse.success()) {
                this.sellers = sellerResponse.data.data;
            }

            const zoneResponse = await Request.getAuth<IData<IZone[]>>(adminAPI.zones.get);
            if (zoneResponse.success()) {
                this.zones = zoneResponse.data.data;
            }
            const shopPlanResponse = await Request.getAuth<IData<IShopPlan[]>>(adminAPI.shop_plans.get);
            if (shopPlanResponse.success()) {
                this.shop_plans = shopPlanResponse.data.data;
            }
            const response = await Request.getAuth<IData<IShop>>(adminAPI.shops.show(this.id));
            if (response.success()) {
                const shop = response.data.data
                this.location = {
                    latitude: shop.latitude,
                    longitude: shop.longitude
                };
                let shop_owner = Shop.getOwner(shop);
                this.logo_helper.addDefaultFile({url: shop.logo});
                this.cover_image_helper.addDefaultFile({url: shop.cover_image});
                this.selected_module = shop.module_id;
                if (shop_owner != null) {
                    this.sellers = array_add_unique(this.sellers, shop_owner);
                    this.selected_owner = shop_owner.id;
                }
                this.shop = shop;
                this.page_loading = false;
            } else {
                ToastNotification.unknownError();
            }

        } catch (error) {
            handleException(error);
        }
        this.page_loading = false;
    }
});

</script>
