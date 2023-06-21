<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('shop_reviews')"/>
        <Card>
                <Table :data="shop_reviews" :loading="table_loading" :option="table_header">
                    <template #order_id="data">
                        <router-link :to="{name: 'admin.orders.edit', params: {id:data.value}}">#{{
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
import {ITableOption} from "@js/types/models/models";
import {IShopReview} from "@js/types/models/review";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Button from "@js/components/buttons/Button.vue";
import CardHeader from "@js/components/CardHeader.vue";
import VModal from "@js/components/VModal.vue";


export default defineComponent({
    components: {VModal, CardHeader, Button, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    data() {
        return {
            table_loading: true,
            shop_reviews: [] as IShopReview[],
        }
    },
    computed: {
        table_header(): ITableOption<IShopReview> {
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
                        label: this.$t('customer'),
                        field: 'customer',
                        sort: true,
                        data: (rev) => rev.customer.first_name + " " + rev.customer.last_name
                    },

                    {
                        label: this.$t('rating'),
                        field: 'rating',
                        sort: true
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
                onRefresh: () => this.getReviews(true),
                columnCustomizer: true,
                placeholder: {
                    message: this.$t('there_is_no_any_reviews')
                }
            };
        },
        breadcrumb_items() {
            return [
                {
                    text: this.$t('shop_reviews'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        generateData(data: (IShopReview)[]) {
            let list = [];
            const self = this;
            data?.forEach((review) => {
                list.push({
                    'ID': review.id,
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
                const response = await Request.getAuth<IData<IShopReview[]>>(sellerAPI.shop_reviews.get, {forced: forced});
                this.shop_reviews = response.data.data;
            } catch (error) {
                handleException(error);
            } finally {
                this.table_loading = false;

            }
        }
    },
    mounted() {
        this.setTitle(this.$t('shop_reviews'));
        this.getReviews();
    }
});

</script>
