<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('create_coupon')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col xl="5">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="coupon.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>
                        <CardBody class="pb-0">

                            <FormInput v-model="coupon.code" :errors="errors"
                                       :label="$t('code')" name="code" required/>


                            <TextEditor v-model="coupon.description" :label="$t('description')"/>


                            <DateTimePicker v-model="coupon.started_at"
                                            :errors="errors"
                                            :label="$t('started_at')" :title="$t('started_at')" name="started_at"/>

                            <DateTimePicker v-model="coupon.expired_at" :errors="errors"
                                            :label="$t('expired_at')"
                                            :min-date="coupon.started_at" :title="$t('expired_at')"
                                            name="expired_at"></DateTimePicker>


                        </CardBody>

                    </Card>
                </Col>

                <Col xl="7">
                    <Card>
                        <CardHeader :title="$t('discount')" icon="confirmation_number"/>
                        <CardBody class="pb-0">
                            <Row>
                                <Col lg="6">
                                    <FormCheckbox v-model="coupon.first_order"
                                                  :errors="errors"
                                                  :label="$t('first_order')" name="first_order"/>
                                </Col>

                                <Col lg="6">
                                    <FormCheckbox v-model="coupon.delivery_free"
                                                  :errors="errors"
                                                  :label="$t('delivery_free')" name="delivery_free"/>

                                </Col>

                            </Row>


                            <div :class="[{'item-disabled o-35': coupon.delivery_free}]">
                                <Row>
                                    <Col lg="6">
                                        <FormInput v-model="coupon.discount" :errors="errors"
                                                   :label="$t('discount')"
                                                   :max="coupon.discount_type==='percent'?100:null"
                                                   :prefix-or-suffix="coupon.discount_type"
                                                   :required="!coupon.delivery_free" min="0"
                                                   name="discount"
                                                   type="number"/>
                                    </Col>
                                    <Col lg="6">

                                        <FormSelect :data="charge_types" :errors="errors"
                                                    :helper="charge_type_helper"
                                                    :label="$t('discount_type')"
                                                    :onSelected="(e)=>coupon.discount_type=e"
                                                    :placeholder="$t('discount_type')"
                                                    :selected="coupon.discount_type"
                                                    name="discount_type"/>

                                    </Col>
                                    <Col lg="6">
                                        <FormInput v-model="coupon.min_order" :errors="errors"
                                                   :label="$t('min_order')" :required="!coupon.delivery_free" min="0"
                                                   name="min_order"
                                                   no-spacing
                                                   show-currency type="number"/>
                                    </Col>
                                    <Col lg="6">
                                        <FormInput v-model="coupon.max_discount" :errors="errors"
                                                   :label="$t('max_discount')" :required="!coupon.delivery_free" min="0"
                                                   name="max_discount"
                                                   show-currency type="number"/>
                                    </Col>
                                </Row>
                            </div>

                            <Row>
                                <Col :class="[{'item-disabled o-35': coupon.first_order}]" lg="6">
                                    <FormInput v-model="coupon.limit" :errors="errors"
                                               :label="$t('limit')" min="0" name="limit" type="number"/>
                                </Col>
                                <Col lg="6">
                                    <FormSelect
                                        :class="[{'item-disabled o-35':coupon.shop_id!=null || coupon.zone_id!=null}]"
                                        :data="modules"
                                        :helper="module_helper"
                                        :label="$t('module')"
                                        :onSelected="(e)=>coupon.module_id=e"
                                        :placeholder="$t('select_module')"
                                        :selected="coupon.module_id"
                                        name="module_id"/>

                                </Col>
                            </Row>
                            <Row>
                                <Col lg="6">
                                    <FormSelect
                                        :class="[{'item-disabled o-35':coupon.module_id!=null || coupon.zone_id!=null}]"
                                        :data="shops"
                                        :helper="shop_helper"
                                        :label="$t('shop')"
                                        :onSelected="(e)=>coupon.shop_id=e"
                                        :placeholder="$t('select_shop')"
                                        :selected="coupon.shop_id"
                                        name="shop_id"/>

                                </Col>
                                <Col lg="6">
                                    <FormSelect
                                        :class="[{'item-disabled o-35':coupon.module_id!=null || coupon.shop_id!=null}]"
                                        :data="zones"
                                        :helper="zone_helper"
                                        :label="$t('zone')"
                                        :onSelected="(e)=>coupon.zone_id=e"
                                        :placeholder="$t('select_zone')"
                                        :selected="coupon.zone_id"
                                        name="zone_id"/>
                                </Col>
                            </Row>
                        </CardBody>
                    </Card>
                </Col>
            </Row>

            <div class="text-end">
                <CreateButton :loading="loading" @click="create"/>
            </div>

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
import {ICoupon} from "@js/types/models/coupon";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {IData} from "@js/types/models/data";
import {IShop, Shop} from "@js/types/models/shop";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IModule, Module} from "@js/types/models/module";
import {IZone, Zone} from "@js/types/models/zone";
import {IBreadcrumb} from "@js/types/models/models";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import {stringToUTCDateString} from "@js/shared/utils";
import NavigatorService from "@js/services/navigator_service";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        CreateButton,
        DateTimePicker,
        ...Components, Layout
    },
    mixins: [FormMixin, DiscountTypeMixin, UtilMixin],
    data() {
        const shop_helper = Shop.select_helper();

        return {
            shop_helper,
            loading: false,
            page_loading: true,
            coupon: {
                active: true,
                delivery_free: false,
                limit: 1,
                discount_type: 'amount'
            } as ICoupon,
            shops: [] as IShop[],
            modules: [] as IModule[],
            zones: [] as IZone[],
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('coupons'),
                    route: 'admin.coupons.index',
                },
                {
                    text: this.$t("create"),
                    active: true,
                },
            ];
        },
        module_helper: Module.select_helper,
        zone_helper: Zone.select_helper,
    },
    methods: {
        async create() {
            this.loading = true;

            try {
                const response = await Request.postAuth(adminAPI.coupons.create, {
                    ...this.coupon,
                    started_at: stringToUTCDateString(this.coupon.started_at),
                    expired_at: stringToUTCDateString(this.coupon.expired_at),
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('coupon_is_created'));
                    RequestCache.removeSimilar(adminAPI.coupons.get);
                    NavigatorService.goBackOrRoute({name: 'admin.coupons.index'});
                }
            } catch (error) {
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
        this.setTitle(this.$t('create_coupon'));
        try {
            const response = await Request.getAuth<IData<IShop[]>>(adminAPI.shops.get);
            this.shops = response.data.data;
            const moduleResponse = await Request.getAuth<IData<IModule[]>>(adminAPI.modules.get);
            if (moduleResponse.success()) {
                this.modules = moduleResponse.data.data;
            }
            const zoneResponse = await Request.getAuth<IData<IZone[]>>(adminAPI.zones.get);
            if (zoneResponse.success()) {
                this.zones = zoneResponse.data.data;
            }
            this.page_loading = false;
        } catch (error) {
            handleException(error);
        }
    }
})

</script>
