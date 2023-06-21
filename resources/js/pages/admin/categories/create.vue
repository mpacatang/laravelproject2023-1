<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_category')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="8" xl="6">
                    <Card>

                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="category.active" :checked="category.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" noSpacing/>
                            </div>
                        </CardHeader>


                        <CardBody>

                            <Row>
                                <Col lg="6" >

                                    <FormSelect :data="modules" :errors="errors"
                                                :helper="module_helper"
                                                :label="$t('module')"
                                                :onSelected="(e)=>category.module_id=e"
                                                :placeholder="$t('select_module')"
                                                :selected="category.module_id"
                                                name="module_id">
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="You can't change module after creating"></InfoTooltip>
                                        </template>
                                    </FormSelect>
                                </Col>

                                <Col lg="6">
                                    <FormInput v-model="category.name" :errors="errors"
                                               :label="$t('name')" name="name"/>
                                </Col>
                            </Row>



                            <TextEditor v-model="category.description" :label="$t('description')"/>


                            <FileUpload :height=126 :max-files=1
                                        :on-added="images_helper.onFileAdded"
                                        :on-removed="images_helper.onFileRemoved"
                                        preview-as-list
                                        :label="$t('image')"
                                        show-file-manager/>

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
import {ICategory} from "@js/types/models/category";
import {Components} from "@js/components/components";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import {IModule, Module} from "@js/types/models/module";
import {IData} from "@js/types/models/data";
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
            page_loading: true,
            category: {active: true} as ICategory,
            images_helper: new FileUploadHelper(),
            modules: [] as IModule[],
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
                    text: this.$t("create"),
                    active: true,
                },
            ];
        },

    },
    methods: {
        async create() {
            this.clearAllError();
            this.loading = true;

            try {
                const response = await Request.postAuth(adminAPI.categories.create, {
                    ...this.category,
                    image: this.images_helper.getImageDataFile(),

                });
                if (response.success()) {
                    ToastNotification.success(this.$t('category_is_created'));
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
        }
    },
    async mounted() {
        this.setTitle(this.$t('create_category'));
        const moduleResponse = await Request.getAuth<IData<IModule[]>>(adminAPI.modules.get);
        if (moduleResponse.success()) {
            this.modules = moduleResponse.data.data;
        }
        this.page_loading = false;
    }
})

</script>
