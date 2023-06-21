<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_employee')"/>

        <PageLoading :loading="page_loading">
            <Row>

                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('general')" icon="dns" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="seller.active" no-spacing
                                            :errors="errors"
                                            :label="$t('active')" name="active"/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="seller.first_name" :errors="errors"
                                               :label="$t('first_name')" name="first_name"
                                               required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="seller.last_name" :errors="errors"
                                               :label="$t('last_name')" name="last_name"
                                               required/>
                                </Col>
                            </Row>

                            <FormInput v-model="seller.mobile_number" :errors="errors" :label="$t('mobile_number')"
                                       name="mobile_number" required
                                       type="tel"/>

                            <FormInput v-model="seller.email" :errors="errors" :label="$t('email')"
                                       name="email" required type="email"/>

                            <FormPasswordInput v-model="seller.password" :errors="errors" :label="$t('password')"
                                       :placeholder="$t('password_(if_you_need_to_change)')"
                                       name="password" no-spacing required />


                        </CardBody>
                    </Card>

                </Col>


                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('identity')" icon="badge" type="msr"/>

                        <CardBody class="pb-0">

                            <Row v-if="!seller.is_owner">
                                <Col lg="8">
                                    <div>

                                        <FormSelect :data="roles" :helper="role_helper"
                                                    :onSelected="(e)=>seller.role_id=e"
                                                    :errors="errors"
                                                    :label="$t('role')"
                                                    :selected="seller.role_id"
                                                    class="" name="role_id" required
                                                    :placeholder="$t('select_role')"/>

                                    </div>
                                </Col>
                            </Row>


                            <FileUpload
                                :label="$t('avatar')" container-height="126"
                                max-files="1"
                                :default-files="avatar_helper.defaultFiles"
                                preview-as-list
                                v-on:file-added="avatar_helper.onFileUpload"
                                v-on:file-removed="onFileRemoved"
                            />

                        </CardBody>
                    </Card>

                </Col>

                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('bank_details')" icon="account_balance" type="msr"/>
                        <CardBody class="pb-0">
                            <VItem align-items="center" class="p-1-5 mb-2" color="info" display="flex" rounded soft>
                                <Icon icon="info" type="msr"></Icon>
                                <strong class="ms-1">{{ $t('be_careful') }}:</strong>&nbsp;
                                {{ $t(' you_will_receive_all_payments_in_this_bank') }}
                            </VItem>


                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="seller.bank_name" :errors="errors"
                                               :label="$t('bank_name')"
                                               :placeholder="$t('enter_a_bank_name')" name="bank_name"></FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="seller.account_number" :errors="errors"
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
                <UpdateButton :loading="loading" @click="update"/>
            </div>
        </PageLoading>

    </Layout>
</template>

<script lang="ts">

import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {Components} from "@js/components/components";
import Layout from "@js/pages/seller/layouts/Layout.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import {IData} from "@js/types/models/data";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {ISeller, ISellerRole, SellerRole} from "@js/types/models/seller";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {IBreadcrumb} from "@js/types/models/models";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        FormPasswordInput,
        UpdateButton,
        VModal,
        LoadingButton,
        ...Components,
        Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            seller: {} as ISeller,
            roles: [] as ISellerRole[],
            selected_role: null,
            avatar_helper: new FileUploadHelper(),
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("employees"),
                    route: 'seller.employees.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        },
        role_helper: SellerRole.select_helper,
    },
    methods: {

        async onFileRemoved(file) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(sellerAPI.sellers.remove_avatar(this.id));
                    if (response.success()) {
                        this.avatar_helper.onFileRemoved(file);

                        ToastNotification.success(this.$t('seller_image_deleted'));
                    } else {
                        ToastNotification.error(this.$t('seller_image_is_not_deleted'));
                    }
                } catch (error) {
                    handleException(error);

                }
            } else {
                this.avatar_helper.onFileRemoved(file);

            }
        },
        async update() {
            this.loading = true;
            try {
                const response = await Request.patchAuth(sellerAPI.sellers.update(this.id), {
                    ...this.seller, 'avatar': this.avatar_helper.getImageDataFile(),
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('seller_is_edited'));
                    RequestCache.removeSimilar(sellerAPI.sellers.get);
                    NavigatorService.goBackOrRoute('seller.employees.index');
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
        this.setTitle(this.$t('edit_seller'))

        try {
            const response = await Request.getAuth<IData<ISeller>>(sellerAPI.sellers.show(this.id));
            const roleResponse = await Request.getAuth<IData<ISellerRole[]>>(sellerAPI.roles.get);
            this.seller = response.data.data;
            this.roles = roleResponse.data.data;
            this.selected_role = this.seller.role_id;
            this.avatar_helper.addDefaultFile({url: this.seller.avatar});
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }

});

</script>
