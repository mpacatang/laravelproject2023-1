<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_addon')"/>

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
                                           min="0" name="price" show-currency type="number">
                                </FormInput>
                            </Col>
                        </Row>


                        <TextEditor v-model="addon.description" :label="$t('description')"/>

                        <FileUpload :default-files="image_helper.defaultFiles" :height=150
                                    :max-files="1" preview-as-list
                                    :label="$t('image')"
                                    v-on:file-added="image_helper.onFileUpload"
                                    v-on:file-removed="image_helper.onFileRemoved"/>

                        <div class="text-end mt-3">
                            <CreateButton :loading="loading" @click="create"/>
                        </div>


                    </CardBody>

                </Card>

            </Col>


        </Row>


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
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
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

        return {
            loading: false,
            addon: {
                active: true,
            } as IAddon,
            image_helper: new FileUploadHelper(),
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('addons'),
                    route: 'seller.addons.index',
                },
                {
                    text: this.$t('create'),
                    active: true,
                },
            ];
        }

    },
    methods: {

        async create() {
            this.loading = true;
            try {
                const response = await Request.postAuth(sellerAPI.addons.create, {
                    ...this.addon, 'image': this.image_helper.getImageDataFile()
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('addon_created'));
                    RequestCache.removeSimilar(sellerAPI.addons.get);
                    NavigatorService.goBackOrRoute({name:'seller.addons.index'});
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
    mounted() {
        this.setTitle(this.$t('create_addon'))

    }
});

</script>
