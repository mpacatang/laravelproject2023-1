<template>
    <Layout>

        <div id="cardPaymentBrick_container"></div>

    </Layout>


</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from 'vue';
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import StarRating from "@js/components/shared/StarRating.vue";


export default defineComponent({
    components: {StarRating, Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {}
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
                            theme: 'dark' // 'default' |'dark' | 'bootstrap' | 'flat'
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
                        // handle form ready
                    },
                    onError: (error) => {
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
        this.addExternalJS("https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js", function (){
            self.addExternalJS("https://sdk.mercadopago.com/js/v2", function (){
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
