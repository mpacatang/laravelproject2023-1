<template>
  <div>

    <PageLoading :loading="page_loading" class="mt-5">
      <Row justify-content="center">
        <Col lg="5">
          <Card>
            <CardHeader icon="admin_panel_settings" title="Create Super Admin">
              <div class="float-end">
                Step 4
              </div>
            </CardHeader>
            <CardBody class="pb-0">

              <Row>
                <Col lg="6">
                  <FormInput v-model="step.admin.first_name" :disabled="already_created"
                             :errors="errors" :label="$t('first_name')"
                             name="first_name" required/>
                </Col>
                <Col lg="6">
                  <FormInput v-model="step.admin.last_name" :disabled="already_created"
                             :errors="errors" :label="$t('last_name')"
                             name="last_name" required/>
                </Col>
              </Row>

              <Row>
                <Col lg="6">
                  <FormInput v-model="step.admin.mobile_number" :disabled="already_created"
                             :errors="errors"
                             :label="$t('mobile_number')" name="mobile_number" required
                             type="tel"/>
                </Col>
                <Col lg="6">

                  <FormInput v-model="step.admin.email" :disabled="already_created" :errors="errors"
                             :label="$t('email')" name="email" required type="email"/>

                </Col>
              </Row>
              <FormInput v-model="step.admin.password" :disabled="already_created" :errors="errors"
                         :label="$t('password')" name="password" no-spacing required/>


              <div class="text-end mb-3 mt-3">
                <Button class="me-2-5 fw-medium" color="info" variant="text"
                        @click="goToDocs">
                  Go to Docs
                </Button>
                <LoadingButton v-if="!step.can_next" :disabled="step.can_next"
                               :loading="loading"
                               class="fw-medium" color="bluish-purple" flexed-icon
                               @click="create">
                  Create Admin
                </LoadingButton>
                <LoadingButton v-else :loading="loading" flexed-icon
                               hide-icon @click="next">
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

import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import {IIStep4, IIStep5} from "@js/types/models/installation";
import {DocsNavigation} from "@js/services/navigator_service";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI, installationAPI} from "@js/services/api/request_url";
import {ToastNotification} from "@js/services/toast_notification";
import {IAdmin} from "@js/types/models/admin";
import Response from "@js/services/api/response";
import {useAdminDataStore} from "@js/services/state/states";

export default defineComponent({
  components: {
    ...Components,
  },
  mixins: [ValidationErrorMixin],
  data() {
    return {
      loading: false,
      page_loading: true,
      already_created: false,
      step: {
        admin: {}
      } as IIStep4,

    }
  },

  methods: {
    goToDocs() {
      DocsNavigation.goToBackend();
    },
    async create() {
      this.loading = true;
      try {
        const response = await Request.postAuth<IIStep5>(installationAPI.step_4.create, {...this.step.admin});
        if (response.success()) {

          const response_login = await Request.postAuth<any>(adminAPI.auth.login, {
            email: this.step.admin.email,
            password: this.step.admin.password
          });
          const store = useAdminDataStore();
          store.updateUserData(response_login.data.admin);
          store.updateAuthToken(response_login.data.token);
          ToastNotification.success("Super admin created & Logged In")
          this.already_created = true;
          this.step = response.data;
        }
        this.clearAllError();
      } catch (e) {
        this.clearAllError();

        if (Response.is422(e)) {
          this.errors = e.response.data.errors;
        } else if (Response.isInstallationFallback(e)) {
          const step = e.response.data.fallback;
          this.$router.replace({name: 'installations.' + step})
        } else if (Response.isInstallationError(e)) {
          const error = e.response.data.error;
          const details = e.response.data.details;
          console.info(error);
          ToastNotification.error(error);
          console.info("Installation got some errors:" + details);
        } else {
            console.info(e);
            handleException(e);
        }
      }
      this.loading = false;
    },
    async next() {
      this.$router.push({name: 'installations.step_5'});
    }
  },
  async mounted() {
    try {
        const response = await Request.getAuth<IIStep4>(installationAPI.step_4.get, {forced: true});
        const step = response.data;
      if (step.admin == null) {
        step.admin = {} as IAdmin;
      } else {
        this.already_created = true;
      }
      this.step = step;
    } catch (error) {
      if (Response.isInstallationFallback(error)) {
        const step = error.response.data.fallback;
        this.$router.replace({name: 'installations.' + step})

      }
    }
    this.page_loading = false;
  }
})

</script>
