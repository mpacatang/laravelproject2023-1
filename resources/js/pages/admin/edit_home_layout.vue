<template>
    <Layout>
        <PageHeader
            :sub-title="'&nbsp;['+ active_layout_count+' '+$t('active_layout') +']'"
            :title="$t('home_layout')" class="mb-2">
            <div>
                <Button color="bluish-purple" size="md" variant="soft" @click="()=>update(true)">
                    <Icon class="me-1-5" icon="visibility" size="20"></Icon>
                    {{ $t('update') }} & {{ $t('preview') }}
                </Button>
                <UpdateButton class="ms-2" color="primary" radius="md" @click="()=>update(false)"/>
            </div>
        </PageHeader>

        <Row>

            <Col xl="3" lg="5">
                <div v-if="home_layouts.length!==0" id="task-list-one" class="task-list-items">
                    <draggable v-model="home_layouts" class="list-group">
                        <template v-slot:item="{item}">
                            <div
                                :class="[ {'border-bluish-purple': item===selected_layout}]"
                                class="card cursor-pointer border shadow-none" @click="()=>selectLayout(item)">
                                <CardHeader :icon="getIconFromType(item)" :title="getTextFromType(item.type)">
                                    <div class="float-end">
                                        <FormSwitch v-model="item.active" :checked="item.active" name="active"
                                                    no-spacing/>
                                    </div>
                                </CardHeader>
                                <CardBody :class="[{'opacity-65': !item.active}]">
                                    <div v-if="item.items">

                                        <div v-if="isFeaturedProduct(item)" class="d-flex flex-wrap gap-2">
                                            <p>- You can set any product as New, Featured or an Advertisement</p>
                                            <!--                                            <div v-for="product in item.items"-->
                                            <!--                                                 class="d-inline-block border rounded p-1">-->
                                            <!--                                                <NetworkImage-->
                                            <!--                                                    :src="product.images[0].image"-->
                                            <!--                                                    class="img-fluid"-->
                                            <!--                                                    height="40"/>-->
                                            <!--                                            </div>-->
                                        </div>
                                        <div v-if="isLatestProduct(item)" class="d-flex flex-wrap gap-2">
                                            <p>- It is set by date of creation, so that customer can view new
                                                products</p>
                                        </div>
                                        <div v-if="isPopularProduct(item)" class="d-flex flex-wrap gap-2">
                                            <p>- It is set by selling counts, so customer can see what's trending on
                                                market</p>
                                        </div>
                                        <div v-else-if="isShop(item)" class="d-flex flex-wrap gap-2">
                                            <p>- You can set any shop as New, Featured or Advertisement</p>

                                        </div>

                                        <div v-else-if="isBanner(item)" class="d-flex flex-wrap gap-2">
                                            <p>- Banner is useful for advertising coupons, events and more</p>

                                        </div>


                                    </div>

                                    <div v-else-if="isRecommendedProduct(item)" class="d-flex flex-wrap gap-2">
                                        <p>- Recommended products will show based on customer's interest. </p>

                                    </div>

                                    <div v-else-if="isOther(item) && item.images!==null">
                                        <div v-for="image in item.images"
                                             class="d-inline-block border me-2 rounded p-1">
                                            <NetworkImage
                                                :src="image"
                                                class="img-fluid"
                                                height="40"/>
                                        </div>
                                    </div>

                                    <p class="mb-0">

                                        <span class="align-middle">{{ item.assign }}</span>
                                    </p>
                                </CardBody>


                            </div>


                        </template>
                    </draggable>
                </div>
            </Col>

            <Col xl="9" lg="7">
                <div v-if="selected_layout">
                    <Card>
                        <CardHeader :icon="getIconFromType(selected_layout)"
                                    :title="getTextFromType(selected_layout.type)"/>
                        <CardBody class="pb-0">
                            <div v-if="isShop(selected_layout)">
                                <h5 class="mt-0 mb-3 fw-semibold text-secondary">
                                    {{ $t('selected_shops') }}
                                </h5>
                                <Row v-if="selected_layout.items.length>0">
                                    <Col v-for="shop in selected_layout.items" xl="3" lg="4" xs="6" :key="shop.id">
                                        <div class="border rounded pt-3 px-3 pb-1 text-center position-relative  mb-3">
                                            <NetworkImage
                                                :placeholder-src="shopLandscapePlaceholder"
                                                :src="shop.cover_image"
                                                class="img-fluid"
                                                height="80"
                                                rounded
                                                show-full-image/>

                                            <h5 class="mt-2 mb-2 text-secondary">
                                                {{ shop.name }}
                                            </h5>
                                            <span
                                                class="notch-action top-right cursor-pointer"
                                                @click="handleAddOrRemoveShop(shop)">
                                                   <Icon icon="remove" size="xs"/>
                                        </span>
                                        </div>
                                    </Col>
                                </Row>
                                <div v-else><span
                                    class="text-danger">{{
                                        $t('this_layout_is_disabled_please_add_shops_for_enabled')
                                    }}</span>
                                </div>
                                <template v-if="selected_layout.items.length!==shops.length">
                                    <h5 class="mt-0 fw-medium mb-3 text-secondary">
                                        {{ $t('shops') }}
                                    </h5>
                                    <Row>
                                        <Col v-for="shop in shops" :class="[{'d-none': !canAddShop(shop)}]" lg="3">
                                            <div class=" border rounded pt-3 px-3 pb-1
                                  text-center position-relative mb-3">
                                                <NetworkImage
                                                    :src="shop.logo"
                                                    class="img-fluid"
                                                    height="84"/>

                                                <h5 class="mt-2 mb-2 text-secondary">
                                                    {{ shop.name }}
                                                </h5>
                                                <span
                                                    v-if="canAddShop(shop)"
                                                    class="notch-action top-right cursor-pointer"
                                                    style="cursor: copy"
                                                    @click="handleAddOrRemoveShop(shop)">
                                          <Icon icon="add" size="xs"/>

                                        </span>
                                            </div>
                                        </Col>
                                    </Row>
                                </template>
                            </div>
                            <div v-else-if="isFeaturedProduct(selected_layout)">
                                <h5 class="mt-0 mb-3 fw-semibold text-secondary">
                                    {{ $t('selected_products') }}
                                </h5>
                                <Row v-if="selected_layout.items.length>0">
                                    <Col v-for="product in selected_layout.items" xl="3" lg="4" xs="6" >

                                        <div
                                            class="border rounded pt-3 px-3 pb-1 text-center position-relative mb-3">
                                            <NetworkImage
                                                :placeholder-src="productPortraitPlaceholder"
                                                :src="product.images[0].image"
                                                class="img-fluid"
                                                height="64"
                                                rounded
                                                show-full-image/>
                                            <router-link
                                                :to="{name: 'customer.products.show', params:{id:product.id}}"
                                                target="_blank">
                                                <h5 class="mt-2 mb-2 text-secondary max-lines">
                                                    {{ product.name }}
                                                </h5>
                                            </router-link>

                                            <span
                                                class="notch-action top-right cursor-pointer"
                                                style="padding: 3px; box-shadow: 0 3px 8px 0 rgb(0 0 0 / 10%)"
                                                @click="handleAddOrRemoveProduct(product)">
                                                     <Icon icon="remove" size="xs"/>

                                            </span>
                                        </div>
                                    </Col>
                                </Row>
                                <div v-else><span
                                    class="text-danger">{{
                                        $t('this_layout_is_disabled_please_add_products_for_enabled')
                                    }}</span>
                                </div>
                                <template v-if="selected_layout.items.length!==products.length">
                                    <h5 class="mb-3 mt-0 fw-medium text-secondary">
                                        {{ $t('products') }}
                                    </h5>
                                    <Row>
                                        <Col v-for="product in products" :class="[{'d-none': !canAddProduct(product)}]"
                                             lg="3">

                                            <div
                                                class="border rounded pt-3 px-3 pb-1 text-center position-relative mb-3">
                                                <NetworkImage
                                                    :src="product.images[0].image"
                                                    class="img-fluid"
                                                    :placeholder-src="productPortraitPlaceholder"
                                                    height="64"/>
                                                <router-link
                                                    :to="{name: 'customer.products.show', params:{id:product.id}}"
                                                    target="_blank">
                                                    <h5 class="mt-2 mb-2 text-secondary max-lines">
                                                        {{ product.name }}
                                                    </h5>
                                                </router-link>
                                                <span class="notch-action top-right cursor-pointer"
                                                      style="cursor: copy" @click="handleAddOrRemoveProduct(product)">
                                                         <Icon icon="add" size="xs"/>
                                            </span>

                                            </div>

                                        </Col>
                                    </Row>
                                </template>

                            </div>
                            <div v-else-if="isLatestProduct(selected_layout) || isPopularProduct(selected_layout)">
                                <Row v-if="selected_layout.items.length>0">
                                    <Col v-for="product in selected_layout.items" xl="3" lg="4" xs="6" >
                                        <div
                                            class="border rounded pt-3 px-3 pb-1 text-center position-relative mb-3">
                                            <NetworkImage
                                                :placeholder-src="productPortraitPlaceholder"
                                                :src="product.images[0].image"
                                                class="img-fluid"
                                                height="64"
                                                rounded/>
                                            <router-link
                                                :to="{name: 'customer.products.show', params:{id:product.id}}"
                                                target="_blank">
                                                <h5 class="mt-2 mb-2 text-secondary max-lines">
                                                    {{ product.name }}
                                                </h5>
                                            </router-link>
                                        </div>
                                    </Col>
                                </Row>
                            </div>
                            <div v-else-if="isBanner(selected_layout)">
                                <h5 class="mt-0 mb-3 fw-semibold text-secondary">
                                    {{ $t('selected_banners') }}
                                </h5>
                                <Row v-if="selected_layout.items.length>0">
                                    <Col v-for="banner in selected_layout.items" xl="3" lg="4" xs="6" >
                                        <div
                                            class="border rounded pt-3 px-3 pb-1 text-center position-relative mb-3">
                                            <NetworkImage
                                                :placeholder-src="banner.shop_id!=null?shopLandscapePlaceholder:productLandscapePlaceholder"
                                                :src="banner.image"
                                                class="img-fluid"
                                                height="64"
                                                rounded/>
                                            <template v-if="banner.product">
                                                <router-link
                                                    :to="{name: 'customer.products.show', params:{id:banner.product.id}}"
                                                    target="_blank">
                                                    <h5 class="mt-2 mb-2 max-lines">
                                                        {{ banner.product?.name }}
                                                    </h5>
                                                </router-link>
                                            </template>
                                            <template v-else-if="banner.shop">
                                                <router-link
                                                    :to="{name: 'customer.shops.show', params:{id:banner.shop.id}}"
                                                    target="_blank">
                                                    <h5 class="mt-2 mb-2 max-lines">
                                                        {{ banner.shop?.name }}
                                                    </h5>
                                                </router-link>
                                            </template>
                                            <span
                                                class="notch-action top-right cursor-pointer"
                                                style="padding: 3px; box-shadow: 0 3px 8px 0 rgb(0 0 0 / 10%)"
                                                @click="handleAddOrRemoveBanner(banner)">
                                                     <Icon icon="remove" size="xs"/>

                                            </span>
                                        </div>
                                    </Col>
                                </Row>
                                <div v-else><span
                                    class="text-danger">{{
                                        $t('this_layout_is_disabled_please_add_products_for_enabled')
                                    }}</span>
                                </div>
                                <template v-if="selected_layout.items.length!==banners.length">

                                    <h5 class="mb-3 mt-0  fw-medium text-secondary">
                                        {{ $t('Banners') }}
                                    </h5>
                                    <Row>
                                        <div v-for="banner in banners" :class="[{'d-none': !canAddBanner(banner)}]"
                                             class="col-lg-3">
                                            <div
                                                class="border rounded pt-3 px-3 pb-1 text-center position-relative mb-3">
                                                <NetworkImage
                                                    :src="banner.image"
                                                    class="img-fluid"
                                                    height="64"/>
                                                <template v-if="banner.product">
                                                    <router-link
                                                        :to="{name: 'customer.products.show', params:{id:banner.product.id}}"
                                                        target="_blank">
                                                        <h5 class="mt-2 mb-2 max-lines">
                                                            {{ banner.product?.name }}
                                                        </h5>
                                                    </router-link>
                                                </template>
                                                <template v-else-if="banner.shop">
                                                    <router-link
                                                        :to="{name: 'customer.shops.show', params:{id:banner.shop.id}}"
                                                        target="_blank">
                                                        <h5 class="mt-2 mb-2 max-lines">
                                                            {{ banner.shop?.name }}
                                                        </h5>
                                                    </router-link>
                                                </template>
                                                <span class="notch-action top-right cursor-pointer"
                                                      style="cursor: copy" @click="handleAddOrRemoveBanner(banner)">
                                                         <Icon icon="add" size="xs"/>
                                            </span>
                                            </div>

                                        </div>
                                    </Row>
                                </template>

                                <div class="text-end mb-3">
                                    <router-link :to="{name:'admin.home_banners.create'}">
                                        <CreateButton>{{ $t('create_banner') }}</CreateButton>
                                    </router-link>
                                </div>


                            </div>
                            <div v-else-if="isRecommendedProduct(selected_layout)">
                                <p>It will use customer's interest to show products</p>
                            </div>
                            <div v-else-if="isOther(selected_layout)">
                                <FileUpload id="" :default-files="otherImagesHelper.defaultFiles"
                                            :options="{url: '/',}" container-height="126"
                                            v-on:file-added="otherImagesHelper.onFileUpload"
                                            v-on:file-removed="file=>removeImage(file)"/>
                            </div>
                        </CardBody>
                    </Card>
                </div>
            </Col>
        </Row>
    </Layout>
</template>


<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Draggable from "vue3-draggable";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {HomeLayout, IHomeLayout} from "@js/types/models/home_layout";
import {IProduct} from "@js/types/models/product";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IShop} from "@js/types/models/shop";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import {ILocalFile} from "@js/types/models/models";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import {IHomeBanner} from "@js/types/models/home_banners";
import {array_contains_unique, array_remove_unique, array_toggle_unique} from "@js/shared/array_utils";
import CreateButton from "@js/components/buttons/CreateButton.vue";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import {RequestCache} from "@js/services/api/cache";

export default defineComponent({
    components: {
        UpdateButton,
        CreateButton,
        ...Components, Draggable, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {

        return {
            loading: false,
            home_layouts: [] as IHomeLayout[],
            products: [] as IProduct[],
            shops: [] as IShop[],
            banners: [] as IHomeBanner[],
            otherImagesHelper: new FileUploadHelper({max: 4}),
            selected_layout: null as IHomeLayout,
        }
    },

    methods: {
        getIconFromType: HomeLayout.getIcon,
        isRecommendedProduct: HomeLayout.isRecommendedProduct,
        getTextFromType(type) {
            return HomeLayout.getTextFromType(type);
        },
        isProduct(layout: IHomeLayout) {
            return HomeLayout.isProduct(layout.type);
        },
        isShop(layout: IHomeLayout) {
            return HomeLayout.isShop(layout.type);
        },
        isOther(layout: IHomeLayout) {
            return HomeLayout.isOther(layout.type);
        },
        isBanner(layout: IHomeLayout) {
            return HomeLayout.isBanner(layout.type);
        },

        isFeaturedProduct(layout: IHomeLayout) {
            return HomeLayout.isFeaturedProduct(layout.type);
        },

        isLatestProduct(layout: IHomeLayout) {
            return HomeLayout.isLatestProduct(layout.type);
        },
        isPopularProduct(layout: IHomeLayout) {
            return HomeLayout.isPopularProduct(layout.type);
        },
        selectLayout(layout) {
            this.selected_layout = layout;
        },
        canAddProduct(product: IProduct): boolean {
            let selected_products = this.selected_layout.items;
            for (let i = 0; i < selected_products.length; i++) {
                if (product.id === selected_products[i].id) {
                    return false;
                }
            }
            return true;
        },
        canAddBanner(banner: IHomeBanner): boolean {
            let selected_banners = this.selected_layout.items;
            return !array_contains_unique(selected_banners, banner);
        },
        canAddShop(shop: IShop): boolean {
            let selected_shops = this.selected_layout.items;
            for (let i = 0; i < selected_shops.length; i++) {
                if (shop.id === selected_shops[i].id) {
                    return false;
                }
            }
            return true;
        },
        handleAddOrRemoveProduct(product: IProduct) {
            if (this.canAddProduct(product)) {
                this.selected_layout.items.push(product);
            } else {
                this.selected_layout.items = array_remove_unique(this.selected_layout.items, product);
                // this.selected_layout.items.push(product);
            }
        },
        handleAddOrRemoveBanner(banner: IHomeBanner) {
            this.selected_layout.items = array_toggle_unique(this.selected_layout.items, banner);

        },
        handleAddOrRemoveShop(shop: IShop) {
            if (this.canAddShop(shop)) {
                this.selected_layout.items.push(shop);
            } else {
                this.selected_layout.items = array_remove_unique(this.selected_layout.items, shop);
                // this.selected_layout.items.push(product);
            }
        },
        async removeImage(file: ILocalFile) {
            if (file.uploaded) {
                try {
                    const response = await Request.patchAuth(adminAPI.home_layouts.remove_image, {'image': file.dataURL});
                    if (response.success()) {
                        ToastNotification.success(this.$t('image_removed'));
                        this.otherImagesHelper.onFileRemoved(file);
                    }
                } catch (error) {
                    ToastNotification.success(this.$t('image_cant_remove'));
                    handleException(error);
                }
            } else {
                this.otherImagesHelper.onFileRemoved(file);
            }

        },
        async update(preview = false) {
            this.loading = true;
            try {
                let list = HomeLayout.toJSON({layouts: this.home_layouts, images: this.otherImagesHelper.getFiles()});
                const response = await Request.patchAuth(adminAPI.home_layouts.update, {'layouts': list});
                if (response.success()) {
                    this.otherImagesHelper.shiftAllImageToUploaded();
                    RequestCache.removeSimilar(adminAPI.home_layouts.get);
                    ToastNotification.success(this.$t('home_layout_is_updated'));
                    if (preview) {
                        let route = this.$router.resolve({name: 'customer.home'})
                        window.open(route.href, '_blank');
                    }
                }
            } catch (error) {
                handleException(error);
            }
            this.loading = false;

        }
    },
    computed: {
        breadcrumb_items() {
            return [
                {
                    text: this.$t('home_layout'),
                    active: true,
                },
            ];
        },
        active_layout_count() {
            let count = 0;
            for (const homeLayout of this.home_layouts) {
                if (homeLayout.active) {
                    count++;
                }
            }
            return count;
        }
    },
    async mounted() {
        this.setTitle(this.$t('edit_home_layout'));
        try {
            const response = await Request.getAuth<IData<IHomeLayout>>(adminAPI.home_layouts.get, );
            if (response.success()) {
                this.home_layouts = response.data.data;
                this.selected_layout = this.home_layouts[0];
                const self = this;
                this.home_layouts.forEach(function (home_layout: IHomeLayout) {
                    if (HomeLayout.isOther(home_layout.type) && home_layout.images != null) {
                        home_layout.images.forEach(function (image) {
                            self.otherImagesHelper.addDefaultFile({url: image});
                        });
                    }
                });
            } else {
                ToastNotification.unknownError();
            }
            const productsResponse = await Request.getAuth<IData<IProduct[]>>(adminAPI.products.get);
            if (productsResponse.success()) {
                this.products = productsResponse.data.data;
            }
            const shopResponse = await Request.getAuth<IData<IShop[]>>(adminAPI.shops.get);
            if (shopResponse.success()) {
                this.shops = shopResponse.data.data;
            }
            const bannerResponse = await Request.getAuth<IData<IHomeBanner[]>>(adminAPI.home_banners.get);
            if (bannerResponse.success()) {
                this.banners = bannerResponse.data.data;
            }
        } catch (error) {
            handleException(error);
        }
    }
});

</script>

<style scoped>

</style>
