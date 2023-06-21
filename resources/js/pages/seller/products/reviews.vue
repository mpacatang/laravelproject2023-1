<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('product_reviews')"/>
        <Card>
            <Table :data="product_reviews" :loading="table_loading" :option="table_header">

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
import {IProductReview} from "@js/types/models/review";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Button from "@js/components/buttons/Button.vue";
import CardHeader from "@js/components/CardHeader.vue";
import VModal from "@js/components/VModal.vue";
import DeleteActionButton from "@js/components/buttons/DeleteActionButton.vue";


export default defineComponent({
    components: {DeleteActionButton, VModal, CardHeader, Button, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            id: this.$route.params.id,
            table_loading: true,
            product_reviews: [] as IProductReview[],

        }
    },
    computed: {
        table_header(): ITableOption<IProductReview> {
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
                        label: this.$t('product'),
                        field: 'product',
                        sort: true,
                        data: (rev) => rev.product.name,
                        onClick: (rev) => this.$router.push({name: 'seller.products.edit', params: {id: rev.product_id}})

                    },
                    {
                        label: this.$t('customer'),
                        field: 'customer',
                        sort: true,
                        data: (rev) => rev.customer.first_name + " " + rev.customer.last_name,
                        onClick: (rev) => this.$router.push({
                            name: 'seller.customers.edit',
                            params: {id: rev.customer_id}
                        })
                    },
                    {
                        label: this.$t('rating'),
                        field: 'rating',
                        sort: true,
                    },

                    {
                        label: this.$t('review'),
                        field: 'review',
                    },

                    {
                        label: this.$t('date_&_time'),
                        field: 'updated_at',
                        width: 260,
                        sort: true,
                        data: (rev) => this.getFormattedDateTime(rev.updated_at)
                    },
                ],
                exports: {
                    enableAll: true,
                    autoData: this.generateData
                },
                onRefresh: this.getReviews,
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_reviews')
                }
            };
        },
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('product_reviews'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: IProductReview[]) {
            let list = [];
            const self = this;
            data?.forEach((review) => {
                list.push({
                    'ID': review.id,
                    "Product": review.product?.name,
                    "Customer": review.customer.first_name + " " + review.customer.last_name,
                    "Rating": review.rating,
                    "Review": review.review,
                    "Time": self.getFormattedDateTime(review.updated_at),
                });
            });
            return {
                data: list,
                fileName: 'Shop Reviews'
            };
        },
        async getReviews(forced = false) {
            this.table_loading = true;

            try {
                const response = await Request.getAuth<IData<IProductReview[]>>(sellerAPI.products.reviews(this.id), {forced: forced});
                this.product_reviews = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;

            }
        },

    },
    mounted() {
        this.setTitle(this.$t('product_reviews'));
        this.getReviews();
    }
});

</script>
