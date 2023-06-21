<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_sub_category')"/>

        <Row>
            <Col lg="8" xl="6">
                <Card>
                    <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                        <div class="float-end">
                            <FormSwitch v-model="sub_category.active" :errors="errors" :label="$t('active')"
                                        name="active" no-spacing/>
                        </div>
                    </CardHeader>

                    <CardBody>

                        <FormInput v-model="sub_category.name" :errors="errors"
                                   :label="$t('name')" name="name"/>

                        <FormSelect :data="categories" :errors="errors"
                                    :helper="category_select_helper"
                                    :label="$t('category')" :on-selected="(e)=>sub_category.category_id=e"
                                    :selected="sub_category.category_id" name="category_id">
                            <template #label-suffix>
                                <InfoTooltip tooltip="You can't change category after creating"></InfoTooltip>
                            </template>
                        </FormSelect>

                        <div class="text-end mt-3">
                            <CreateButton :loading="loading" @click="create"/>
                        </div>
                    </CardBody>
                </Card>
            </Col>
        </Row>
    </Layout>
</template>


<script lang="ts">

import Layout from "../layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {ICategory, ISubCategory} from "@js/types/models/category";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb, IFormSelectOption} from "@js/types/models/models";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        CreateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            loading: false,
            sub_category: {active: true} as ISubCategory,
            categories: [] as ICategory[],
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
                    text: this.$t("create"),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async create() {
            this.loading = true;
            try {
                const response = await Request.postAuth(adminAPI.sub_categories.create, {
                    ...this.sub_category,
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('sub_category_is_created'));
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
        this.setTitle(this.$t('create_sub_category'))
        try {
            const response = await Request.getAuth<IData<ICategory[]>>(adminAPI.categories.get);
            this.categories = response.data.data;
        } catch (error) {
            handleException(error);
        }
    }
});

</script>
