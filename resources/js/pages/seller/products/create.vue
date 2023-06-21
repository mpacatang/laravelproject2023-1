<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="id!=null?$t('create_product_variant'):$t('create_product')"/>

        <PageLoading :loading="page_loading">

            <Row>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('variant')" icon="extension" type="msr"></CardHeader>
                        <CardBody class="pb-0">

                            <FormSelect :data="variant_products" :errors="errors" :helper="variant_product_helper"
                                        :label="$t('product')"
                                        :placeholder="$t('connect_with_any_your_product')"
                                        :selected="product.product_variant_id"
                                        :onSelected="(e)=>product.product_variant_id=e"
                                        name="variant_product_id"/>
                        </CardBody>
                    </Card>
                    <Card>
                        <CardHeader :title="$t('main_product')" icon="dns">
                            <div class="float-end">
                                <FormSwitch v-model="product.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInput v-model="product.name" :errors="errors"
                                       :label="$t('product_name')" :placeholder="$t('product_name')" name="name"/>

                            <FormSelect :data="categories" :errors="errors" :helper="category_select_helper"
                                        :label="$t('sub_category')"
                                        :placeholder="$t('select_sub_category')" :selected="selected_category"
                                        :onSelected="(e)=>product.sub_category_id=e"
                                        grouped
                                        required
                                        name="sub_category_id"/>


                            <FormSelect v-if="canChangeAddon" :data="addons" :errors="errors" :helper="addon_helper"
                                        :label="$t('addons')"
                                        :no-choice-text="$t('there_is_no_addons_in_the_shop')"
                                        :selected="selected_addons" multiple
                                        :onSelected="onAddonsChange"
                                        :placeholder="$t('select_addon_if_you_want')"
                                        name="addons"/>


                        </CardBody>
                    </Card>


                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('meta')" icon="edit_note"/>

                        <CardBody class="pb-0">

                            <Row>
                                <Col v-if="changePrescription" class="mb-3" lg="6">
                                    <FormSwitch v-model="product.need_prescription"
                                                :errors="errors"
                                                :label="$t('need_prescription')"
                                                name="need_prescription" no-spacing></FormSwitch>

                                </Col>
                                <Col v-if="changeFoodType" class="mb-3" lg="6">
                                    <div class="d-flex align-items-center">
                                        <FormLabel class="mb-0" for="food_type">{{ $t('food_type') }}</FormLabel>
                                        <div class="flex-grow-1 ms-2">
                                            <FormSelect :data="food_types" :helper="food_type_helper"
                                                        :onSelected="(e)=>product.food_type = e"
                                                        :errors="errors"
                                                        :placeholder="$t('select_type')"
                                                        :selected="product.food_type" name="food_type"
                                                        no-label no-spacing/>
                                        </div>
                                    </div>

                                </Col>
                                <Col v-if="changeAvailability" class="mb-3" lg="6">
                                    {{ $t('availability') }} :
                                    <Button class="ms-1" color="primary" size="sm" variant="soft"
                                            flexed-icon @click="showTimeModal">
                                        {{
                                            product.available_from == null ? $t('not_set') :
                                                getFormattedTimeFromTime(product.available_from, {second: false}) + "-" + getFormattedTimeFromTime(product.available_to, {second: false})
                                        }}
                                        <Icon class="ms-1-5" icon="edit" size="16"></Icon>
                                    </Button>
                                    <Icon v-if="product.available_from != null || product.available_to!=null"
                                          class="ms-2 cursor-pointer" color="danger" icon="alarm_off"
                                          size="sm" @click="removeAvailability"/>
                                    <FormValidationError :errors="errors" name="available_from"/>
                                    <FormValidationError :errors="errors" name="available_to"/>
                                </Col>

                            </Row>


                            <TextEditor v-model="product.description" :label="$t('description')"/>

                            <Row class="mt-3">
                                <Col lg="6">
                                    <FormSelect :data="units" :errors="errors" :helper="unit_helper"
                                                :label=" $t('unit_type')"
                                                :onSelected="(e)=>product.unit_id=e"

                                                :placeholder="$t('select_unit_type_if_you_want')"
                                                :selected="product.unit_id"
                                                name="unit_id"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="product.unit_title" :errors="errors"
                                               :label="$t('unit_title')" :placeholder="$t('unit_title')"
                                               name="unit_title"/>
                                </Col>

                            </Row>


                            <FileUpload :default-files="images_helper.defaultFiles"
                                        height="130"
                                        :errors="errors"
                                        :label="$t('images')"

                                        v-on:file-added="images_helper.onFileUpload"
                                        v-on:file-removed="images_helper.onFileRemoved"
                            />
                        </CardBody>
                    </Card>
                </Col>

                <Col lg="12">
                    <Card>
                        <CardHeader :title="$t('options')" icon="amp_stories"/>

                        <CardBody class="pb-0">

                            <ul class="nav  nav-pills arrow-navtabs bg-light" role="tablist">

                                <li class="nav-item" role="presentation">
                                    <a aria-expanded="false" aria-selected="false" class="nav-link px-3 active"
                                       data-bs-toggle="tab" href="#option_new" role="tab" tabindex="-1">
                                        {{ $t('new') }} {{ product.unit_title ?? $t('option') }}
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <div id="option_new"
                                     class="tab-pane active" role="tabpanel">
                                    <Row>
                                        <Col lg="3">
                                            <FormInput v-model="new_product_option.option_value" :errors="errors"
                                                       :label="$t('option')"
                                                       min="0"
                                                       :placeholder="$t('blank') + ' (-)'"
                                                       name="option_value" type="text">
                                                <template #suffix>
                                                    <span>{{ product.unit_title ?? $t('option') }}</span>
                                                </template>
                                            </FormInput>
                                        </Col>
                                        <Col lg="9">
                                            <Row>
                                                <Col lg="4">
                                                    <FormInput v-model="new_product_option.price" :errors="errors"
                                                               :label="$t('price')"
                                                               min="0"
                                                               name="price" required
                                                               show-currency type="number">

                                                    </FormInput>
                                                </Col>
                                                <Col lg="4">
                                                    <FormInput v-model="new_product_option.discount"
                                                               :errors="errors"
                                                               :label="$t('discount')"
                                                               min="0"
                                                               name="discount"
                                                               :prefix-or-suffix="new_product_option.discount_type"
                                                               type="number">
                                                    </FormInput>

                                                </Col>
                                                <Col lg="4">
                                                    <FormSelect :data="charge_types"
                                                                :errors="errors"
                                                                :helper="charge_type_helper"
                                                                :label="$t('discount_type')"
                                                                :onSelected="(e)=>new_product_option.discount_type = e"
                                                                name="discount_type"
                                                                :placeholder="$t('discount_type')"
                                                                :selected="new_product_option.discount_type"/>
                                                </Col>
                                                <Col v-if="canChangeStock" lg="4">
                                                    <FormInput v-model="new_product_option.stock" :errors="errors"
                                                               :label="$t('stock')"
                                                               min="0"
                                                               name="stock" required
                                                               type="number"/>


                                                </Col>
                                                <Col lg="4">
                                                    <FormInput v-model="new_product_option.sku" :errors="errors"
                                                               :label="$t('SKU')"
                                                               :placeholder="$t('unique_sku')" name="sku"/>

                                                </Col>
                                                <Col lg="4">
                                                    <FormInput v-model="new_product_option.barcode" :errors="errors"
                                                               :label="$t('barcode')"
                                                               :placeholder="$t('unique_barcode_(if_you_want)')"
                                                               name="barcode"/>

                                                </Col>

                                            </Row>
                                        </Col>

                                    </Row>
                                </div>
                            </div>

                        </CardBody>
                    </Card>

                    <div class="text-end mb-3">
                        <CreateButton :loading="loading" @click="createProduct"/>
                    </div>

                </Col>


            </Row>


            <VModal v-model="showing_time_modal" close-btn>
                <Card class="mb-0">
                    <CardHeader :title="$t('set_availability_for_this_product')" icon="more_time"/>
                    <CardBody>
                        <DateTimePicker v-model="product.available_from" :errors="errors"
                                        :label="$t('available_from')"
                                        name="available_from" variant="time"/>
                        <DateTimePicker v-model="product.available_to" :errors="errors" :label="$t('available_to')"
                                        name="available_to" variant="time"/>


                        <Button class="float-end" color="primary" @click="hideTimeModal">
                            <Icon class="me-1-5" icon="more_time"></Icon>
                            {{ $t('save') }}
                        </Button>
                    </CardBody>
                </Card>
            </VModal>
        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {IProductOption} from "@js/types/models/product_option";
import {Addon, IAddon} from "@js/types/models/addon";
import {Components} from "@js/components/components";
import Layout from "@js/pages/seller/layouts/Layout.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import {IData} from "@js/types/models/data";
import {ICategory, SubCategory} from "@js/types/models/category";
import {IProduct} from "@js/types/models/product";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {IUnit, Unit} from "@js/types/models/unit";
import {IBreadcrumb, IFormSelectOption} from "@js/types/models/models";
import {IModule, Module} from "@js/types/models/module";
import FoodTypeMixin from "@js/shared/mixins/FoodTypeMixin";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import {useSellerDataStore} from "@js/services/state/states";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        DateTimePicker,
        CreateButton,
        VModal,
        LoadingButton,
        ...Components,
        Layout
    },
    mixins: [FormMixin, DiscountTypeMixin, UtilMixin, FoodTypeMixin],
    data() {
        return {
            id: this.$route.params.id === '' ? null : this.$route.params.id,
            loading: false,
            page_loading: true,
            showing_time_modal: false,
            product: {
                active: true
            } as IProduct,
            variant_products: [] as IProduct[],
            new_product_option: {
                stock: 100
            } as IProductOption,
            images_helper: new FileUploadHelper(),
            categories: [] as ICategory[],
            selected_category: null,
            addons: [] as IAddon[],
            selected_addons: [] as IAddon[],
            units: [] as IUnit[],
            module: {} as IModule
        }
    },
    computed: {
        canChangeStock() {
            return Module.canChangeProductStock(this.module);
        },
        canChangeAddon() {
            return Module.canChangeProductAddon(this.module);
        },
        changePrescription(): boolean {
            return Module.canChangeProductPrescription(this.module);
        },
        changeFoodType(): boolean {
            return Module.canChangeProductFoodType(this.module);
        },
        changeAvailability(): boolean {
            return Module.canChangeProductAvailability(this.module);
        },
        category_select_helper: SubCategory.select_helper,
        addon_helper: Addon.select_helper,
        variant_product_helper(): IFormSelectOption {
            return {
                option: {
                    label: 'name',
                    value: 'product_variant_id'
                }
            };
        },
        unit_helper: Unit.select_helper,
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("products"),
                    route: 'seller.products.index',
                },
                {
                    text: this.$t('create'),
                    active: true,
                },
            ];
        },
    },
    methods: {
        onAddonsChange(addons) {
            this.selected_addons = addons;
        },
        showTimeModal() {
            this.showing_time_modal = true;
        },
        hideTimeModal() {
            this.showing_time_modal = false;
        },
        removeAvailability() {
            this.product.available_from = null;
            this.product.available_to = null;
        },
        async createProduct() {
            this.loading = true;
            try {
                let body = {
                    ...this.product,
                    ...this.new_product_option,
                    addons: this.selected_addons,
                    images: this.images_helper.getImageDataFiles(),
                }
                const response = await Request.postAuth(sellerAPI.products.create, Request.getClearBody(body));
                if (response.success()) {
                    ToastNotification.success(this.$t(' product_is_created'));
                    RequestCache.removeSimilar(sellerAPI.products.get);
                    NavigatorService.goBackOrRoute({name: 'seller.products.index'})

                }
            } catch (error) {
                if(Response.hasShopPlanNeedUpgrade(error)){
                    ToastNotification.show({
                        message:this.$t('you_need_to_upgrade_plan'),
                        type: 'error',
                        indeterminate: true,
                        action:{
                            text: this.$t('upgrade'),
                            callback: ()=> this.$router.push({name: 'seller.shops.plan'})
                        },

                    });
                }
                else if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        },
    },
    async mounted() {
        this.setTitle(this.$t('create_product'));
        try {
            if (this.id != null) {
                const response = await Request.getAuth<IData<IProduct>>(sellerAPI.products.show(this.id));
                if (response.success()) {
                    this.product = response.data.data;
                    this.selected_category = this.product.sub_category_id;
                    this.selected_addons = this.product.addons?.map((addon) => addon.id) ?? [];
                } else {
                    ToastNotification.unknownError();
                }
            }
            const response = await Request.getAuth<IData<IProduct[]>>(sellerAPI.products.get);
            this.variant_products = response.data.data;


            const categoryResponse = await Request.getAuth<IData<ICategory[]>>(sellerAPI.categories.get);
            this.categories = categoryResponse.data.data;
            const addonResponse = await Request.getAuth<IData<IAddon[]>>(sellerAPI.addons.get);
            this.addons = addonResponse.data.data;
            const unitResponse = await Request.getAuth<IData<IUnit[]>>(sellerAPI.units.get);
            this.units = unitResponse.data.data;

            const store = useSellerDataStore();
            this.module = store.module;
            store.$subscribe((mut, state) => {
                this.module = state.module;
            })
            this.page_loading = false;

        } catch (error) {
            handleException(error);
        }
    }

});

</script>
