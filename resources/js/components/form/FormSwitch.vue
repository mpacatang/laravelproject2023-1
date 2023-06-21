<template>
    <div :class="[{'mb-3':!noSpacing}]" class="form-check form-switch">
        <input :id="name" :checked="modelValue" :class="[{'is-invalid': errorFor(name)}]" :disabled="disabled"
               :value="modelValue"
               class="form-check-input cursor-pointer" role="switch" type="checkbox" @change="onChange"
               @input="updateValue">
        <label :for="name" class="form-check-label" style="cursor:pointer;">{{ label }}
            <span v-if="hasFieldSlot('label-suffix')" class="ms-1"><slot
                name="label-suffix"></slot>
            </span>
        </label>
        <span v-if="warningNote" class="text-danger d-block">{{ warningNote }}</span>
        <FormValidationError :errors="errors" :name="name"></FormValidationError>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import FormValidationError from "@js/components/form/FormValidationError.vue";

export default defineComponent({
    components: {FormValidationError},
    props: {
        modelValue: Boolean,
        label: String,
        name: {
            type: String,
            required: true
        },
        noSpacing: {
            type: Boolean,
            default: false,
        },
        errors: null,
        onChange: Function,
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

        },
        hasFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
        },

    }
});
</script>

<style scoped>

</style>
