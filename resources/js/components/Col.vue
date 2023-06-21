<template>
    <div :class="getClass">
        <slot></slot>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";


export default defineComponent({

    props: {

        xxl: {
            type: [Number, String],
        },
        xl: {
            type: [Number, String],
        },
        lg: {
            type: [Number, String],
        },
        md: {
            type: [Number, String],
        },
        sm: {
            type: [Number, String],
        },
        xs: {
            type: [Number, String],
        },
        auto: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        getClass() {
            let classText = "";
            if (this.xs != null) {
                classText += `col-${this.xs} `;
            }
            if (this.sm != null) {
                classText += `col-sm-${this.sm} `;
            }
            if (this.md != null) {
                classText += `col-md-${this.md} `;
            }
            if (this.lg != null) {
                classText += `col-lg-${this.lg} `;
            }
            if (this.xl != null) {
                classText += `col-xl-${this.xl} `;
            }
            if (this.xxl != null) {
                classText += `col-xxl-${this.xxl} `;
            }
            if (classText.trim().length == 0) {
                classText += 'col';
            }
            if (this.auto) {
                classText += " col-auto";
            }
            return classText;
        },
        getFontSize() {

        }
    }
});
</script>

