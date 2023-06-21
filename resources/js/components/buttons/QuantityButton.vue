<template>
    <div>
        <div :class="size" class="border quantity-container d-flex">

            <VItem :bg-color="canDecrease?'danger':'secondary'" align-items="center" circular class="cursor-pointer"
                   display="inline-flex"
                   justify-content="center" soft style="padding: 2px 3px;" @click="decreaseQuantity">
                <Icon icon="remove" size="20" type="msr"/>
            </VItem>

            <input :id="name" :class="[{'is-invalid': errorFor(name)}]"
                   :max="max"
                   :min="min"
                   :name="name" :step="step"
                   :value="modelValue" class="form-control text-center bg-transparent fw-medium" style="width: 36px"
                   type="number"
                   v-bind="$attrs"
                   @change.prevent="updateValue" @input="updateValue"/>


            <VItem align-items="center" bg-color="primary" circular class="cursor-pointer" display="inline-flex"
                   justify-content="center" soft style="padding: 2px 3px;" @click="increaseQuantity">
                <Icon icon="add" size="20" type="msr"/>
            </VItem>
            <!--            <span class="" @click="increaseQuantity"><Icon icon="add" size="sm" type="msr"/></span>-->

        </div>
<!--        <FormValidationError :errors="errorFor(name)"></FormValidationError>-->
    </div>
    <!--    <div class=" bg-light border quantity-container d-inline-block">-->
    <!--        -->
    <!--        <span class="action-btn action-down" @click="decreaseQuantity"><Icon icon="remove" type="msr" size="sm"/></span>-->
    <!--        <span class="action-btn action-up" @click="increaseQuantity"><Icon icon="add" type="msr" size="sm"/></span>-->

    <!--        <input :id="name" :class="[{'is-invalid': errorFor(name)}]"-->
    <!--               :max="max"-->
    <!--               :min="min"-->
    <!--               :name="name" :step="step"-->
    <!--               :value="modelValue" class="form-control bg-light" type="number" v-bind="$attrs" @change="onChange"-->
    <!--               @input="updateValue"/>-->
    <!--        <FormValidationError :errors="errorFor(name)"></FormValidationError>-->

    <!--    </div>-->
</template>

<script lang="ts">
import {defineComponent, PropType} from "vue";
import Icon from "@js/components/Icon.vue";
import VItem from "@js/components/VItem.vue";
import FormValidationError from "@js/components/form/FormValidationError.vue";

export default defineComponent({
    components: {FormValidationError, VItem, Icon},
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            required: true
        },
        size: {
            type: String as PropType<'sm' | 'md' | 'lg'>,
            default: 'md'
        },
        min: {
            type: [Number, String],
        },
        max: {
            type: [Number, String],
        },
        step: {
            type: [Number, String],
        },
        onChange: Function,
        automatic: {
            type: Boolean,
            default: false
        }

    },
    computed: {
        canDecrease() {
            return this.modelValue > 1;
        }
    },
    methods: {
        errorFor(field: string): any {
            if (this.errors != null) {
                console.log(this.errors[field]);
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
            if (this.automatic) {
                this.$emit('update:modelValue', parseInt(event.target.value));
            }
            this.$emit("onchange", event);
            if (this.onChange) {
                this.onChange(parseInt(event.target.value))
            }

        },
        increaseQuantity() {
            if (this.automatic) {
                this.$emit('update:modelValue', parseInt(this.modelValue) + 1);
            }
            if (this.onChange) {
                this.onChange(parseInt(this.modelValue) + 1);
            }
        },
        decreaseQuantity() {
            if (this.modelValue > 1) {
                if (this.automatic) {
                    this.$emit('update:modelValue', parseInt(this.modelValue) - 1);
                }
                if (this.onChange) {
                    this.onChange(parseInt(this.modelValue) - 1)
                }
            }

        },
    }
});
</script>

<style scoped>


.quantity-container {
    border-radius: 40px;
    padding: 8px 10px;
    max-width: 110px;
    user-select: none;


}

.quantity-container.sm {
    padding: 4px 6px;
}

.quantity-container input {
    border: 0;
    border-radius: 0;
    height: unset;
    -moz-appearance: textfield;
    appearance: textfield;
    padding: 0 !important;
}

.quantity-container input::-webkit-outer-spin-button,
.quantity-container input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

</style>
