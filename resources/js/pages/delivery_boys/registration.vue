<template>
    <template v-if="can_register">
        <div class="px-xl-5 mx-xl-5 py-3 mx-2">
            <div class="mb-3 d-flex justify-content-center align-items-center">
                <Icon icon="local_shipping" size="32"></Icon>
                <h3 class="mb-0 fw-medium ms-2-5 mt-0 text-capitalize">{{ $t('delivery_registration') }}</h3>
            </div>

            <PageLoading :loading="page_loading" class="mt-3">

                <!--            <PageHeader :title="$t('owner_information')"></PageHeader>-->

                <Row>

                    <Col lg="6" md="12">
                        <Card>

                            <CardHeader :title="$t('general')" icon="edit_note" type="msr">

                            </CardHeader>

                            <CardBody class="pb-0">

                                <Row>
                                    <Col lg="6">
                                        <FormInput v-model="delivery_boy.first_name" :errors="errors"
                                                   :label="$t('first_name')" name="first_name"
                                                   required/>
                                    </Col>
                                    <Col lg="6">
                                        <FormInput v-model="delivery_boy.last_name" :errors="errors"
                                                   :label="$t('last_name')" name="last_name"
                                                   required/>
                                    </Col>
                                </Row>

                                <Row>
                                    <Col lg="6">
                                        <FormInput v-model="delivery_boy.mobile_number" :errors="errors"
                                                   :label="$t('mobile_number')"
                                                   name="mobile_number" required
                                                   type="tel">
                                            <template #prefix>
                                                <Icon icon="call" size="18"></Icon>
                                            </template>
                                        </FormInput>
                                    </Col>
                                    <Col lg="6">
                                        <FormInput v-model="delivery_boy.email" :errors="errors" :label="$t('email')"
                                                   name="email" required type="email">
                                            <template #prefix>
                                                <Icon icon="mail" size="18"></Icon>
                                            </template>
                                        </FormInput>
                                    </Col>
                                </Row>

                                <Row>
                                    <Col lg="6">
                                        <FormPasswordInput v-model="delivery_boy.password" :errors="errors"
                                                           :label="$t('password')"
                                                           name="password" required type="password"/>
                                    </Col>
                                    <Col lg="6">
                                        <FormSelect :data="zones" :errors="errors"
                                                    :helper="zone_helper"
                                                    :label="$t('zone')"
                                                    :onSelected="(e)=>delivery_boy.zone_id=e"
                                                    :placeholder="$t('select_zone')"
                                                    :selected="delivery_boy.zone_id"
                                                    name="zone_id">
                                            <template #label-suffix>
                                                <InfoTooltip
                                                    tooltip="If you set zone then only that zone's seller can see this delivery"/>
                                            </template>
                                        </FormSelect>
                                    </Col>
                                </Row>


                                <FileUpload
                                    :label="$t('avatar')" height="126" max-files="1"
                                    preview-as-list

                                    v-on:file-added="avatar_helper.onFileUpload"
                                    v-on:file-removed="avatar_helper.onFileRemoved"
                                />


                            </CardBody>
                        </Card>

                    </Col>


                    <Col lg="6">
                        <Card>

                            <CardHeader :title="$t('identity')" icon="badge" type="msr">
                                <div class="float-end">
                                    <FormSwitch v-model="delivery_boy.salary_based" :errors="errors"
                                                :label="$t('salary_based')"
                                                name="salary_based" no-spacing/>
                                </div>
                            </CardHeader>

                            <CardBody class="pb-0">
                                <FormSelect :data="identity_types" :errors="errors"
                                            :helper="identity_type_helper"
                                            :label="$t('identity_type')"
                                            :onSelected="(e)=>delivery_boy.identity_type=e"
                                            :placeholder="$t('select_identity_type')"
                                            name="identity_type"
                                            required/>


                                <FormInput v-model="delivery_boy.identity_number" :errors="errors"
                                           :label="$t('identity_number')"
                                           name="identity_number" required
                                           type="text"/>

                                <FormInput v-model="delivery_boy.vehicle_number" :errors="errors"
                                           :label="$t('vehicle_number')"
                                           name="vehicle_number" required
                                           type="text"/>


                                <FileUpload
                                    :height="126" :label="$t('identity_image')" :max-files="1"
                                    preview-as-list

                                    zone-id="identity_zone"
                                    v-on:file-added="identity_image_helper.onFileUpload"
                                    v-on:file-removed="identity_image_helper.onFileRemoved"
                                />
                            </CardBody>
                        </Card>

                    </Col>

                    <Col lg="6">
                        <Card>
                            <CardHeader :title="$t('bank_details')" icon="account_balance" type="msr">

                            </CardHeader>
                            <CardBody class="pb-0">
                                <VItem class="p-1-5 mb-2" color="info" rounded soft>
                                    <span>
                                        <Icon icon="info" size="20" type="msr"></Icon>
                                    <strong class="ms-1">{{ $t('be_careful') }}:</strong>&nbsp;
                                    {{ $t('delivery_boy_will_receive_all_payments_in_this_bank') }}
                                    </span>
                                </VItem>


                                <Row>
                                    <Col lg="6">
                                        <FormInput v-model="delivery_boy.bank_name" :errors="errors"
                                                   :label="$t('bank_name')"
                                                   :placeholder="$t('enter_a_bank_name')" name="bank_name"></FormInput>
                                    </Col>
                                    <Col lg="6">
                                        <FormInput v-model="delivery_boy.account_number" :errors="errors"
                                                   :label="$t('account_number')"
                                                   :placeholder="$t('enter_a_account_number')" min="0"
                                                   name="account_number"
                                                   type="number"></FormInput>

                                    </Col>
                                </Row>


                            </CardBody>
                        </Card>
                    </Col>

                </Row>

                <div class="d-flex justify-content-between">
                    <Button class="fw-medium" color="primary" flexed-icon variant="text" @click="showing_terms=true">
                        <Icon class="me-1-5" icon="shield" size="20"/>
                        {{ $t('privacy') + " & " + $t('privacy') }}
                    </Button>
                    <LoadingButton :loading="loading" color="bluish-purple" flexed-icon icon="local_shipping"
                                   @click="create">
                        {{ $t('register') }}
                    </LoadingButton>

                </div>

            </PageLoading>
        </div>
    </template>
    <template v-else>
        <div class="mt-5">
            <h3 class="text-center fw-medium mt-5">Currently, Self registration is disabled</h3>
        </div>
    </template>

    <PrivacyTerms v-model="showing_terms"/>
</template>


<script lang="ts">

import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {IData} from "@js/types/models/data";
import {IZone, Zone} from "@js/types/models/zone";
import {deliveryBoyAPI} from "@js/services/api/request_url";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import DeliveryIdentityTypeMixin from "@js/shared/mixins/DeliveryIdentityTypeMixin";
import {BusinessSetting} from "@js/types/models/business_setting";
import PrivacyTerms from "@js/components/PrivacyTerms.vue";

export default defineComponent({
    components: {
        PrivacyTerms,
        FormPasswordInput,
        ...Components
    },
    mixins: [FormMixin, UtilMixin, DeliveryIdentityTypeMixin],
    data() {
        return {
            loading: false,
            page_loading: true,

            delivery_boy: {
                active: false,
            } as IDeliveryBoy,
            zones: [] as IZone[],
            avatar_helper: new FileUploadHelper(),
            identity_image_helper: new FileUploadHelper(),
            can_register: false,
            showing_terms: false
        }
    },
    computed: {
        zone_helper: Zone.select_helper,
    },
    methods: {
        async create() {
            this.loading = true;
            try {
                const response = await Request.postAuth(deliveryBoyAPI.auth.register,
                    {
                        ...this.delivery_boy,
                        'avatar': this.avatar_helper.getImageDataFile(),
                        'identity_image': this.identity_image_helper.getImageDataFile(),
                    });

                if (response.success()) {
                    ToastNotification.success("Delivery boy is created. We contact you soon");
                    this.$router.replace({name: 'landing'});
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    ToastNotification.unknownError();
                }
            }
            this.loading = false;
        }
    },
    async mounted() {
        this.setTitle(this.$t('create_delivery_boy'));
        const zoneResponse = await Request.get<IData<IZone[]>>(deliveryBoyAPI.zones.get);
        if (zoneResponse.success()) {
            this.zones = zoneResponse.data.data;
        }
        this.page_loading = false;

        this.can_register = BusinessSetting.instance.can_delivery_boy_self_register;
    }
});

</script>
