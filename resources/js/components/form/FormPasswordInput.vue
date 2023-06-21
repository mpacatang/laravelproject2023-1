<template>
    <div :class="[{'mb-3':!noSpacing, 'mt-3':topSpacing}]">
        <label v-if="!noLabel" :for="name" class="form-label">{{ label }} <span v-if="required"
                                                                                class="text-danger">*</span></label>
        <div :class="[{'is-invalid': errorFor(name)}]" class="input-group">
            <span v-if="hasFieldSlot('prefix')" id="basic-addon1" class="input-group-text"><slot
                name="prefix"></slot></span>
            <input :id="name" :class="[{'is-invalid': errorFor(name)}]"
                   :max="max"
                   :min="min"
                    :type="this.show_password ? 'text' : 'password'"
                   :name="name" :placeholder="getPlaceholder" :step="step" :value="modelValue"
                   class="form-control" v-bind="$attrs" @change="onChange"
                   @input="updateValue"/>
            <div class="input-group-text cursor-pointer" @click="togglePassword">
                <Icon icon="visibility" v-if="!show_password" size="18"></Icon>
                <Icon icon="visibility_off" v-else  size="18"></Icon>
            </div>

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
    components: {FormValidationError, Icon},

    props: {
        label: String,
        show: {
            type: Boolean,
            required: false
        },
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
        min: {
            type: [Number, String],
        },
        max: {
            type: [Number, String],
        },
        step: {
            type: [Number, String],
        },
        noSpacing: {
            type: Boolean,
            default: false,
        },
        topSpacing: {
            type: Boolean,
            default: false,
        },
        modelValue: {
            type: [Number, String],
        },
        noLabel: {
            type: Boolean,
            default: false
        },

    },
    data(){
        return {
            show_password: false
        }
    },
    computed: {
        getPlaceholder() {
            return this.placeholder ?? this.label;
        },
        type(){
            return this.show_password ? 'text' : 'password';
        }
    },
    methods: {
        togglePassword(){
          this.show_password = !this.show_password;
        },
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
