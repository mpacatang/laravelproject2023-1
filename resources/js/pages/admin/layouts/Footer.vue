<template>
  <footer class="footer mt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">{{ new Date().getFullYear() }} © {{appName}} - MarkBuilt.ca</div>
        <div class="col-md-6">
            <div class="text-md-end footer-links d-none d-md-block">
                <a href="javascript: void(0);">About</a>
                <a href="javascript: void(0);">Support</a>
                <a href="javascript: void(0);">Contact Us</a>
            </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";

export default defineComponent({
    mixins: [UtilMixin],

})
</script>

