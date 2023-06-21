<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('other_settings')"/>
        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('fcm')"  icon="campaign">
                            <div class="float-end">
                                <span class="cursor-pointer"  @click="goToFcmDocs">How to find <span class="text-primary">server key</span></span>
                            </div>
                        </CardHeader>


                        <CardBody class="pb-0">
                            <FormInputArea v-model="other_setting.fcm_server_key"
                                           :errors="errors"
                                           :label="$t('fcm_server_key')"
                                           name="fcm_server_key"
                                           rows="3" type="text"/>

                        </CardBody>
                    </Card>
                </Col>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('google_analytics')" icon="monitoring" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="other_setting.google_analytics_enable" :errors="errors"
                                            :label="$t('google_analytics_enable')"
                                            name="google_analytics_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInput v-model="other_setting.google_analytics_id" :errors="errors"
                                       :label="$t('google_analytics_id')"
                                       name="google_analytics_id" type="text"/>


                        </CardBody>
                    </Card>
                </Col>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('map')" icon="map" type="msr">

                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInput v-model="other_setting.google_map_api_key" :errors="errors"
                                       :label="$t('google_map_api_key')"
                                       name="google_map_api_key" type="text"/>

                            <FormInputArea v-model="other_setting.mapbox_api_key" :errors="errors"
                                       :label="$t('mapbox_api_key')"
                                       name="mapbox_api_key" type="text"/>


                        </CardBody>
                    </Card>
                </Col>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('recaptcha')" icon="robot" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="other_setting.recaptcha_enable" :errors="errors"
                                            :label="$t('enable')"
                                            name="payment_razorpay_enable" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody class="pb-0">
                            <FormInput v-model="other_setting.recaptcha_site_key" :errors="errors"
                                       :label="$t('site_key')"
                                       name="recaptcha_site_key" type="text"/>

                            <FormInput v-model="other_setting.recaptcha_secret_key" :errors="errors"
                                       :label="$t('secret_key')"
                                       name="recaptcha_secret_key" type="text"/>
                        </CardBody>
                    </Card>
                </Col>




            </Row>
            <div class="d-flex justify-content-between">
            <span class="fw-medium cursor-pointer ms-1"> Need any help? <span @click="goToOtherDocs" class="text-primary">Check our documentation</span></span>
                <UpdateButton :loading="loading" @click="update"/>
            </div>

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
import FormInputArea from "@js/components/form/FormInputArea.vue";
import {IOtherSetting} from "@js/types/models/business_setting";

export default defineComponent({
    components: {
        FormInputArea,
        UpdateButton,

        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            other_setting: {} as IOtherSetting,
        }
    },
    computed: {

        breadcrumb_items(): IBreadcrumb[] {
            return [

                {
                    text: this.$t("other_setting"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        goToOtherDocs(){
          DocsNavigation.goToOtherSetup();
        },
        goToFcmDocs(){
            DocsNavigation.goToFirebase();
        },
        async update() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.business_settings.other_setting, {
                    ...this.other_setting
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('other_settings_is_updated'));
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
        this.setTitle(this.$t('other_setting'));
        try {
            const response = await Request.getAuth<IData<IOtherSetting>>(adminAPI.business_settings.get_data);
            this.other_setting = response.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
