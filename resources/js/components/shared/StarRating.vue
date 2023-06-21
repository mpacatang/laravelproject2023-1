<template>
    <div :style="{fontSize: this.size+'px'}" class="d-inline-flex" :class="[{'cursor-pointer': interactive}]" @mouseleave="mouseOut" @click="$emit('rating:changed', hoverRating)">
        <i v-for="star in fullStar" :key="'full ' + star" :style="{marginRight: starSpacing+'px'}"  :class="['text-star-'+roundedValue]"
           class="fas fa-star" @mouseover="mouseOverAt(star)"/>
        <i v-if="halfStar" :style="{marginRight: starSpacing+'px',}"
           class="fas fa-star-half-alt" :class="['text-star-'+roundedValue]"/>
        <i v-for="star in emptyStar" :key="'empty ' + star" class="far fa-star text-muted"
           :style="{marginRight: starSpacing+'px'}"
           @mouseover="mouseOverAt(fullStar+star)"/>
    </div>
</template>

<script lang="ts">
import {TextColorTypes} from "@js/types/custom_types";
import {PropType} from "vue";

export default {
    props: {
        rating: {
            type: Number,
            default: 5,
        },
        size: {
            type: Number,
            default: 16
        },
        starSpacing: {
            type: Number,
            default: 0
        },
        activeColor: {
            type: String as PropType<TextColorTypes>,
            default: "success"
        },
        interactive: {
            type: Boolean,
            default: false
        },


    },
    data(){
      return {
          hoverRating: this.rating
      }
    },
    computed: {
        value() {
            if(this.interactive){
                return this.hoverRating > 5 ? 5 : this.hoverRating < 0 ? 0 : this.hoverRating;
            }else {
                return this.rating > 5 ? 5 : this.rating < 0 ? 0 : this.rating;
            }
        },
        halfStar() {
            const fraction = Math.round((this.value - Math.floor(this.value)) * 100);
            return fraction > 0 && fraction < 70;
        },
        fullStar() {
            return this.halfStar ? Math.floor(this.value) : Math.ceil(this.value);
        },
        emptyStar() {
            return 5 - this.fullStar - (this.halfStar ? 1 : 0);
        },
        roundedValue(){
            return Math.round(this.value);
        }

    },
    methods:{
        mouseOverAt(index: number){
            this.hoverRating=index;
        },
        mouseOut(index: number){
            this.hoverRating=this.rating;

        }
    }

}
</script>

<style scoped>

</style>
