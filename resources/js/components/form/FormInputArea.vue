<template>
    <div :class="[{'mb-3':!noSpacing, 'mt-3':topSpacing}]">
        <label v-if="!withOutLabel" :for="name" class="form-label">{{ label }} <span v-if="required"
                                                                                     class="text-danger">*</span></label>
        <div :class="[{'is-invalid': errorFor(name)}]" class="input-group">
            <span v-if="hasFieldSlot('prefix')" id="basic-addon1" class="input-group-text"><slot
                name="prefix"></slot></span>
            <textarea :id="name" :class="[{'is-invalid': errorFor(name)}]"
                      :name="name" :placeholder="getPlaceholder"
                      :rows="rows"
                      :value="modelValue" class="form-control" v-bind="$attrs" @change="onChange"
                      @input="updateValue" />


            <span v-if="hasFieldSlot('suffix')" id="basic-addon1" class="input-group-text"><slot
                name="suffix"></slot></span>

        </div>
        <FormValidationError :errors="errors" :name="name"></FormValidationError>
    </div>
</template>

<script lang="ts">
import Icon from "../Icon.vue";
import {defineComponent} from "vue";
import FormValidationError from "@js/components/form/FormValidationError.vue";

export default defineComponent({
    components: {FormValidationError, Icon,},
    inheritAttrs: false,
    props: {
        label: String,

        onChange: Function,
        name: {
            type: String,
            required: true
        },
        required: {
            type: Boolean,
            default: false
        },
        placeholder: String,
        errors: null,
        noSpacing: {
            type: Boolean,
            default: false,
        },
        topSpacing: {
            type: Boolean,
            default: false,
        },
        modelValue: {
            type: [Number, String, Object],
        },
        withOutLabel: {
            type: Boolean,
            default: false
        },
        rows: {
            type: Number,
            default: 2
        }

    },
    computed: {
        getPlaceholder() {
            return this.placeholder ?? this.label;
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
            this.$emit('update:modelValue', event.target.value);
            this.$emit("onchange", event);

        },
        hasFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
        },


    }
});
</script>

<style scoped>

</style>
