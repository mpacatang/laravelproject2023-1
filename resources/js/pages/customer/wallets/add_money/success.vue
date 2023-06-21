<template>
    <div class="mt-5 text-center">
        <Icon icon="task_alt" size="50" color="success"></Icon>
        <h3 class=" fw-semibold mt-3">{{ $t('payment_success') }}</h3>
        <p class="mt-2 fw-medium">{{ $t('we_will_redirect_you_in') }} 5 seconds...</p>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Icon from "@js/components/Icon.vue";

export default defineComponent({
    components: {Icon},
    mixins: [UtilMixin],
    methods: {
        handleSuccess() {
            if (window.flutter_inappwebview != null) {
                window.flutter_inappwebview
                    .callHandler('flutter_channel', true)
            }
            if (window.opener) {
                window.opener.postMessage({'payment_success': true});
            }
            window.postMessage({'payment_success': true})

            window.close();
        },
    },
    mounted() {
        this.setTitle(this.$t('payment_success'));
        setTimeout(() => this.handleSuccess(), 5000);
    },
    async created() {
    }
});
</script>

<style scoped>
</style>
