<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_role')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6" lg="8">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note">
                            <div class="float-end">
                                <FormSwitch v-model="role.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>
                        <CardBody>

                            <FormInput v-model="role.title" :errors="errors"
                                       :label="$t('title')" name="title"/>

                            <FormLabel name="permissions">{{ $t('permissions') }}</FormLabel>
                            <FormValidationError :errors="errors" name="permissions"/>
                            <Row class="mt-2">
                                <Col lg="4" sm="6" v-for="permission in permissions">
                                    <FormCheckbox v-model="permission.active" :label="$t(permission.value)"
                                                  :name="permission.name"></FormCheckbox>
                                </Col>
                            </Row>

                            <div class="text-end">
                                <UpdateButton :loading="loading" @click="update"/>
                            </div>
                        </CardBody>
                    </Card>
                </Col>
            </Row>
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
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {Components} from "@js/components/components";
import Layout from "@js/pages/seller/layouts/Layout.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {ISellerRole, SellerRole} from "@js/types/models/seller";
import {IData} from "@js/types/models/data";
import {IBreadcrumb} from "@js/types/models/models";
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
    mixins: [FormMixin, DiscountTypeMixin, UtilMixin],
    data() {
        return {
            id: this.$route.params.id,
            page_loading: true,
            loading: false,
            role: {} as ISellerRole,
            permissions: [],
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("roles"),
                    route: 'seller.roles.index',
                },
                {
                    text: this.$t('update'),
                    active: true,
                },
            ];
        },

    },
    methods: {
        async update() {
            this.loading = true;
            try {
                let permissions = [];
                for (const permission of this.permissions) {
                    if (permission.active) {
                        permissions.push(permission.value);
                    }
                }

                const response = await Request.patchAuth(sellerAPI.roles.update(this.id), {
                    ...this.role, 'permissions': permissions
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('role_is_updated'));
                    RequestCache.removeSimilar(sellerAPI.roles.get);
                    NavigatorService.goBackOrRoute({name:'seller.roles.index'});
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
        this.setTitle(this.$t('create_role'))

        try {
            const response = await Request.getAuth<IData<ISellerRole>>(sellerAPI.roles.show(this.id));
            if (response.success()) {
                this.role = response.data.data;
                let selectedPermissions = JSON.parse(this.role.permissions);
                this.permissions = SellerRole.getSelectableRoles(selectedPermissions);
                this.page_loading = false;
            }
        } catch (error) {
            handleException(error);
        }
    }

})


</script>
