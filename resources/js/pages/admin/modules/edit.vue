<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_module')"/>

        <PageLoading :loading="page_loading">

            <Row class="mt-3">
                <Col lg="6">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="active" :checked="active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody>



                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="module.type" :errors="errors" :label="$t('type')"
                                               name="type" disabled>

                                    </FormInput>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="module.title" :errors="errors"
                                               :label="$t('title')"  name="title">
                                        <template #label-suffix>
                                            <InfoTooltip tooltip="It should be unique, so that other module can't conflict"/>
                                        </template>
                                    </FormInput>
                                </Col>


                            </Row>

                            <div class="text-end">
                                <UpdateButton :loading="loading" @click="()=>update(false)" />
                            </div>

                        </CardBody>
                    </Card>
                </Col>

            </Row>

            <VModal v-model="show_alert_modal">
                <Card class="mb-0">
                    <CardHeader :title="$t('inactive_alert')"
                                icon="warning"></CardHeader>
                    <CardBody>
                        <p class="mb-1-5">{{ $t('there_is_few_other_things_also_become_inactive') }}</p>
                        <p class="mb-1 fw-medium">{{ $t('connected_to') }}</p>
                        <ul>
                            <li>{{ $t('related_shops') }}</li>
                            <li>{{ $t('related_products') }}</li>
                            <li>{{ $t('related_addons') }}</li>
                        </ul>

                        <div class="text-end">
                            <Button class="me-2" color="secondary" variant="soft"
                                    @click="()=>this.show_alert_modal=false">
                                {{ $t('cancel') }}
                            </Button>
                            <Button color="danger" flexed-icon @click="()=>this.update(true)">
                                <Icon class="me-1-5" icon="warning" type="msr"/>
                                {{ $t('update') }}
                            </Button>
                        </div>
                    </CardBody>
                </Card>
            </VModal>

        </PageLoading>

    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import ChargeTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {IModule} from "@js/types/models/module";
import VModal from "@js/components/VModal.vue";
import {deepCopy} from "@js/shared/utils";
import {RequestCache} from "@js/services/api/cache";


export default defineComponent({
    components: {
        VModal,
        UpdateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin, ChargeTypeMixin],
    data() {

        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            module: {} as IModule,
            show_alert_modal: false,
            active: true,
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('modules'),
                    route: 'admin.modules.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async update(confirmation = false) {
            if (this.loading)
                return;

            if (!confirmation && this.module.active && !this.active) {
                this.show_alert_modal = true;
                return;
            }
            this.show_alert_modal = false;
            try {

                const response = await Request.patchAuth(adminAPI.modules.update(this.id), {
                    ...this.module,
                    active: this.active
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('module_updated'));
                    RequestCache.removeSimilar(adminAPI.modules.get);
                    NavigatorService.goBackOrRoute({name: 'admin.modules.index'});
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error)
                }
            }
            this.loading = false;
        },

    },
    async mounted() {
        this.setTitle(this.$t('edit_module'))
        this.module = (await Request.getAuth<IData<IModule>>(adminAPI.modules.show(this.id))).data.data;
        this.active = deepCopy(this.module.active)
        this.page_loading = false;
    }
});

</script>
