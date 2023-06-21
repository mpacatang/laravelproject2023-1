<template>
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="error-text-box">
                <svg viewBox="0 0 600 200">
                    <!-- Symbol-->
                    <symbol id="s-text">
                        <text dy=".35em" text-anchor="middle" x="50%" y="50%">310!</text>
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
                <h3 class="mt-0 mb-2">Whoops! You are blocked by admin</h3>
                <Button class="mt-3" flexed-icon @click="logout">
                    <Icon icon="logout"></Icon>
                    {{ $t('logout') }}
                </Button>

            </div>
            <!-- end row -->

        </div> <!-- end col -->
    </div>
</template>
<script lang="ts">
import Layout from "../layouts/Layout.vue";
import {defineComponent} from "vue";
import Button from "@js/components/buttons/Button.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Icon from "@js/components/Icon.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {useSellerDataStore} from "@js/services/state/states";

export default defineComponent({
    components: {Icon, Button, Layout},
    mixins: [UtilMixin],
    methods: {
        async logout() {
            try {
                const response = await Request.postAuth(sellerAPI.auth.logout, {});
            } catch (error) {
                handleException(error);
            } finally {
            }
            useSellerDataStore().logout();
            this.$router.push({name: 'seller.login'});
        }
    }
})
</script>
