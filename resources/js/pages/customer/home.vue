<template>
    <Layout>
        <template #topbar_extra>
            <div class="d-flex d-md-none border-bottom overflow-scroll transparent-scrollbar px-2">
                <div v-for="(category, index) in getCategories" :class="[{'': index<getCategories.length-1}]"
                     class="single-category" @click="goToFilterCategory(category)">
                    <p class="m-0 fw-medium text-nowrap">{{ category.name }}</p>
                </div>
                <p class="text-purple single-category m-0 fw-medium text-nowrap" @click="goToSearch">
                    {{ $t('view_all') }}</p>
            </div>
        </template>
        <div id="customer-home">
            <PageLoading :loading="page_loading">

                <div class="d-flex">
                    <div class="me-3 d-none d-md-block" style="min-width: 250px;">
                        <div class="bg-soft-secondary p-2 rounded text-center text-dark text-capitalize">
                            <h5 class="m-0 fw-medium">{{ $t('categories') }}</h5>
                        </div>
                        <div class="border mt-1 rounded">
                            <div v-for="(category, index) in getCategories"
                                 :class="[{'border-bottom': index<getCategories.length-1}]"
                                 class="single-category "
                                 @click="goToFilterCategory(category)">
                                <p class="m-0 fw-medium">{{ category.name }}</p>
                            </div>
                        </div>
                        <div class="bg-soft-purple p-1-5 mt-1 rounded text-center cursor-pointer"
                             @click="goToSearch">
                            <h5 class="m-0 fw-medium">{{ $t('show_all') }}</h5>
                        </div>

                    </div>
                    <swiper
                        :freeMode="false"
                        :loop="true"
                        :modules="modules"
                        :navigation="true"
                        :slideToClickedSlide="true"
                        :slidesPerView="getBannerPerView"
                        :spaceBetween="24"
                        :watchSlidesProgress="true"
                        class="mySwiper"
                    >
                        <swiper-slide v-for="banner in getBannerLayout.items" :key="banner.id">
                            <router-link
                                :to="banner.product_id!=null?{name: 'customer.products.show', params:{id:banner.product_id}}:
                        {name: 'customer.shops.show', params:{id:banner.shop_id}}">
                                <NetworkImage
                                    :placeholder-src="banner.product_id!=null?productLandscapePlaceholder:shopLandscapePlaceholder"
                                    :src="banner.image"
                                    class="img-fluid"
                                    height="356"
                                    object-fit="cover"
                                    rounded
                                    width="100%"/>
                            </router-link>
                        </swiper-slide>
                    </swiper>
                </div>


                <div class="mt-4">
                    <template v-for="layout in getLayouts" :key="layout.id">

                        <div class="mb-2">

                            <template v-if="isShopLayout(layout)">
                                <p class="d-block d-lg-none font-18 fw-medium ls-1">
                                    {{ getTitleFromLayout(layout) }}</p>

                                <div class="d-flex">
                                    <div class="me-3  d-none d-lg-block">
                                        <Card class="card-shop shadow-none"
                                              style="height: 264px; width: 250px;">
                                            <CardBody class="ps-3" style="z-index: 1;">
                                                <p class="font-24 fw-semibold  text-center mb-0 ">{{
                                                        $t('trending')
                                                    }}</p>
                                                <p class="font-24 fw-semibold   text-center">{{ $t('shops') }}</p>
                                            </CardBody>
                                        </Card>
                                    </div>
                                    <swiper
                                        :freeMode="false"
                                        :loop="true"
                                        :modules="modules"
                                        :navigation="true"
                                        :slideToClickedSlide="true"
                                        :slidesPerView="getShopsPerView"
                                        :spaceBetween="24"
                                        :watchSlidesProgress="true"
                                        class="mySwiper"
                                    >
                                        <swiper-slide v-for="shop in layout.items" :key="shop.id">
                                            <router-link :to="{name:'customer.shops.show', params: {id: shop.id}}"
                                                         class="nav-link">
                                                <Card class="overflow-hidden shadow-none">
                                                    <NetworkImage
                                                        :placeholder-src="shopLandscapePlaceholder"
                                                        :src="shop.cover_image"
                                                        height="200"
                                                        object-fit="cover"
                                                        width="100%"
                                                    />
                                                    <CardBody class="p-2 px-2-5">

                                                        <div class="d-flex align-items-center">
                                                            <NetworkImage
                                                                :placeholder-src="shopPortraitPlaceholder"
                                                                :src="shop.logo"
                                                                circular
                                                                object-fit="cover"
                                                                size="40"
                                                            />
                                                            <div class="ms-2-5">
                                                                <h5 class="fw-medium mt-0 mb-0-5">
                                                                    {{ shop.name }}
                                                                </h5>
                                                                <div>
                                                                    <StarRating :rating="shop.rating" :size="12"
                                                                                star-spacing="1"/>
                                                                    <span class="ms-1 font-13 fw-medium">
                                                                        ({{
                                                                            getPrecise(shop.rating)
                                                                        }}-{{ shop.ratings_count }})
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </CardBody>
                                                </Card>
                                            </router-link>
                                        </swiper-slide>

                                    </swiper>
                                </div>

                            </template>

                            <div v-if="isProductLayout(layout)" class="w-100">
                                <p class="d-block d-lg-none font-18 fw-medium">{{ getTitleFromLayout(layout) }}</p>

                                <div class="d-flex">

                                    <div class="me-3 d-none d-lg-block ">
                                        <template v-if="layout.type==='recommended_products'">
                                            <Card class="card-recommended shadow-none"
                                                  style="height: 210px; width: 250px;">
                                                <CardBody class="ps-3" style="z-index: 1;">
                                                    <p class="font-22 fw-semibold  mb-0">
                                                        {{ $t('recommended') }}
                                                    </p>
                                                    <p class="font-22 fw-semibold ">{{ $t('for_you') }}</p>


                                                </CardBody>
                                            </Card>
                                        </template>
                                        <template v-else-if="layout.type==='popular_products'">
                                            <Card class="card-trending border shadow-none"
                                                  style="height: 210px; width: 250px;">
                                                <CardBody class="ps-3 text-center" style="z-index: 1;">
                                                    <p class="font-24 fw-medium  text-center mb-0 ">
                                                        {{ $t('trending') }}
                                                    </p>
                                                    <p class="font-24 fw-medium   text-center">
                                                        {{ $t('products') }}
                                                    </p>

                                                    <Button class="fw-medium mt-3" color="dark" @click="goToSearch">
                                                        {{ $t('shop_Now') }}
                                                    </Button>
                                                </CardBody>
                                            </Card>
                                        </template>
                                        <template v-else-if="layout.type==='latest_products'">
                                            <Card class="card-trending border shadow-none"
                                                  style="height: 210px; width: 250px;">
                                                <CardBody class="ps-3 text-center" style="z-index: 1;">
                                                    <p class="font-24 fw-medium  text-center mb-0 ">
                                                        {{ $t('latest') }}
                                                    </p>
                                                    <p class="font-24 fw-medium   text-center">
                                                        {{ $t('products') }}
                                                    </p>

                                                    <Button class="fw-medium mt-3" color="dark" @click="goToSearch">
                                                        {{ $t('shop_Now') }}
                                                    </Button>
                                                </CardBody>
                                            </Card>
                                        </template>
                                        <template v-else-if="layout.type==='featured_products'">
                                            <Card class="card-trending border shadow-none"
                                                  style="height: 210px; width: 250px;">
                                                <CardBody class="ps-3 text-center" style="z-index: 1;">
                                                    <p class="font-24 fw-medium  text-center mb-0 ">
                                                        {{ $t('featured') }}
                                                    </p>
                                                    <p class="font-24 fw-medium   text-center">
                                                        {{ $t('products') }}
                                                    </p>

                                                    <Button class="fw-medium mt-3" color="dark" @click="goToSearch">
                                                        {{ $t('shop_Now') }}
                                                    </Button>
                                                </CardBody>
                                            </Card>
                                        </template>
                                    </div>

                                    <swiper
                                        :freeMode="false"
                                        :loop="true"
                                        :modules="modules"
                                        :navigation="true"
                                        :slideToClickedSlide="true"
                                        :slidesPerView="getProductsPerView"
                                        :spaceBetween="24"
                                        :watchSlidesProgress="true"
                                        class="mySwiper"
                                    >
                                        <swiper-slide v-for="product in layout.items" :key="product.id">

                                            <Card class="">
                                                <router-link
                                                    :to="{name:'customer.products.show', params: {id: product.id}}"
                                                    class="nav-link">
                                                    <div class="text-center p-2-5">
                                                        <NetworkImage
                                                            :placeholder-src="productLandscapePlaceholder"
                                                            :src="getProductImage(product)"
                                                            class="img-fluid"
                                                            height="116"
                                                            object-fit="contain" rounded/>
                                                    </div>
                                                    <hr class="m-0 p-0 hr-muted">
                                                    <div class="p-1-5 px-2-5">
                                                        <p
                                                            class="max-lines mb-0 fw-medium mt-0 font-13 font-md-15">
                                                            {{ product.name }}
                                                        </p>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-0">
                                                            <div>
                                                                <i class="fas fa-star text-star-5 font-13"/>
                                                                <span class="font-12 fw-medium">
                                                                    ({{ getRatingText(product) }})</span>
                                                            </div>
                                                            <span class="font-14 fw-medium">
                                                        {{ getFormattedCurrency(getMinPrice(product)) }}
                                                    </span>

                                                        </div>
                                                    </div>
                                                </router-link>
                                            </Card>
                                        </swiper-slide>
                                    </swiper>
                                </div>

                            </div>
                        </div>

                    </template>
                </div>


            </PageLoading>


            <div class="module-selector">
                <div id="accordionPanelsStayOpenExample" class="accordion bg-transparent">
                    <div class="accordion-item border-0">
                        <div id="module-selector-accordion"
                             class="accordion-collapse collapse">
                            <div class="accordion-body p-0 pt-1">
                                <template v-for="module in remain_module">
                                    <div class="d-flex justify-content-end align-items-center gap-3 cursor-pointer"
                                         @click="onSelectModule(module)">
                                        <span class="font-17 text-white text-uppercase fw-medium">{{
                                                module.title
                                            }}</span>
                                        <VItem class="mb-1  p-2-5  rounded-circle bg-card shadow">
                                            <img :alt="module.title" :src="module.image" width="36">
                                        </VItem>
                                    </div>
                                </template>

                            </div>
                        </div>

                        <h2 class="accordion-header p-0">
                            <div
                                class="accordion-button p-0 shadow-none arrow-none d-flex
                        justify-content-end align-items-center gap-3 cursor-pointer"
                                data-bs-target="#module-selector-accordion" data-bs-toggle="collapse">
                            <span class="font-17 text-white text-uppercase fw-medium">
                                {{ selected_module.title }}
                            </span>
                                <div class="mb-1 p-2-5  rounded-circle bg-card shadow-lg border border-primary">
                                    <img :alt="selected_module.title" :src="selected_module.image" width="36">
                                </div>
                                <!--                                <img :alt="selected_module.title" :src="selected_module.image" width="34">-->
                            </div>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </Layout>

    <Layout v-if="false" id="home_2">
        <template #topbar_extra>
            <div class="d-flex d-md-none border-bottom overflow-scroll transparent-scrollbar px-2">
                <div v-for="(category, index) in getCategories" :class="[{'': index<getCategories.length-1}]"
                     class="single-category" @click="goToFilterCategory(category)">
                    <p class="m-0 fw-medium ">{{ category.name }}</p>
                </div>
                <p class="text-purple single-category m-0 fw-medium text-nowrap" @click="goToSearch">
                    {{ $t('view_all') }}</p>
            </div>
        </template>
        <div id="customer-home">
            <PageLoading :loading="page_loading">

                <div class="d-flex">
                    <div class="me-3 d-none d-md-block" style="min-width: 250px;">
                        <div class="bg-soft-secondary p-2 rounded text-center text-dark text-capitalize">
                            <h5 class="m-0 fw-medium">{{ $t('categories') }}</h5>
                        </div>
                        <div class="border mt-1 rounded">
                            <div v-for="(category, index) in getCategories"
                                 :class="[{'border-bottom': index<getCategories.length-1}]"
                                 class="single-category"
                                 @click="goToFilterCategory(category)">
                                <p class="m-0 fw-medium">{{ category.name }}</p>
                            </div>
                        </div>
                        <div class="bg-soft-purple p-1-5 mt-1 rounded text-center cursor-pointer"
                             @click="goToSearch">
                            <h5 class="m-0 fw-medium">{{ $t('show_all') }}</h5>
                        </div>

                    </div>
                    <swiper
                        :freeMode="false"
                        :loop="true"
                        :modules="modules"
                        :navigation="true"
                        :slideToClickedSlide="true"
                        :slidesPerView="getBannerPerView"
                        :spaceBetween="24"
                        :watchSlidesProgress="true"
                        class="mySwiper"
                    >
                        <swiper-slide v-for="banner in getBannerLayout.items" :key="banner.id">
                            <router-link
                                :to="banner.product_id!=null?{name: 'customer.products.show', params:{id:banner.product_id}}:
                            {name: 'customer.shops.show', params:{id:banner.shop_id}}">
                                <NetworkImage
                                    :placeholder-src="banner.product_id!=null?productLandscapePlaceholder:shopLandscapePlaceholder"
                                    :src="banner.image"
                                    class="img-fluid"
                                    height="320"
                                    object-fit="cover"
                                    rounded
                                    width="100%"/>
                            </router-link>
                        </swiper-slide>
                    </swiper>
                </div>


                <div class="mt-4">
                    <template v-for="layout in getLayouts" :key="layout.id">

                        <div class="mb-2">

                            <template v-if="isShopLayout(layout)">
                                <p class="d-block d-lg-none font-18 fw-medium ls-1">
                                    {{ getTitleFromLayout(layout) }}</p>

                                <div class="d-flex">
                                    <div class="me-3  d-none d-lg-block">
                                        <Card class="card-shop shadow-none"
                                              style="height: 264px; width: 250px;">
                                            <CardBody class="ps-3" style="z-index: 1;">
                                                <p class="font-24 fw-semibold  text-center mb-0 ">{{
                                                        $t('trending')
                                                    }}</p>
                                                <p class="font-24 fw-semibold   text-center">{{ $t('shops') }}</p>
                                            </CardBody>
                                        </Card>
                                    </div>
                                    <swiper
                                        :freeMode="false"
                                        :loop="true"
                                        :modules="modules"
                                        :navigation="true"
                                        :slideToClickedSlide="true"
                                        :slidesPerView="getShopsPerView"
                                        :spaceBetween="24"
                                        :watchSlidesProgress="true"
                                        class="mySwiper"
                                    >
                                        <swiper-slide v-for="shop in layout.items" :key="shop.id">
                                            <router-link :to="{name:'customer.shops.show', params: {id: shop.id}}"
                                                         class="nav-link">
                                                <Card class="overflow-hidden shadow-none">
                                                    <NetworkImage
                                                        :placeholder-src="shopLandscapePlaceholder"
                                                        :src="shop.cover_image"
                                                        height="200"
                                                        object-fit="cover"
                                                        width="100%"
                                                    />
                                                    <CardBody class="p-2 px-2-5">

                                                        <div class="d-flex align-items-center">
                                                            <NetworkImage
                                                                :placeholder-src="shopPortraitPlaceholder"
                                                                :src="shop.logo"
                                                                circular
                                                                object-fit="cover"
                                                                size="40"
                                                            />
                                                            <div class="ms-2-5">
                                                                <h5 class="fw-medium mt-0 mb-0-5">{{
                                                                        shop.name
                                                                    }}</h5>
                                                                <div>
                                                                    <StarRating :rating="shop.rating" :size="12"
                                                                                star-spacing="1"/>
                                                                    <span class="ms-1 font-13"> (
                                                                {{ getPrecise(shop.rating) }}
                                                                -
                                                                {{ shop.ratings_count }})
                                                            </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </CardBody>
                                                </Card>
                                            </router-link>
                                        </swiper-slide>

                                    </swiper>
                                </div>

                            </template>

                            <div v-if="isProductLayout(layout)" class="w-100">
                                <p class="d-block d-lg-none font-18 fw-medium">{{ getTitleFromLayout(layout) }}</p>

                                <div class="d-flex">

                                    <div class="me-3 d-none d-lg-block ">
                                        <template v-if="layout.type==='recommended_products'">
                                            <Card class="card-recommended shadow-none"
                                                  style="height: 210px; width: 250px;">
                                                <CardBody class="ps-3" style="z-index: 1;">
                                                    <p class="font-22 fw-semibold  mb-0">
                                                        {{ $t('recommended') }}
                                                    </p>
                                                    <p class="font-22 fw-semibold ">{{ $t('for_you') }}</p>


                                                </CardBody>
                                            </Card>
                                        </template>
                                        <template v-else-if="layout.type==='popular_products'">
                                            <Card class="card-trending border shadow-none"
                                                  style="height: 210px; width: 250px;">
                                                <CardBody class="ps-3 text-center" style="z-index: 1;">
                                                    <p class="font-24 fw-medium  text-center mb-0 ">
                                                        {{ $t('trending') }}
                                                    </p>
                                                    <p class="font-24 fw-medium   text-center">{{
                                                            $t('products')
                                                        }}</p>

                                                    <Button class="fw-medium mt-3" color="dark">
                                                        {{ $t('shop_Now') }}
                                                    </Button>
                                                </CardBody>
                                            </Card>
                                        </template>
                                        <template v-else-if="layout.type==='latest_products'">
                                            <Card class="card-trending border shadow-none"
                                                  style="height: 210px; width: 250px;">
                                                <CardBody class="ps-3 text-center" style="z-index: 1;">
                                                    <p class="font-24 fw-medium  text-center mb-0 ">
                                                        {{ $t('latest') }}
                                                    </p>
                                                    <p class="font-24 fw-medium   text-center">{{
                                                            $t('products')
                                                        }}</p>

                                                    <Button class="fw-medium mt-3" color="dark">{{
                                                            $t('shop_Now')
                                                        }}
                                                    </Button>
                                                </CardBody>
                                            </Card>
                                        </template>
                                        <template v-else-if="layout.type==='featured_products'">
                                            <Card class="card-trending border shadow-none"
                                                  style="height: 210px; width: 250px;">
                                                <CardBody class="ps-3 text-center" style="z-index: 1;">
                                                    <p class="font-24 fw-medium  text-center mb-0 ">
                                                        {{ $t('featured') }}
                                                    </p>
                                                    <p class="font-24 fw-medium   text-center">{{
                                                            $t('products')
                                                        }}</p>

                                                    <Button class="fw-medium mt-3" color="dark">{{
                                                            $t('shop_Now')
                                                        }}
                                                    </Button>
                                                </CardBody>
                                            </Card>
                                        </template>
                                    </div>

                                    <swiper
                                        :freeMode="false"
                                        :loop="true"
                                        :modules="modules"
                                        :navigation="true"
                                        :slideToClickedSlide="true"
                                        :slidesPerView="getProductsPerView"
                                        :spaceBetween="24"
                                        :watchSlidesProgress="true"
                                        class="mySwiper"
                                    >
                                        <swiper-slide v-for="product in layout.items" :key="product.id">

                                            <Card class="">
                                                <router-link
                                                    :to="{name:'customer.products.show', params: {id: product.id}}"
                                                    class="nav-link">
                                                    <div class="">
                                                        <div class="text-center p-2-5">
                                                            <NetworkImage
                                                                :placeholder-src="productLandscapePlaceholder"
                                                                :src="getProductImage(product)"
                                                                class="img-fluid"
                                                                height="116"
                                                                object-fit="contain" rounded/>
                                                        </div>
                                                        <hr class="m-0 p-0 hr-muted">
                                                        <div class="p-1-5 px-2-5">
                                                            <p
                                                                class="max-lines mb-0 fw-medium mt-0 font-13 font-md-15">
                                                                {{ product.name }}
                                                            </p>
                                                            <div
                                                                class="d-flex align-items-center justify-content-between mt-0">
                                                                <div>
                                                                    <i class="fas fa-star text-star-5 font-13"/>
                                                                    <span class="font-12">
                                                                        ({{ getRatingText(product) }})</span>
                                                                </div>
                                                                <span class="font-14 fw-medium">
                                                            {{ getFormattedCurrency(getMinPrice(product)) }}
                                                        </span>

                                                            </div>
                                                        </div>

                                                    </div>
                                                </router-link>
                                            </Card>
                                        </swiper-slide>
                                    </swiper>
                                </div>

                            </div>
                        </div>

                    </template>
                </div>


            </PageLoading>


            <div class="module-selector">
                <div id="accordionPanelsStayOpenExample" class="accordion bg-transparent">
                    <div class="accordion-item border-0">
                        <div id="module-selector-accordion"
                             class="accordion-collapse collapse">
                            <div class="accordion-body p-0 pt-1">
                                <template v-for="module in remain_module">
                                    <div class="d-flex justify-content-end align-items-center gap-3 cursor-pointer"
                                         @click="onSelectModule(module)">
                                            <span class="font-17 text-white text-uppercase fw-medium">{{
                                                    module.title
                                                }}</span>
                                        <VItem class="mb-1  p-2-5  rounded-circle bg-card shadow">
                                            <img :alt="module.title" :src="module.image" width="36">
                                        </VItem>
                                    </div>
                                </template>

                            </div>
                        </div>

                        <h2 class="accordion-header p-0">
                            <div
                                class="accordion-button p-0 shadow-none arrow-none d-flex
                            justify-content-end align-items-center gap-3 cursor-pointer"
                                data-bs-target="#module-selector-accordion" data-bs-toggle="collapse">
                                <span class="font-17 text-white text-uppercase fw-medium">
                                    {{ selected_module.title }}
                                </span>
                                <div class="mb-1 p-2-5  rounded-circle bg-card shadow-lg border border-primary">
                                    <img :alt="selected_module.title" :src="selected_module.image" width="36">
                                </div>
                                <!--                                <img :alt="selected_module.title" :src="selected_module.image" width="34">-->
                            </div>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </Layout>

</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from 'vue';
import Request from "@js/services/api/request";
import {IData} from "@js/types/models/data";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {HomeLayout, IHomeLayout} from "@js/types/models/home_layout";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {ICategory} from "@js/types/models/category";
import Product, {IProduct} from "@js/types/models/product";
import {IShop} from "@js/types/models/shop";
import StarRating from "@js/components/shared/StarRating.vue";
import {Swiper, SwiperSlide} from 'swiper/vue';
import 'swiper/css';
import "swiper/css/free-mode"
import "swiper/css/thumbs"
import {Controller, FreeMode, Navigation, Thumbs, Zoom} from 'swiper';
import {IModule, Module} from "@js/types/models/module";
import {Collapse} from "bootstrap";
import {useCustomerDataStore} from "@js/services/state/states";

export default defineComponent({
    components: {StarRating, Layout, Swiper, SwiperSlide, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            modules: [Zoom, FreeMode, Navigation, Controller, Thumbs],
            layouts: [] as IHomeLayout[],
            categories: [] as ICategory[],
            isCategoryPinned: false,
            selected_module: {} as IModule,
            collapse_accordion: {} as Collapse
        }
    },
    methods: {
        async getData() {
            this.page_loading = true;
            const category_ids = useCustomerDataStore().getPreference().category_ids.join(",");
            const url = Request.addParameters(customerAPI.home_layouts.get, {
                addModule: true,
                parameters: {
                    "category_ids": category_ids
                }
            });
            try {
                const categoryResponse = await Request.getAuth<IData<ICategory[]>>(Request.addParameters(customerAPI.categories.get, {addModule: true}));
                this.categories = categoryResponse.data.data;
                const response = await Request.getAuth<IData<IHomeLayout[]>>(url);
                this.layouts = response.data.data;

            } catch (error) {
                handleException(error);
            } finally {
                this.page_loading = false;
            }
        },
        getTitleFromLayout(layout: IHomeLayout) {
            return this.$t(layout.type);
        },
        isShopLayout(layout: IHomeLayout) {
            return HomeLayout.isShop(layout.type);
        },
        isProductLayout(layout: IHomeLayout) {
            return HomeLayout.isProduct(layout.type);
        },

        getMinPrice(product: IProduct) {
            return Product.getMinPrice(product);
        },
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        getFloorRating(item: IProduct | IShop) {
            return Math.floor(this.getRating(item));
        },
        getRatingText(item: any): string {
            return this.getRating(item).toFixed(1);
        },
        getRating(item: any): number {
            return item.rating;

        },
        goToFilterCategory(category: ICategory) {
            this.$router.push({name: 'customer.search', query: {categories: category.id}});
        },
        goToSearch() {
            this.$router.push({name: 'customer.search'});
        },
        addBackdrop() {
            if (this.backdrop_element == null) {
                this.backdrop_element = document.createElement('div');
                this.backdrop_element.classList.add(...['offcanvas-backdrop', 'fade', 'show', 'opacity-80']);
                const self = this;
                this.backdrop_element.addEventListener('click', () => {
                    self.closeAccordion();
                })
            }

            document.body.appendChild(this.backdrop_element);
        },
        removeBackdrop() {
            if (this.backdrop_element != null)
                this.backdrop_element.remove();
        },
        closeAccordion() {
            this.collapse_accordion?.hide();
        },
        onSelectModule(module: IModule) {
            if (this.selected_module.id != module.id) {
                this.selected_module = module;
                useCustomerDataStore().updateModuleId(module.id);
            }
            this.getData();
            this.closeAccordion();
        }

    },
    computed: {
        getCategories() {
            return this.categories.slice(0, 7);
        },
        getBanners() {
            const layout = this.getBannerLayout;
            if (layout != null) {
                return layout.items;
            }
        },
        getBannerPerView() {
            const width = window.screen.width;
            return width > 992 ? 2 : 1;
        },
        getShopsPerView() {
            const width = window.screen.width;
            return width > 1441 ? 3.5 : width > 768 ? 2.2 : width > 576 ? 2 : 1;
        },
        getProductsPerView() {
            const width = window.screen.width;
            return width > 1440 ? 5 : width > 768 ? 3 : width > 576 ? 2 : 2;
        },
        remain_module() {
            const self = this
            return Module.getCachedModules().filter((module) => {
                return self.selected_module.id != module.id;
            })
        },
        getBannerLayout(): IHomeLayout | null {
            for (const layout of this.layouts) {
                if (layout.type === 'home_banner')
                    return layout;
            }
            return null;
        },
        getLayouts(): IHomeLayout[] {
            return (this.layouts ?? []).filter(function (layout) {
                if (layout.type !== 'other') {
                    return layout;
                }
            });
        }
    },
    mounted() {
        this.setTitle(this.$t('home'))

        if(useCustomerDataStore().getPreference().first_time){
            this.$router.replace({name: 'customer.preference'});
        }

        this.getData();
        const self = this;



        document.defaultView.onscroll = () => {
            self.isCategoryPinned = window.scrollY > 350;
        }

        let store = useCustomerDataStore();
        this.selected_module = Module.getModuleFromId(store.module_id);

        this.collapse_accordion = new Collapse('#module-selector-accordion', {
            toggle: false
        });
        const collapseElement = document.getElementById('module-selector-accordion')

        if (this.collapse_accordion != null && collapseElement != null) {
            collapseElement.addEventListener('show.bs.collapse',() => {
                self.addBackdrop();
            });
            collapseElement.addEventListener('hide.bs.collapse', () => {
                self.removeBackdrop();
            });
        }
    }
});

</script>

<style scoped>
.category-container{
    padding: 1rem;
    margin-top: 1px;
    position:sticky;
    top: 71px;
    align-items: center;
    z-index: 999;
    transition: all 0.1s ease-in-out;
}

/*.category-container.pinned{*/
/*    margin-left: -140px;*/
/*    margin-right: -140px;*/
/*}*/

</style>

