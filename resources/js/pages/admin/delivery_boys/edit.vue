<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_delivery_boy')"/>

        <PageLoading :loading="page_loading">

            <div v-if="!delivery_boy.approved" class="mb-3">
                <VItem align-items="center" border class="mt-2 card shadow-none" radius="4" soft
                       style="width: 460px">
                    <div class="d-flex align-items-center p-2 px-3">
                        <Icon class="text-primary" icon="warning" size="20"></Icon>
                        <span class="mx-2 fw-medium text-primary">{{ $t('take_a_action') }}</span>
                    </div>
                    <hr class="dashed m-0">
                    <div class="p-2 px-3">
                        <ul>
                            <li class="fw-medium">It's a new delivery</li>
                            <li>Take look every entities and make approve.</li>
                            <li>Delete is also remove delivery from database</li>
                        </ul>
                        <div class=" text-end mt-2">
                            <Button class="fw-medium" color="success" variant="text" @click="approveDeliveryBoy">
                                {{ $t('approve') }}
                            </Button>
                            <Button class="fw-medium ms-3" color="danger" variant="text" @click="deleteDeliveryBoy">
                                {{ $t('delete') }}
                            </Button>
                        </div>
                    </div>

                </VItem>
            </div>

            <Row>


                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('general')" icon="edit_note">

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
                                    <template v-if="delivery_boy.shop">
                                        <FormLabel>{{ $t('shop') }}</FormLabel>
                                        <router-link
                                            :to="{name: 'admin.shops.edit', params: {id:delivery_boy.shop_id}}">
                                            <h5>{{ delivery_boy.shop?.name }}</h5>
                                        </router-link>
                                    </template>
                                    <template v-else-if="delivery_boy.zone_id">
                                        <FormLabel>{{ $t('zone') }}</FormLabel>
                                        <router-link
                                            :to="{name: 'admin.zones.edit', params: {id:delivery_boy.zone_id}}">
                                            <h5>{{ delivery_boy.zone?.name ?? '-' }}</h5>
                                        </router-link>

                                    </template>
                                </Col>
                            </Row>


                            <FileUpload
                                :default-files="avatar_helper.defaultFiles" :height="126" :max-files="1"
                                v-on:file-added="avatar_helper.onFileUpload"
                                preview-as-list
                                show-file-manager
                                :label="$t('avatar')"
                                v-on:file-removed="onAvatarRemoved"
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
                            <FormSelect :data="identity_types" :helper="identity_type_helper"
                                        :onSelected="(e)=>delivery_boy.identity_type=e" name="identity_type"
                                        :placeholder="$t('select_identity_type')"
                                        :label="$t('identity_type')"
                                        :errors="errors"
                                        required
                                        :selected="delivery_boy.identity_type"/>

                            <FormInput v-model="delivery_boy.identity_number" :errors="errors"
                                       :label="$t('identity_number')"
                                       name="identity_number" required
                                       type="text"/>

                            <FormInput v-model="delivery_boy.vehicle_number" :errors="errors"
                                       :label="$t('vehicle_number')"
                                       name="vehicle_number" required
                                       type="text"/>


                            <FileUpload
                                :default-files="identity_image_helper.defaultFiles" :height="126"
                                :max-files="1"
                                preview-as-list
                                show-file-manager
                                :label="$t('identity_image')"
                                v-on:file-added="identity_image_helper.onFileUpload"
                                v-on:file-removed="onIdentityImageRemoved"
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

            <div class="text-end  mb-3">
                <UpdateButton :loading="loading" @click="update"/>
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
import {handleException} from "@js/services/api/handle_exception";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import {ToastNotification} from "@js/services/toast_notification";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import DeliveryIdentityTypeMixin from "@js/shared/mixins/DeliveryIdentityTypeMixin";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, ILocalFile} from "@js/types/models/models";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import {defineComponent} from "vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        FormPasswordInput,
        UpdateButton,

        ...Components, Layout
    },
    mixins: [FormMixin, DeliveryIdentityTypeMixin, UtilMixin],
    data() {
        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            delivery_boy: {} as IDeliveryBoy,
            avatar_helper: new FileUploadHelper({max: 1}),
            identity_image_helper: new FileUploadHelper({max: 1}),
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("delivery_boys"),
                    route: 'admin.delivery_boys.index',
                },
                {
                    text: this.$t("edit"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async approveDeliveryBoy() {
            try {
                const response = await Request.patchAuth<IData<any>>(adminAPI.delivery_boys.approve(this.id));
                if (response.success()) {
                    this.delivery_boy.approved = true;
                    ToastNotification.success(this.$t('delivery_boy_approved'))
                }
            } catch (error) {
                handleException(error);
            }
        },
        async deleteDeliveryBoy() {
            try {
                const response = await Request.deleteAuth<IData<any>>(adminAPI.delivery_boys.delete(this.id));
                if (response.success()) {
                    NavigatorService.goBackOrRoute({name: 'admin.delivery_boys.index'});
                    ToastNotification.success(this.$t('delivery_boy_archived'))
                }
            } catch (error) {
                handleException(error);
            }
        },

        async onAvatarRemoved(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(adminAPI.delivery_boys.remove_avatar(this.id));
                    if (response.success()) {
                        this.avatar_helper.removeFile(file);
                        ToastNotification.success(this.$t('avatar_deleted'));
                    } else {
                        ToastNotification.error(this.$t('avatar_deleted_is_not_deleted'));
                    }
                } catch (error) {
                    handleException(error);
                }
            } else {
                this.avatar_helper.removeFile(file);
            }
        },
        async onIdentityImageRemoved(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(adminAPI.delivery_boys.remove_identity_image(this.id));
                    if (response.success()) {
                        this.identity_image_helper.removeFile(file);
                        ToastNotification.success(this.$t('identity_image_deleted'));
                    } else {
                        ToastNotification.error(this.$t('identity_image_is_not_deleted'));
                    }
                } catch (error) {
                    handleException(error);
                }
            } else {
                this.identity_image_helper.removeFile(file);
            }
        },
        async update() {
            this.loading = true;
            try {
                const response = await Request.patchAuth(adminAPI.delivery_boys.update(this.id),
                    {
                        ...this.delivery_boy,
                        'avatar': this.avatar_helper.getImageDataFile(),
                        'identity_image': this.identity_image_helper.getImageDataFile(),
                    });

                if (response.success()) {
                    ToastNotification.success(this.$t('delivery_boy_is_updated'));
                    RequestCache.removeSimilar(adminAPI.delivery_boys.get);
                    NavigatorService.goBackOrRoute({name: 'admin.delivery_boys.index'});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        }
    },
    async mounted() {
        this.setTitle(this.$t('edit_delivery_boy'));
        try {
            const delivery_boy: IDeliveryBoy = (await Request.getAuth<IData<IDeliveryBoy>>(adminAPI.delivery_boys.show(this.id))).data.data;
            this.avatar_helper.addDefaultFile({url: delivery_boy.avatar});
            this.identity_image_helper.addDefaultFile({url: delivery_boy.identity_image});
            this.delivery_boy = delivery_boy;
            this.page_loading = false
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
