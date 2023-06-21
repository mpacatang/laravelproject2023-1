<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_customer')"/>
        <Row>
            <Col xl="6">
                <Card>

                    <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                        <div class="float-end">
                            <FormSwitch
                                v-model="customer.active" :errors="errors" :label="$t('active')" name="active"
                                no-spacing>
                            </FormSwitch>
                        </div>
                    </CardHeader>

                    <CardBody class="pb-0">
                        <Row>
                            <Col lg="6">
                                <FormInput v-model="customer.first_name" :errors="errors"
                                           :label="$t('first_name')"
                                           name="first_name"/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="customer.last_name" :errors="errors"
                                           :label="$t('last_name')"
                                           name="last_name"/>

                            </Col>
                        </Row>

                        <Row>
                            <Col lg="6">
                                <FormInput v-model="customer.mobile_number" :errors="errors"
                                           :label="$t('mobile_number')"
                                           name="mobile_number"
                                           type="tel">
                                    <template #prefix>
                                        <Icon icon="call" size="18"></Icon>
                                    </template>
                                </FormInput>

                            </Col>
                            <Col lg="6">
                                <FormInput v-model="customer.email" :errors="errors"
                                           :label="$t('email')"
                                           name="email"
                                           type="email">
                                    <template #prefix>
                                        <Icon icon="mail" size="18"></Icon>
                                    </template>
                                </FormInput>


                            </Col>
                        </Row>


                        <Row>
                            <Col lg="6">
                                <FormCheckbox v-model="set_mobile_number_as_verified" :errors="errors"
                                              :label="$t('set_mobile_number_as_verified')"
                                              name="set_mobile_number_as_verified"
                                />
                            </Col>
                            <Col lg="6">
                                <FormCheckbox v-model="set_email_as_verified" :errors="errors"
                                              :label="$t('set_email_as_verified')"
                                              name="set_email_as_verified"
                                />

                            </Col>
                        </Row>

                        <FormInput v-model="password" :errors="errors"
                                   :label="$t('password')"
                                   :placeholder="$t('enter_a_password_(if_you_need_to_change)')"
                                   name="password"
                                   type="password"
                        />


                    </CardBody>
                </Card>
            </Col>

            <Col xl="6">
                <Card>
                    <CardHeader :title="$t('avatar')" icon="account_circle" type="msr"/>

                    <CardBody class="pb-0">
                        <FileUpload :default-files="avatar_helper.defaultFiles" :height="126"
                                    :max-files="1"
                                    :label="$t('avatar')"
                                    :on-added="avatar_helper.onFileUpload"
                                    :on-removed="avatar_helper.onFileRemoved" preview-as-list
                                    show-file-manager/>

                    </CardBody>
                </Card>
            </Col>

        </Row>


        <div class="text-end mb-3">
            <CreateButton :loading="loading" @click="update"/>

        </div>

    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {ICustomer} from "@js/types/models/customer";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import Response from "@js/services/api/response";
import {Components} from "@js/components/components";
import {ToastNotification} from "@js/services/toast_notification";
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
            id: this.$route.params.id,
            loading: false,
            customer: {
                active: true
            } as ICustomer,
            password: null,
            avatar_helper: new FileUploadHelper(),
            set_email_as_verified: false,
            set_mobile_number_as_verified: false,
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('customers'),
                    route: 'admin.customers.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        }
    },
    methods: {

        async update() {
            try {
                const customerResponse = await Request.postAuth(adminAPI.customers.create, {
                    ...this.customer,
                    'avatar': this.avatar_helper.getImageDataFile(),
                    'password': this.password,
                    set_email_as_verified: this.set_email_as_verified,
                    set_mobile_number_as_verified: this.set_mobile_number_as_verified
                });
                if (customerResponse.success()) {
                    ToastNotification.success(this.$t('customer_is_created'));
                    RequestCache.removeSimilar(adminAPI.customers.get);
                    NavigatorService.goBackOrRoute({name: 'admin.customers.index'});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
        },

    },
    async mounted() {
        this.setTitle(this.$t('create_customer'));

    }
});

</script>
