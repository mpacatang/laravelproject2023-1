<template>
    <label :for="this.for" class="form-label">
        <slot/>
        <span v-if="required" class="text-danger"> *</span>
        <span v-if="hasFieldSlot('label-suffix')" class="ms-1"><slot
            name="label-suffix"></slot>
        </span>
    </label>
</template>

<script lang="ts">

import {defineComponent} from "vue";

export default defineComponent({

    props: {
        required: {
            type: Boolean,
            default: false
        },
        for: String
    },
    methods: {
        hasFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
        },
    }
});
</script>

<style scoped>

</style>
