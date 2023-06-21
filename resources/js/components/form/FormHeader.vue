<template>
    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3 fw-bold">{{ title }}</h5>
</template>


<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
    props: {
        title: String,
    }
});
</script>
