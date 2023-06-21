<template>
    <template v-if="type==='feather'">
        <span :class="getIconClass" :style="{fontSize: getFontSize + `px`, fontWeight: weight}"/>
    </template>
    <template v-else>
    <span :class="getIconClass" :style="{fontSize: getFontSize + `px`, fontWeight: weight}"><template
        v-if="icon">{{ icon }}</template><template
        v-else><slot></slot></template></span>
    </template>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import {TextColorTypes} from "@js/types/custom_types";

export default defineComponent({
    inheritAttrs: true,
    props: {
        type: {
            type: String as PropType<'mi' | 'ms' | 'mso' | 'msr' | 'feather'>,
            default: 'msr'
        },
        icon: {
            type: String,
        },
        color: {
            type: String as PropType<TextColorTypes>,
            default: null
        },
        soft: {
            type: Boolean,
            default: false
        },
        weight: {
            type: String as PropType<"100" | "200" | "300" | "400" | "500" | "600" | "700" | "800" | "900">,
            default: "400"
        },
        size: {
            type: String as PropType<'sm' | 'md' | 'xs' | 'xxs' | Number>,
            default: 'md'
        },
        spin: Boolean,
        fill: {
            type: Boolean,
            default: false
        },


    },
    computed: {
        getIconClass() {
            let classText = "";

            if (this.type === 'feather') {
                classText += 'fe fe-' + this.icon;
            } else if (this.type === 'ms') {
                classText += 'material-symbols'
            } else if (this.type === 'msr') {
                classText += 'material-symbols-rounded'
            } else if (this.type === 'mso') {
                classText += 'material-symbols-outlined'
            } else {
                classText += 'material-symbols-outlined'
            }

            if (this.spin) {
                classText += " animate-spin";
            }
            if (this.soft) {
                if (this.color) {
                    classText += " text-soft-" + this.color;
                }
            } else if (this.color) {
                classText += " text-" + this.color;
            }

            if (this.fill) {
                classText += " fill";
            }
            return classText;
        },
        getFontSize() {
            switch (this.size) {
                case 'md':
                    return 22;
                case "sm":
                    return 20;
                case "xs":
                    return 18;
                case "xxs":
                    return 16;
                default:
                    return this.size;
            }
        }
    }
});
</script>

<style scoped>

</style>
