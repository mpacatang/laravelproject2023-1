<template>
    <Layout>
        <PageLoading :loading="page_loading">
            <PageHeader :items="breadcrumb_items" :title="$t('edit_product')"/>

            <Row>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('variant')" icon="extension"></CardHeader>
                        <CardBody class="pb-0">

                            <FormSelect :data="variant_products" :errors="errors" :helper="variant_product_helper"
                                        :label="$t('product')"
                                        :onSelected="(e)=>product.product_variant_id=e"
                                        :placeholder="$t('connect_with_any_your_product')"
                                        :selected="product.product_variant_id"
                                        name="variant_product_id">
                                <template #label-suffix>
                                     <span
                                         class="text-muted fw-medium font-12">[{{
                                             $t('attach_this_product_with_some_other_product')
                                         }}]</span>
                                </template>
                            </FormSelect>
                        </CardBody>
                    </Card>
                    <Card>

                        <CardHeader :title="$t('product')" icon="dns">
                            <div class="float-end">
                                <FormSwitch v-model="product.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" noSpacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInput v-model="product.name" :errors="errors"
                                       :label="$t('product_name')" :placeholder="$t('product_name')" name="name"/>


                            <FormSelect :data="categories" :errors="errors" :helper="category_select_helper"
                                        :label="$t('sub_category')"
                                        :onSelected="(e)=>product.category_id=e"
                                        :placeholder="$t('select_sub_category')" :selected="selected_category"
                                        grouped
                                        name="sub_category_id"/>


                            <FormSelect v-if="canChangeAddon" :data="addons" :errors="errors" :helper="addon_helper"
                                        :onSelected="onAddonsChange" :placeholder="$t('select_addon_if_you_want')"
                                        :selected="selected_addons" multiple
                                        :no-choice-text="$t('there_is_no_addons_in_the_shop')"

                                        :label="$t('addons')"
                                        name="addons"/>


                        </CardBody>
                    </Card>
                </Col>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('meta')" icon="edit_note"/>

                        <CardBody>

                            <Row>
                                <Col v-if="changePrescription" lg="6" class="mb-3">
                                    <FormSwitch v-model="product.need_prescription"
                                                :errors="errors"
                                                :label="$t('need_prescription')"
                                                name="need_prescription" no-spacing></FormSwitch>

                                </Col>
                                <Col v-if="changeFoodType" lg="6"  class="mb-3">
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
                                <Col lg="6" v-if="changeAvailability"  class="mb-3">
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
                                                :label="$t('unit_type')"
                                                :placeholder="$t('select_unit_type_if_you_want')"
                                                :selected="selected_unit"
                                                :onSelected="(e)=>product.unit_id=e"
                                                name="unit_type_id"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="product.unit_title" :errors="errors"
                                               :label="$t('unit_title')" :placeholder="$t('unit_title')"
                                               name="unit_title"/>
                                </Col>

                            </Row>

                            <FileUpload :default-files="images_helper.defaultFiles"
                                        height="126"
                                        show-file-manager
                                        :label="$t('images')"
                                        v-on:file-added="images_helper.onFileUpload" v-on:file-removed="onFileRemoved"
                            />

                            <div class="text-end mt-3">
                                <UpdateButton :loading="loading" @click="updateProduct"/>
                            </div>

                        </CardBody>
                    </Card>

                </Col>



                <Col lg="12">
                    <Card>
                        <CardHeader :title="$t('options')" icon="amp_stories"/>

                        <CardBody>

                            <ul class="nav  nav-pills arrow-navtabs bg-light">
                                <li v-for="(product_option, index) in product.options" :key="product_option.id"
                                    class="nav-item"
                                    role="presentation">
                                    <a :class="['nav-link px-3', {'active': index===0}]"
                                       :href="'#option_'+product_option.id"
                                       data-bs-toggle="tab" role="tab"
                                       tabindex="-1">{{ product_option.option_value ?? "-" }}
                                        <InfoTooltip v-if="optionHasLowStock(product_option)"
                                                     :tooltip="$t('low_stock_alert')"
                                                     class="ms-1 text-orange"></InfoTooltip>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a aria-expanded="false" aria-selected="false" class="nav-link px-3"
                                       data-bs-toggle="tab" href="#option_new" role="tab" tabindex="-1">
                                        {{ $t('new') }} {{ product.unit_title ?? $t('option') }}
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <Col v-for="(product_option, index) in product.options"
                                     :id="'option_'+product_option.id"
                                     :key="product_option.id"
                                     :class="['tab-pane', {'active': index===0}]" role="tabpanel">
                                    <Row>
                                        <Col lg="3">
                                            <FormInput v-model="product_option.option_value" :errors="errors"
                                                       :label="$t('option')"
                                                       min="0"
                                                       name="option_value" type="text">
                                                <template #suffix>
                                                    <span>{{ product.unit_title ?? $t('option') }}</span>
                                                </template>
                                            </FormInput>
                                        </Col>
                                        <Col lg="9">
                                            <Row>

                                                <Col lg="4">
                                                    <FormInput v-model="product_option.price" :errors="errors"
                                                               :label="$t('price')"
                                                               min="0"
                                                               name="price" required
                                                               show-currency type="number">

                                                    </FormInput>
                                                </Col>
                                                <Col lg="4">
                                                    <FormInput v-model="product_option.discount" :errors="errors"
                                                               :label="$t('discount')"
                                                               min="0"
                                                               name="discount"
                                                               :prefix-or-suffix="product_option.discount_type"

                                                               type="number">
                                                    </FormInput>

                                                </Col>
                                                <Col lg="4">

                                                    <FormSelect :data="charge_types" :helper="charge_type_helper"
                                                                :onSelected="(value)=>product_option.discount_type=value"
                                                                :placeholder="$t('discount_type')"
                                                                :label="$t('discount_type')"
                                                                :selected="product_option.discount_type"/>
                                                </Col>
                                                <Col lg="4"  v-if="canChangeStock">
                                                    <FormInput v-model="product_option.stock" :errors="errors"
                                                               :label="$t('stock')"
                                                               min="0"
                                                               name="stock" required
                                                               type="number"/>


                                                </Col>
                                                <Col lg="4">
                                                    <FormInput v-model="product_option.sku" :errors="errors"
                                                               :label="$t('SKU')"
                                                               :placeholder="$t('unique_sku')" name="sku" required/>

                                                </Col>
                                                <Col lg="4">
                                                    <FormInput v-model="product_option.barcode" :errors="errors"
                                                               :label="$t('barcode')"
                                                               :placeholder="$t('unique_barcode_(if_you_want)')"
                                                               name="barcode"/>

                                                </Col>


                                            </Row>
                                        </Col>
                                        <Col>
                                            <div class="text-end">
                                                <LoadingButton  v-if="canChangeStock" class="me-2" color="secondary" icon="block"
                                                               icon-size="18" variant="soft"
                                                               @click="outOfStockOption(product_option)">
                                                    {{ $t('set_out_of_stock') }}
                                                </LoadingButton>
                                                <UpdateButton :loading="loading"
                                                              @click="updateOption(product_option)"/>
                                            </div>
                                        </Col>
                                    </Row>
                                </Col>

                                <div id="option_new"
                                     class="tab-pane" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <FormInput v-model="new_product_option.option_value" :errors="errors"
                                                       :label="$t('option')"
                                                       min="0"
                                                       name="option_value" type="text">
                                                <template #suffix>
                                                    <span>{{ product.unit_title ?? $t('option') }}</span>
                                                </template>
                                            </FormInput>
                                        </div>
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
                                                <Col lg="4" v-if="canChangeStock">
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
                                        <div class="col">
                                            <div class="text-end">
                                                <CreateButton :loading="loading" @click="createOption"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </CardBody>
                    </Card>

                </Col>


            </Row>

            <VModal v-if="product" v-model="showing_time_modal" close-btn>
                <Card class="mb-0">
                    <CardHeader :title="$t('set_availability_for_this_product')"/>
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

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {IProductOption} from "@js/types/models/product_option";
import {IAddon} from "@js/types/models/addon";
import Product, {IProduct} from "@js/types/models/product";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {IData} from "@js/types/models/data";
import {ICategory} from "@js/types/models/category";
import {IBreadcrumb, IFormSelectOption, ILocalFile} from "@js/types/models/models";
import {Components} from "@js/components/components";
import {IUnit} from "@js/types/models/unit";
import VModal from "@js/components/VModal.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Button from "@js/components/buttons/Button.vue";
import {IModule, Module} from "@js/types/models/module";
import FoodTypeMixin from "@js/shared/mixins/FoodTypeMixin";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import {defineComponent} from "vue";
import {RequestCache} from "@js/services/api/cache";


export default defineComponent({
    components: {
        DateTimePicker,
        CreateButton,
        UpdateButton,
        Button,
        VModal,
        ...Components, Layout,
    },
    mixins: [FormMixin, DiscountTypeMixin, UtilMixin, FoodTypeMixin],
    data() {
        return {
            id: this.$route.params.id,
            page_loading: true,
            loading: false,
            showing_time_modal: false,
            product: {} as IProduct,
            variant_products: [] as IProduct[],
            new_product_option: {
                stock:100,
            } as IProductOption,
            images_helper: new FileUploadHelper(),
            addons: [] as IAddon[],
            selected_addons: [] as number[],
            categories: [] as ICategory[],
            selected_category: null as number,
            units: [] as IUnit[],
            selected_unit: null,
            module: {} as IModule

        }
    },
    computed: {
        canChangeStock(){
          return Module.canChangeProductStock(this.module);
        },
        canChangeAddon(){
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
        category_select_helper(): IFormSelectOption {
            return {
                option: {
                    value: 'id',
                    label: 'name'
                },
                optgroup: {
                    label: 'name',
                    options: 'sub_categories'
                }
            };
        },
        addon_helper(): IFormSelectOption {
            return {
                option: {
                    label: 'name',
                    value: 'id'
                }
            };
        },
        unit_helper(): IFormSelectOption {
            return {
                option: {
                    label: 'title',
                    value: 'id'
                }
            };
        },
        variant_product_helper(): IFormSelectOption {
            return {
                option: {
                    label: 'name',
                    value: 'product_variant_id'
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("products"),
                    route: 'admin.products.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        },

    },
    methods: {
        optionHasLowStock: Product.optionHasLowStock,

        onAddonsChange(addons: number[]) {
            this.selected_addons = addons;
        },


        showTimeModal() {
            this.showing_time_modal = true;
        },
        hideTimeModal() {
            this.showing_time_modal = false;
        },
        async removeAvailability() {
            this.loading = true;
            try {
                const response = await Request.patchAuth<IData<IProduct>>(adminAPI.products.remove_availability(this.product.id), {});
                if (response.success()) {
                    this.product = response.data.data;
                    ToastNotification.success(this.$t('availability_removed'), true);
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    ToastNotification.unknownError();
                }
            }
            this.loading = false;
        },
        async onFileRemoved(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(adminAPI.product_images.delete(file.id));
                    if (response.success()) {
                        this.images_helper.removeFile(file);
                        ToastNotification.success('Product image deleted');
                    } else {
                        ToastNotification.error('Product image is not deleted');
                    }
                } catch (error) {
                    handleException(error);
                }
            } else {
                this.images_helper.removeFile(file);
            }
        },

        async updateProduct() {
            this.loading = true;
            try {


                let body = {
                    ...this.product,
                    sub_category_id: this.selected_category,
                    addons: this.selected_addons,
                    images: this.images_helper.getImageDataFiles(),
                    unit_id: this.selected_unit
                }

                body = Request.getClearBody(body);

                const response = await Request.patchAuth(adminAPI.products.update(this.product.id), body);

                if (response.success()) {
                    ToastNotification.success(this.$t('product_is_updated'), true);
                    RequestCache.removeSimilar(adminAPI.products.get);
                    NavigatorService.goBackOrRoute({name: 'admin.products.index'});
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
        },
        async updateOption(product_option: IProductOption) {
            try {
                await Request.patchAuth(adminAPI.product_options.update(product_option.id), product_option);
                ToastNotification.success(this.$t('option_updated'), true);

            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }

        },
        async createOption() {
            try {
                const response = await Request.postAuth<IProductOption>(adminAPI.product_options.create, {
                    ...this.new_product_option,
                    'product_id': this.id
                });
                this.product.options.push(response.data);
                this.new_product_option = {stock:100};
                ToastNotification.success(this.$t('option_created'));
            } catch (error) {
                    if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },

        async outOfStockOption(product_option: IProductOption) {
            product_option.stock = 0;
            return this.updateOption(product_option)
        }
    },
    async mounted() {
        this.setTitle(this.$t('edit_product'));
        try {
            const response = await Request.getAuth<IData<IProduct>>(adminAPI.products.show(this.id));

            if (response.success()) {
                let product = response.data.data;
                this.selected_category = product.sub_category_id;
                this.selected_addons = product?.addons?.map((addon) => addon.id) ?? [];
                this.product = product;
                this.selected_unit = product.unit_id;

                if (product.images && product.images.length > 0) {
                    for (const product_image of product.images) {
                        this.images_helper.addDefaultFile({url: product_image.image, id: product_image.id});
                    }
                }
                this.module = product.shop.module;
                const categoryResponse = await Request.getAuth<IData<ICategory[]>>(adminAPI.shops.categories(product.shop_id));
                this.categories = categoryResponse.data.data;
                const addonResponse = await Request.getAuth<IData<IAddon[]>>(adminAPI.shops.addons(product.shop_id));
                this.addons = addonResponse.data.data;
                const unitResponse = await Request.getAuth<IData<IUnit[]>>(adminAPI.units.get);
                this.units = unitResponse.data.data;

                const productsResponse = await Request.getAuth<IData<IProduct[]>>(adminAPI.products.get);
                this.variant_products = productsResponse.data.data;

                this.page_loading = false;

            } else {
                ToastNotification.unknownError();
            }
        } catch (error) {
            handleException(error);
        }
    }
});


</script>
