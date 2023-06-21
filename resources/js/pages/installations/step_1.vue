<template>
  <div>

    <PageLoading :loading="page_loading" class="mt-5">
      <Row justify-content="center">
        <Col lg="5">
          <Card>
            <CardHeader icon="priority" title="Permission & Version Checking">
              <div class="float-end">
                Step 1
              </div>
            </CardHeader>
            <CardBody class="pb-0">

              <template v-if="step.need_to_update_php">
                <div
                    class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2">
                  <Icon class="me-2" color="danger" icon="cancel" size="20"></Icon>
                  <span>PHP version need to update</span>
                </div>
              </template>
              <template v-else>
                <div
                    class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2">
                  <Icon class="me-2" color="success" icon="check_circle" size="20"></Icon>
                  PHP Version {{ step.php_version }}
                </div>
              </template>

              <template v-if="step.curl_enable">
                <div
                    class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2">
                  <Icon class="me-2" color="success" icon="check_circle" size="20"></Icon>
                  Curl Enabled
                </div>
              </template>
              <template v-else>
                <div
                    class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2">
                  <Icon class="me-2" color="danger" icon="cancel" size="20"></Icon>
                  Curl is not enabled
                </div>
              </template>


              <template v-if="step.env_write_permission">
                <div
                    class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2">
                  <Icon class="me-2" color="success" icon="check_circle" size="20"></Icon>
                    Env (.env) write permission
                </div>
              </template>
                <template v-else>
                    <div
                        class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2 text-danger">
                        <Icon class="me-2" color="danger" icon="cancel" size="20"></Icon>
                        Env (.env) write permission is not set
                    </div>
                </template>


                <template v-if="step.bootstrap_permission">
                    <div
                        class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2">
                        <Icon class="me-2" color="success" icon="check_circle" size="20"></Icon>
                        Bootstrap/cache (folder) write permission
                    </div>
                </template>
                <template v-else>
                    <div
                        class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2 text-danger">
                        <Icon class="me-2" color="danger" icon="cancel" size="20"></Icon>
                        Bootstrap/cache (folder) write permission is not set
                    </div>
                </template>


                <template v-if="step.storage_permission">
                    <div
                        class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2">
                        <Icon class="me-2" color="success" icon="check_circle" size="20"></Icon>
                        Storage (folder) write permission
                    </div>
                </template>
                <template v-else>
                    <div
                        class="d-flex border rounded border-dashed p-2 px-2-5 align-items-center fw-medium mb-2 text-danger">
                        <Icon class="me-2" color="danger" icon="cancel" size="20"></Icon>
                        Storage (folder) write permission is not set
                    </div>
                </template>


                <div class="text-end mb-3">
                    <Button v-if="!step.can_next" class="me-2-5 fw-medium" color="danger" variant="text"
                            @click="goToDocs">
                        Go to Docs
                    </Button>
                    <LoadingButton :disabled="!step.can_next" :loading="loading" flexed-icon hide-icon
                                   @click="next">
                        Next ->
                </LoadingButton>
              </div>

            </CardBody>


          </Card>
        </Col>
      </Row>

    </PageLoading>

  </div>
</template>

<script lang="ts">

import Response from "@js/services/api/response";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IIStep1} from "@js/types/models/installation";
import Request from "@js/services/api/request";
import {installationAPI} from "@js/services/api/request_url";
import {DocsNavigation} from "@js/services/navigator_service";

export default defineComponent({
  components: {

    ...Components,
  },
  mixins: [],
  data() {
    return {
      loading: false,
      page_loading: true,
      step: {} as IIStep1
    }
  },

  methods: {
    goToDocs() {
      DocsNavigation.goToBackend();
    },
    async next() {
      if (!this.step.can_next)
        return;

      this.$router.replace({name: 'installations.step_2'});

    }
  },
  async mounted() {
    try {
      const response = await Request.getAuth<IIStep1>(installationAPI.step_1.get,{forced: true});
      this.step = response.data;
    } catch (error) {

    }
    this.page_loading = false;
  }
})

</script>
