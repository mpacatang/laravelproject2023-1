<template>
    <Layout>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="error-text-box">
                    <svg viewBox="0 0 600 200">
                        <!-- Symbol-->
                        <symbol id="s-text">
                            <text text-anchor="middle" x="50%" y="50%" dy=".35em">309!</text>
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
                    <h3 class="mt-0 mb-2">You haven't any shop yet</h3>
                    <p class="text-muted mb-3">Please contact admin to assign a shop to you & Then
                        you can access all resource related to your shop</p>

                </div>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
    </Layout>
</template>
<script lang="ts">
import Layout from "../layouts/Layout.vue";

export default {
    components: {Layout}
}
</script>
