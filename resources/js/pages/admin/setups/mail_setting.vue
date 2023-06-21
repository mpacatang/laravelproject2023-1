<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('mail_settings')"/>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="8" xl="6">
                    <Card>
                        <CardHeader :title="$t('mail')" icon="mail" type="msr">

                        </CardHeader>

                        <CardBody>
                            <Row>
                                <Col lg="6">

                                    <FormInput v-model="mail_setting.mail_driver" :errors="errors"
                                               :label="$t('driver')"
                                               name="mail_driver" type="text"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="mail_setting.mail_host" :errors="errors"
                                               :label="$t('host')"
                                               name="mail_host" type="text"/>

                                </Col>
                            </Row>

                            <Row>
                                <Col lg="6">

                                    <FormInput v-model="mail_setting.mail_port" :errors="errors"
                                               :label="$t('port')"
                                               name="mail_port" type="text"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="mail_setting.mail_encryption" :errors="errors"
                                               :label="$t('encryption')"
                                               name="mail_encryption" type="text"/>

                                </Col>
                            </Row>

                            <Row>
                                <Col lg="6">

                                    <FormInput v-model="mail_setting.mail_username" :errors="errors"
                                               :label="$t('username')"
                                               name="mail_username" type="text"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="mail_setting.mail_password" :errors="errors"
                                               :label="$t('password')"
                                               name="mail_password" type="text"/>

                                </Col>
                            </Row>

                            <Row>
                                <Col lg="6">

                                    <FormInput v-model="mail_setting.mail_from_email" :errors="errors"
                                               :label="$t('email')"
                                               name="mail_from_email" type="text"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="mail_setting.mail_from_name" :errors="errors"
                                               :label="$t('name')"
                                               name="mail_from_name" type="text"/>

                                </Col>
                            </Row>

                            <div class="d-flex justify-content-between">
                                <span class="fw-medium cursor-pointer">Need any help? <span @click="goToMailDocs" class="text-primary">Check our mail documentation</span></span>
                                <UpdateButton :loading="loading" @click="update"/>
                            </div>


                        </CardBody>
                    </Card>
                </Col>


            </Row>
        </PageLoading>


    </Layout>
</template>


<script lang="ts">

import Layout from "../layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import {DocsNavigation} from "@js/services/navigator_service";
import {IMailSetting} from "@js/types/models/business_setting";

export default defineComponent({
    components: {
        UpdateButton,

        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            mail_setting: {} as IMailSetting,
        }
    },
    computed: {

        breadcrumb_items(): IBreadcrumb[] {
            return [

                {
                    text: this.$t("mail_setting"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        goToMailDocs(){
          DocsNavigation.goToMail();
        },
        async update() {
            this.loading = true;
            this.clearAllError();
            try {
                const response = await Request.postAuth(adminAPI.business_settings.mail_setting, {
                    ...this.mail_setting
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('mail_settings_is_updated'));
                } else {
                    ToastNotification.unknownError();
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
        this.setTitle(this.$t('mail_setting'));
        try {
            const response = await Request.getAuth<IData<IMailSetting>>(adminAPI.business_settings.get_data);
            this.mail_setting = response.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
