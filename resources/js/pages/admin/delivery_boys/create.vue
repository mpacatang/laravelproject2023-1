<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_delivery_boy')"/>

        <PageLoading :loading="page_loading">

            <Row>

                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="delivery_boy.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
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
                                show-file-manager
                                v-on:file-added="avatar_helper.onFileUpload"
                                v-on:file-removed="avatar_helper.onFileRemoved"
                            />


                        </CardBody>
                    </Card>

                </Col>


                <Col xl="6">
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
                                show-file-manager
                                zone-id="identity_zone"
                                v-on:file-added="identity_image_helper.onFileUpload"
                                v-on:file-removed="identity_image_helper.onFileRemoved"
                            />
                        </CardBody>
                    </Card>

                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('bank_details')" icon="account_balance" type="msr">

                        </CardHeader>
                        <CardBody class="pb-0">
                            <VItem align-items="center" class="p-1-5 mb-2" color="info" display="flex" rounded soft>
                                <Icon icon="info" type="msr"></Icon>
                                <strong class="ms-1">{{ $t('be_careful') }}:</strong>&nbsp;
                                {{ $t('delivery_boy_will_receive_all_payments_in_this_bank') }}
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
                                               :placeholder="$t('enter_a_account_number')" min="0" name="account_number"
                                               type="number"></FormInput>

                                </Col>
                            </Row>


                        </CardBody>
                    </Card>
                </Col>

            </Row>
            <div class="text-end mb-3">
                <CreateButton :loading="loading" @click="create"/>
            </div>

        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import DeliveryIdentityTypeMixin from "@js/shared/mixins/DeliveryIdentityTypeMixin";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import {ToastNotification} from "@js/services/toast_notification";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import {IZone, Zone} from "@js/types/models/zone";
import {IData} from "@js/types/models/data";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default {
    components: {
        FormPasswordInput,
        CreateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, DeliveryIdentityTypeMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            delivery_boy: {
                active: true,
            } as IDeliveryBoy,
            zones: [] as IZone[],
            avatar_helper: new FileUploadHelper(),
            identity_image_helper: new FileUploadHelper(),
        }
    },
    computed: {
        zone_helper: Zone.select_helper,
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("delivery_boys"),
                    route: 'admin.delivery_boys.index',
                },
                {
                    text: this.$t("create"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async create() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.delivery_boys.create,
                    {
                        ...this.delivery_boy,
                        'avatar': this.avatar_helper.getImageDataFile(),
                        'identity_image': this.identity_image_helper.getImageDataFile(),
                    });

                if (response.success()) {
                    ToastNotification.success(this.$t('delivery_boy_created'));
                    RequestCache.removeSimilar(adminAPI.delivery_boys.get);
                    NavigatorService.goBackOrRoute({name: 'admin.delivery_boys.index'});
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
        const zoneResponse = await Request.getAuth<IData<IZone[]>>(adminAPI.zones.get);
        if (zoneResponse.success()) {
            this.zones = zoneResponse.data.data;
        }
        this.page_loading = false;
    }
}

</script>
