<template>
    <div class="page-loading">
        <div v-if="loading"
             v-bind="$attrs">
            <Loading :loading="loading"/>
        </div>
        <div :class="[{'data-hide':loading}, {'data-show': !loading}, {'item-disabled': disabled}]">
            <slot v-if="!loading"/>
        </div>
    </div>

</template>

<script lang="ts">
import {defineComponent} from "vue";
import Loading from "@js/components/Loading.vue";

export default defineComponent({
    components: {Loading},
    props: {
        loading: {
            type: Boolean,
            default: false,
            required: true
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    }
});
</script>

<style scoped>

</style>
