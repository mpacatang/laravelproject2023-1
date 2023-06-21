<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_category')"/>

        <PageLoading :loading="page_loading">

            <Row>
                <Col lg="8" xl="6">

                <Card>
                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="category.active" :errors="errors" :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <Row>
                                <Col lg="6">

                                    <FormSelect :data="modules" :errors="errors"
                                                :helper="module_helper"
                                                :label="$t('module')"
                                                :onSelected="(e)=>category.module_id=e"
                                                :placeholder="$t('select_module')"
                                                :selected="category.module_id"
                                                disabled
                                                name="module_id"
                                    >
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="You can't change module after created"></InfoTooltip>
                                        </template>
                                    </FormSelect>
                                </Col>

                                <Col lg="6">
                                    <FormInput v-model="category.name" :errors="errors"
                                               :label="$t('name')" name="name"/>
                                </Col>
                            </Row>

                            <TextEditor v-model="category.description" :label="$t('description')"/>


                            <FileUpload :default-files="images_helper.defaultFiles" :height=126
                                        :max-files=1
                                        :on-added="images_helper.onFileAdded"
                                        :on-removed="onFileRemoved"
                                        show-file-manager
                                        :label="$t('image')"

                                        preview-as-list/>

                            <div class="text-end mt-3">
                                <UpdateButton :loading="loading" @click="()=>this.update()"/>

                            </div>


                        </CardBody>
                    </Card>

                </Col>
            </Row>

            <VModal v-model="show_alert_modal">
                <Card class="mb-0">
                    <CardHeader :title="$t('inactive_alert')"
                                icon="warning"></CardHeader>
                    <CardBody>
                        <p class="mb-1-5">{{ $t('there_is_few_other_things_also_become_inactive') }}</p>
                        <p class="mb-1 fw-medium">{{ $t('connected_to') }}</p>
                        <ul>
                            <li>{{ $t('related_sub_categories') }}</li>
                            <li>{{ $t('related_products') }}</li>
                        </ul>

                        <div class="text-end">
                            <Button class="me-2" color="secondary" variant="soft"
                                    @click="()=>this.show_alert_modal=false">
                                {{ $t('cancel') }}
                            </Button>
                            <Button color="danger" flexed-icon @click="()=>this.update(true)">
                                <Icon class="me-1-5" icon="warning" type="msr"/>
                                {{ $t('update') }}
                            </Button>
                        </div>
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
import {defineComponent} from "vue";
import {ICategory} from "@js/types/models/category";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import {IBreadcrumb} from "@js/types/models/models";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {IModule, Module} from "@js/types/models/module";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        UpdateButton,
        VModal,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,

            category: null as ICategory,
            modules: [],
            images_helper: new FileUploadHelper(),
            show_alert_modal: false,
        }
    },
    computed: {
        module_helper: Module.select_helper,
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('categories'),
                    route: 'admin.categories.index',
                },
                {
                    text: this.$t("edit"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async onFileRemoved(file) {
            if (file.uploaded) {
                try {
                    const response = await Request.patchAuth(adminAPI.categories.remove_image(this.id));
                    if (response.success()) {
                        this.images_helper.onFileRemoved(file);
                        ToastNotification.success(this.$t('category_image_deleted'));
                    } else {
                        ToastNotification.error(this.$t('category_image_is_not_deleted'));
                    }
                } catch (error) {
                    handleException(error);
                }
            }
        },
        async update(confirmation = false) {
            if (this.loading)
                return;

            if (!confirmation && !this.category.active) {
                this.show_alert_modal = true;
                return;
            }
            this.show_alert_modal = false;

            this.loading = true;
            try {
                const response = await Request.patchAuth(adminAPI.categories.update(this.id), {
                    ...this.category,
                    image: this.images_helper.getImageDataFile()

                });
                if (response.success()) {
                    ToastNotification.success(this.$t('category_is_edited'));
                    RequestCache.removeSimilar(adminAPI.categories.get);
                    NavigatorService.goBackOrRoute({name: 'admin.categories.index'});
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


    },
    async mounted() {
        this.setTitle(this.$t('edit_category'));

        try {
            const response = await Request.getAuth<IData<ICategory>>(adminAPI.categories.update(this.id));
            if (response.success()) {
                this.category = response.data.data;
                this.images_helper.addDefaultFile({url: this.category.image});
                const moduleResponse = await Request.getAuth<IData<IModule[]>>(adminAPI.modules.get);
                if (moduleResponse.success()) {
                    this.modules = moduleResponse.data.data;
                }
            } else {
                ToastNotification.unknownError();
            }
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
})

</script>
