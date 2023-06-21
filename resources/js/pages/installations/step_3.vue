<template>
    <div>

        <PageLoading :loading="page_loading" class="mt-5">
            <Row justify-content="center">
                <Col lg="5">
                    <Card>
                        <CardHeader icon="database" title="Migrations">
                            <div class="float-end">
                                Step 3
                            </div>
                        </CardHeader>
                        <CardBody class="pb-0">

                            <div v-for="migration in step.migrations"
                                 class="border border-dashed p-2 rounded d-flex align-items-center mb-1-5">
                                <Icon v-if="migration.pending" class="me-2" color="danger" icon="cancel"
                                      size="20"></Icon>
                                <Icon v-else class="me-2" color="success" icon="check_circle" size="20"></Icon>
                                {{ migration.migration }}
                            </div>


                            <div class="text-end mb-3 mt-3">
                                <Button class="me-2-5 fw-medium" color="info" variant="text"
                                        @click="goToDocs">
                                    Go to Docs
                                </Button>
                                <LoadingButton v-if="!step.can_next" :disabled="step.can_next" :loading="loading"
                                               class="fw-medium" color="bluish-purple" flexed-icon @click="migrate">
                                    Migrate
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
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IIStep3} from "@js/types/models/installation";
import {DocsNavigation} from "@js/services/navigator_service";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {installationAPI} from "@js/services/api/request_url";
import {ToastNotification} from "@js/services/toast_notification";
import Response from "@js/services/api/response";

export default defineComponent({
    components: {
        ...Components,
    },
    mixins: [ValidationErrorMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            step: {} as IIStep3,

        }
    },

    methods: {
        goToDocs() {
            DocsNavigation.goToBackend();
        },
        async migrate() {
            this.loading = true;
            try {
                const response = await Request.postAuth<IIStep3>(installationAPI.step_3.migrate, {});
                if (response.success()) {
                    ToastNotification.success("Migration completed")
                    this.step = response.data;
                }
            } catch (error) {
                console.info(error);
                handleException(error);
            }

            this.loading = false;
        },
        async next() {
            this.$router.push({name: 'installations.step_4'})
        }
    },
    async mounted() {
        try {
            const response = await Request.getAuth<IIStep3>(installationAPI.step_3.get, {forced: true});
            this.step = response.data;
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
