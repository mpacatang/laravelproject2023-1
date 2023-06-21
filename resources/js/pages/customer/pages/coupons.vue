<template>
    <Layout>
        <PageHeader :title="$t('coupons')">

        </PageHeader>

        <PageLoading :loading="page_loading">
            <template v-if="coupons==null ||  coupons.length===0">
                <p class="text-center mt-2 fw-medium">
                    {{ $t('there_is_no_any_coupon_available') }}
                </p>
            </template>
            <Row v-else class="row-cols-2 row-cols-lg-3 row-cols-xxl-4 row-cols-xxxl-5 g-lg-3 g-md-2-5 g-2">
                <Col v-for="coupon in coupons">
                    <Card class="h-100 cursor-pointer" @click="copyCoupon(coupon)">
                        <CardBody>
                            <p class="fw-semibold mb-1">{{ coupon.code }}</p>
                            <p class="mb-1">{{ coupon.description }}</p>
                            <div class="d-flex justify-content-between">
                              <span
                                  class="text-muted">{{
                                      $t('expired_at')
                                  }} {{ getFormattedDate(new Date(coupon.expired_at)) }}</span>

                            </div>
                            <hr v-if="coupon.delivery_free" class="dashed">
                            <span v-if="coupon.delivery_free" class="text-success">
                            {{ $t('delivery_free') }}
                        </span>
                        </CardBody>
                    </Card>
                </Col>
            </Row>



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
import Product, {IProduct} from "@js/types/models/product";
import {IShop} from "@js/types/models/shop";
import StarRating from "@js/components/shared/StarRating.vue";
import {ICoupon} from "@js/types/models/coupon";
import NavigatorService from "@js/services/navigator_service";
import {ToastNotification} from "@js/services/toast_notification";

export default defineComponent({
    components: {Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            coupons: [] as ICoupon[],
        };
    },
    computed: {},
    methods: {
        copyCoupon(coupon: ICoupon){
            NavigatorService.copyToClipboard(coupon.code);
            ToastNotification.success('"'+coupon.code+'" copied')
        },
        async getCoupons() {
            this.page_loading = true;
            try {
                const response = await Request.getAuth<IData<ICoupon[]>>(customerAPI.coupons.get, {forced: true});
                this.coupons = response.data.data;
            } catch (error) {
                handleException(error);
            }
            this.page_loading = false;
        },

    },
    mounted() {
        this.setTitle(this.$t('coupons'));
    },
    created() {
        this.getCoupons();
    }
});

</script>

<style scoped>

</style>
