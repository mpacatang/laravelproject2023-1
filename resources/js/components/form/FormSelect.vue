<template>
   <div :class="[{'mb-3':!noSpacing}]">
       <label v-if="!noLabel" :for="name" class="form-label">{{ label }}
           <span v-if="required" class="text-danger">*</span>
           <span v-if="hasFieldSlot('label-suffix')" class="ms-1"><slot
               name="label-suffix"></slot>
            </span>
       </label>
       <div :class="[{'is-invalid': errorFor(name)}]">
           <select id="select2" :class="id" :disabled="isDisabled" :multiple="multiple" class="form-control select2" required>
           </select>
       </div>
       <FormValidationError :errors="errors" :name="name"></FormValidationError>
   </div>
</template>

<script lang="ts">
import Choices from 'choices.js';
import {defineComponent, PropType} from "vue";
import FormLabel from "@js/components/form/FormLabel.vue";
import {IFormSelectOption} from "@js/types/models/models";
import FormValidationError from "@js/components/form/FormValidationError.vue";


export default defineComponent({
    components: {FormValidationError, FormLabel},
    props: {
        name: {
            type: String
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
        init: {
            type: Boolean,
            default: true
        },
        data: Object,
        helper: {
            type: Object as PropType<IFormSelectOption>,
            required: true
        },
        placeholder: String,
        onSelected: Function as PropType<(selected: any | any[]) => void>,
        selected: [String, Array, Number, Object],

        grouped: {
            type: Boolean,
            default: false
        },
        multiple: {
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
        noSearch:{
          type: Boolean,
          default: false
        },
        disabled: Boolean,
        errors: null,
        noChoiceText: String

    },
    data() {
        return {
            choice: null,
            id: 's' + Math.random().toString(36).substring(2, 7),
        }
    },
    watch: {
        data() {
            if (this.choice) {
                this.createChoice();
            }
        },
        selected(newVal, oldVal) {

            if (newVal !== oldVal && this.choice) {
                this.createChoice();

            }
            this.onSelected(newVal);
        },
        disabled(newVal){

        }
    },
    methods: {
        hasFieldSlot(fieldName) {
            return typeof this.$slots[fieldName] !== 'undefined'
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
        makeChoices() {

            const self = this;
            if (this.grouped) {

                const list = this.getPlaceholder();
                this.data.forEach(function (item) {
                    list.push({
                        label: self.getOptLabel(item),
                        choices: self.getData(item[self.helper.optgroup.options],)
                    })
                });
                return list;
            } else {
                return this.getPlaceholder().concat(...this.getData(this.data));

            }
        },

        getPlaceholder() {
            if (this.multiple) {
                return [];
            } else {
                return [{
                    label: this.placeholder ?? "Select",
                    value: "",
                }];
            }
        },
        getData(data) {
            const self = this;
            const list: any[] = []
            data.forEach(function (item: any) {
                list.push({
                    value: item[self.helper.option.value],
                    label: self.getLabel(item),
                    selected: self.isSelected(item[self.helper.option.value])
                })
            })
            return list;
        },
        getOptLabel(item) {
            let label = this.helper.optgroup.label;
            if (label instanceof Function) {
                return label(item);
            }
            return item[label];
        },
        getLabel(item) {
            let label = this.helper.option.label;
            if (label instanceof Function) {
                return label(item);
            }
            return item[label];
        },
        createChoice() {
            if (this.init) {
                if (this.choice) {
                    this.choice.destroy();
                }

                this.choice = new Choices(this.element, {
                    itemSelectText: '',
                    placeholderValue: this.placeholder ?? 'select',
                    placeholder: true,
                    shouldSort: false,

                    searchEnabled: !this.noSearch,
                    allowHTML: true,
                    removeItemButton: this.multiple,
                    removeItems: this.multiple,
                    noChoicesText: this.noChoiceText??'No choices',
                    // classNames: {},
                    choices: this.makeChoices()

                }).setChoiceByValue('aselect');
            }
        },
        isSelected(value) {
            if (this.selected instanceof Array) {
                for (let i = 0; i < this.selected.length; i++) {
                    if (this.selected[i] == value)
                        return true;
                }
                return false;
            } else {
                return this.selected == value;
            }
        }
    },
    computed: {
        isDisabled(){
            return this.disabled;
        }
    },
    mounted() {
        const self = this;
        this.element = document.querySelector(`#select2.` + this.id);
        if (this.element != null) {
            this.element.addEventListener('change', function (e) {
                if (self.onSelected) {
                    const target = e.target;
                    if (self.multiple) {
                        const list: any[] = [];
                        for (let i = 0; i < target.length; i++) {
                            list.push(target[i].value);
                        }
                        self.onSelected(list);
                    } else {
                        let value = target.value
                        self.onSelected(value.length !== 0 ? value : null);
                    }
                }
            })

            this.createChoice();
        }
    }
});
</script>

<style scoped>
.choices {
    margin-bottom: 0px !important;
}
</style>
