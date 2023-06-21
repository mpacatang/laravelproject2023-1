<template>
    <div :class="[{'mb-3':!noSpacing, 'mt-3':topSpacing}]">
        <label v-if="!noLabel" :for="name" class="form-label d-flex">{{ label }}
            <span v-if="required" class="text-danger ms-1">*</span>
            <span v-if="hasFieldSlot('label-suffix')" class="ms-1 flex-grow-1"><slot
                name="label-suffix"></slot>
            </span>
        </label>
        <div :class="[{'is-invalid': errorFor(name)}]" class="input-group">
            <template v-if="prefixOrSuffix">
                <template v-if="prefixOrSuffix==='amount' && currencyPosition==='left'">
                     <span class="input-group-text">
                        <span>{{ currencySign }}</span>
                    </span>
                </template>
            </template>
            <template v-else-if="showCurrency && currencyPosition==='left'">
                <span class="input-group-text">
                    <span>{{ currencySign }}</span>
                </span>
            </template>
            <span v-if="hasFieldSlot('prefix')" class="input-group-text"><slot
                name="prefix"></slot></span>
            <input :id="name" :class="[{'is-invalid': errorFor(name)}]"
                   :max="max"
                   :min="min"
                   :name="name" :placeholder="getPlaceholder" :step="step"
                   :type="type" :value="modelValue" class="form-control" v-bind="$attrs" @change="onChange"
                   @input="updateValue" :required="required"/>
            <template v-if="prefixOrSuffix">
                <template v-if="prefixOrSuffix==='amount' && currencyPosition==='right'">
                     <span class="input-group-text">
                        <span>{{ currencySign }}</span>
                    </span>
                </template>
                <template v-if="prefixOrSuffix==='percent'">
                     <span class="input-group-text">
                        <span>%</span>
                    </span>
                </template>
            </template>
            <template v-else-if="showCurrency && currencyPosition==='right'">
                <span class="input-group-text">
                    <span>{{ currencySign }}</span>
                </span>
            </template>
            <span v-if="hasFieldSlot('suffix')" class="input-group-text"><slot
                name="suffix"></slot></span>

        </div>
        <FormValidationError :errors="errors" :name="name"></FormValidationError>
    </div>
</template>

<script lang="ts">
import Icon from "../Icon.vue";
import {defineComponent, PropType} from "vue";
import {getCurrencyPosition, getCurrencySymbol} from "@js/services/state/state_helper";
import FormValidationError from "@js/components/form/FormValidationError.vue";

type TYPES =
    'text' |
    'password' |
    'email' |
    'number' |
    'url' |
    'tel' |
    'search' |
    'range' |
    'color' |
    'date' |
    'time' |
    'datetime' |
    'datetime-local' |
    'month' |
    'week';


export default defineComponent({
    components: {FormValidationError, Icon},

    props: {
        label: String,
        type: {
            type: String as PropType<TYPES>,
            default: 'text',
        },
        prefixOrSuffix: {
            type: String as PropType<'percent' | 'amount'>,

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
        showCurrency: {
            type: Boolean,
            default: false
        },


    },
    computed: {
        getPlaceholder() {
            return this.placeholder ?? this.label;
        },

        currencySign() {
            return getCurrencySymbol();
        },
        currencyPosition() {
            return getCurrencyPosition();
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
