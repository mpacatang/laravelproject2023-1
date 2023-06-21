<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('delivery_boy_reviews')"/>
        <Card>
            <Table :data="delivery_boy_reviews" :loading="table_loading" :option="table_header">
                <template #order_id="data">
                    <router-link :to="{name: 'seller.orders.edit', params: {id:data.value}}">#{{
                            data.value
                        }}
                    </router-link>

                </template>

            </Table>
        </Card>


    </Layout>
</template>

<script lang="ts">

import PageHeader from "@js/components/PageHeader.vue";
import Layout from "@js/pages/seller/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import Icon from "@js/components/Icon.vue";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {IData} from "@js/types/models/data";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import {IDeliveryBoyReview} from "@js/types/models/review";
import VModal from "@js/components/VModal.vue";
import CardHeader from "@js/components/CardHeader.vue";
import CardBody from "@js/components/CardBody.vue";
import LoadingButton from "@js/components/buttons/LoadingButton.vue";
import Button from "@js/components/buttons/Button.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import DeleteActionButton from "@js/components/buttons/DeleteActionButton.vue";


export default defineComponent({
    components: {
        DeleteActionButton,
        Card,
        Button,
        LoadingButton,
        CardBody,
        CardHeader,
        VModal,
        Icon,
        Table,
        PageHeader,
        Layout,
    },
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            delivery_boy_reviews: [] as IDeliveryBoyReview[],
        }
    },
    computed: {
        table_header(): ITableOption<IDeliveryBoyReview> {
            return {
                columns: [
                    {
                        label: this.$t('ID'),
                        field: 'id',
                        labelStyle: {
                            fontWeight: "medium"
                        },
                        width: 150
                    },
                    {
                        label: this.$t('delivery_boy'),
                        field: 'delivery_boy',
                        sort: true,
                        data: (rev) => rev.delivery_boy.first_name + " " + rev.delivery_boy.last_name,
                        onClick: (rev) => this.$router.push({
                            name: 'seller.delivery_boys.edit',
                            params: {id: rev.delivery_boy_id}
                        })
                    },
                    {
                        label: this.$t('customer'),
                        field: 'customer',
                        sort: true,
                        data: (rev) => rev.customer.first_name + " " + rev.customer.last_name,

                    },
                    {
                        label: this.$t('order_id'),
                        field: 'order_id',
                    },
                    {
                        label: this.$t('rating'),
                        field: 'rating',
                        sort: true
                    },

                    {
                        label: this.$t('review'),
                        field: 'review',
                        width: 260,
                        onCopy: (a) => a.review
                    },
                    {
                        label: this.$t('date_&_time'),
                        field: 'updated_at',
                        width: 260,
                        sort: true,
                        data: (rev) => this.getFormattedDateTime(rev.updated_at)
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 150,
                        printNone: true
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: () => this.getReviews(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_reviews_yet')
                }

            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('delivery_boy_reviews'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IDeliveryBoyReview[]) {
            let list = [];
            const self = this;
            data?.forEach((review) => {
                list.push({
                    'ID': review.id,
                    "Delivery": review.delivery_boy.first_name + " " + review.delivery_boy.last_name,
                    "Customer": review.customer.first_name + " " + review.customer.last_name,
                    "Order ID": review.order_id,
                    "Rating": review.rating,
                    "Review": review.review,
                    "Time": self.getFormattedDateTime(review.updated_at),
                });
            });
            return {
                data: list,
                fileName: 'Delivery Reviews'
            };
        },

        async getReviews(forced = false) {
            this.table_loading = true;
            try {
                const response = await Request.getAuth<IData<IDeliveryBoyReview[]>>(sellerAPI.delivery_boy_reviews.get, {forced: forced});
                this.delivery_boy_reviews = response.data.data;

            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;
            }
        }

    },
    mounted() {
        this.setTitle(this.$t('delivery_boy_reviews'));
        this.getReviews();
    }
});

</script>
