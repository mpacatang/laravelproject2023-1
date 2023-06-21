<template>
    <div class="card">
        <slot></slot>

        <div v-if="loading" class="card-disabled">
            <div class="card-portlets-loader"></div>
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
        },
    }
});
</script>
