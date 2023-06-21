<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('import_products')"/>

        <PageLoading :loading="shops_loading">
            <Row align-items="center" justify-content="center">
                <Col :class="[{'area-disabled opacity-50': !can_upload}]" lg="6">
                    <Card>
                        <CardHeader :title="$t('import_product')" icon="backup"/>
                        <CardBody>
                            <FormSelect :data="shops" :errors="errors" :helper="shop_helper"
                                        :label="$t('shop')"
                                        :onSelected="(e)=>this.selected_shop=e"
                                        :placeholder="$t('select_shop_to_continue')"
                                        :selected="selected_shop"
                                        name="shop_id"/>

                            <FileUpload :accepted-files="file_type" :errors="errors"
                                        :max-files="1"
                                        :placeholder="$t('only_xlsx_allowed')"
                                        :validator="fileValidator"
                                        name="file"
                                        no-label
                                        preview-as-list
                                        v-on:file-added="file_helper.onFileUpload"
                                        v-on:file-removed="file_helper.onFileRemoved"/>

                            <div class="d-flex align-items-center justify-content-between">
                                <p class="mb-0"><span class="fw-medium text-danger">{{ $t('note') }}:</span>
                                    <span class="ms-1">{{ $t('use_only_our_product_template') }}
                            </span>

                                </p>

                                <div>
                                    <Button class="me-2" color="purple" flexed-icon variant="outline"
                                            @click="downloadTemplate">
                                        <Icon class="me-1" icon="download" size="sm"></Icon>
                                        {{ $t('download_template') }}
                                    </Button>
                                    <LoadingButton :loading="loading" flexed-icon icon="fact_check"
                                                   @click="validateFile">
                                        {{ $t('validate_data') }}
                                    </LoadingButton>
                                </div>
                            </div>
                        </CardBody>
                    </Card>
                </Col>

                <Col v-if="validated_data.length>0" lg="12">
                    <Card>
                        <CardHeader :title="$t('check_before_actual_update')" icon="fact_check">
                            <div class="text-end">
                                <Button class="me-2" color="red" variant="soft" @click="clearData">
                                    <Icon class="me-1-5" icon="cancel"/>
                                    {{ $t('cancel') }}
                                </Button>
                                <LoadingButton :loading="loading" color="primary" icon="publish"
                                               variant="fill" @click="importFile" flexed-icon>
                                    {{ $t('confirm') }} & {{ $t('export') }}
                                </LoadingButton>
                            </div>
                        </CardHeader>
                        <Table :data="validated_data" :loading="loading" :option="table_header">
                            <template #image="data">
                                <template v-if="data.value">
                                    <NetworkImage :src="data.value" :height="50"></NetworkImage>
                                </template>
                                <template v-else>-</template>
                            </template>
                        </Table>
                    </Card>

                </Col>
            </Row>
        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI, sellerAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {handleException} from "@js/services/api/handle_exception";
import {Components} from "@js/components/components";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import {IProductImport} from "@js/types/models/product";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import {FileService} from "@js/services/file_service";
import Table from "@js/components/Table.vue";
import {ToastNotification} from "@js/services/toast_notification";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";
import {IShop, Shop} from "@js/types/models/shop";
import {IData} from "@js/types/models/data";
import AdminModuleSelectorMixin from "@js/shared/mixins/AdminModuleSelectorMixin";

export default defineComponent({
    components: {
        Table,
        ...Components,
        Layout
    },
    mixins: [FormMixin, UtilMixin, AdminModuleSelectorMixin],
    data() {
        const breadcrumb_items = [
            {
                text: this.$t("products"),
                route: 'seller.products.index',
            },
            {
                text: this.$t('import'),
                active: true,
            },
        ] as IBreadcrumb[];
        const file_type = FileService.types.xlsx;
        const file_helper = new FileUploadHelper({max: 1});


        const shop_helper = Shop.select_helper();

        return {
            shop_helper,
            breadcrumb_items,
            file_type,
            file_helper,

            loading: false,
            shops_loading: true,
            validated_data: [] as IProductImport[],
            can_upload: true,
            shops: [] as IShop[],
            selected_shop: null,


        }
    },
    computed: {

        table_header(): ITableOption<IProductImport> {
            return {
                columns: [
                    {
                        label: this.$t('image'),
                        field: 'image',
                    },
                    {
                        label: this.$t('name'),
                        field: 'name',
                        sort: true,
                        onCopy: (product) => product.name,

                    },

                    {
                        label: this.$t('sub_category'),
                        field: 'sub_category',
                        sort: true,
                        data: (product) => product.sub_category.name,

                    },


                    {
                        label: this.$t('price'),
                        field: 'price',
                        sort: true,
                        data: (product) => product.price,

                    },

                    {
                        label: this.$t('stock'),
                        field: 'stock',
                        sort: true,
                        data: (product) => product.stock,

                    },
                    {
                        label: this.$t('unit'),
                        field: 'unit',
                        sort: true,
                        data: (product) => product.unit?.title ?? product.unit_id,

                    },
                    {
                        label: this.$t('unit_title'),
                        field: 'unit_title',
                        sort: true,
                        data: (product) => product.unit_title,

                    },
                    {
                        label: this.$t('sku'),
                        field: 'sku',
                        sort: true,
                        data: (product) => product.sku,

                    },
                    {
                        label: this.$t('barcode'),
                        field: 'barcode',
                        sort: true,
                        data: (product) => product.barcode,
                    },
                ],
                removeTopAction: true,
                placeholder: {
                    message: this.$t('there_is_no_any_products')
                }
            }
        },


    },
    methods: {
        fileValidator(file: File): boolean {
            this.clearAllError();
            if (FileService.verifyFileType(file, FileService.extensions.xlsx)) {
                return true;
            }
            this.addError('file', "Please upload xlsx file only");
            return false;
        },
        downloadTemplate() {
            FileService.downloadImportProductDummyTemplate()
        },
        clearData() {
            this.can_upload = true;
            this.validated_data = [];
        },
        async validateFile() {
            const zip = this.file_helper.getFile();

            if (this.selected_shop == null) {
                ToastNotification.error(this.$t('please_select_shop'));
                return;
            }
            if (zip == null) {
                this.addError('file', this.$t('please_upload_xlsx_file'));
                return;
            }
            this.loading = true;

            const fd = new FormData();
            fd.append('fileName', 'fileName');
            fd.append('file', zip);
            fd.append('shop_id', this.selected_shop);
            fd.append('mimeType', zip.type);

            try {
                let url = adminAPI.products.validate_import_data;
                const response = await Request.postAuth<IProductImport[]>(url, fd);
                if (response.success()) {
                    this.validated_data = response.data;
                    this.can_upload = false;
                    // RequestCache.removeSimilar(adminAPI.products.get);
                    // NavigatorService.goBackOrRoute({name: 'admin.products.index'})
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            } finally {

            }
            this.loading = false;
        },
        async importFile() {
            const zip = this.file_helper.getFile();

            if (zip == null) {
                this.addError('file', this.$t('please_upload_xlsx_file'));
                return;
            }
            this.loading = true;

            const fd = new FormData();
            fd.append('fileName', 'fileName');
            fd.append('file', zip);
            fd.append('mimeType', zip.type);

            try {
                let url = sellerAPI.products.import_data;
                const response = await Request.postAuth<IProductImport[]>(url, fd);
                if (response.success()) {
                    ToastNotification.success(this.$t('products_are_imported'))
                    RequestCache.removeSimilar(adminAPI.products.get);
                    NavigatorService.goBackOrRoute({name: 'seller.products.index'});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            } finally {

            }
            this.loading = false;
        },
        onChangeAdminModule() {
            this.getShops(true);
        },
        async getShops(forced = false) {
            try {
                const response = await Request.getAuth<IData<IShop[]>>(Request.addAdminModule(adminAPI.shops.get), {forced: forced});
                this.shops = response.data.data;
                this.shops_loading = false;
            } catch (error) {
                handleException(error);
            }
        }

    },
    async mounted() {
        this.setTitle(this.$t('import_products'));
        await this.getShops();

    }

});

</script>
