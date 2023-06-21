<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_banner')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xxl="6" xl="8">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" noSpacing/>
                            </div>
                        </CardHeader>
                        <CardBody>

                            <FileUpload :default-files="null" :height=126
                                        :max-files=1
                                        :on-added="images_helper.onFileAdded"
                                        :on-removed="images_helper.onFileRemoved"
                                        :validator="fileValidator"
                                        :label="$t('image')"
                                        show-file-manager
                                        preview-as-list
                            />
                            <FormValidationError :errors="errors" name="image"></FormValidationError>

                            <Row class="mt-3 align-items-center">
                                <Col>
                                    <FormSelect :data="shops" :helper="shop_helper"
                                                :onSelected="onChangeShop"
                                                :errors="errors"
                                                :label="$t('select_shop')" name="shop_id"
                                                :placeholder="$t('select_shop')"
                                                :selected="selected_shop"/>
                                </Col>

                                <Col lg="1">
                                    <div class="text-center fw-semibold text-muted">Or</div>
                                </Col>

                                <Col>
                                    <FormSelect :data="products" :helper="product_helper"
                                                :onSelected="onChangeProduct"
                                                :errors="errors"
                                                :placeholder="$t('select_product')"
                                                :label="$t('select_product')" name="product_id"
                                                :selected="selected_product"/>

                                </Col>

                            </Row>

                            <div class="text-end">
                                <CreateButton :loading="loading" @click="create"/>
                            </div>


                        </CardBody>

                    </Card>

                </Col>

            </Row>


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
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import ChargeTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {IData} from "@js/types/models/data";
import {IShop, Shop} from "@js/types/models/shop";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Product, {IProduct} from "@js/types/models/product";
import {IBreadcrumb} from "@js/types/models/models";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        CreateButton,
        DateTimePicker,
        ...Components, Layout
    },
    mixins: [FormMixin, ChargeTypeMixin, UtilMixin],
    data() {
        const shop_helper= Shop.select_helper();

        return {
            shop_helper,
            loading: false,
            page_loading: true,
            active: true,
            shops: [] as IShop[],
            products: [] as IProduct[],
            images_helper: new FileUploadHelper(),
            selected_shop: null,
            selected_product: null
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('banners'),
                    route: 'admin.home_banners.index',
                },
                {
                    text: this.$t("create"),
                    active: true,
                },
            ];
        },
        product_helper: Product.select_helper

    },
    methods: {
        onChangeShop(id) {
            if (id != null) {
                this.selected_shop = id;
                this.selected_product = null;
            }
        },
        onChangeProduct(id) {
            if (id != null) {
                this.selected_product = id;
                this.selected_shop = null;
            }
        },
        fileValidator(file): boolean {
            this.errors = {};
            if (file.height == 2000 && file.width == 4000) {
                return true;
            }
            this.addError('image', "Please upload image with 4000*2000 dimension");
        },

        async create() {
            this.errors = {};
            if (this.images_helper.getFiles().length == 0) {
                this.addError('image', "Please upload banner");
                return;
            }
            this.loading = true;

            try {
                const response = await Request.postAuth(adminAPI.home_banners.create, Request.getClearBody({
                    active: this.active,
                    image: this.images_helper.getImageDataFile(),
                    shop_id: this.selected_shop,
                    product_id: this.selected_product
                }));
                if (response.success()) {
                    ToastNotification.success(this.$t('banner_is_created'));
                    RequestCache.removeSimilar(adminAPI.home_banners.get);
                    NavigatorService.goBackOrRoute({name: 'admin.home_banners.index'});
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            } finally {
                this.loading = false;
            }
        }
    },
    async mounted() {
        this.setTitle(this.$t('create_home_banner'));

        try {
            const response = await Request.getAuth<IData<IShop[]>>(adminAPI.shops.get);
            this.shops = response.data.data;
            const productResponse = await Request.getAuth<IData<IProduct[]>>(adminAPI.products.get);
            this.products = productResponse.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
})

</script>
