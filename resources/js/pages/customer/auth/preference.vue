<template>
    <Layout>
        <div class="d-flex align-items-center justify-content-between">
            <PageHeader :title="$t('preference')"/>

            <div :class="[{'mb-3': selected_categories.length===0, 'mb-2': selected_categories.length!==0}]" class="">
                <Button class="fw-semibold" color="danger" flexed-icon variant="text" @click="submit">
                    {{ $t('skip') }}
                    <Icon v-if="selected_categories.length===0" class="ms-1" icon="arrow_right_alt" size="18"></Icon>
                </Button>
                <Button v-if="selected_categories.length!==0" class="fw-semibold ms-3 px-2 py-1" color="primary"
                        flexed-icon
                        @click="submit">
                    {{ $t('next') }}
                    <Icon class="ms-1" icon="arrow_right_alt" size="18"></Icon>
                </Button>
            </div>
        </div>


        <PageLoading :loading="page_loading">
            <Row v-if="categories.length>0">
                <Col xxl="2" lg="3" md="4" xs="6"  v-for="category in categories" :key="category.id">
                    <Card :class="[{'border': isSelected(category)}]"
                          class="cursor-pointer position-relative"
                          @click="selectCategory(category)">
                        <CardBody>
                            <div class="text-center">
                                <NetworkImage
                                    :placeholder-src="categoryPlaceholder"
                                    :src="category.image"
                                    class="img-fluid"
                                    height="120"
                                    object-fit="contain" rounded/>
                            </div>
                            <h5
                                class="text-center mt-2-5 mb-0 fw-medium">
                                {{ category.name }}
                            </h5>


                        </CardBody>
                        <div v-if="isSelected(category)"
                             class="p-1 preference-category-check bg-primary text-white">
                            <Icon icon="check" size="16"></Icon>
                        </div>

                    </Card>


                </Col>
            </Row>
            <div v-else class="text-center mt-5">
                <h4 class="fw-medium mb-3">{{ $t('there_is_no_category') }}</h4>
            </div>


        </PageLoading>
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from "vue";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import StarRating from "@js/components/shared/StarRating.vue";
import {ICategory} from "@js/types/models/category";
import {array_contains_unique, array_toggle} from "@js/shared/array_utils";
import {useCustomerDataStore} from "@js/services/state/states";
import NavigatorService from "@js/services/navigator_service";

export default defineComponent({
    components: {StarRating, Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            categories: [] as ICategory[],
            selected_categories: [] as ICategory[],
        };
    },
    computed: {},
    methods: {
        selectCategory(category: ICategory): void {
            this.selected_categories = array_toggle(this.selected_categories, category);
        },
        isSelected(category: ICategory): boolean {
            return array_contains_unique(this.selected_categories, category)
        },
        submit() {
            useCustomerDataStore().updateCategoryPreference(this.selected_categories.map((i) => i.id));
            this.$router.push({name: NavigatorService.getCustomerFirstRoute()});
        },
        async getCategories(): Promise<void> {
            this.page_loading = true;
            try {
                const response = await Request.get<IData<ICategory[]>>(customerAPI.categories.get);
                this.categories = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.page_loading = false;
        },


    },
    mounted() {
        this.setTitle(this.$t('preference'));
    },
    created() {
        this.getCategories();
    }
});

</script>

<style scoped>
.preference-category-check {
    position: absolute;
    right: -12px;
    top: -12px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
