<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_unit')"/>

        <Row>
            <Col lg="6">
                <Card>
                    <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                        <div class="float-end">
                            <FormSwitch v-model="unit.active" :errors="errors"
                                        :label="$t('active')"
                                        name="active" no-spacing/>
                        </div>
                    </CardHeader>

                    <CardBody>

                        <Row>
                            <Col lg="6">
                                <FormInput v-model="unit.type" :errors="errors"
                                           :label="$t('type')" :placeholder="$t('enter_unit_type')" name="type"
                                           required/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="unit.title" :errors="errors" :label="$t('title')"
                                           name="title" required>
                                    <template #label-suffix>
                                        <InfoTooltip tooltip="It should be unique, so that other unit can't conflict"/>
                                    </template>
                                </FormInput>
                            </Col>
                        </Row>

                        <TextEditor v-model="unit.description" :label="$t('description')"/>


                        <div class="text-end mt-3">
                            <CreateButton :loading="loading" @click="create">{{ $t('create') }}</CreateButton>
                        </div>

                    </CardBody>
                </Card>
            </Col>

        </Row>

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
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import {IUnit} from "@js/types/models/unit";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import {Shop} from "@js/types/models/shop";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";


export default defineComponent({
    components: {
        CreateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {

        const shop_helper = Shop.select_helper();
        const breadcrumb_items = [
            {
                text: this.$t('units'),
                route: 'admin.units.index',
            },
            {
                text: this.$t('create'),
                active: true,
            },
        ] as IBreadcrumb[];
        return {
            shop_helper,
            breadcrumb_items,
            id: this.$route.params.id,
            loading: false,
            unit: {
                active: true
            } as IUnit,

        }
    },
    computed: {

    },
    methods: {

        async create() {
            this.loading = true;
            try {

                const response = await Request.postAuth(adminAPI.units.create, {
                    ...this.unit
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('unit_created'));
                    RequestCache.removeSimilar(adminAPI.units.get);
                    NavigatorService.goBackOrRoute('admin.units.index');
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
        this.setTitle(this.$t('create_units'))

    }
});

</script>

