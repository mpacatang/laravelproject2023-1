<template>
    <div>

        <PageLoading :loading="page_loading" class="mt-5">
            <Row justify-content="center">
                <Col lg="5">
                    <Card>
                        <CardHeader icon="database" title="Database Setup">
                            <div class="float-end">
                                Step 2
                            </div>
                        </CardHeader>
                        <CardBody class="pb-0">

                            <FormInput v-model="step.db_host" :errors="errors" label="Database Host" name="db_host"
                                       placeholder="localhost"/>

                            <FormInput v-model="step.db_name" :errors="errors" label="Database Name" name="db_name"
                                       placeholder="test_shopy"/>

                            <FormInput v-model="step.db_username" :errors="errors" label="Database Username"
                                       name="db_username" placeholder="test_username"/>

                            <FormInput v-model="step.db_password" :errors="errors" label="Database Password"
                                       name="db_password" placeholder="test_password"/>


                            <p v-if="!step.database_valid" class="fw-medium d-flex align-items-center text-danger">
                                <Icon class="me-1-5" icon="cancel" size="18"></Icon>
                                <span class="fw-semibold me-1">Error:</span>
                                <template v-if="step.reason">
                                    {{ step.reason }}
                                </template>
                                <template v-else>
                                    Database can't connect. Recheck all entities
                                </template>
                            </p>

                            <div class="text-end mb-3">
                                <Button class="me-2-5 fw-medium" color="info" variant="text"
                                        @click="goToDocs">
                                    Go to Docs
                                </Button>
                                <LoadingButton :loading="loading" flexed-icon hide-icon
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
import {IIStep2} from "@js/types/models/installation";
import {DocsNavigation} from "@js/services/navigator_service";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {installationAPI} from "@js/services/api/request_url";
import {ToastNotification} from "@js/services/toast_notification";

export default defineComponent({
    components: {
        ...Components,
    },
    mixins: [ ValidationErrorMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            step: {} as IIStep2,

        }
    },

    methods: {
        goToDocs() {
            DocsNavigation.goToBackend();
        },
        async next() {
            this.loading = true;
            this.step.database_valid = true;
            try {
                const response = await Request.postAuth<IIStep2>(installationAPI.step_2.update_db, this.step);
                this.step.database_valid = response.data.database_valid;
                this.step.reason = response.data.reason;
                this.step.can_next = response.data.can_next;
                if(this.step.can_next){
                    ToastNotification.success("Database setup complete")
                    this.$router.replace({name: 'installations.step_3'});
                }
                this.clearAllError();
            } catch (error) {
                console.info(error);
                this.clearAllError();
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }

            this.loading = false;
        }
    },
    async mounted() {
        try {
            const response = await Request.getAuth<IIStep2>(installationAPI.step_2.get, {forced: true});
            this.step = response.data;
            this.step.database_valid = true;
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
