<template>
    <div v-if="loading" class="text-center">
        <div class="bouncing-loader">
            <div></div>
            <div></div>
            <div></div>
        </div>

    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
    props: {
        loading: {
            type: Boolean,
            default: false
        }
    }
});
</script>

<style scoped>

</style>
