<template>
    <Layout>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="error-text-box">
                    <svg viewBox="0 0 600 200">
                        <!-- Symbol-->
                        <symbol id="s-text">
                            <text dy=".35em" text-anchor="middle" x="50%" y="50%">312!</text>
                        </symbol>
                        <!-- Duplicate symbols-->
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                    </svg>
                </div>
                <div class="text-center">
                    <h3 class="mt-0 mb-3 fw-medium">{{$t('self_delivery_is_not_active')}}</h3>
                    <router-link :to="{name: 'seller.shops.settings'}">
                        <Button variant="soft" color="bluish-purple" class="fw-medium">
                            <Icon icon="local_shipping"  class="me-1-5"></Icon>
                            {{ $t('make_it_active') }} ->
                        </Button>
                    </router-link>

                </div>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
    </Layout>
</template>
<script lang="ts">
import Layout from "@js/pages/seller/layouts/Layout.vue";
import {defineComponent} from 'vue';
import Button from "@js/components/buttons/Button.vue";
// import Icon from "@js/components/Icon.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Icon from "@js/components/Icon.vue";

export default defineComponent({
    components: {Icon, Button, Layout},
    mixins: [UtilMixin]
})
</script>
