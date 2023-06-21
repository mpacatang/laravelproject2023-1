<template>
    <div class="linear-progress-bar" v-if="loading">
        <div class="value"></div>
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
.linear-progress-bar{
    position: absolute;
    top: 0;
}
</style>
