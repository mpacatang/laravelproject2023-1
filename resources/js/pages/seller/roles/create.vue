<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_role')"/>

        <Row>
            <Col xl="6" lg="8">

                <Card>
                    <CardHeader icon="edit_note" :title="$t('general')">
                        <div class="float-end">
                            <FormSwitch v-model="role.active" :errors="errors"
                                        :label="$t('active')"
                                        name="active" no-spacing/>
                        </div></CardHeader>
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
                            <CreateButton :loading="loading" @click="create"/>
                        </div>
                    </CardBody>
                </Card>
            </Col>
        </Row>

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
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";
import {ISellerRole, SellerRole} from "@js/types/models/seller";
import {IBreadcrumb} from "@js/types/models/models";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        CreateButton,
        VModal,
        LoadingButton,
        ...Components,
        Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            role: {
                active: true
            } as ISellerRole,
            loading: false,
            permissions: SellerRole._getSelectableRoles,
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
                    text: this.$t('create'),
                    active: true,
                },
            ];
        },

    },
    methods: {
        async create() {
            this.loading = true;
            try {
                let permissions = [];
                for (const permission of this.permissions) {
                    if (permission.active) {
                        permissions.push(permission.value);
                    }
                }

                const response = await Request.postAuth(sellerAPI.roles.create, {
                    ...this.role, 'permissions': permissions
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('role_is_created'));
                    RequestCache.removeSimilar(sellerAPI.roles.get);
                    NavigatorService.goBackOrRoute({name:'seller.roles.index'});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                    ToastNotification.unknownError();
                }
            }
            this.loading = false;
        },
    },
    async mounted() {
        this.setTitle(this.$t('create_role'))

    }

});

</script>
