<template>
    <template v-if="loading">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" :stroke="stroke" stroke-width="3" stroke-miterlimit="10"/>
            </svg>
        </div>
    </template>
</template>

<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
    props: {
        loading: {
            type: Boolean,
            default: false
        },
        stroke: {
            type: String,
            default: 'white'
        }
    }
});
</script>

<style scoped>

.loader {
    position: relative;
    /*margin: 0 auto;*/
    width: 18px;
}

.loader::before {
    content: '';
    display: block;
    padding-top: 100%;
}

.circular {
    animation: rotate 1.25s linear infinite;
    height: 100%;
    transform-origin: center center;
    width: 100%;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto;
}

.path {
    stroke-dasharray: 1, 200;
    stroke-dashoffset: 0;
    /*stroke: white;*/
    animation: dash 1.5s ease-in-out infinite;
    stroke-linecap: round;
}

@keyframes rotate {
    100% {
        transform: rotate(360deg);
    }
}

@keyframes dash {
    0% {
        stroke-dasharray: 1, 200;
        stroke-dashoffset: 0;
    }
    50% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -35px;
    }
    100% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -124px;
    }
}

</style>
