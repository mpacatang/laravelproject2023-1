<template>
    <div id="modal" ref="modal" aria-modal="true"

         class="modal fade"
         role="dialog"

         tabindex="-1"
         v-bind="$attrs">
        <div :class="[{'modal-lg': lg,'modal-xl': xl, 'modal-dialog-scrollable': scrollable}]"
             class="modal-dialog modal-dialog-centered">
            <div :class="{'overflow-hidden':overflowHidden}" class="modal-content">
                <slot></slot>
                <button v-if="closeBtn" class="btn-close" @click="hideModal"/>
            </div>


        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {Modal} from 'bootstrap';

export default defineComponent({
    inheritAttrs: false,
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        lg: {
            type: Boolean,
            default: false
        },
        xl: {
            type: Boolean,
            default: false
        },
        closeBtn: {
            type: Boolean,
            default: false
        },
        scrollable: {
            type: Boolean,
            default: false
        },
        overflowHidden: {
            type: Boolean,
            default: false
        }

    },
    watch: {
        modelValue(newVal, oldVal) {
            this.showing = newVal;
            if (this.showing) {
                this.showModal();
            } else {
                this.hideModal();
            }
        }
    },
    data() {
        return {
            showing: this.$props.modelValue,
            backdrop: null,
            modal: null as Modal,
        }
    },

    methods: {
        showModal() {

            if (this.modal) {
                this.showing = true;
                this.modal.show();
            }
        },
        hideModal() {
            if (this.modal) {
                this.showing = false;
                this.modal.hide();
            }
        },
    },
    mounted() {
        let modal = this.$refs.modal;
        this.modal = new Modal(modal);
        const self = this;
        if(this.modelValue){
            this.modal.show();
        }
        modal.addEventListener('hide.bs.modal', event => {
            self.$emit('update:modelValue', false);
            self.showing = false;
        })
    },
    unmounted(){
        this.hideModal();
    }


});
</script>
