<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_addon')"/>

        <PageLoading :loading="page_loading">

            <Row>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="addon.active" :checked="addon.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" noSpacing/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <FormSelect :data="shops" :errors="errors" :helper="shop_helper"
                                        :label="$t('select_shop')"
                                        :onSelected="(e)=>addon.shop_id=e"
                                        :placeholder="$t('select_shop')"
                                        :selected="addon.shop_id"
                                        name="shop_id"/>

                            <Row class="mt-3">
                                <Col lg="6">
                                    <FormInput v-model="addon.name" :errors="errors"
                                               :label="$t('name')" :placeholder="$t('enter_addon_name')" name="name"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="addon.price" :errors="errors" :label="$t('price')"
                                               min="0" name="price" show-currency type="number">
                                    </FormInput>
                                </Col>
                            </Row>


                            <TextEditor v-model="addon.description" :label="$t('description')"/>

                            <FileUpload :height=150
                                        :label="$t('image')" :max-files="1"
                                        preview-as-list
                                        show-file-manager
                                        v-on:file-added="image_helper.onFileUpload"
                                        v-on:file-removed="image_helper.onFileRemoved"/>

                            <div class="text-end mt-3">
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
import {IAddon} from "@js/types/models/addon";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import {IShop, Shop} from "@js/types/models/shop";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        CreateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        const shop_helper= Shop.select_helper();

        return {
            shop_helper,
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            addon: {
                active: true
            } as IAddon,
            image_helper: new FileUploadHelper(),
            shops: [] as IShop[],

        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('addons'),
                    route: 'admin.addons.index',
                },
                {
                    text: this.$t('create'),
                    active: true,
                },
            ];
        },
    },
    methods: {
        async create() {
            this.loading = true;
            try {
                const body = Request.getClearBody({
                    ...this.addon, 'image': this.image_helper.getImageDataFile()
                });
                const response = await Request.postAuth(adminAPI.addons.create, body);
                if (response.success()) {
                    ToastNotification.success(this.$t('addon_created'));
                    RequestCache.removeSimilar(adminAPI.addons.get);
                    NavigatorService.goBackOrRoute({name: 'admin.addons.index'});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error)
                }
            }
            this.loading = false;
        },

    },
    async mounted() {
        this.setTitle(this.$t('create_addons'))
        const response = await Request.getAuth<IData<IShop[]>>(adminAPI.shops.get);
        this.shops = response.data.data;
        this.page_loading = false;
    }
});

</script>

