<template>
    <div :class="[{'mb-3':!noSpacing}]">
        <label v-if="!noLabel" :for="name" class="form-label">{{ label }}
            <span v-if="required" class="text-danger">*</span>
            <span v-if="hasFieldSlot('label-suffix')" class="ms-1"><slot
                name="label-suffix"></slot>
            </span>
        </label>
        <div :id="getId">
        </div>
        <FormValidationError :errors="errors" :name="name"></FormValidationError>
    </div>

</template>

<script lang="ts">

import Quill from "quill/dist/quill";
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import FormValidationError from "@js/components/form/FormValidationError.vue";

export default {
    components: {FormValidationError},
    inheritAttrs: false,
    props: {
        name: {
            type: String
        },
        editorId: {
            type: String,
            default: '__new'
        },
        label: {
            type: String
        },
        required: {
            type: Boolean,
            default: false
        },
        noLabel: {
            type: Boolean,
            default: false
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
        errors: null,
    },
    computed: {
        getId() {
            return "editor" + this.editorId;
        }
    },
    data() {
        return {
            quill: undefined,
        }
    },
    watch: {
        modelValue(newVal, oldVal) {
            if (newVal !== this.quill.root.innerHTML && this.modelValue && this.quill) {
                this.quill.root.innerHTML = this.modelValue;
            }
        }
    },
    methods:{
        hasFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
        },
    },
    mounted() {

        const self = this;
        this.quill = new Quill(document.getElementById(this.getId), {
            theme: 'snow',

        });
        if (this.modelValue) {

            this.quill.root.innerHTML = this.modelValue;
        }
        this.quill.on('text-change', function (delta, oldDelta, source) {
            if (source === 'user') {

                self.$emit('update:modelValue', self.quill.root.innerHTML);
            }
        });


    }
}
</script>

<style scoped>

</style>
