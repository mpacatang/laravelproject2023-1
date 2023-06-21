<template>
    <Layout>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="7">
                    <Card>
                        <CardHeader :title="$t('profile')" icon="person"></CardHeader>

                        <CardBody>
                            <FileUpload
                                :default-files="avatar_helper.defaultFiles"
                                max-files="1"
                                preview-as-list
                                v-on:file-added="avatar_helper.onFileUpload"
                                v-on:file-removed="onFileRemove"
                            />
                            <Row class="mt-3">
                                <Col lg="6">
                                    <FormInput v-model="admin.first_name" :errors="errors" :label="$t('first_name')"
                                               name="first_name"
                                               required type="text"/>

                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="admin.last_name" :errors="errors" :label="$t('last_name')"
                                               name="last_name"
                                               required type="text"/>

                                </Col>
                            </Row>
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="admin.email" :errors="errors" :label="$t('email')"
                                               name="email"
                                               required type="email"/>

                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="admin.mobile_number" :errors="errors"
                                               :label="$t('mobile_number')"
                                               name="mobile_number"
                                               required type="tel"/>

                                </Col>
                            </Row>
                            <div class="float-end">
                                <LoadingButton :loading="loading" icon="upload" @click="updateProfile">
                                    {{ $t('update') }}
                                </LoadingButton>
                            </div>
                        </CardBody>
                    </Card>


                </Col>
                <Col lg="5">
                    <Card>
                        <CardHeader :title="$t('change_password')" icon="key"></CardHeader>
                        <CardBody>
                            <FormPasswordInput v-model="password" :errors="errors" :label="$t('password')"
                                               name="password"
                                               required/>

                            <FormPasswordInput v-model="confirm_password" :errors="errors"
                                               :label="$t('confirm_password')"
                                               name="confirm_password"
                                               required/>

                            <div class="text-end">
                                <LoadingButton :loading="loading" color="danger" icon="lock" @click="updatePassword">
                                    {{ $t('update_password') }}
                                </LoadingButton>
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
import {defineComponent} from "vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {ILocalFile} from "@js/types/models/models";
import {IAdmin} from "@js/types/models/admin";
import {ISeller} from "@js/types/models/seller";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import {useAdminDataStore} from "@js/services/state/states";

export default defineComponent({
    components: {FormPasswordInput, Layout, ...Components},
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {
        return {
            loading: false,
            page_loading: true,

            avatar_helper: new FileUploadHelper({max: 1}),
            admin: {} as IAdmin,
            password: '',
            confirm_password: '',
        };
    },
    methods: {
        async getProfile() {
            try {
                const response = await Request.getAuth<IData<IAdmin>>(adminAPI.profile.show);
                this.admin = response.data.data;
                useAdminDataStore().updateUserData(response.data.data);
                if (this.admin.avatar != null) {
                    this.avatar_helper.addDefaultFile({url: this.admin.avatar});
                }
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        async updateProfile() {
            try {
                this.loading = true;
                let body = {...this.admin, avatar: this.avatar_helper.getImageDataFile()};

                const response = await Request.patchAuth<IData<IAdmin>>(adminAPI.profile.update, body);
                if (response.success()) {
                    useAdminDataStore().updateUserData(response.data.data);
                    ToastNotification.success(this.$t('profile_is_updated'));
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
        async updatePassword() {
            this.clearAllError();
            if (this.password.length == 0) {
                this.addError('password', this.$t('fill_password_first'));
                return;
            }
            if (this.password.length != 0 && this.password !== this.confirm_password) {
                this.addError('confirm_password', this.$t('both_password_are_not_same'));
                return;
            }
            try {
                this.loading = true;
                let body = {password: this.password};
                const response = await Request.patchAuth<IData<ISeller>>(adminAPI.profile.update, body);
                if (response.success()) {
                    ToastNotification.success(this.$t('password_changed'));
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
        async onFileRemove(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.patchAuth<IData<IAdmin>>(adminAPI.profile.remove_avatar);
                    useAdminDataStore().updateUserData(response.data.data);
                    if (response.success()) {
                        ToastNotification.success(this.$t('profile_image_removed'));
                    }
                } catch (error) {
                    handleException(error);
                }
            }
            this.avatar_helper.removeFile(file);
        }
    },
    mounted() {
        this.setTitle(this.$t('profile'));
        this.getProfile();
    }
});

</script>

<style scoped>

</style>
