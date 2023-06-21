<template>
    <div :class="[{'mb-3':!noSpacing, 'mt-3':topSpacing}]">
        <label v-if="!noLabel" :for="name" class="form-label">{{ label }}
            <span v-if="required" class="text-danger">*</span>
            <span v-if="hasFieldSlot('label-suffix')" class="ms-1"><slot
                name="label-suffix"></slot>
            </span>
        </label>
        <flatPicker
            v-model="pickerValue"
            :config="getConfig"
            class="form-control d-block"
            @on-change="onChange"
            @on-close="onClose"
            :placeholder="placeholder"
        ></flatPicker>
        <FormValidationError :errors="errors" :name="name"></FormValidationError>
    </div>

</template>

<script lang="ts">
import flatPicker from "vue-flatpickr-component";
// import "flatpickr/dist/flatpickr.css";
// import 'flatpickr/dist/themes/dark.css'


import {defineComponent, PropType} from "vue";
import {getTimeFormat} from "@js/services/state/state_helper";
import FormValidationError from "@js/components/form/FormValidationError.vue";

export default defineComponent({
    components: {FormValidationError, flatPicker},
    inheritAttrs: false,

    props: {
        label: String,
        modelValue: {
            type: [Number, String],
        },
        minDate: {
            type: [String, Date],
        },
        variant: {
            type: String as PropType<'datetime' | 'date' | 'time'>,
            default: 'datetime'
        },
        name: {
            type: String,
            required: true
        },

        noSpacing: {
            type: Boolean,
            default: false,
        },
        topSpacing: {
            type: Boolean,
            default: false,
        },
        noLabel: {
            type: Boolean,
            default: false
        },
        placeholder: {
            type: String
        },
        errors: null,
        required: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            pickerValue: this.modelValue
        }
    },
    computed: {
        getConfig() {
            const time_24hr = getTimeFormat() == '24h';
            let timeFormatter = time_24hr ? 'H:i' : 'h:i K', dateFormatter = 'Y-m-d', formatter;
            formatter = this.variant == 'time' ? timeFormatter : dateFormatter + " " + timeFormatter;

            return {
                static: true,
                enableTime: this.variant != 'date',
                dateFormat: formatter,
                minDate: this.minDate,
                time_24h: time_24hr,
                noCalendar: this.variant == 'time'

            }
        }
    },
    watch: {
        pickerValue(newVal) {
            this.$emit('update:modelValue', newVal);
        },
        modelValue(){
          this.pickerValue = this.modelValue;
        }
    },
    methods: {
        onClose(){
        },
        onChange(){},
        hasFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
        },

    }
});
</script>

<style scoped>

</style>
