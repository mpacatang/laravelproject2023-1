<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('app_settings')"/>
        <PageLoading :loading="page_loading">
            <Row>

                <Col xl="4" lg="6">
                    <Card>
                        <CardHeader :title="$t('customer_app')" icon="person" type="msr"></CardHeader>

                        <CardBody>
                            <FormInput v-model="app_setting.customer_android_app_min_version" :errors="errors"
                                       :label="$t('android_app_min_version')"
                                       min="1"
                                       name="android_app_min_version" type="number"/>

                            <FormInput v-model="app_setting.customer_android_app_url" :errors="errors"
                                       :label="$t('android_app_url')"
                                       name="android_app_url"/>

                            <FormInput v-model="app_setting.customer_ios_app_min_version" :errors="errors"
                                       :label="$t('ios_app_min_version')"
                                       name="ios_app_min_version"
                                       type="number"/>

                            <FormInput v-model="app_setting.customer_ios_app_url" :errors="errors"
                                       :label="$t('ios_app_url')"
                                       name="ios_app_url" no-spacing/>

                        </CardBody>
                    </Card>
                </Col>
                <Col xl="4" lg="6">
                    <Card>
                        <CardHeader :title="$t('delivery_boy_app')" icon="local_shipping" type="msr"></CardHeader>

                        <CardBody>
                            <FormInput v-model="app_setting.delivery_boy_android_app_min_version" :errors="errors"
                                       :label="$t('android_app_min_version')"
                                       min="1"
                                       name="android_app_min_version" type="number"/>

                            <FormInput v-model="app_setting.delivery_boy_android_app_url" :errors="errors"
                                       :label="$t('android_app_url')"
                                       name="android_app_url"/>

                            <FormInput v-model="app_setting.delivery_boy_ios_app_min_version" :errors="errors"
                                       :label="$t('ios_app_min_version')"
                                       name="ios_app_min_version"
                                       type="number"/>

                            <FormInput v-model="app_setting.delivery_boy_ios_app_url" :errors="errors"
                                       :label="$t('ios_app_url')"
                                       name="ios_app_url" no-spacing/>

                        </CardBody>
                    </Card>
                </Col>
                <Col xl="4" lg="6">
                    <Card>
                        <CardHeader :title="$t('seller_app')" icon="person_4" type="msr"></CardHeader>

                        <CardBody>
                            <FormInput v-model="app_setting.seller_android_app_min_version" :errors="errors"
                                       :label="$t('android_app_min_version')"
                                       min="1"
                                       name="android_app_min_version" type="number"/>

                            <FormInput v-model="app_setting.seller_android_app_url" :errors="errors"
                                       :label="$t('android_app_url')"
                                       name="android_app_url"/>

                            <FormInput v-model="app_setting.seller_ios_app_min_version" :errors="errors"
                                       :label="$t('ios_app_min_version')"
                                       name="ios_app_min_version"
                                       type="number"/>

                            <FormInput v-model="app_setting.seller_ios_app_url" :errors="errors"
                                       :label="$t('ios_app_url')"
                                       name="ios_app_url" no-spacing/>

                        </CardBody>
                    </Card>
                </Col>

                <div class="text-end mb-3">
                    <UpdateButton :loading="loading" @click="update"/>
                </div>

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
import {IAppSetting} from "@js/types/models/business_setting";

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
            app_setting: {} as IAppSetting,
        }
    },
    computed: {

        breadcrumb_items(): IBreadcrumb[] {
            return [

                {
                    text: this.$t("app_setting"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async update() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.business_settings.app_setting, {
                    ...this.app_setting
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('app_settings_is_updated'));
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
        this.setTitle(this.$t('app_setting'));
        try {
            const response = await Request.getAuth<IData<IAppSetting>>(adminAPI.business_settings.get_data);
            this.app_setting = response.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
