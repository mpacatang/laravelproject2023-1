<template>

    <Button class="p-1-5" color="danger" flexed-icon radius="md" variant="soft">
        <Icon icon="delete" size="19"/>
    </Button>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import Button from "@js/components/buttons/Button.vue";
import Icon from "@js/components/Icon.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";

export default defineComponent({
    components: {Icon, Button},
    mixins: [UtilMixin]
});
</script>

<style scoped>

</style>
