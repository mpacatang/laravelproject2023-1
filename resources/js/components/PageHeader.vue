<template>
    <div class="d-flex mb-3 align-items-center">
        <div class="flex-grow-1 d-flex align-items-center">
            <!--            <VItem align-items="center" border circular class="p-1 me-1-5 cursor-pointer back-button" display="flex"-->
            <!--                   justify-content="center" soft text-color="primary" @click="goBack">-->
            <!--                <Icon icon="arrow_back" size="sm"></Icon>-->
            <!--            </VItem>-->
            <div :style="{backgroundColor: getBackgroundColor}" class="d-inline pre-header"></div>

            <h4 class="flex-grow-1 page-title fw-medium text-capitalize m-0 p-0"> {{ title }} <span
                v-if="subTitle" class="font-15 text-muted">{{ subTitle }}</span></h4>
        </div>
        <div class="d-none d-md-block">
            <template v-if="getItems.length>0">
                <ol class="breadcrumb m-0">
                    <li
                        v-for="(item, index) in getItems"
                        :key="index"
                        :class="{ active: item.active }"
                        class="breadcrumb-item"
                    >
                        <template v-if="item.active">{{ item.text }}</template>
                        <router-link v-else :to="{name: item.route}">{{ item.text }}</router-link>
                    </li>
                </ol>
            </template>
            <slot v-else></slot>
        </div>
    </div>
    <!-- end page title -->
</template>


<script lang="ts">

import {defineComponent, PropType} from "vue";

import {IBreadcrumb} from "@js/types/models/models";
import Icon from "@js/components/Icon.vue";
import VItem from "@js/components/VItem.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";

export default defineComponent({
    components: {VItem, Icon},
    mixins: [UtilMixin],
    props: {
        title: {
            type: String,
            default: '',
        },
        subTitle: {
            type: String,
            default: '',
        },
        items: {
            type: Array as PropType<IBreadcrumb[]>,
            default: () => {
                return [] as IBreadcrumb[]
            },
        },
    },
    data() {
        return {

        }
    },
    methods: {
        goBack() {
            this.$router.go(-1);
        }
    },
    computed: {
        getBackgroundColor() {
            let colors = [
                "#3a7cff4d",
                "#7F54C94d",
                "#ff679b4d",
                "#fd7e144d",
                "#02a8b54d",
                "#fb31394d",
                "#7e50fa4d",
            ];

            function randomIntFromInterval(min, max) { // min and max included
                return Math.floor(Math.random() * (max - min + 1) + min)
            }

            return colors[randomIntFromInterval(0, colors.length - 1)];
        },
        getItems(): IBreadcrumb[] {
            if (this.items.length === 0)
                return [];
            return [
                ...[{
                    text: this.appName,

                }],
                ...this.items
            ]
        }
    }
});
</script>
<style scoped>
.back-button {
    margin-left: -50px;
    margin-right: 15px !important;
}


.pre-header {
    margin-bottom: 0;
    display: inline-block !important;
    width: 6px;
    margin-right: 12px;
    border-radius: 1px;
    /*color: #AFE9DA;*/
    height: 20px;
    /*background: linear-gradient(180deg, rgba(243,98,137,0.35) 0%, rgba(70,70,250,0.3) 100%);*/
    /*background: rgba(243, 98, 137, 0.35);*/
    /*background: rgba(70, 70, 250, 0.3);*/
}
</style>
