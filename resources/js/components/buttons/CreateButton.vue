<template>
    <LoadingButton :loading="loading" color="primary" flexed-icon icon="add" radius="4">
        {{ $t('create') }}
    </LoadingButton>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import Button from "@js/components/buttons/Button.vue";
import Icon from "@js/components/Icon.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";

export default defineComponent({
    components: {LoadingButton, Icon, Button},
    mixins: [UtilMixin],
    props: {
        loading: {
            type: Boolean,
            default: false,
        },
    }

});
</script>

<style scoped>

</style>
