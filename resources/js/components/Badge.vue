<template>
    <div :class="getBtnClass" class="badge">
        <slot/>
    </div>
</template>

<script lang="ts">

import {defineComponent} from "vue";

export default defineComponent({
    props: {

        outline: {
            type: Boolean,
            default: false
        },
        soft: {
            type: Boolean,
            default: false
        },
        text: {
            type: Boolean,
            default: false
        },
        primary: {
            type: Boolean,
            default: false
        },
        success: {
            type: Boolean,
            default: false
        },
        light: {
            type: Boolean,
            default: false
        },
        danger: {
            type: Boolean,
            default: false
        },
        iconCentered: {
            type: Boolean,
            default: false
        },
        lg: {
            type: Boolean,
            default: false,
        },
        xl: {
            type: Boolean,
            default: false,
        }

    },
    methods: {
        addColorClass(base: string) {
            if (this.success) {
                return base += 'success'
            } else if (this.danger) {
                return base += 'danger'
            } else if (this.light) {
                return base += 'light'
            } else {
                return base += 'primary'
            }
        }
    },
    computed: {
        getBtnClass() {
            let classText = (this.iconCentered ? 'd-inline-flex align-items-center ' : '');
            if (this.outline) {
                classText += this.addColorClass("outline-");
            } else if (this.soft) {
                classText += this.addColorClass("bg-soft-");
                classText += " ";
                classText += this.addColorClass('text-');
            } else {
                classText += this.addColorClass(this.text ? 'text-' : 'bg-');
            }

            if (this.lg) {
                classText += " badge-lg"
            } else if (this.xl) {
                classText += " badge-xl"
            }

            return classText;
        },

    }
});
</script>

<style scoped>

</style>
