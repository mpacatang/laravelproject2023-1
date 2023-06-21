<template>
    <Layout>
        <Row>
            <Col lg="6">
                <Card>
                    <CardHeader :class="[{'py-2':isEdit}]" :title="$t('my_address')" icon="pin_drop" type="msr">
                        <div v-if="isEdit" class="float-end">
                            <Button class="py-1" color="primary" flexed-icon variant="soft" @click="onCreateAddress">
                                <Icon class="me-1-5" icon="add" size="20"></Icon>
                                {{ $t('create_new') }}
                            </Button>
                        </div>
                    </CardHeader>
                    <template v-if="addresses==null || addresses.length===0">
                        <p class="text-center fw-medium my-3">{{ $t('there_is_no_any_saved_address') }}</p>
                    </template>
                    <template v-else>
                        <CardBody>


                            <div v-for="address in addresses" class="mb-3 border p-2 d-flex rounded align-items-start">
                                <NetworkImage :src="mapPlaceholder" class="rounded" rounded width="100"/>
                                <div class="ms-2-5 flex-grow-1">
                                    <p class="font-14 mb-0-5 fw-semibold text-success">{{ address.type }}</p>
                                    <p class="mb-0-5 d-flex align-items-center">
                                        <Icon icon="location_on" size="16" type="msr"/>
                                        <span class="fw-medium ms-1">{{ address.address }}</span></p>
                                    <p class="mb-0 d-flex  align-items-center">
                                        <Icon icon="map" size="16" type="msr"/>
                                        <span class="ms-1">{{ address.city }} - {{ address.pincode }}</span></p>
                                </div>
                                <Button class="p-1" color="teal" flexed-icon variant="soft"
                                        @click="onEditAddress(address)">
                                    <Icon icon="edit" size="18"></Icon>
                                </Button>

                            </div>
                        </CardBody>
                    </template>
                </Card>
            </Col>
            <Col lg="6">
                <Card>

                    <CardHeader v-if="isEdit" :title="$t('edit_address')" icon="edit_location" type="msr"/>
                    <CardHeader v-else :title="$t('create_address')" icon="add_location_alt" type="msr"/>

                    <CardBody>
                        <GoogleMap :location="location" height="350" pick-enabled search-enabled
                                   @change-location="onLocationChange"></GoogleMap>
                        <FormValidationError :errors="errors" name="latitude"></FormValidationError>

                        <FormInput v-model="address.address" :errors="errors"
                                   :label="$t('address')" name="address"
                                   required top-spacing/>
                        <Row>
                            <Col lg="6">
                                <FormInput v-model="address.type" :errors="errors"
                                           :label="$t('type')" name="type" placeholder="Home, Office, Etc"
                                           required/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="address.landmark" :errors="errors"
                                           :label="$t('landmark')" name="landmark"
                                           required/>
                            </Col>
                        </Row>

                        <Row>
                            <Col lg="6">
                                <FormInput v-model="address.city" :errors="errors"
                                           :label="$t('city')" name="city"
                                           required/>
                            </Col>
                            <Col lg="6">
                                <FormInput v-model="address.pincode" :errors="errors"
                                           :label="$t('pincode')" name="pincode"
                                           required/>
                            </Col>
                        </Row>
                        <div class="float-end">
                            <template v-if="isEdit">
                                <UpdateButton :loading="loading" @click="saveAddress"></UpdateButton>
                            </template>
                            <template v-else>
                                <CreateButton :loading="loading" @click="saveAddress"></CreateButton>
                            </template>

                        </div>
                    </CardBody>
                </Card>
            </Col>
        </Row>
    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from "vue";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import QuantityButton from "@js/components/buttons/QuantityButton.vue";
import Button from "@js/components/buttons/Button.vue";
import {ICustomerAddress} from "@js/types/models/customer_address";
import {Images} from "@js/shared/constant";
import GoogleMap from "@js/components/maps/GoogleMap.vue";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import {ILatLng} from "@js/types/models/models";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import CreateButton from "@js/components/buttons/CreateButton.vue";

export default defineComponent({
    components: {CreateButton, UpdateButton, GoogleMap, Button, QuantityButton, Layout, ...Components},
    mixins: [UtilMixin, ValidationErrorMixin],
    data() {
        return {
            page_loading: true,
            loading: false,
            addresses: [] as ICustomerAddress[],
            address: {} as ICustomerAddress,
            location: {} as ILatLng
        };
    },
    computed: {
        mapPlaceholder() {
            return Images.mapPlaceholder;
        },
        isEdit() {
            return this.address.id != null;
        }
    },
    methods: {
        async getAddresses() {
            try {
                this.page_loading = true;
                const response = await Request.getAuth<IData<ICustomerAddress[]>>(customerAPI.addresses.get);
                this.addresses = response.data.data;
                this.page_loading = false;
            } catch (error) {
                handleException(error);
            }
        },
        async saveAddress() {
            try {
                const isEdit = this.isEdit
                this.loading = true;
                let response;
                if (isEdit) {
                    response = await Request.patchAuth(customerAPI.addresses.update(this.address.id), {
                        ...this.address, ...this.location
                    });
                } else {
                    response = await Request.postAuth(customerAPI.addresses.create, {
                        ...this.address, ...this.location
                    });
                }
                if (response.success()) {
                    this.clearAllError();
                    this.getAddresses().then();
                    this.address = {};
                    this.location = {};
                    if (isEdit) {
                        ToastNotification.success(this.$t('address_is_updated'));
                    } else {
                        ToastNotification.success(this.$t('address_is_created'));
                    }
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
        onLocationChange(location) {
            this.location = location;
        },
        onCreateAddress() {
            this.address = {};
            this.location = {};
        },
        onEditAddress(address: ICustomerAddress) {
            this.address = address;
            this.location = {
                latitude: address.latitude,
                longitude: address.longitude
            }
        }
    },
    mounted() {
        this.setTitle(this.$t('addresses'));
        this.getAddresses();
    }
});

</script>

<style scoped>

</style>
