<template>
    <div :ref="viewerId">
    </div>
</template>

<script lang="ts">

import {defineComponent} from "vue";

export default defineComponent({
    props: {
        viewerId: {
            type: String,
            default: 'view'
        },
        text: {
            type: String,
            required: true
        }
    },
    watch:{
      text(){
          this.$refs[this.viewerId].innerHTML = this.text
      }
    },
    methods: {},
    computed: {},
    mounted() {
        this.$refs[this.viewerId].innerHTML = this.text;
    }
});
</script>

<style scoped>

</style>
