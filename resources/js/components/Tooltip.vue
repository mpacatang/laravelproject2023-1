<template>
    <span ref="tip_ref" :data-bs-placement="position" :data-bs-title="tooltip"
          data-bs-toggle="tooltip">
        <slot></slot>
    </span>

</template>
<script lang="ts">

import {defineComponent, PropType} from "vue";
import {Tooltip} from "bootstrap";


export default defineComponent({
    props: {
        tooltip: {
            type: String,
            required: true,
        },
        position: {
            type: String as PropType<'top' | 'left' | 'right' | 'bottom'>,
            default: 'right'
        }
    },
    watch: {
        tooltip(newVal) {
            if (this.instance) {
                this.instance.setContent({'.tooltip-inner': newVal})

            }
        }
    },
    data() {
        return {
            instance: null as Tooltip
        }
    },
    mounted() {
        this.instance = Tooltip.getOrCreateInstance(this.$refs.tip_ref)
    },
});
</script>
