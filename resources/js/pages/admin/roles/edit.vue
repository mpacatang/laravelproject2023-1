<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_role')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xxl="6" xl="8">
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
                            <div class="row mt-2">
                                <Col lg="4" sm="6" v-for="permission in permissions">
                                    <FormCheckbox v-model="permission.active" :label="$t(permission.value)"
                                                  :name="permission.name"></FormCheckbox>
                                </Col>
                            </div>

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
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {Components} from "@js/components/components";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {AdminRole, IAdminRole} from "@js/types/models/admin";
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
            role: {} as IAdminRole,
            permissions: [],
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("roles"),
                    route: 'admin.roles.index',
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

                const response = await Request.patchAuth(adminAPI.roles.update(this.id), {
                    ...this.role, 'permissions': permissions
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('role_is_updated'));
                    RequestCache.removeSimilar(adminAPI.roles.get);
                    NavigatorService.goBackOrRoute({name: 'admin.roles.index'});

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
        this.setTitle(this.$t('create_role'))

        try {
            const response = await Request.getAuth<IData<IAdminRole>>(adminAPI.roles.show(this.id));
            if (response.success()) {
                this.role = response.data.data;
                let selectedPermissions = JSON.parse(this.role.permissions);
                this.permissions = AdminRole.getSelectableRoles(selectedPermissions);
                this.page_loading = false;
            } else {
                ToastNotification.unknownError();
            }
        } catch (error) {
            handleException(error);
        }
    }

})


</script>
