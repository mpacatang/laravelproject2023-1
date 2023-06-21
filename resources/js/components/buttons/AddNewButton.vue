<template>
    <Button flexed-icon  color="primary" radius="4" >
        <Icon class="me-1" icon="add" size="sm"></Icon>
        {{ $t('add_new') }}
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
