<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_employee')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('general')" icon="edit_note">
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
                                       name="password" no-spacing required />


                        </CardBody>
                    </Card>

                </Col>


                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('identity')" icon="badge"/>

                        <CardBody class="pb-0">


                            <Row>
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
                                show-file-manager
                                preview-as-list
                                v-on:file-added="avatar_helper.onFileUpload"
                                v-on:file-removed="avatar_helper.onFileRemoved"
                            />


                        </CardBody>
                    </Card>

                </Col>


            </Row>
            <div class="text-end mb-3">
                <CreateButton :loading="loading" @click="create"/>
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
import CreateButton from "@js/components/buttons/CreateButton.vue";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";


export default defineComponent({
    components: {
        FormPasswordInput,
        CreateButton,
        VModal,
        LoadingButton,
        ...Components,
        Layout
    },
    mixins: [FormMixin, UtilMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            admin: {
                active: true
            } as IAdmin,
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
                    text: this.$t('create'),
                    active: true,
                },
            ];
        },
        role_helper: AdminRole.select_helper,
    },
    methods: {

        async create() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.admins.create, {
                    ...this.admin, 'avatar': this.avatar_helper.getImageDataFile()
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('employee_is_created'));
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
        this.setTitle(this.$t('create_employee'))
        try {
            const roleResponse = await Request.getAuth<IData<IAdminRole[]>>(adminAPI.roles.get);
            this.roles = roleResponse.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }

});

</script>
