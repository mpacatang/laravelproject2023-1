<template>
    <div :class="getClass" :style="getStyle">
        <slot/>
    </div>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import badge from "@js/components/Badge.vue";
import {ColorTypes, TextColorTypes} from "@js/types/custom_types";

export default defineComponent({
    props: {
        color: {
            type: String as PropType<ColorTypes>
        },
        textColor: {
            type: String as PropType<TextColorTypes>
        },
        bgColor: {
            type: String as PropType<ColorTypes>
        },
        display: {
            type: String as PropType<'flex' | 'block' | 'inline' | 'inline-flex' | 'inline-block'>,
            default: 'block'
        },
        justifyContent: {
            type: String as PropType<'start' | 'end' | 'center' | 'between' | 'around' | 'evenly'>,
        },
        alignItems: {
            type: String as PropType<'start' | 'end' | 'center' | 'between' | 'baseline' | 'stretch'>,
        },
        flex: {
            type: String as PropType<'row' | 'column'>,
        },
        as: {
            type: String as PropType<'badge'>
        },
        soft: {
            type: Boolean,
            default: false
        },
        xsoft: {
            type: Boolean,
            default: false
        },
        rounded: {
            type: Boolean,
            default: false
        },
        circular: {
            type: Boolean,
            default: false
        },
        radius: {
            type: [Number, String],
        },
        border: {
            type: Boolean,
            default: false
        }

    },
    data() {
        return {}
    },
    methods: {
        addBGColorClass(base: string) {
            return base += (this.bgColor ?? this.color)
        },
        addTextColorClass(base: string) {
            let newColor = this.textColor;
            if(newColor==null){
                if(this.soft || this.xsoft){
                    newColor = this.color;
                }else{
                    return base+="white";
                }
            }
            return base += (newColor);
        }

    },
    computed: {
        getClass() {
            let classText = "";
            if (this.bgColor || this.color) {
                classText += this.soft ? (this.addBGColorClass("bg-soft-")) :(
                    this.xsoft ? (this.addBGColorClass("bg-xsoft-")):
                    (this.addBGColorClass("bg-")));
            }

            if (this.textColor || this.color) {
                classText += this.addTextColorClass(" text-");
            }

            if (this.rounded) {
                classText += " rounded"
            } else if (this.circular) {
                classText += " rounded-circle"
            }

            if (this.border) {
                classText += " border";
            }

            classText += (" d-" + this.display);

            if (this.alignItems) {
                classText += (" align-items-" + this.alignItems)
            }
            if (this.justifyContent) {
                classText += (" justify-content-" + this.justifyContent)
            }
            if (this.flex) {
                classText += (" flex-" + this.flex)
            }

            if(this.as){
                classText += (" "+this.as)
            }

            return classText;
        },

        getStyle() {
            let styles = {};
            if (this.radius) {
                styles['borderRadius'] = `${this.radius}px`;
            }
            return styles;
        }

    }
});
</script>

<style scoped>

</style>
