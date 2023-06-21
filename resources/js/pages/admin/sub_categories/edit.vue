<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_sub_category')"/>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="8" xl="6">
                    <Card>
                        <CardHeader :title="$t('general')" icon="create_new_folder" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="sub_category.active" :errors="errors" :label="$t('active')"
                                            name="active" noSpacing/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <FormInput v-model="sub_category.name" :errors="errors"
                                       :label="$t('name')" name="name"/>

                            <FormSelect :data="categories" :errors="errors"
                                        :helper="category_select_helper"
                                        :label="$t('category')" :on-selected="(e)=>sub_category.category_id=e"
                                        :selected="sub_category.category_id" disabled name="category_id">
                                <template #label-suffix>
                                    <InfoTooltip tooltip="You can't change category after created"></InfoTooltip>
                                </template>
                            </FormSelect>


                            <div class="text-end mt-3">
                                <UpdateButton :loading="loading" @click="()=>update()"/>

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
                            <li>{{ $t('related_products') }}</li>
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
import {ICategory, ISubCategory} from "@js/types/models/category";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, IFormSelectOption} from "@js/types/models/models";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import VModal from "@js/components/VModal.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        VModal,
        UpdateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {

        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            sub_category: null as ISubCategory,
            categories: [],
            show_alert_modal: false,
        }
    },
    computed: {
        category_select_helper(): IFormSelectOption {
            return {
                option: {
                    value: 'id',
                    label: 'name'
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('sub_categories'),
                    route: 'admin.sub_categories.index',
                },
                {
                    text: this.$t("edit"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async update(confirmation = false) {
            if (this.loading)
                return;

            if (!confirmation && !this.sub_category.active) {
                this.show_alert_modal = true;
                return;
            }
            this.show_alert_modal = false;

            this.loading = true;
            try {
                const response = await Request.patchAuth(adminAPI.sub_categories.update(this.id), {
                    ...this.sub_category,
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('sub_category_is_edited'));
                    RequestCache.removeSimilar(adminAPI.sub_categories.get);
                    NavigatorService.goBackOrRoute({name: 'admin.sub_categories.index'});
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
        this.setTitle(this.$t('edit_sub_category'))
        try {
            const response = await Request.getAuth<IData<ICategory[]>>(adminAPI.categories.get);
            this.categories = response.data.data;
            const subCategoryResponse = await Request.getAuth<IData<ISubCategory>>(adminAPI.sub_categories.show(this.id));
            this.sub_category = subCategoryResponse.data.data;
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
