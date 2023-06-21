<template>
    <div class="card-header d-flex align-items-center">


        <template v-if="isFieldSlot('prefix')">
            <slot name="prefix"/>
        </template>
        <template v-else>
            <Icon v-if="icon" :icon="icon" style="margin-right: 9px" :weight="iconWeight" :type="type" :size="size" ></Icon>
        </template>

        <h5 :style="{ marginTop: icon!=null?`2px`:`0px`, marginLeft: isFieldSlot('prefix')?`9px`:`0` }"
            class="card-title mb-0 fw-medium">{{ title }}</h5>


        <div class="flex-grow-1">
            <slot></slot>
        </div>

    </div>

</template>

<script lang="ts">
import Icon from "./Icon.vue";
import {defineComponent, PropType} from 'vue'

export default defineComponent({
    components: {Icon},
    props: {
        title: String,
        icon: String,
        type: {
            type: String as PropType<'mi' | 'ms' | 'mso' | 'msr'>,
        },

        iconWeight: {
            type:  String as PropType<"100" | "200" | "300" | "400" | "500" | "600" | "700" | "800" | "900">,
            default: "400"
        },
        size: {
            type:  String as PropType<'sm' | 'md' | 'xs' | 'xxs'>,
            default: 'md'
        },
    },
    methods: {
        isFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
        },
    }
});
</script>
