<template>


    <Card>
        <Table :data="shop_reviews" :loading="table_loading" :option="table_header">

            <template #actions="data">
                <DeleteActionButton @click="deleteReview(data.row)"/>
            </template>
        </Table>

    </Card>

    <VModal v-model="show_delete_alert" close-btn>
        <Card class="mb-0">
            <CardHeader :title="$t('delete_confirmation')" icon="delete_forever"></CardHeader>
            <CardBody>
                <p>{{ $t('are_you_sure_to_delete_this_review') }}</p>
                <div class="float-end">
                    <Button class="me-2" color="secondary" variant="soft" @click="hideDeleteReviewModal">{{ $t('cancel') }}</Button>
                    <Button color="danger" icon-centered @click="deleteReviewWithConfirmation">
                        <Icon class="me-1" icon="delete" type="msr"/>
                        {{ $t('delete') }}
                    </Button>
                </div>
            </CardBody>
        </Card>
    </VModal>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import Table from "@js/components/Table.vue";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IData} from "@js/types/models/data";
import {IBreadcrumb, ITableOption} from "@js/types/models/models";
import CardBody from "@js/components/CardBody.vue";
import Card from "@js/components/Card.vue";
import Icon from "@js/components/Icon.vue";
import PageHeader from "@js/components/PageHeader.vue";
import DeleteActionButton from "@js/components/buttons/DeleteActionButton.vue";
import VModal from "@js/components/VModal.vue";
import CardHeader from "@js/components/CardHeader.vue";
import Button from "@js/components/buttons/Button.vue";
import {IDeliveryBoyReview, IShopReview} from "@js/types/models/review";
import {ToastNotification} from "@js/services/toast_notification";


export default defineComponent({
    components: {DeleteActionButton, VModal, CardHeader, Button, CardBody, Card, Icon, Table, PageHeader, Layout,},
    mixins: [UtilMixin],
    props: {
        id: {
            type: Number,
            required: true
        },
    },
    data() {
        return {
            table_loading: true,
            shop_reviews: [] as IShopReview[],
            selected_review: null as IDeliveryBoyReview,
            show_delete_alert: false
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
                        label: this.$t('shop'),
                        field: 'shop',
                        sort: true,
                        data: (rev) => rev.shop.name

                    },
                    {
                        label: this.$t('customer'),
                        field: 'customer',
                        sort: true,
                        data: (rev) => rev.customer.first_name + " " + rev.customer.last_name,
                        onClick: (rev) => this.$router.push({
                            name: 'admin.customers.edit',
                            params: {id: rev.customer_id}})
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
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        width: 120,
                        printNone: true
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
                    "Shop": review.shop?.name,
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
        deleteReview(review: IDeliveryBoyReview) {
            this.selected_review = review;
            this.show_delete_alert = true;
        },
        hideDeleteReviewModal() {
            this.show_delete_alert = false;
        },
        async deleteReviewWithConfirmation() {
            if (this.selected_review == null) {
                return;
            }
            try {
                const response = await Request.deleteAuth(adminAPI.shop_reviews.delete(this.selected_review.id));
                if (response.success()) {
                    ToastNotification.success(this.$t('review_removed'));
                    await this.getReviews();
                }
            } catch (error) {
                handleException(error);
            } finally {
                this.show_delete_alert = false;
                this.selected_review = null;
            }
        },
        async getReviews() {
            this.table_loading = true;

            try {
                const response = await Request.getAuth<IData<IShopReview[]>>(adminAPI.shops.reviews(this.id));
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
