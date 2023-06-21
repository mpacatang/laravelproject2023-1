<template>
    <Layout>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-4 mb-4 text-center">
                <h1 class="text-error mt-4">404</h1>
                <div class="text-center">
                    <h3 class="mt-0 mb-2">Whoops! Page not found </h3>
                    <p class="text-muted mb-3">It's looking like you may have taken a wrong turn. Don't worry...
                        it happens to the best of us. You might want to check your internet connection.
                        Here's a little tip that might help you get back on track.</p>

                </div>

                <Button color="primary" @click="goToHome">{{$t('go_to_home')}}</Button>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
    </Layout>
</template>
<script lang="ts">
import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from "vue";
import Button from "@js/components/buttons/Button.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";

export default defineComponent({
    components: {Button, Layout},
    mixins: [UtilMixin],
    methods:{
        goToHome(){
            this.$router.push({name: 'customer.home'})
        }
    }
})
</script>
