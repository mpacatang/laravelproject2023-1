<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('send_notification')"/>
        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('notification')" icon="stay_current_portrait" type="msr"></CardHeader>

                        <CardBody>
                            <FormInput v-model="notification.title" :errors="errors"
                                       :label="$t('title')"
                                       name="title" type="text"/>

                            <TextEditor v-model="notification.body" :errors="errors"
                                        :label="$t('body')"
                                        name="body" type="text"/>

                            <FileUpload :default-files="image_helper.defaultFiles" :height="126"
                                        :label="$t('promotional_image')"
                                        :max-files="1"
                                        :on-added="image_helper.onFileUpload"
                                        :on-removed="image_helper.onFileRemoved"
                                        preview-as-list
                                        show-file-manager/>

                            <div class="d-flex justify-content-between align-items-center">
                                <FormLabel>{{ $t('actions') }}</FormLabel>
                                <Button v-if="canSeeAddAction"
                                        class="fw-medium" color="primary" flexed-icon variant="text" @click="addAction">
                                    <Icon class="me-1" icon="add" size="16"/>
                                    {{ $t('add_action') }}
                                </Button>
                            </div>
                            <template v-if="notification.actions">
                                <template v-for="action in notification.actions">
                                    <Row align-items="center">
                                        <Col>
                                            <FormInput v-model="action.text"
                                                       :errors="errors"
                                                       :placeholder="$t('button_text')"
                                                       name="text"
                                                       no-label type="text"/>
                                        </Col>
                                        <Col>
                                            <FormInput v-model="action.click_action"
                                                       :errors="errors"
                                                       :placeholder="$t('click_action')"
                                                       name="action"
                                                       no-label type="text"/>
                                        </Col>
                                        <Col auto>
                                            <div
                                                class="border rounded-circle p-1 d-flex align-items-center mb-3 cursor-pointer"
                                                @click="removeAction(action)">
                                                <Icon icon="close" size="14" type="msr"></Icon>
                                            </div>
                                        </Col>

                                    </Row>
                                </template>
                                <div v-if="!canSeeAddAction" class="float-end">
                                    <Button class="fw-medium" color="info" flexed-icon variant="text"
                                            @click="this.show_info_modal = true;">
                                        <Icon class="me-1-5" icon="info" size="16"/>
                                        {{ $t('how_to_set_click_action') }}
                                    </Button>
                                </div>
                            </template>


                        </CardBody>

                    </Card>

                </Col>
                <Col xl="6">
                    <Card>
                        <CardHeader :title="$t('send')" icon="phonelink_ring" type="msr"></CardHeader>

                        <CardBody>

                            <Row>
                                <Col lg="4">
                                    <FormCheckbox v-model="notification.all_customers" :label="$t('all_customers')"
                                                  name="all_customers"/>
                                </Col>
                                <Col lg="4">
                                    <FormCheckbox v-model="notification.all_sellers" :label="$t('all_sellers')"
                                                  name="all_sellers"/>
                                </Col>
                                <Col lg="4">
                                    <FormCheckbox v-model="notification.all_delivery_boys"
                                                  :label="$t('all_delivery_boys')"
                                                  name="all_delivery_boys"/>
                                </Col>
                            </Row>

                            <div class="d-flex align-items-center justify-content-end mt-3">
                                <span class="fw-medium me-1-5">{{ $t('scheduled_at') }}</span>
                                <DateTimePicker v-model="notification.schedule_at" :errors="errors"
                                                :min-date="Date.now()"
                                                :placeholder="$t('schedule_notification')" name="schedule_at" no-label
                                                no-spacing/>

                                <div class="ms-2">
                                    <LoadingButton v-if="notification.schedule_at" :loading="loading"
                                                   color="bluish-purple" flexed-icon icon="schedule_send" @click="send">
                                        {{ $t('set_schedule') }}
                                    </LoadingButton>
                                    <LoadingButton v-else :loading="loading" flexed-icon icon="send" @click="send">
                                        {{ $t('send') }}
                                    </LoadingButton>
                                </div>

                            </div>


                        </CardBody>

                    </Card>

                </Col>
            </Row>

            <VModal v-model="show_info_modal" lg close-btn>
                <Card class="mb-0">
                    <CardHeader :title="$t('how_to_set_click_action')" icon="help"/>
                    <CardBody>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Supported For</th>
                                <th>Click Action</th>
                                <th>Description</th>
                                <th>Example</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>Customer</td>
                                <td>shops/:id</td>
                                <td>Open specific shop (id)</td>
                                <td>shops/2</td>
                            </tr>
                            <tr>
                                <td>Customer</td>
                                <td>products/:id</td>
                                <td>Open specific product (id)</td>
                                <td>products/100014</td>
                            </tr>
                            </tbody>

                        </table>

                        <span class="fw-medium"><span class="text-primary fw-semibold">{{ $t('note') }}: </span> Developer can add more action easily. We are working on other actions</span>
                    </CardBody>
                </Card>
            </VModal>

        </PageLoading>


    </Layout>
</template>


<script lang="ts">

import Layout from "../layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Badge from "@js/components/Badge.vue";
import PageLoading from "@js/components/PageLoading.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IManualNotification, IManualNotificationAction, ManualNotification} from "@js/types/models/manual_notification";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import {stringToUTCDateString} from "@js/shared/utils";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import VModal from "@js/components/VModal.vue";
import {array_remove} from "@js/shared/array_utils";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        VModal,
        DateTimePicker,
        PageLoading,
        Badge,
        CardBody,
        Card,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        const breadcrumb_items = [
            {
                text: this.$t("manual_notifications"),
                route: 'admin.manual_notifications.index',
            },
            {
                text: this.$t('create'),
                active: true,
            },
        ];
        const image_helper = new FileUploadHelper({max: 1});

        return {
            image_helper,
            breadcrumb_items,
            loading: false,
            page_loading: false,
            show_info_modal: false,
            notification: {} as IManualNotification,
        }
    },
    computed: {
        canSeeAddAction() {
            return this.notification.actions == null || this.notification.actions.length === 0;
        }
    },
    methods: {
        addAction() {
            if (this.notification.actions == null) {
                this.notification.actions = [{}];
            } else {
                if (ManualNotification.validateActions(this.notification)) {
                    this.notification.actions.push({})
                } else {
                    ToastNotification.error(this.$t('please_fill_actions_properly'));
                }

            }
        },
        removeAction(action: IManualNotificationAction) {
            this.notification.actions = array_remove(this.notification.actions, action);
        },

        async send() {
            if (!ManualNotification.validateActions(this.notification)) {
                ToastNotification.error(this.$t('please_fill_actions_properly'));
                return;
            }
            this.loading = true;
            this.clearAllError();
            try {
                const response = await Request.postAuth(adminAPI.manual_notifications.create, {
                    ...this.notification,
                    schedule_at: stringToUTCDateString(this.notification.schedule_at),
                    actions: this.notification.actions != null ? JSON.stringify(this.notification.actions) : null,
                    image: this.image_helper.getImageDataFile()
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('notification_sent'));
                    RequestCache.removeSimilar(adminAPI.manual_notifications.get);
                    NavigatorService.goBackOrRoute('admin.manual_notifications.index');
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        },

    },
    async mounted() {
        this.setTitle(this.$t('manual_notification'));
    }
});

</script>
