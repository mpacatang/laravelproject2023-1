<template>
    <PageLoading :loading="page_loading" class="mt-3">

        <Row justify-content="center">
            <Col lg="8">
                <div class="text-end">
                    <Button class="d-print-none" color="primary" flexed-icon variant="soft"
                            @click="print">
                        <Icon icon="print" size="sm" type="msr"/>
                        <span class="ms-1">{{ $t('Print') }}</span>
                    </Button>
                </div>
            </Col>
            <Col id="printable_area" class="mt-2" lg="8">
                <Card class="print-shadow-none">

                    <CardBody>

                        <div class="d-flex flex-wrap mt-5 justify-content-between">
                            <div class="">
                                <h3 class="card-title">{{ $t('invoice') }} <b class="ms-1">
                                    #{{order.id }} </b>
                                </h3>
                            </div>
                            <div class="">
                                <div class="">
                                    <p class="font-13 mb-1"><strong>Order Date: </strong> &nbsp;&nbsp;&nbsp;
                                        {{ getFormattedDateTime(order.created_at) }}</p>
                                    <p class="font-13 mb-0"><strong>Order Status: </strong>&nbsp;&nbsp;&nbsp;
                                        <span
                                            class="badge bg-success">Paid</span></p>

                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap mt-4 justify-content-between gap-2">
                            <div class="">
                                <span class="font-13 text-muted">{{ $t('shop_information') }}</span>
                                <address>
                                    {{ order.shop.name }}<br>
                                    <abbr title="Mobile Number">P: </abbr> {{ order.shop.mobile_number }}<br>
                                    <abbr title="Address">Address: </abbr> {{ order.shop.address }}
                                </address>
                            </div>
                            <div class="">
                                <span class="font-13 text-muted">{{ $t('customer_information') }}</span>
                                <address>
                                    {{ order.customer.first_name }} {{ order.customer.last_name }}<br>
                                    <abbr title="Mobile Number">P:</abbr> {{ order.customer.mobile_number }}
                                </address>
                            </div>
                            <div class="">
                                <span class="font-13 text-muted">{{ $t('shipping_address') }}</span>

                                <address v-if="order.address">
                                    Address: {{ order.address.address }}<br/>
                                    {{ order.address.landmark }}<br v-if="order.address.landmark"/>
                                    {{ order.address.city }} - {{ order.address.pincode }}
                                </address>
                                <address v-else>{{ $t('on_shop') }}</address>
                            </div>
                        </div>




                        <Row>
                            <Col lg="12">
                                <div class="table-responsive">
                                    <table class="table mt-4">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ $t('item') }}</th>
                                            <th>{{ $t('quantity') }}</th>
                                            <th>{{ $t('item_cost') }}</th>
                                            <th class="text-end">{{ $t('total') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <template v-for="(item, index) in order.items">
                                            <tr>
                                                <td>{{ parseInt(index) + 1 }}</td>
                                                <td>
                                                    <b>{{ item.product.name }}</b> <br>
                                                    {{ item.product.unit_title }} -
                                                    {{ item.product_option.option_value }}
                                                </td>
                                                <td>{{ item.quantity }}</td>
                                                <td>
                                                    {{ getFormattedCurrency(item.product_option.calculated_price) }}
                                                </td>
                                                <td class="text-end">
                                                    {{
                                                        getFormattedCurrency(item.product_option.calculated_price * item.quantity)
                                                    }}
                                                </td>
                                            </tr>

                                            <tr v-for="addon in item.addons" v-if="item.addons">
                                                <td><i> - {{ $t('addon') }}</i></td>
                                                <td>{{ addon.addon.name }}
                                                </td>
                                                <td>{{ addon.quantity }}</td>
                                                <td>
                                                    {{ getFormattedCurrency(addon.price) }}
                                                </td>
                                                <td class="text-end">
                                                    {{ getFormattedCurrency(addon.price * addon.quantity) }}
                                                </td>
                                            </tr>

                                        </template>


                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive-->
                            </Col>
                        </Row>

                        <div class="d-flex">
                            <div class="w-50">
                                <div class="clearfix pt-3">
                                    <h6 class="text-muted">Notes:</h6>
                                    <small>
                                        {{ order.notes }}
                                    </small>
                                </div>
                            </div> <!-- end col -->
                            <div class="w-50">
                                <div class="float-end mt-3 mt-sm-0">
                                    <p><b>{{ $t('sub_total') }}:</b> <span
                                        class="float-end">{{
                                            getFormattedCurrency(order.order_amount)
                                        }}</span></p>
                                    <p v-if="order.delivery_charge"><b>{{ $t('delivery_charge') }}:</b>
                                        <span
                                            class="float-end">&nbsp;&nbsp; {{
                                                getFormattedCurrency(order.delivery_charge)
                                            }}</span>
                                    </p>

                                    <p v-if="order.coupon_discount"><b>{{ $t('coupon_discount') }}:</b>
                                        <span
                                            class="float-end">&nbsp; &nbsp;  - {{
                                                getFormattedCurrency(order.coupon_discount)
                                            }}</span>
                                    </p>
                                    <p><b>{{ $t('extra_charges') }}:</b> <span
                                        class="float-end">&nbsp;&nbsp; {{
                                            getFormattedCurrency(get_total_charges)
                                        }}</span>
                                    </p>
                                    <hr class="dashed"/>

                                    <p class="fw-semibold font-18">{{ $t('total') }}:
                                        <span class="font-20 float-end">
                                            {{ getFormattedCurrency(order.total) }}
                                             </span>
                                    </p>

                                </div>
                                <div class="clearfix"></div>
                            </div> <!-- end col -->
                        </div>

                    </CardBody>

                </Card>
            </Col>
        </Row>
    </PageLoading>

</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/customer/layouts/Layout.vue";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import Order, {IOrder} from "@js/types/models/order";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IData} from "@js/types/models/data";
import Icon from "@js/components/Icon.vue";
import PageLoading from "@js/components/PageLoading.vue";
import Row from "@js/components/Row.vue";
import CardBody from "@js/components/CardBody.vue";
import Col from "@js/components/Col.vue";
import Button from "@js/components/buttons/Button.vue";
import Card from "@js/components/Card.vue";


export default defineComponent({
    components: {Card, Button, Col, CardBody, Row, PageLoading, Icon, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            id: this.$route.params.id,
            order: null as IOrder,

        }
    },
    computed: {
        breadcrumb_items() {
            return [
                {
                    text: "Orders",
                    active: true,
                },
            ];
        },
        get_total_charges() {
            return Order.getTotalCharges(this.order);
        },

    },
    methods: {
        goBack() {
            this.$router.go(-1);
        },
        async getOrders() {
            try {
                const otp = this.$route.query?.i_otp;
                let response;
                if (otp) {
                    response = await Request.get<IData<IOrder>>(customerAPI.orders.invoice(this.id) + "?i_otp=" + otp);
                } else {
                    response = await Request.getAuth<IData<IOrder>>(customerAPI.orders.show(this.id));
                }
                this.order = response.data.data;
                this.page_loading = false;

            } catch (error) {
                handleException(error);
            }
        },

        getPaymentFromText(order) {
            return Order.getPaymentTextFromOrder(order);
        },
        getTypeFromText(payment) {
            return Order.getTypeText(payment);
        },

        print() {

            try {

                let html = '<HTML lang="en">\n<HEAD>\n<title></title>';
                if (document.getElementsByTagName != null) {
                    const headTags = document.getElementsByTagName("head");
                    if (headTags.length > 0) html += headTags[0].innerHTML;
                    const footerTags = document.getElementsByTagName("footer-script");
                    if (footerTags.length > 0) html += footerTags[0].innerHTML;

                }

                html += "\n</HEAD>\n<BODY>\n";
                const printReadyElem = document.getElementById('printable_area');

                if (printReadyElem != null) html += printReadyElem.innerHTML;
                else {
                    alert("Error, no contents.");
                    return;
                }

                html += "\n</BODY>\n</HTML>";
                const printWin = window.open("", "print");
                printWin.document.open();
                printWin.document.addEventListener("visibilitychange", () => {
                    printWin.close();
                });
                printWin.document.write(html);
                printWin.document.close();
                printWin.document.addEventListener('DOMContentLoaded', () => {
                    printWin.print();
                })

                printWin.onafterprint = (function () {
                    printWin.close();
                })

            } catch (e) {
                console.log("Here");
            }
        }


    },
    async mounted() {
        this.setTitle(this.$t('order_invoice'));

        await this.getOrders();
    }
});

</script>
