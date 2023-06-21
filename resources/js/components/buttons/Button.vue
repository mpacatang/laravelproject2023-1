<template>
    <button :class="getBtnClass" :style="getStyle">
        <slot/>
    </button>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import {ButtonSizeTypes, ColorTypes} from "@js/types/custom_types";

export default defineComponent({
    props: {
        color: {
            type: String as PropType<ColorTypes>,
            default: "primary"
        },
        variant: {
            type: String as PropType<'soft' | 'outline' | 'fill' | 'text' | 'link'>,
            default: 'fill'
        },
        size: {
            type: String as PropType<ButtonSizeTypes>,
        },
        radius: {
            type: String as PropType<'sm' | 'md' | 'lg' | 'pill' | Number>,
            default: 'md'
        },

        flexedIcon: Boolean

    },
    computed: {
        getBtnClass() {
            let classText = 'btn ';
            if (this.flexedIcon) {
                classText += "d-inline-flex align-items-center justify-content-center ";
            }
            switch (this.variant) {
                case "outline":
                    classText += 'btn-outline-';
                    break;
                case "soft":
                    classText += 'btn-soft-';
                    break;
                case "text":
                    classText += 'border-0 p-0 text-';
                    break;
                case "fill":
                    classText += "btn-";
                    break;
                case "link":
                    classText += 'p-0 btn-link';
                    break;
            }

            if (this.variant != 'link')
                classText += (this.color);
            if (this.size && ['sm', 'lg', 'md'].includes(this.size)) {
                classText += (" btn-" + this.size);
            } else if (this.size) {
                classText += (" font-" + this.size);
            }

            if (this.radius) {
                if (['sm', 'md', 'lg', 'pill'].includes(this.radius)) {
                    classText += (" rounded-" + this.radius);
                }
            }

            return classText;
        },

        getStyle() {
            let styles = {};
            if (this.radius) {
                if (!['sm', 'md', 'lg', 'pill'].includes(this.radius)) {
                    styles['borderRadius'] = `${this.radius}px`;
                }
            }
            return styles;
        }

    }
});
</script>

<style scoped>

</style>
