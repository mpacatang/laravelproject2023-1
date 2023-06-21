<template>
    <Layout>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="6">
                    <Card>
                        <CardHeader :title="$t('profile')"></CardHeader>

                        <CardBody>
                            <FileUpload
                                :default-files="avatar_helper.defaultFiles"
                                max-files="1"
                                preview-as-list
                                no-label
                                v-on:file-added="avatar_helper.onFileUpload"
                                v-on:file-removed="onFileRemove"
                            />
                            <Row class="mt-3">
                                <Col lg="6">
                                    <FormInput v-model="customer.first_name" :errors="errors" :label="$t('first_name')"
                                               name="first_name"
                                               required type="text"/>

                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="customer.last_name" :errors="errors" :label="$t('last_name')"
                                               name="last_name"
                                               required type="text"/>

                                </Col>
                            </Row>
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="customer.email" :errors="errors" :label="$t('email')"
                                               name="email"
                                               required type="email"/>

                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="customer.mobile_number" :errors="errors"
                                               :label="$t('mobile_number')"
                                               name="mobile_number"
                                               required type="tel"/>

                                </Col>
                            </Row>
                            <FormInput v-model="password" :errors="errors" :label="$t('password')"
                                       name="password"
                                       required type="password"/>
                            <div class="float-end">
                                <LoadingButton :loading="loading" @click="updateProfile">{{
                                        $t('update')
                                    }}
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

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from "vue";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {ICustomer} from "@js/types/models/customer";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {ILocalFile} from "@js/types/models/models";

export default defineComponent({
    components: {Layout, ...Components},
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {
        return {
            avatar_helper: new FileUploadHelper({max: 1}),
            customer: {} as ICustomer,
            password: '',
            loading: false,
            page_loading: true

        };
    },
    methods: {
        async getProfile() {
            try {
                const response = await Request.getAuth<IData<ICustomer>>(customerAPI.profile.show);
                this.customer = response.data.data;
                if (this.customer.avatar != null) {
                    this.avatar_helper.addDefaultFile({url: this.customer.avatar});
                }
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        async updateProfile() {
            try {
                this.loading = true;
                let body = {...this.customer, avatar: this.avatar_helper.getImageDataFile()};
                if (this.password.length > 0) {
                    body['password'] = this.password;
                }
                const response = await Request.patchAuth<IData<ICustomer>>(customerAPI.profile.update, body);
                if (response.success()) {
                    ToastNotification.success(this.$t('profile_is_updated'));
                    this.avatar_helper.shiftAllImageToUploaded();
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
                    const response = await Request.patchAuth<IData<any>>(customerAPI.profile.remove_avatar);
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
        this.setTitle(this.$t('my_profile'));

        this.getProfile();
    }
});

</script>

<style scoped>

</style>
