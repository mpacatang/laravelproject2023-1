<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_zones')"/>

        <Row>
            <Col lg="6">
                <Card>
                    <CardHeader :title="$t('general')" icon="edit_note">
                        <div class="float-end">
                            <FormSwitch v-model="active" no-spacing
                                          :errors="errors"
                                          :label="$t('active')" name="active"/>
                        </div>
                    </CardHeader>

                    <CardBody>

                        <FormInput v-model="name" :errors="errors"
                                   :label="$t('name')" name="name">
                            <template #label-suffix>
                                <InfoTooltip tooltip="It should be unique, so that other zone can't conflict"/>
                            </template>
                        </FormInput>

                        <FormInputArea v-model="polygon" :errors="errors" name="polygon"
                                       :label="$t('coordinates')" disabled no-spacing
                                       type="textarea" rows="6"/>


                        <div class="text-end mt-3">
                            <CreateButton :loading="loading" @click="create"></CreateButton>

                        </div>

                    </CardBody>

                </Card>

            </Col>
            <Col lg="6">
                <Card>

                    <CardHeader :title="$t('draw_a_zone')" icon="where_to_vote"></CardHeader>
                    <CardBody class="p-2">
                        <PolygonGoogleMap :on-polygon-change="onChangePolygon" height="500" search-enabled/>

                    </CardBody>
                </Card>
            </Col>


        </Row>


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
import UtilMixin from "@js/shared/mixins/UtilMixin";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";
import {IBreadcrumb} from "@js/types/models/models";

export default defineComponent({
    components: {
        CreateButton,
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
            loading: false,
            active: true,
            name: null,
            polygon: null,

        }
    },
    computed: {


    },
    methods: {
        async create() {
            if(this.polygon==null){
                ToastNotification.error(this.$t('please_select_area'));
                return;
            }
            this.loading = true;
            try {

                let coordinate = "";
                for (const polygon of this.polygon) {
                    coordinate += (polygon.lat() + "," + polygon.lng() + ";");
                }
                coordinate = coordinate.slice(0,-1);

                const response = await Request.postAuth(adminAPI.zones.create, {
                    name: this.name,
                    active: this.active,
                    polygon: coordinate
                });

                if (response.success()) {
                    RequestCache.removeSimilar(adminAPI.zones.get);
                    ToastNotification.success(this.$t('zone_is_created'));
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
        }
    },
    mounted(){
        this.setTitle(this.$t('create_zone'));
    }
})

</script>
