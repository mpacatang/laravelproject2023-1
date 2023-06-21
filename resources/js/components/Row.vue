<template>
    <div :class="getClass">
        <slot></slot>
    </div>

</template>
<script lang="ts">

import {defineComponent, PropType} from "vue";
import {ITableOption} from "@js/types/models/models";


export default defineComponent({
    props: {
        justifyContent: {
            type: String as PropType<'start' | 'end' | 'center' | 'between' | 'around' | 'evenly'>,
        },
        rowCols: {
            type: [Number , String]
        },
        gap:{
            type: [Number , String]
        },
        alignItems: {
            type: String as PropType<'start' | 'end' | 'center' | 'between' | 'baseline' | 'stretch'>,
        },
    },
    computed: {
        getClass() {
            let classText = "row";

            if (this.justifyContent) {
                classText += (" justify-content-" + this.justifyContent)
            }
            if(this.rowCols){
                classText += (" row-cols-" + this.rowCols)
            }
            if(this.gap){
                classText += (" g-" + this.gap)
            }
            if (this.alignItems) {
                classText += (" align-items-" + this.alignItems)
            }
            return classText;
        },



    }
});
</script>
