<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_employee')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('general')" icon="edit_note">
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
                                               name="password" no-spacing required/>


                        </CardBody>
                    </Card>

                </Col>


                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('identity')" icon="badge"/>

                        <CardBody class="pb-0">


                            <Row>
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
                                container-height="126" max-files="1"
                                v-on:file-added="avatar_helper.onFileUpload"
                                v-on:file-removed="avatar_helper.onFileRemoved"
                                :label="$t('avatar')"
                                preview-as-list
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
                <CreateButton :loading="loading" @click="create"/>
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
import {IBreadcrumb, IFormSelectOption} from "@js/types/models/models";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import FormPasswordInput from "@js/components/form/FormPasswordInput.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        FormPasswordInput,
        CreateButton,
        VModal,
        LoadingButton,
        ...Components,
        Layout
    },
    mixins: [FormMixin, UtilMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            seller: {
                active: true
            } as ISeller,
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
                    text: this.$t('create'),
                    active: true,
                },
            ];
        },
        role_helper: SellerRole.select_helper,
    },
    methods: {

        async create() {
            this.loading = true;
            try {
                const response = await Request.postAuth(sellerAPI.sellers.create, {
                    ...this.seller, 'avatar': this.avatar_helper.getImageDataFile()
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('seller_is_created'));
                    RequestCache.removeSimilar(sellerAPI.sellers.get);
                    NavigatorService.goBackOrRoute('seller.employees.index');

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
        this.setTitle(this.$t('create_seller'))
        try {
            const roleResponse = await Request.getAuth<IData<ISellerRole[]>>(sellerAPI.roles.get);
            this.roles = roleResponse.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }

});

</script>
