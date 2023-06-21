<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('sms_settings')"/>
        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6" lg="8">
                    <Card>
                        <CardHeader :title="$t('twilio')" icon="sms" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="sms_setting.sms_twilio_enable" :errors="errors"
                                            :label="$t('enable')"
                                            name="payment_razorpay_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody>
                            <FormInput v-model="sms_setting.sms_twilio_sid" :errors="errors"
                                       :label="$t('id')"
                                       name="sms_twilio_sid" type="text"/>

                            <FormInput v-model="sms_setting.sms_twilio_token" :errors="errors"
                                       :label="$t('token')"
                                       name="sms_twilio_token" type="text"/>

                            <FormInput v-model="sms_setting.sms_twilio_mobile_number" :errors="errors"
                                       :label="$t('mobile_number')"
                                       name="sms_twilio_mobile_number" type="text">
                                <template #prefix>
                                    <Icon icon="phone" size="18"></Icon>
                                </template>
                            </FormInput>


                            <div class="d-flex justify-content-between">
                                <span class="fw-medium cursor-pointer">Need any help? <span @click="goToSMSDocs" class="text-primary">Check our sms documentation</span></span>
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
import {ISmsSetting} from "@js/types/models/business_setting";

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
            sms_setting: {} as ISmsSetting,
        }
    },
    computed: {

        breadcrumb_items(): IBreadcrumb[] {
            return [

                {
                    text: this.$t("sms_setting"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        goToSMSDocs(){
          DocsNavigation.goToSMS();
        },
        async update() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.business_settings.sms_setting, {
                    ...this.sms_setting
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('sms_settings_is_updated'));
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
        this.setTitle(this.$t('sms_setting'));
        try {
            const response = await Request.getAuth<IData<ISmsSetting>>(adminAPI.business_settings.get_data);
            this.sms_setting = response.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
