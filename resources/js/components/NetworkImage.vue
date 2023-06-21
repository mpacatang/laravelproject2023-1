<template>
    <img v-if="!canShowTextImage" :alt="alt"
         :class="{'rounded-circle': circular, 'rounded': rounded, 'cursor-pointer': canShowFullImage|showWindowPreview,}"
         :onerror="onImgError"
         :src="getSrc"
         :style="[{'filter': getFilter},{'height': getHeight},{'width': getWidth}, {'object-fit':objectFit},]" class="d-inline img-fluid" @click="onImageClick"/>

    <div v-if="canShowTextImage" :class="{'rounded-circle': circular, 'rounded': rounded}"
         :style="[{'height': getHeight},{'width': getWidth}]"

         class="d-flex justify-content-center align-items-center mb-0 bg-soft-primary">
        <span class="font-14 fw-semibold">{{ getFilteredText }}</span>
    </div>

    <VModal v-if="canShowFullImage" v-model="show_preview" class="rounded overflow-hidden" ref="image_modal">
        <img :alt="alt" :src="getSrc"
             class="img-fluid"/>
    </VModal>
</template>

<script lang="ts">
import {defineComponent, PropType} from "vue";
import VModal from "@js/components/VModal.vue";
import {useLayoutStore} from "@js/services/state/states";

export default defineComponent({
    components: {VModal},
    inheritAttrs: false,

    props: {
        src: {
            type: [String, null]
        },
        placeholderSrc: {
            type: [String, null]
        },
        fromText: {
            type: [String, null]
        },
        alt: {
            type: String,
            default: 'Image'
        },
        style: {
            type: Object,
            default: {}
        },
        width: [Number, String],
        height: [Number, String],
        size: [Number, String],
        rounded: {
            type: Boolean,
            default: false
        },
        circular: {
            type: Boolean,
            default: false
        },
        showFullImage: {
            type: Boolean,
            default: false
        },
        showWindowPreview: {
            type: Boolean,
            default: false
        },

        objectFit: {
            type: String as PropType<'contain' | 'cover' | 'fill' | 'none' | 'revert' | 'inherit' | 'initial'>,
            default: 'cover'
        }

    },
    watch:{
        show_preview(){

            // if(this.show_preview){
            //     console.info(this.$refs.image_modal.content);
            //     // document.body.append(this.$refs.image_modal)
            // }
        }
    },
    data() {
        return {
            show_preview: false,
            error_found: false,
            is_dark_mode: false
        }
    },
    methods: {
        onImgError() {
            this.error_found = true;
        },
        onImageClick(e) {
            if (this.canShowFullImage) {
                e.preventDefault();
                this.show_preview = true;
            }else if(this.showWindowPreview){
                e.preventDefault();
                window.open(this.getSrc);
            }
        },
        getDimensions(dim: string) {
            return dim == null ? dim : dim.toString().includes('%') || dim.toString().includes('px') ? dim : dim + "px";
        }
    },
    computed: {
        getFilter(){
            return ((this.src==null || this.error_found) && this.placeholderSrc!=null && this.is_dark_mode) ? 'invert(1)': 'none';
        },
        getFilteredText() {
            if (this.fromText == null)
                return "";
            let texts = this.fromText.split(" ");
            return (texts.length > 0 ? texts[0][0].toString().toUpperCase() : "") + (texts.length > 1 ? texts[1][0].toString().toUpperCase() : "");
        },
        canShowTextImage() {
            return (this.error_found || (this.placeholderSrc == null && this.src == null)) && this.fromText != null;
        },
        canShowFullImage() {
            return this.showFullImage && this.src != null;
        },
        getHeight() {
            return this.getDimensions(this.size) ?? this.getDimensions(this.height);
        },
        getWidth() {
            return this.getDimensions(this.size) ?? this.getDimensions(this.width);
        },
        getSrc() {
            return this.error_found ? this.placeholderSrc ?? '/assets/images/placeholder/any.png' : (this.src ?? this.placeholderSrc ?? '/assets/images/placeholder/any.png');
        }
    },
    created(){
        const store = useLayoutStore();
        this.is_dark_mode = store.layout.is_dark;
        store.$subscribe((mut, state) => {
            this.is_dark_mode = store.layout.is_dark;
        });
    }

});
</script>

<style scoped>

</style>
