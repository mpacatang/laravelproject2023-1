<template>
    <button ref="tip_ref" :data-bs-placement="position" :data-bs-title="tooltip" class="btn-clean"
            data-bs-toggle="tooltip"
            role="button">
        <Icon icon="info" size="14"/>
    </button>

</template>
<script lang="ts">

import {defineComponent, PropType} from "vue";
import {Tooltip} from "bootstrap";
import Icon from "@js/components/Icon.vue";


export default defineComponent({
    components: {Icon},
    props: {
        tooltip: {
            type: String,
            required: true
        },
        position: {
            type: String as PropType<'top' | 'left' | 'right' | 'bottom'>,
            default: 'right'
        }
    },
    data() {
        return {}
    },
    mounted() {
        Tooltip.getOrCreateInstance(this.$refs.tip_ref, {})
    },
});
</script>
