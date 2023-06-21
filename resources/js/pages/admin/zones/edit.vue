<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_zones')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="6">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note">
                            <div class="float-end">
                                <FormSwitch v-model="active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" noSpacing/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <FormInput v-model="name" :errors="errors"
                                       :label="$t('name')" name="name">
                                <template #label-suffix>
                                    <InfoTooltip tooltip="It should be unique, so that other zone can't conflict"/>
                                </template>
                            </FormInput>

                            <FormInputArea :errors="errors"
                                           :label="$t('coordinates')"
                                           :model-value="polygon_text??polygon"
                                           disabled
                                           name="polygon"
                                           no-spacing rows="6" type="textarea"/>


                            <div class="text-end mt-3">
                                <UpdateButton :loading="loading" @click="()=>this.update()"/>

                            </div>

                        </CardBody>

                    </Card>

                </Col>
                <Col lg="6">
                    <Card>
                        <CardHeader :title="$t('draw_a_zone')" icon="where_to_vote"></CardHeader>
                        <CardBody class="p-2">
                            <PolygonGoogleMap :on-polygon-change="onChangePolygon" :polygon="selected_polygon"
                                              height="500" search-enabled/>
                        </CardBody>
                    </Card>
                </Col>


            </Row>


            <VModal v-model="show_alert_modal">
                <Card class="mb-0">
                    <CardHeader :title="$t('inactive_alert')"
                                icon="warning"></CardHeader>
                    <CardBody>
                        <p class="mb-1-5">{{ $t('there_is_few_other_things_also_become_inactive') }}</p>
                        <p class="mb-1 fw-medium">{{ $t('connected_to') }}</p>
                        <ul>
                            <li>{{ $t('related_shops') }}</li>
                            <li>{{ $t('related_products') }}</li>
                            <li>{{ $t('related_addons') }}</li>
                        </ul>

                        <div class="text-end">
                            <Button class="me-2" color="secondary" variant="soft"
                                    @click="()=>this.show_alert_modal=false">
                                {{ $t('cancel') }}
                            </Button>
                            <Button color="danger" flexed-icon @click="()=>this.update(true)">
                                <Icon class="me-1-5" icon="warning" type="msr"/>
                                {{ $t('update') }}
                            </Button>
                        </div>
                    </CardBody>
                </Card>
            </VModal>


        </PageLoading>


    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import GoogleMap from "@js/components/maps/GoogleMap.vue";
import PolygonGoogleMap from "@js/components/maps/PolygonGoogleMap.vue";
import FormInputArea from "@js/components/form/FormInputArea.vue";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import VModal from "@js/components/VModal.vue";
import {IZone} from "@js/types/models/zone";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";
import {IBreadcrumb} from "@js/types/models/models";

export default defineComponent({
    components: {
        VModal,
        UpdateButton,
        FormInputArea,
        PolygonGoogleMap,
        GoogleMap,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        const breadcrumb_items = [
            {
                text: this.$t('zones'),
                route: 'admin.zones.index',
            },
            {
                text: this.$t("create"),
                active: true,
            },
        ] as IBreadcrumb[];

        return {
            breadcrumb_items,
            id: this.$route.params.id,
            loading: false,
            page_loading: true,

            zone: null as IZone,
            active: true,
            name: null,
            polygon: null,
            selected_polygon: null,
            polygon_text: null,
            show_alert_modal: false
        }
    },
    computed: {
        breadcrumb_items() {
            return [
                {
                    text: this.$t('zones'),
                    nameRoute: 'admin.zones.index',
                },
                {
                    text: this.$t("edit"),
                    active: true,
                },
            ];
        },
    },
    watch: {},
    methods: {
        async update(confirmation = false) {
            if (this.loading)
                return;

            if (!confirmation && this.zone.active && !this.active) {
                this.show_alert_modal = true;
                return;
            }
            this.show_alert_modal = false;

            this.loading = true;
            try {
                let coordinate = "";
                if (this.polygon == null) {
                    coordinate = null;
                } else {
                    for (const polygon of this.polygon) {
                        coordinate += (polygon.lat() + "," + polygon.lng() + ";");
                    }
                    coordinate = coordinate.slice(0, -1);
                }


                const response = await Request.patchAuth(adminAPI.zones.update(this.id), {
                    name: this.name,
                    active: this.active,
                    polygon: coordinate
                });

                if (response.success()) {
                    ToastNotification.success(this.$t('zone_is_updated'));
                    RequestCache.removeSimilar(adminAPI.zones.get);
                    NavigatorService.goBackOrRoute({name: 'admin.zones.index'});
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
        onChangePolygon(polygon) {
            this.polygon = polygon.getPath().getArray();
            this.polygon_text = "(" + this.polygon.map(arr => arr.lat() + ", " + arr.lng()).join("), (") + ")";
            this.selected_polygon = null;
        }
    },
    async mounted() {
        this.setTitle(this.$t('edit_zone'));
        try {
            const response = await Request.getAuth<IData<any>>(adminAPI.zones.show(this.id));
            if (response.success()) {
                let zone = response.data.data;
                this.zone = response.data.data;
                this.name = zone.name;
                this.active = zone.active;
                this.selected_polygon = zone.coordinates.coordinates[0];
                this.polygon_text = "(" + zone.coordinates.coordinates[0].map(arr => arr[0] + ", " + arr[1]).join("), (") + ")";
                this.page_loading = false;
            }

        } catch (error) {
            handleException(error);
        }
    },

})

</script>
