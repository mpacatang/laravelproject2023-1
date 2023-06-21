<template>
    <button :class="getBtnClass" :style="getStyle">
        <template v-if="loading">
            <div class="loader me-1-5">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"/>
                </svg>
            </div>
        </template>
        <Icon v-else-if="!hideIcon" :icon="icon" :spin="loading" class="me-1-5" :size="iconSize" type="msr"></Icon>
        <slot/>
    </button>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue";
import Icon from "@js/components/Icon.vue";
import {ColorTypes} from "@js/types/custom_types";
import Button from "@js/components/buttons/Button.vue";

export default defineComponent({
    components: {Button, Icon},
    props: {
        loading: {
            type: Boolean,
            default: false,
        },
        icon:{
            type: String,
            default: 'check',
        },
        color: {
            type: String as PropType<ColorTypes>,
            default: "primary"
        },
        variant: {
            type: String as PropType<'soft' | 'outline' | 'fill' | 'text'>,
            default: 'fill'
        },
        radius: {
            type: String as PropType<'sm' | 'md' | 'lg' | 'pill' | Number>,
            default: 'md'
        },

        iconSize: {
            type: String as PropType<'sm' | 'md' | 'xs' | 'xxs' | Number>,
            default: 'sm'
        },
        flexedIcon: Boolean,
        hideIcon: {
            type: Boolean,
            default: false
        }

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
                    classText += 'p-0 text-';
                    break;
                case "fill":
                    classText += "btn-";
                    break;
            }

            classText += (this.color);

            if (this.size && typeof this.size !== 'number') {
                classText += (" btn-" + this.size);
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

.loader {
    position: relative;
    /*margin: 0 auto;*/
    width: 18px;
}

.loader::before {
    content: '';
    display: block;
    padding-top: 100%;
}

.circular {
    animation: rotate 1.25s linear infinite;
    height: 100%;
    transform-origin: center center;
    width: 100%;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto;
}

.path {
    stroke-dasharray: 1, 200;
    stroke-dashoffset: 0;
    stroke: white;
    animation: dash 1.5s ease-in-out infinite;
    stroke-linecap: round;
}

@keyframes rotate {
    100% {
        transform: rotate(360deg);
    }
}

@keyframes dash {
    0% {
        stroke-dasharray: 1, 200;
        stroke-dashoffset: 0;
    }
    50% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -35px;
    }
    100% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -124px;
    }
}

</style>
