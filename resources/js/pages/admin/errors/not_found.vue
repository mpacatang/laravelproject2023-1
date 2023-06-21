<template>
    <Layout>
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-4 mb-4 text-center">
                <div class="error-text-box">
                    <svg viewBox="0 0 600 200">
                        <!-- Symbol-->
                        <symbol id="s-text">
                            <text dy=".35em" text-anchor="middle" x="50%" y="50%">404!</text>
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
                    <h3 class="mt-0 mb-3">Whoops! Page not found </h3>
                    <Button v-if="isAuth" @click="goToHome">{{ $t('go_to_home') }}</Button>
                    <Button v-else @click="goToLogin">{{ $t('login') }}</Button>

                </div>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
    </Layout>
</template>
<script lang="ts">
import Layout from "../layouts/Layout.vue";
import {defineComponent} from "vue";
import Button from "@js/components/buttons/Button.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {useAdminDataStore} from "@js/services/state/states";

export default defineComponent({
    components: {Button, Layout},
    mixins: [UtilMixin],
    computed: {
        isAuth() {
            return useAdminDataStore().isLoggedIn();
        }
    },
    methods: {
        goToHome() {
            this.$router.push({name: 'admin.dashboard'});
        },
        goToLogin() {
            this.$router.push({name: 'admin.login'});
        }
    }

});
</script>
