<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_employee')"/>

        <PageLoading :loading="page_loading">
            <Row>

                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('general')" icon="dns" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="admin.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="admin.first_name" :errors="errors"
                                               :label="$t('first_name')" name="first_name"
                                               required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="admin.last_name" :errors="errors"
                                               :label="$t('last_name')" name="last_name"
                                               required/>
                                </Col>
                            </Row>

                            <FormInput v-model="admin.mobile_number" :errors="errors" :label="$t('mobile_number')"
                                       name="mobile_number" required
                                       type="tel"/>

                            <FormInput v-model="admin.email" :errors="errors" :label="$t('email')"
                                       name="email" required type="email"/>

                            <FormPasswordInput v-model="admin.password" :errors="errors" :label="$t('password')"
                                       :placeholder="$t('password_(if_you_need_to_change)')"
                                       name="password" no-spacing required />


                        </CardBody>
                    </Card>

                </Col>


                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('identity')" icon="badge" type="msr"/>

                        <CardBody class="pb-0">

                            <Row v-if="!admin.is_super">
                                <Col lg="8">
                                    <div>

                                        <FormSelect :data="roles" :errors="errors"
                                                    :helper="role_helper"
                                                    :label="$t('role')"
                                                    :onSelected="(e)=>admin.role_id=e"
                                                    :placeholder="$t('select_role')"
                                                    :selected="admin.role_id" class="" name="role_id"
                                                    required/>

                                    </div>
                                </Col>
                            </Row>


                            <FileUpload
                                :label="$t('avatar')" container-height="126"
                                max-files="1"
                                preview-as-list
                                show-file-manager
                                :default-files="avatar_helper.defaultFiles"
                                v-on:file-added="avatar_helper.onFileUpload"
                                v-on:file-removed="onFileRemoved"
                            />

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

import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {Components} from "@js/components/components";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import {IData} from "@js/types/models/data";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {AdminRole, IAdmin, IAdminRole} from "@js/types/models/admin";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {IBreadcrumb} from "@js/types/models/models";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        FormPasswordInput,
        UpdateButton,
        VModal,
        LoadingButton,
        ...Components,
        Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            admin: {} as IAdmin,
            roles: [] as IAdminRole[],
            avatar_helper: new FileUploadHelper(),
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("employees"),
                    route: 'admin.employees.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        },
        role_helper: AdminRole.select_helper,
    },
    methods: {

        async onFileRemoved(file) {

            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(adminAPI.admins.remove_avatar(this.id));
                    if (response.success()) {
                        this.avatar_helper.onFileRemoved(file);
                        ToastNotification.success(this.$t('admin_image_deleted'));
                    } else {
                        ToastNotification.error(this.$t('admin_image_is_not_deleted'));
                    }
                } catch (error) {
                    handleException(error);

                }
            } else {
                this.avatar_helper.onFileRemoved(file);
            }
        },
        async update() {
            this.loading = true;
            try {
                const response = await Request.patchAuth(adminAPI.admins.update(this.id), {
                    ...this.admin, 'avatar': this.avatar_helper.getImageDataFile(),
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('admin_is_edited'));
                    RequestCache.removeSimilar(adminAPI.admins.get);
                    NavigatorService.goBackOrRoute({name:'admin.employees.index'});
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
        this.setTitle(this.$t('edit_admin'))

        try {
            const response = await Request.getAuth<IData<IAdmin>>(adminAPI.admins.show(this.id));
            const roleResponse = await Request.getAuth<IData<IAdminRole[]>>(adminAPI.roles.get);
            this.admin = response.data.data;
            this.roles = roleResponse.data.data;
            this.avatar_helper.addDefaultFile({url: this.admin.avatar});
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }

});

</script>
