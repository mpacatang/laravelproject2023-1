<template>
    <Layout>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="7">
                    <Card>
                        <CardHeader :title="$t('update_profile')" icon="manage_accounts"></CardHeader>

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
                                    <FormInput v-model="seller.first_name" :errors="errors" :label="$t('first_name')"
                                               name="first_name"
                                               required type="text"/>

                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="seller.last_name" :errors="errors" :label="$t('last_name')"
                                               name="last_name"
                                               required type="text"/>

                                </Col>
                            </Row>
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="seller.email" :errors="errors" :label="$t('email')"
                                               name="email"
                                               required type="email"/>

                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="seller.mobile_number" :errors="errors"
                                               :label="$t('mobile_number')"
                                               name="mobile_number"
                                               required type="tel"/>

                                </Col>

                            </Row>


                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="seller.bank_name" :errors="errors"
                                               :label="$t('bank_name')"
                                               :placeholder="$t('enter_a_bank_name')" name="bank_name"></FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="seller.account_number" :errors="errors"
                                               :label="$t('account_number')"
                                               :placeholder="$t('enter_a_account_number')" min="0" name="account_number"
                                               type="number"></FormInput>

                                </Col>
                            </Row>
                            <div class="text-end">
                                <UpdateButton :loading="loading" @click="updateProfile"/>
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

import Layout from "@js/pages/seller/layouts/Layout.vue";
import {defineComponent} from "vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {ILocalFile} from "@js/types/models/models";
import {ISeller} from "@js/types/models/seller";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import {useSellerDataStore} from "@js/services/state/states";

export default defineComponent({
    components: {UpdateButton, FormPasswordInput, Layout, ...Components},
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {
        return {
            avatar_helper: new FileUploadHelper({max: 1}),
            seller: {} as ISeller,
            password: '',
            confirm_password: '',
            loading: false,
            page_loading: true

        };
    },
    methods: {
        async getProfile() {
            try {
                const response = await Request.getAuth<IData<ISeller>>(sellerAPI.profile.show);
                this.seller = response.data.data;
                useSellerDataStore().updateUserData(response.data.data);
                if (this.seller.avatar != null) {
                    this.avatar_helper.addDefaultFile({url: this.seller.avatar});
                }
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        async updateProfile() {
            try {
                this.loading = true;
                let body = {...this.seller, avatar: this.avatar_helper.getImageDataFile()};
                const response = await Request.patchAuth<IData<ISeller>>(sellerAPI.profile.update, body);
                if (response.success()) {
                    useSellerDataStore().updateUserData(response.data.data);
                    ToastNotification.success(this.$t('profile_is_updated'));
                    // this.avatar_helper.shiftAllImageToUploaded();
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
                const response = await Request.patchAuth<IData<ISeller>>(sellerAPI.profile.update, body);
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
                    const response = await Request.patchAuth<IData<ISeller>>(sellerAPI.profile.remove_avatar);
                    useSellerDataStore().updateUserData(response.data.data);
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
        this.getProfile();
    }
});

</script>

<style scoped>

</style>
