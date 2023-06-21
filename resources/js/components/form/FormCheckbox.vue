<template>
    <div class="form-check" :class="[{'mb-3':!noSpacing}]">
        <input :id="name" :checked="modelValue" :class="[{'is-invalid': errorFor(name)}]" :disabled="disabled"
               :value="modelValue" class="form-check-input cursor-pointer" type="checkbox" @change="onChange"
               @input="updateValue">
        <label :for="name" class="form-check-label" style="cursor:pointer;">{{ label }}</label>
        <span v-if="warningNote" class="text-danger d-block">{{ warningNote }}</span>
        <FormValidationError :errors="errors" :name="name"></FormValidationError>
    </div>
</template>

<script lang="ts">


import { defineComponent } from "vue";
import FormValidationError from "@js/components/form/FormValidationError.vue";

export default defineComponent({
    components: {FormValidationError},

    props: {
        modelValue: {
            type: [Number, String],
        },
        label: String,
        name: {
            type: String,
            required: true
        },
        noSpacing:{
            type: Boolean,
            default: false,
        },
        errors: null,
        onChange: Function,
        checked: [Boolean, null],
        warningNote: String,
        disabled: {
            type: Boolean,
            default: false
        }

    },
    methods: {
        errorFor(field) {
            if (this.errors != null) {
                let error = this.errors[field];
                if (error != null) {
                    if (typeof error === 'string') {
                        return [error];
                    }
                    return error;
                }
            }
            return null;
        },
        updateValue(event) {
            this.$emit('update:modelValue', event.target.checked);
        }
    }
});
</script>

<style scoped>

</style>
