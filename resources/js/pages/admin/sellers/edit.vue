<template>
    <Layout>
        <PageLoading :loading="page_loading">

            <PageHeader :items="breadcrumb_items" :title="seller.is_owner?$t('edit_owner'):$t('edit_employees')"/>

            <Row>

                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('info')" icon="edit_note" type="msr">
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

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="seller.mobile_number" :errors="errors"
                                               :label="$t('mobile_number')"
                                               name="mobile_number" required
                                               type="tel">
                                        <template #prefix>
                                            <Icon icon="call" size="18"></Icon>
                                        </template>
                                    </FormInput>

                                </Col>
                                <Col lg="6">

                                    <FormInput v-model="seller.email" :errors="errors" :label="$t('email')"
                                               name="email" required type="email">
                                        <template #prefix>
                                            <Icon icon="mail" size="18"></Icon>
                                        </template>
                                    </FormInput>


                                </Col>
                            </Row>


                            <FormInput v-model="seller.password" :errors="errors" :label="$t('password')"
                                       :placeholder="$t('password_(if_you_need_to_change)')"
                                       name="password" no-spacing required type="password"/>


                        </CardBody>
                    </Card>
                    <Card>
                        <CardHeader :title="$t('bank_details')" icon="account_balance" type="msr"/>
                        <CardBody class="pb-0">
                            <VItem align-items="center" class="p-1-5 mb-2" color="info" display="flex" rounded soft>
                                <Icon icon="info" type="msr"></Icon>
                                <strong class="ms-1">{{ $t('be_careful') }}:</strong>&nbsp;
                                {{ $t('seller_will_receive_all_payments_in_this_bank') }}
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


                <Col xl="6">
                    <Card>

                        <CardHeader :title="$t('identity')" icon="badge" type="msr"/>

                        <CardBody class="pb-0">

                            <Row v-if="!seller.is_owner">
                                <Col lg="8">
                                    <FormSelect :data="roles" :errors="errors"
                                                :helper="role_helper" :label="$t('role')"
                                                :onSelected="(e)=>seller.role_id=e"
                                                :placeholder="$t('select_role')"
                                                :selected="seller.role_id"
                                                name="role_id"/>


                                </Col>
                            </Row>

                            <FileUpload
                                :default-files="avatar_helper.defaultFiles" :height=126
                                :max-files=1
                                :label="$t('avatar')"
                                preview-as-list
                                show-file-manager
                                v-on:file-added="avatar_helper.onFileUpload" v-on:file-removed="onFileRemoved"
                            />
                        </CardBody>
                    </Card>
                </Col>
            </Row>
            <div class="text-end mb-3">
                <UpdateButton :loading="loading" @click="update">{{ $t('update') }}</UpdateButton>
            </div>
        </PageLoading>

    </Layout>
</template>

<script lang="ts">

import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {Components} from "@js/components/components";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import {IData} from "@js/types/models/data";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {ISeller, ISellerRole} from "@js/types/models/seller";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {IBreadcrumb, IFormSelectOption} from "@js/types/models/models";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
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
        role_helper(): IFormSelectOption {
            return {
                option: {
                    value: 'id', label: 'title'
                },
            };
        },
    },
    methods: {

        async onFileRemoved(file) {
            if (file.uploaded) {
                try {
                    const response = await Request.deleteAuth(adminAPI.sellers.remove_avatar(this.id));
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
                const response = await Request.patchAuth(adminAPI.sellers.update(this.id), {
                    ...this.seller, 'avatar': this.avatar_helper.getImageDataFile(),
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('seller_is_updated'));
                    RequestCache.removeSimilar(adminAPI.sellers.get);
                    NavigatorService.goBackOrRoute({name: 'admin.sellers.index'});
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
            const response = await Request.getAuth<IData<ISeller>>(adminAPI.sellers.show(this.id));
            this.seller = response.data.data;
            if (this.seller.shop_id != null) {
                const roleResponse = await Request.getAuth<IData<ISellerRole[]>>(adminAPI.shops.roles(this.seller.shop_id));
                this.roles = roleResponse.data.data;
            }

            this.avatar_helper.addDefaultFile({url: this.seller.avatar});
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }

});

</script>
