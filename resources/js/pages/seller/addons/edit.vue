<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_addon')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6" lg="8">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note">
                            <div class="float-end">
                                <FormSwitch v-model="addon.active" noSpacing :checked="addon.active"
                                            :errors="errors"
                                            :label="$t('active')" name="active"/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="addon.name" :errors="errors"
                                               :label="$t('name')" :placeholder="$t('enter_addon_name')" name="name"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="addon.price" :errors="errors" :label="$t('price')"
                                               min="0" name="price" show-currency type="number"/>
                                </Col>
                            </Row>


                            <TextEditor v-model="addon.description" :label="$t('description')"/>

                            <FileUpload :default-files="image_helper.defaultFiles" :height=150
                                        :max-files="1" preview-as-list
                                        :label="$t('image')"
                                        v-on:file-added="image_helper.onFileUpload"
                                        v-on:file-removed="onFileRemoved"/>

                            <div class="text-end mt-3">
                                <UpdateButton :loading="loading" @click="()=>update()"/>
                            </div>

                        </CardBody>
                    </Card>
                </Col>

            </Row>
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
import {IAddon} from "@js/types/models/addon";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import PageLoading from "@js/components/PageLoading.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {ILocalFile} from "@js/types/models/models";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";


export default defineComponent({
    components: {
        UpdateButton,
        PageLoading,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {

        return {
            id: this.$route.params.id,
            loading: false,
            addon: {} as IAddon,
            image_helper: new FileUploadHelper(),
            page_loading: true
        }
    },
    computed: {
        breadcrumb_items() {
            return [
                {
                    text: this.$t('addons'),
                    nameRoute: 'seller.addons.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async onFileRemoved(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(sellerAPI.addons.remove_image(this.id));
                    if (response.success()) {
                        this.image_helper.removeFile(file);
                        ToastNotification.success(this.$t('image_deleted'));
                    } else {
                        ToastNotification.error(this.$t('addon_image_is_not_deleted'));
                    }
                } catch (error) {
                    handleException(error);
                }
            } else {
                this.image_helper.removeFile(file);
            }
        },
        async update() {
            this.loading = true;
            try {

                const response = await Request.patchAuth(sellerAPI.addons.update(this.id), {
                    ...this.addon, 'image': this.image_helper.getImageDataFile()
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('addon_updated'));
                    RequestCache.removeSimilar(sellerAPI.addons.get);
                    NavigatorService.goBackOrRoute({name:'seller.addons.index'});
                } else {
                    ToastNotification.unknownError();
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
        this.setTitle(this.$t('edit_addon'))
        this.addon = (await Request.getAuth<IData<IAddon>>(sellerAPI.addons.show(this.id))).data.data;
        this.image_helper.addDefaultFile({url: this.addon.image});
        this.page_loading = false;
    }
});

</script>
