<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="seller.is_owner?$t('create_shop_owner'):$t('create_shop_employee')"/>

        <PageLoading :loading="page_loading">

            <Row>
                <Col lg="4" md="6">

                    <FormSelect :data="shops" :errors="errors" :helper="shop_helper"
                                :onSelected="onSelectShop"
                                :placeholder="$t('select_shop_to_continue')"

                                :selected="selected_shop"
                                :label="$t('for_shop')"
                                name="shop_id"/>
                </Col>
            </Row>

            <PageLoading v-if="selected_shop" :loading="shop_data_loading">
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

                            <CardHeader :title="$t('identity')" icon="badge"/>

                            <CardBody class="pb-0">

                                <Row>
                                    <Col lg="4">
                                        <FormSwitch v-model="seller.is_owner" :errors="errors" :label="$t('is_owner')"
                                                    name="is_owner">
                                            <template #label-suffix>
                                                <InfoTooltip
                                                    tooltip="If you need to change owner of this shop, then check it."></InfoTooltip>
                                            </template>
                                        </FormSwitch>

                                    </Col>
                                    <Col lg="8">

                                        <div :class="[{'item-disabled': seller.is_owner}]">
                                            <FormSelect :data="roles" :helper="role_helper"
                                                        :onSelected="onChangeRole"
                                                        :errors="errors" :label="$t('role')"
                                                        name="role_id"
                                                        :placeholder="$t('select_role')"/>
                                        </div>
                                    </Col>
                                </Row>



                                <FileUpload
                                    container-height="126" max-files="1"
                                    v-on:file-added="avatar_helper.onFileUpload"
                                    preview-as-list
                                    show-file-manager
                                    :label="$t('avatar')"
                                    v-on:file-removed="avatar_helper.onFileRemoved"
                                />


                            </CardBody>
                        </Card>

                    </Col>



                </Row>
                <div class="text-end mb-3">
                    <CreateButton :loading="loading" @click="create"/>
                </div>
            </PageLoading>
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
import {IBreadcrumb} from "@js/types/models/models";
import {IShop, Shop} from "@js/types/models/shop";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {AdminRole} from "@js/types/models/admin";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        CreateButton,
        VModal,
        LoadingButton,
        ...Components,
        Layout
    },
    mixins: [FormMixin, UtilMixin, UtilMixin],
    data() {
        const shop_helper= Shop.select_helper();

        return {
            shop_helper,
            loading: false,
            page_loading: true,
            shop_data_loading: true,
            seller: {is_owner: false, active: true} as ISeller,
            roles: [] as ISellerRole[],
            selected_role: null,
            avatar_helper: new FileUploadHelper(),
            shops: [] as IShop[],

            selected_shop: null
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("sellers"),
                    route: 'admin.sellers.index',
                },
                {
                    text: this.$t('create'),
                    active: true,
                },
            ];
        },
        role_helper: AdminRole.select_helper,
    },
    methods: {
        onSelectShop(id) {
            if (this.selected_shop != id) {
                this.selected_shop = id;
                if (id != null) {
                    this.selected_role = null;
                    this.getData();
                }
            }
        },
        onChangeRole(id) {
            this.selected_role = id;
        },
        async create() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.sellers.create, {
                    ...this.seller,
                    'avatar': this.avatar_helper.getImageDataFile(),
                    'role_id': this.selected_role,
                    'shop_id': this.selected_shop
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('seller_is_created'));
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
        async getData() {
            this.shop_data_loading = true;
            try {
                const roleResponse = await Request.getAuth<IData<ISellerRole[]>>(adminAPI.shops.roles(this.selected_shop));
                this.roles = roleResponse.data.data;
                this.shop = false;
                this.shop_data_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
    },

    async mounted() {
        this.setTitle(this.$t('create_seller'))
        try {
            const response = await Request.getAuth<IData<IShop[]>>(adminAPI.shops.get);
            this.shops = response.data.data;
            this.page_loading = false;

        } catch (error) {
            handleException(error);
        }
    }

});

</script>
