    <template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('info_settings')"/>
        <PageLoading :loading="page_loading">

            <VItem align-items="center" class="p-2 mb-3" color="info" display="flex" rounded soft>
                <Icon icon="info" type="msr"></Icon>
                <strong class="ms-1">{{ $t('be_careful: ') }} </strong>&nbsp;
                {{ $t('this_information_are_publicly_available') }}
            </VItem>


            <Row>
                <Col xl="5">
                    <Card>
                        <CardHeader :title="$t('contact_details')" icon="person"></CardHeader>
                        <CardBody class="pb-0">
                            <FormInput v-model="info_setting.business_name" :errors="errors"
                                       :label="$t('business_name')" name="business_name"
                                       required/>
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.first_name" :errors="errors"
                                               :label="$t('first_name')" name="first_name"
                                               required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.last_name" :errors="errors"
                                               :label="$t('last_name')" name="last_name"
                                               required/>
                                </Col>
                            </Row>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.mobile_number" :errors="errors"
                                               :label="$t('mobile_number')"
                                               name="mobile_number" required
                                               type="tel">
                                        <template #prefix>
                                            <Icon icon="call" size="18"></Icon>
                                        </template>
                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.email" :errors="errors" :label="$t('email')"
                                               name="email" required type="email">
                                        <template #prefix>
                                            <Icon icon="mail" size="18"></Icon>
                                        </template>
                                    </FormInput>
                                </Col>
                            </Row>





                        </CardBody>
                    </Card>


                </Col>

                <Col xl="7">
                    <Card>
                        <CardHeader :title="$t('social_media')" icon="link"></CardHeader>
                        <CardBody>
                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.instagram_url" :label="$t('instagram')"
                                               name="instagram_url"></FormInput>

                                    <FormInput v-model="info_setting.whatsapp_url" :label="$t('whatsapp')"
                                               name="whatsapp_url"></FormInput>

                                    <FormInput v-model="info_setting.facebook_url" :label="$t('facebook')"
                                               name="facebook_url" no-spacing></FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.twitter_url" :label="$t('twitter')"
                                               name="twitter_url"></FormInput>

                                    <FormInput v-model="info_setting.linkedin_url" :label="$t('linkedin')"
                                               name="linkedin_url"></FormInput>

                                    <FormInput v-model="info_setting.pinterest_url" :label="$t('pinterest')"
                                               name="pinterest_url" no-spacing></FormInput>
                                </Col>
                            </Row>

                        </CardBody>
                    </Card>
                </Col>


                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('company_location')" icon="apartment" type="msr"/>

                        <CardBody class="pb-0">

                            <FormInput v-model="info_setting.address" :errors="errors"
                                       :label="$t('address')"
                                       name="address" required/>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.city" :errors="errors"
                                               :label="$t('city')"
                                               name="city" required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.state" :errors="errors"
                                               :label="$t('state')"
                                               name="state" required/>
                                </Col>
                            </Row>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.country" :errors="errors"
                                               :label="$t('country')"
                                               name="country" required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="info_setting.pincode" :errors="errors"
                                               :label="$t('pincode')"
                                               name="pincode" required type="number"/>
                                </Col>
                            </Row>

                            <Row>
                                <Col lg="6">

                                    <FormInput v-model="location.latitude" :errors="errors"
                                               :label="$t('latitude')"
                                               :placeholder="$t('enter_a_latitude_or_pick_from_map')"
                                               name="latitude" required
                                               step="0.00001" type="number" v-on:onchange="onLatitudeChange"/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="location.longitude" :errors="errors"
                                               :label="$t('longitude')"
                                               :placeholder="$t('enter_a_longitude_or_pick_from_map')"
                                               name="longitude"
                                               required
                                               step="0.00001" type="number" v-on:onchange="onLongitudeChange"/>
                                </Col>
                            </Row>


                        </CardBody>
                    </Card>
                </Col>

                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('pick_from_map')" icon="home_pin" type="msr"/>

                        <CardBody class="p-2">

                            <GoogleMap :location="location" height="364"
                                       pick-enabled
                                       search-enabled
                                       @change-location="onLocationChange"/>

                        </CardBody>
                    </Card>
                </Col>

                <Col xl="12">
                    <Card>
                        <CardHeader :title="$t('about_us')" icon="info" type="msr"></CardHeader>

                        <CardBody class="pb-0">
                            <TextEditor v-model="info_setting.about_us" editor-id="about_us_editor" no-label/>
                        </CardBody>

                    </Card>
                </Col>


            </Row>
            <Card>
                <CardHeader :title="$t('terms_&_privacy')" icon="gavel" type="msr"></CardHeader>

                <CardBody class="pb-0">
                    <Row>
                        <Col lg="6">
                            <TextEditor v-model="info_setting.privacy_policies" :label=" $t('privacy_policies') "
                                        editor-id="privacy_policies"/>
                        </Col>
                        <Col lg="6">
                            <TextEditor v-model="info_setting.terms_conditions" :label="$t('terms_conditions')"
                                        editor-id="terms_conditions"/>
                        </Col>
                    </Row>

                </CardBody>

            </Card>

            <div class="text-end mb-3">
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
import PageLoading from "@js/components/PageLoading.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import GoogleMap from "@js/components/maps/GoogleMap.vue";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import {IInfoSetting} from "@js/types/models/business_setting";

export default defineComponent({
    components: {
        UpdateButton,
        GoogleMap,
        PageLoading,

        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            info_setting: {} as IInfoSetting,
            location: {
                latitude: null,
                longitude: null
            },
        }
    },
    computed: {

        breadcrumb_items(): IBreadcrumb[] {
            return [

                {
                    text: this.$t("info_setting"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        onLocationChange(location) {
            this.location = location;
        },
        onLatitudeChange(e) {
            e.preventDefault();
            this.location = {...this.location, latitude: Number.parseFloat(e.target.value)}
        },
        onLongitudeChange(e) {
            e.preventDefault();
            this.location = {...this.location, longitude: Number.parseFloat(e.target.value)}
        },
        async update() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.business_settings.info_setting, {
                    ...this.info_setting
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('info_settings_is_updated'));
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
        this.setTitle(this.$t('info_setting'));
        try {
            const response = await Request.getAuth<IData<IInfoSetting>>(adminAPI.business_settings.get_data);
            this.info_setting = response.data.data;
            if (this.info_setting.address_latitude != null && this.info_setting.address_longitude != null) {
                this.location = {
                    latitude: this.info_setting.address_latitude,
                    longitude: this.info_setting.address_longitude
                }
            }


            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
})


</script>
