<template>
    <Button class="p-1-5" color="success" flexed-icon radius="md" variant="soft">
        <Icon :icon="icon" size="19"/>
    </Button>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import Button from "@js/components/buttons/Button.vue";
import Icon from "@js/components/Icon.vue";

export default defineComponent({
    components: {Icon, Button},
    props: {
        icon: {
            type: String,
            default: 'edit'
        },
    },
});
</script>

<style scoped>

</style>
