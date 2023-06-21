<template>
    <PageLoading :loading="page_loading">

        <Card>
            <CardHeader :title="$t('setups')" icon="tune" type="msr">
            </CardHeader>
            <CardBody class="pb-0">
                <Row>

                    <Col lg="3">
                        <div class="p-2 border border-dashed  rounded mb-3">
                            <FormSwitch v-model="shop.open"
                                        :errors="errors" :label="$t('currently_open')"
                                        name="currently_open"
                                        no-spacing/>
                        </div>
                    </Col>

                    <Col lg="3">
                        <div class="p-2 border border-dashed  rounded mb-3">
                            <FormSwitch v-model="shop.take_away"
                                        :errors="errors" :label="$t('take_away')"
                                        name="take_away" no-spacing/>

                        </div>
                    </Col>
                    <Col lg="3">
                        <div class="p-2 border border-dashed  rounded mb-3">

                            <FormSwitch v-model="shop.open_for_delivery"
                                        :errors="errors" :label="$t('open_for_delivery')"
                                        name="open_for_delivery" no-spacing/>
                        </div>
                    </Col>
                    <Col lg="3">
                        <div class="p-2 border border-dashed  rounded mb-3">
                            <FormSwitch v-model="shop.self_delivery"
                                        :errors="errors" :label="$t('self_delivery')"
                                        name="self_delivery" no-spacing/>
                        </div>
                    </Col>

                </Row>
            </CardBody>
        </Card>

        <Row>
            <Col xl="6" xxl="4">
                <Card>
                    <CardHeader :title="$t('order_options')" icon="local_mall" type="msr">
                    </CardHeader>

                    <CardBody class="pb-0">
                        <Row>
                            <Col lg="6">
                                <FormInput v-model="shop.min_delivery_time" :errors="errors"
                                           :label="$t('min_delivery_time')"
                                           name="shop.min_delivery_time"
                                           type="number">
                                    <template #suffix>
                                        <Icon icon="schedule" size="xs"/>
                                    </template>
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="You can set approx. minimum delivery time, which includes preparing, packaging and delivery"/>
                                    </template>
                                </FormInput>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="shop.max_delivery_time" :errors="errors"
                                           :label="$t('max_delivery_time')"
                                           min="0"
                                           name="shop.max_delivery_time"
                                           type="number">
                                    <template #suffix>
                                        <Icon icon="schedule" size="xs"/>
                                    </template>
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="You can set approx. maximum delivery time, which includes preparing, packaging and delivery"/>
                                    </template>
                                </FormInput>
                            </Col>

                        </Row>
                        <Row>
                            <Col lg="6">
                                <FormSelect :data="delivery_time_types" :helper="delivery_time_type_helper"
                                            :label="$t('delivery_time_type')" :onSelected="onChangeDeliveryTypeType"
                                            :placeholder="$t('time_type')"
                                            :selected="selected_delivery_time_type"
                                            name="shop.delivery_time_type"/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="shop.min_order_amount" :errors="errors"
                                           :label="$t('min_order_amount')"
                                           :placeholder="$t('min_order_amount')"
                                           min="0"
                                           name="shop.min_order_amount"
                                           show-currency type="number">
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="You can set minimum order amount to make online order for customer"/>
                                    </template>
                                </FormInput>
                            </Col>
                        </Row>


                    </CardBody>
                </Card>
            </Col>
            <Col xl="6" xxl="4">
                <Card>
                    <CardHeader :title="$t('delivery_options')" icon="local_shipping" type="msr">

                    </CardHeader>
                    <CardBody class="pb-0">


                        <FormInput v-model="shop.delivery_range" :errors="errors"
                                   :label="$t('delivery_range')+' ['+$t('in_meter') +']'"
                                   :placeholder="$t('delivery_range_in_meter')"
                                   min="0"
                                   name="shop.delivery_range"
                                   type="number">
                            <template #label-suffix>
                                <InfoTooltip
                                    tooltip="If it blank, then customer can order from any distance [infinite]"/>
                            </template>
                        </FormInput>
                        <Row>
                            <Col lg="6">
                                <FormInput v-model="shop.minimum_delivery_charge" :errors="errors"
                                           :label="$t('minimum_delivery_charge')"
                                           :placeholder="$t('minimum_delivery_charge')" min="0"
                                           name="shop.minimum_delivery_charge"
                                           show-currency type="number">
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="When order is delivery type then delivery charge applied. It's a minimum charge"/>
                                    </template>
                                </FormInput>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="shop.delivery_charge_multiplier" :errors="errors"
                                           :label="$t('delivery_charge_multiplier')"
                                           min="0"
                                           name="shop.delivery_charge_multiplier"
                                           show-currency type="number">
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="Extra delivery charge, which is based on shop to delivery location distance.
                                                 Charge is calculate per KM = (1000 meter)"/>
                                    </template>
                                </FormInput>
                            </Col>
                        </Row>


                    </CardBody>
                </Card>

            </Col>
            <Col xl="6" xxl="4">
                <Card>
                    <CardHeader :title="$t('tax_info')" icon="post_add"></CardHeader>
                    <CardBody class="pb-0">
                        <FormInput v-model="shop.packaging_charge" :errors="errors"
                                   :label="$t('packaging_charge')"
                                   :placeholder="$t('packaging_charge')" name="shop.packaging_charge" show-currency>

                            <template #label-suffix>
                                <InfoTooltip
                                    tooltip="Includes packaging charges, extra cutlery, etc."/>
                            </template>

                        </FormInput>

                        <Row>
                            <Col lg="6">
                                <FormInput v-model="shop.tax" :errors="errors"
                                           :label="$t('tax')"
                                           :placeholder="$t('enter_a_tax')" :prefix-or-suffix="shop.tax_type"
                                           name="shop.tax">
                                    <template #label-suffix>
                                        <InfoTooltip
                                            tooltip="Includes SGST, CGST, and any other taxes"/>
                                    </template>
                                </FormInput>
                            </Col>
                            <Col lg="6">

                                <FormSelect :data="charge_types" :errors="errors"
                                            :helper="charge_type_helper"
                                            :label="$t('tax_type')"
                                            :onSelected="(value)=>shop.tax_type=value"
                                            :placeholder="$t('tax_type')" :selected="shop.tax_type"
                                            name="shop.tax_type"/>
                            </Col>

                        </Row>


                    </CardBody>
                </Card>
            </Col>
        </Row>


        <Card v-if="canChangeShopTiming">
            <CardHeader :title="$t('shop_timing')" icon="more_time" type="msr">

            </CardHeader>

            <CardBody>


                <table class="table table-bordered table-centered mb-0">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 150px">{{ $t('day') }}</th>
                        <th>{{ $t('time') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="day in allDays">
                        <td>
                            <span class="fw-semibold">{{ $t(day) }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <Button v-for="time in getDayTimings(day)" color="success" flexed-icon
                                        variant="outline" @click="deleteTimeConfirmation(time)">
                                                <span class="font-14">
                                                    {{ getFormattedTimeFromTime(time.open_at, {second: false}) }} -
                                                {{ getFormattedTimeFromTime(time.close_at, {second: false}) }}
                                                </span>
                                    <span class="btn-label-right" style="margin-bottom: 4px">
                                                    <Icon class="ms-1" icon="cancel" size="17" type="msr"></Icon>
                                                </span>
                                </Button>
                                <Button color="teal" variant="outline" @click="addTime(day)">
                                    <Icon icon="add" type="msr"/>
                                </Button>
                            </div>
                        </td>
                    </tr>
                    </tbody>

                </table>

            </CardBody>
        </Card>

        <div class="text-end mb-3">
            <UpdateButton :loading="loading" @click="update"/>
        </div>

        <VModal v-if="new_time" v-model="showing_time_modal" close-btn>
            <Card class="mb-0">
                <CardHeader :title="$t('create_schedule_for')+' - '+$t(new_time.day)" icon="more_time"/>
                <CardBody>
                    <DateTimePicker v-model="new_time.open_at" :errors="errors" :label="$t('open_at')" name="open_at"
                                    variant="time"/>
                    <DateTimePicker v-model="new_time.close_at" :errors="errors" :label="$t('close_at')" name="close_at"
                                    variant="time"/>

                    <Button class="float-end" color="primary" flexed-icon @click="createTime()">
                        <Icon icon="more_time" size="sm"></Icon>
                        <span class="ms-1-5">{{ $t('add_time') }}</span>
                    </Button>
                </CardBody>
            </Card>
        </VModal>

        <VModal v-if="delete_time" v-model="deleting_time_modal" close-btn>
            <Card class="mb-0">
                <CardHeader :title="$t('confirmation')" icon="timer_off"/>
                <CardBody>
                    <span>{{
                            $t('are_you_sure_to_delete_time_for') + " " + delete_time.day + " - [" + getFormattedTimeFromTime(delete_time.open_at, {second: false}) + " - " +
                            getFormattedTimeFromTime(delete_time.close_at, {second: false}) + "]"
                        }}</span>
                    <div class="d-flex justify-content-end mt-3">
                        <Button class="me-2" color="light" @click="hideDeleteTimeModal()">{{
                                $t('cancel')
                            }}
                        </Button>
                        <Button @click="deleteTime()">
                            <Icon icon="timer_off" size="sm"></Icon>
                            <span class="ms-1-5">{{ $t('remove_time') }}</span>
                        </Button>

                    </div>
                </CardBody>
            </Card>
        </VModal>

    </PageLoading>
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
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IShop, IShopTime, Shop, ShopTime} from "@js/types/models/shop";
import {IBreadcrumb, IFormSelectOption} from "@js/types/models/models";
import GoogleMap from "@js/components/maps/GoogleMap.vue";
import DeliveryTimeTypeMixin from "@js/shared/mixins/DeliveryTimeTypeMixin";
import {IData} from "@js/types/models/data";
import {IModule} from "@js/types/models/module";
import {array_remove} from "@js/shared/array_utils";
import DiscountTypeMixin from "@js/shared/mixins/ChargeTypeMixin";
import {IShopPlan} from "@js/types/models/shop_plan";
import VModal from "@js/components/VModal.vue";
import DateTimePicker from "@js/components/form/DateTimePicker.vue";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";

export default defineComponent({
    components: {
        UpdateButton,
        DateTimePicker,
        VModal,
        GoogleMap,
        Layout, ...Components
    },
    mixins: [FormMixin, UtilMixin, DeliveryTimeTypeMixin, DiscountTypeMixin],
    props: {
        id: {
            type: Number,
            required: true
        },
    },

    data() {

        return {
            loading: false,
            page_loading: true,
            shop: {} as IShop,
            new_time: {} as IShopTime,
            delete_time: {} as IShopTime,
            showing_time_modal: false,
            deleting_time_modal: false,

        }
    },

    computed: {
        allDays(): string[] {
            return Shop.getDays();
        },
        canChangeShopTiming() {
            return Shop.canChangeShopTiming(this.shop);
        },
        module_helper(): IFormSelectOption<IModule> {
            return {
                option: {
                    label: 'title',
                    value: 'id'
                },
            };
        },
        zone_helper(): IFormSelectOption<IModule> {
            return {
                option: {
                    label: 'name',
                    value: 'id'
                },
            };
        },
        shop_plan_helper(): IFormSelectOption<IShopPlan> {
            return {
                option: {
                    label: (plan) => plan.title + " " + this.getFormattedCurrency(plan.price),
                    value: 'id'
                },
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t("shop"),
                    route: 'admin.shops.index'
                },
                {
                    text: this.$t('create'),
                    active: true,
                },
            ];
        }
    },

    methods: {
        showTimeModal() {
            this.showing_time_modal = true;
        },
        hideTimeModal() {
            this.showing_time_modal = false;
        },
        showDeleteTimeModal() {
            this.deleting_time_modal = true;
        }, hideDeleteTimeModal() {
            this.deleting_time_modal = false;
        },
        addTime(day: string) {
            this.new_time = {
                day: day
            };
            this.errors = {};
            this.showTimeModal();
        },
        deleteTimeConfirmation(time: IShopTime) {
            this.delete_time = time;
            this.showDeleteTimeModal();
        },
        getDayTimings(day: string): IShopTime[] {
            let timings = [];
            if (this.shop.timings != null) {
                this.shop.timings.forEach(function (time) {
                    if (time.day === day) {
                        timings.push(time);
                    }
                });
            }
            return timings;
        },

        async createTime() {
            try {
                let timeResponse = await Request.postAuth<IData<IShopTime>>(
                    adminAPI.shop_times.create(this.id), ShopTime.formatShopTime(this.new_time));
                if (timeResponse.success()) {
                    this.shop.timings ??= [];
                    this.shop.timings.push(timeResponse.data.data);
                    ToastNotification.success(this.$t('time_added'));
                }
                this.hideTimeModal();
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    this.hideTimeModal();
                    handleException(error);
                }
            }

        },
        async deleteTime() {
            if (this.delete_time == null)
                return;
            try {
                let timeResponse = await Request.deleteAuth(adminAPI.shop_times.delete(this.id, this.delete_time.id));
                if (timeResponse.success()) {
                    this.shop.timings = array_remove(this.shop.timings, this.delete_time);
                    ToastNotification.success(this.$t('time_removed'));
                }
                this.hideDeleteTimeModal();
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    this.hideDeleteTimeModal();
                    handleException(error);
                }
            }

        },
        async update() {
            this.loading = true;
            try {
                const response = await Request.patchAuth(adminAPI.shops.update(this.id), {
                    ...this.shop,
                    logo: null,
                    cover_image: null

                });
                if (response.success()) {
                    ToastNotification.success(this.$t('settings_are_updated'));
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
        }
    },
    async mounted() {
        this.setTitle(this.$t('shop_settings'));
        try {
            const response = await Request.getAuth<IData<IShop>>(adminAPI.shops.show(this.id));
            if (response.success()) {
                this.shop = response.data.data
                this.page_loading = false;
            } else {
                ToastNotification.unknownError();
            }

        } catch (error) {
            handleException(error);
        }
        this.page_loading = false;
    }
});

</script>
