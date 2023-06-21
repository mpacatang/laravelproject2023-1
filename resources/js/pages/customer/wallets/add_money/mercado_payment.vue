<template>
    <div id="cardPaymentBrick_container" v-show="!page_loading"></div>
    <PageLoading :loading="page_loading">
    </PageLoading>
</template>
<script lang="ts">
import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent, PropType} from "vue";
import {Components} from "@js/components/components";
import Button from "@js/components/buttons/Button.vue";
import {IPaymentData} from "@js/types/models/payment";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import VModal from "@js/components/VModal.vue";

export default defineComponent({
    components: {VModal, Button, Layout, ...Components},
    mixins: [UtilMixin],
    props: {
        payment_data: {
            type: Object as PropType<IPaymentData>,
            required: true
        },
        amount: {
            type: Number,
            required: true
        },
        customer_id: {
            type: Number,
            required: true
        },
        token: {
            type: String,
            required: true
        },

    },
    data() {
        return {
            page_loading: true,
            loading: false,
        };
    },
    methods: {
        async initPayment() {
            const mp = new window.MercadoPago("TEST-676dc636-dcfb-47b8-9205-8098e6e4f92f");

            const bricksBuilder = mp.bricks();
            const settings = {
                initialization: {
                    amount: 100, //value of the payment to be processed
                },
                customization: {
                    visual: {
                        style: {
                            theme: 'default' // 'default' |'dark' | 'bootstrap' | 'flat'
                        }
                    }
                },
                callbacks: {
                    onSubmit: (cardFormData) => {
                        console.info(cardFormData);
                        return new Promise<void>((resolve, reject) => {
                            fetch("/process_payment", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                },
                                body: JSON.stringify(cardFormData)
                            })
                                .then((response) => {
                                    // get payment result
                                    resolve();
                                })
                                .catch((error) => {
                                    // get payment result error
                                    reject();
                                })
                        });
                    },
                    onReady: () => {
                        this.page_loading=false;
                        console.info("on Ready");
                        // handle form ready
                    },
                    onError: (error) => {
                        console.info(error);
                        // handle error
                    }
                }
            }

            var cardPaymentBrickController = await bricksBuilder.create('cardPayment', 'cardPaymentBrick_container', settings);
        }
    },
    computed: {},
    mounted() {
        const self = this;
        this.addExternalJS("https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js", function () {
            self.addExternalJS("https://sdk.mercadopago.com/js/v2", function () {
                self.initPayment();
            });
        });


    },
    created() {

    }
});
</script>
<style scoped>
</style>
