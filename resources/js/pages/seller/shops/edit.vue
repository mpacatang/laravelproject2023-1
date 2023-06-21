<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_shop')"/>

        <PageLoading :loading="page_loading">
            <Row>

                <Col xl="5">
                    <Card>
                        <CardHeader :title="$t('general')" icon="storefront" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="shop.active"
                                            :errors="errors" :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody>

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
                                    <FormLabel name="module">{{ $t('module') }}</FormLabel>
                                    <h5 class="fw-medium mt-0">{{ shop.module?.title ?? "-" }}</h5>
                                </Col>

                            </Row>


                        </CardBody>
                    </Card>
                </Col>

                <Col xl="7">

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
                                                :errors="errors"
                                                :label="$t('cover_image')" name="cover_image"
                                                v-on:file-added="cover_image_helper.onFileUpload"
                                                v-on:file-removed="cover_image_helper.onFileRemoved"/>


                                </Col>
                            </Row>

                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
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

                <Col xl="6">
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
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/seller/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import {IShop} from "@js/types/models/shop";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {IBreadcrumb, IFormSelectOption, ILocalFile} from "@js/types/models/models";
import {IData} from "@js/types/models/data";
import {ISeller, Seller} from "@js/types/models/seller";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import GoogleMap from "@js/components/maps/GoogleMap.vue";
import {array_add_all_unique, array_get_from_id} from "@js/shared/array_utils";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import {IZone, Zone} from "@js/types/models/zone";
import {IModule} from "@js/types/models/module";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        UpdateButton,
        ...Components, Layout, GoogleMap
    },
    mixins: [FormMixin, UtilMixin],
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
            selected_sellers: [] as ISeller[],
            zones: [] as IZone[],

        }
    },
    computed: {
        getSelectedZone() {
            if (this.shop.zone_id != null && this.zones != null) {
                return array_get_from_id(this.zones, this.shop.zone_id);
            }
        },
        seller_helper: Seller.select_helper,
        zone_helper: Zone.select_helper,
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("shop"),
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        }
    },
    methods: {
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
        async onLogoRemoved(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(sellerAPI.shops.remove_logo);
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
                    const response = await Request.deleteAuth(sellerAPI.shops.remove_cover_image);
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
        onSellerChange(sellers) {
            this.selected_sellers = sellers;
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
                const response = await Request.patchAuth(sellerAPI.shops.update, {
                    ...this.shop, ...this.location,
                    sellers: this.selected_sellers,
                    logo: this.logo_helper.getImageDataFile(),
                    cover_image: this.cover_image_helper.getImageDataFile()
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('shop_is_edited'));
                    RequestCache.removeSimilar(sellerAPI.shops.show);
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
            const sellerResponse = await Request.getAuth<IData<ISeller[]>>(sellerAPI.sellers.available);
            if (sellerResponse.success()) {
                this.sellers = sellerResponse.data.data;
            } else {
                ToastNotification.unknownError();
            }

            const zoneResponse = await Request.getAuth<IData<IZone[]>>(sellerAPI.zones.get);
            if (zoneResponse.success()) {
                this.zones = zoneResponse.data.data;
            }

            const response = await Request.getAuth<IData<IShop>>(sellerAPI.shops.show);
            if (response.success()) {
                const shop = response.data.data
                this.location = {
                    latitude: shop.latitude,
                    longitude: shop.longitude
                };
                this.selected_sellers = shop.sellers?.map((seller) => seller.id) ?? [];
                this.logo_helper.addDefaultFile({url: shop.logo});
                this.cover_image_helper.addDefaultFile({url: shop.cover_image});

                this.shop = shop;
                this.sellers = array_add_all_unique(this.sellers, shop.sellers);
                this.page_loading = false;
            }
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
