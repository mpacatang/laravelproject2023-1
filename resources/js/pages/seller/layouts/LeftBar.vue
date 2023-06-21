<template>

    <div id="leftside-menu" class="leftside-menu">
        <router-link :to="{name: 'seller.dashboard'}">
            <Logo/>
        </router-link>
        <button :title="layout.leftbar?.mode==='sm-hover'?$t('full'):$t('minimize')" aria-label="Show Full Sidebar"
                class="btn button-sm-hover p-0" type="button">
            <!--            {{layout.leftbar}}-->
            <Icon v-if="layout.leftbar?.mode==='sm-hover'" icon="switch_left" type="msr" @click="toggleLeftbarSmHover"/>
            <Icon v-else icon="switch_right" type="msr" @click="toggleLeftbarSmHover"/>
        </button>

        <SimpleBar id="leftside-menu-container" class="h-100">

            <ul class="side-nav py-1-5">
                <template v-for="item in getMenuItems" :key="`item-${item.title}-option`">
                    <template v-if="item.isHeader">
                        <hr class="dashed">
                        <li :key="`item-${item.title}-option`" class="side-nav-title">
                            {{ item.title }}
                        </li>
                    </template>
                    <template v-else>
                        <li class="side-nav-item">
                            <template v-if="hasItems(item)">
                                <a :id="'leftmenu_'+item.id" :href="`#collapse_`+item.id" class="side-nav-link" data-bs-toggle="collapse" :class="item.classList">
                                    <Icon class="icon" :icon="item.icon" type="msr" size="20"></Icon>
                                    <span class="ms-2">{{ item.title }}</span>
                                    <span class="menu-arrow"></span>
                                    <span v-if="item.pinnable" class="menu-pin" @click.prevent="togglePin(item)"></span>
                                </a>
                                <div :id="'collapse_'+item.id" class="collapse">
                                    <ul class="side-nav-second-level">
                                        <template v-for="subitem in item.children" :key="subitem.title">
                                            <li class="side-nav-item">
                                                <router-link :id="'leftmenu_'+subitem.id" :to="{name: subitem.route}" class="side-nav-link">
                                                    <span> {{ subitem.title }} </span>
                                                </router-link>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                            </template>
                            <template v-else>
                                <router-link :id="'leftmenu_'+item.id" :to="item.route!=null?{name: item.route}:item.href" class="side-nav-link" :class="item.classList">
                                    <Icon class="icon" :icon="item.icon" type="mso" size="20"></Icon>
                                    <span class="ms-2"> {{ item.title }} </span>
                                    <span v-if="item.pinnable" class="menu-pin" @click.prevent="togglePin(item)"></span>

                                </router-link>
                            </template>
                        </li>
                    </template>


                </template>
            </ul>

        </SimpleBar>
        <div class="leftbar-hide-enable" @click="toggleLeftbarHide">
            <Icon icon="chevron_right" size="md"></Icon>
        </div>
    </div>

</template>


<script lang="ts">

import Icon from "../../../components/Icon.vue";
import {adminPermissions, isAdminPermission, isSellerPermission, sellerPermissions} from "@js/services/permissions";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {Collapse} from "bootstrap";
import {SimpleBar} from 'simplebar-vue3';
import {IMenuItem} from "@js/types/models/models";
import {ILayout, useLayoutStore, useSellerDataStore,} from "@js/services/state/states";
import {
    setLeftbarShow,
    toggleLeftbarSmHover,
    toggleSellerPinnedNavigationToState,
} from "@js/services/state/state_helper";
import {array_remove} from "@js/shared/array_utils";
import Logo from "@js/components/Logo.vue";
import {PropType} from "vue";

export default {
    components: {Logo, Icon, SimpleBar},
    mixins: [UtilMixin],
    props: {
        layout: {
            type: Object as PropType<ILayout>,
            required: true,
        },
    },
    data() {
        return {
            menuRef: null,
            pinned: [],

        }
    },
    computed: {
        menuItems() {
            let items: IMenuItem[] = [
                {
                    title: "manage",
                    isHeader: true
                },
                {
                    id: 'pos',
                    title: this.$t('POS'),
                    icon: 'local_convenience_store',
                    pinnable: true,
                    route: 'seller.pos.create',
                    permission: sellerPermissions.pos
                },
                {
                    id: 'orders',
                    title: this.$t('orders'),
                    icon: 'receipt_long',
                    pinnable: true,
                    route: 'seller.orders.index',
                    permission: sellerPermissions.orders
                },
                {
                    id: 'products',
                    title: this.$t('products'),
                    icon: 'inventory_2',
                    pinnable: true,
                    route: 'seller.products.index',
                    permission: sellerPermissions.products

                },
                {
                    id: 'addons',
                    title: this.$t('addons'),
                    icon: 'extension',
                    pinnable: true,
                    route: 'seller.addons.index',
                    permission: sellerPermissions.addons
                },
                {
                    title: "setup",
                    isHeader: true
                },

                {
                    title: this.$t('shop'),
                    icon: 'storefront',
                    id: 'shop_menu',
                    pinnable: true,
                    permission: sellerPermissions.shops,
                    children: [
                        {
                            id: 'shop_general',
                            title: this.$t('general'),
                            route: 'seller.shops.edit',
                        },
                        {
                            id: 'shop_settings',
                            title: this.$t('settings'),
                            route: 'seller.shops.settings',
                        }, {
                            id: 'shop_plans',
                            title: this.$t('plan'),
                            route: 'seller.shops.plan',
                        },
                    ]
                },

                {
                    id: 'shop_reviews',
                    pinnable: true,
                    title: this.$t('reviews'),
                    icon: 'thumbs_up_down',
                    route: 'seller.shops.reviews',
                    permission: sellerPermissions.reviews

                },
                {
                    id: 'shop_revenues',
                    title: this.$t('revenues'),
                    icon: 'account_balance_wallet',
                    pinnable: true,

                    route: 'seller.shop_revenues.index',
                    permission: sellerPermissions.revenues

                },
                {
                    id: 'shop_payouts',
                    title: this.$t('payouts'),
                    icon: 'payments',
                    pinnable: true,
                    route: 'seller.shop_payouts.index',
                    permission: sellerPermissions.revenues

                },


                {
                    title: "delivery",
                    isHeader: true
                },
                {
                    id: 'delivery_boys',
                    title: this.$t('delivery_boy'),
                    icon: 'local_shipping',
                    pinnable: true,
                    route: 'seller.delivery_boys.index',
                    permission: sellerPermissions.delivery_boys
                },
                {
                    id: 'delivery_boy_other_menu',
                    title: this.$t('other'),
                    icon: 'electric_moped',
                    pinnable: true,
                    permission: sellerPermissions.delivery_boys,

                    children: [
                        {
                            id: 'delivery_boy_reviews',
                            title: this.$t('reviews'),
                            route: 'seller.delivery_boy_reviews.index',
                        },
                        {
                            id: 'delivery_boy_revenues',
                            title: this.$t('revenues'),
                            route: 'seller.delivery_boy_revenues.index',
                        },
                        {
                            id: 'delivery_boy_payouts',
                            title: this.$t('payouts'),
                            route: 'seller.delivery_boy_payouts.index',
                        },


                    ]
                },
                {
                    title: this.$t('system'),
                    isHeader: true
                },
                {
                    title: this.$t('categories'),
                    icon: 'category',
                    pinnable: true,
                    id: 'categories_menu',
                    permission: sellerPermissions.categories,

                    children: [
                        {
                            id: 'categories',
                            title: this.$t('categories'),

                            route: 'seller.categories.index',
                        },
                        {
                            id: 'sub_categories',
                            title: this.$t('sub_categories'),

                            route: 'seller.sub_categories.index',
                        },
                    ]
                },
                {
                    id: 'coupons',
                    title: this.$t('coupons'),
                    icon: 'confirmation_number',
                    route: 'seller.coupons.index',
                    pinnable: true,
                    permission: sellerPermissions.coupons

                },
                {
                    id: 'report',
                    title: this.$t('report'),
                    icon: 'request_page',
                    pinnable: true,
                    route: 'seller.reports.report',
                    permission: sellerPermissions.report
                },
                {
                    title: this.$t('manage_employee'),
                    isHeader: true
                },
                {
                    id: 'roles',
                    title: this.$t('roles'),
                    icon: 'assignment_ind',
                    pinnable: true,
                    route: 'seller.roles.index',
                    permission: sellerPermissions.roles

                },
                {
                    id: 'employees',
                    title: this.$t('employees'),
                    icon: 'manage_accounts',
                    pinnable: true,
                    route: 'seller.employees.index',
                    permission: sellerPermissions.employees

                },
            ];

            let list: IMenuItem[] = [];

            for (const item of items) {
                if (item.permission == null || isSellerPermission(item.permission)) {
                    list.push(item);
                }
            }

            return list;
        },
        getMenuItems(): IMenuItem[] {
            let items: IMenuItem[] = [];

            if (isSellerPermission(sellerPermissions.dashboard)) {
                items.push({
                    id: 'dashboard',
                    title: this.$t('dashboard'),
                    icon: 'dashboard',
                    route: 'seller.dashboard',
                })
            }


            const pinnedItems: IMenuItem[] = [];

            for (const pin of this.pinned) {
                const item = this.getPinnedItem(pin);
                if (item != null)
                    pinnedItems.push({
                        ...item,
                        classList: 'pinned-tab'
                    });
            }

            if (pinnedItems.length > 0) {
                items.push({
                    title: this.$t('pinned'),
                    isHeader: true
                })
                items.push(...pinnedItems)
            }

            const otherItems: IMenuItem[] = [];

            for (const menuItem of this.menuItems) {
                if (!this.isPinnedItem(menuItem))
                    otherItems.push(menuItem);
            }
            // return items;
            items.push(...otherItems);


            let removeItems: IMenuItem[] = [];
            for (let i = 0; i < items.length; i++) {
                if (items[i].isHeader) {
                    if (i + 1 < items.length) {
                        if (items[i + 1].isHeader) {
                            removeItems.push(items[i]);
                        }
                    } else {
                        removeItems.push(items[i]);
                    }

                }
            }
            for (let removeItem of removeItems) {
                items = array_remove(items, removeItem)
            }


            return items;
        }
    },

    created(){
        const navigationStore = useSellerDataStore();
        this.pinned = navigationStore.pinned_navigations;
    },
    mounted: function () {
        this.activateCollapse();
        const navigationStore = useSellerDataStore();
        // this.pinned = navigationStore.pinned;
        const self = this;
        navigationStore.$subscribe((mut, state) => {
            self.pinned = state.pinned_navigations;
            self.activateMenuItems();
        });
        this.activateMenuItems();
        // window.test =  this.activateMenuItems;
    },
    methods: {
        toggleLeftbarSmHover() {
            toggleLeftbarSmHover();
        },
        togglePin(item) {
            toggleSellerPinnedNavigationToState(item.id)
        },
        toggleLeftbarHide() {
            const navigationStore = useLayoutStore();
            let full_layout = navigationStore.getFullLayoutOption();
            setLeftbarShow(!full_layout.show_leftbar)
        },
        isPinnedItem(menuItem: IMenuItem) {
            if (!menuItem.pinnable)
                return false;
            for (const pin of this.pinned) {
                if (menuItem.id == pin)
                    return true;
            }
            return false;
        },
        getPinnedItem(id: string) {
            for (const menuItem of this.menuItems) {
                if (menuItem.pinnable && menuItem.id == id)
                    return menuItem;
            }
            return null;
        },

        hasItems(item): boolean {
            return item && item.children !== undefined
                ? item.children.length > 0
                : false
        },
        activateCollapse() {
            const allCollapse = document.querySelectorAll('#leftside-menu .collapse');

            for (const collapseElement of allCollapse) {
                collapseElement.addEventListener('show.bs.collapse', function () {
                    for (const otherCollapse of allCollapse) {
                        if (otherCollapse != collapseElement) {
                            const collapse = Collapse.getOrCreateInstance(otherCollapse, {toggle: false});
                            collapse.hide();
                        }
                    }
                });
            }
        },

        activateMenuItems() {

            let route: string|null = this.$route.name;
            if (route != null) {
                let route2: string = route.replace('create', 'index');
                route2=route2.replace('edit', 'index');
                route2=route2.replace('on_map', 'index');
                for (const menuItem of this.getMenuItems) {
                    if (menuItem.children != null) {
                        for (const item of menuItem.children) {
                            if (item.route === route || item.route === route2) {

                                const element = document.getElementById('leftmenu_' + item.id);
                                if (element != null) {
                                    element.classList.add('active');
                                    element.parentElement.classList.add('menuitem-active');
                                }
                                const parentElement = document.getElementById('leftmenu_' + menuItem.id);

                                if (parentElement) {
                                    if(parentElement.parentElement.querySelector('.collapse')) {
                                        let parentCollapse = Collapse.getOrCreateInstance(parentElement.parentElement.querySelector('.collapse'), {
                                            toggle: false
                                        });
                                        parentCollapse.show();
                                    }
                                    parentElement.classList.add('active');

                                    parentElement.parentElement.classList.add('menuitem-active');
                                }
                            }
                        }
                    }
                    else {
                        if (menuItem.route === route  || menuItem.route === route2) {
                            const element = document.getElementById('leftmenu_' + menuItem.id);
                            if (element != null) {
                                element.classList.add('active');
                                element.parentElement.classList.add('menuitem-active');
                            }
                        }
                    }
                }
            }

            setTimeout(function () {
                const activatedItem = document.querySelector<HTMLElement>('li.menuitem-active .active');
                if (activatedItem != null)
                    scrollToElement(activatedItem);
            }, 200);

            function scrollToElement(activatedItem: HTMLElement) {
                const simplebarContent = document.querySelector('.leftside-menu .simplebar-content-wrapper');
                const offset = activatedItem.offsetTop - 300;
                if (simplebarContent && offset > 100) {
                    scrollTo(simplebarContent, offset, 600);
                }
            }

            // scrollTo (Left Side Bar Active Menu)
            function easeInOutQuad(t, b, c, d) {
                t /= d / 2;
                if (t < 1) return c / 2 * t * t + b;
                t--;
                return -c / 2 * (t * (t - 2) - 1) + b;
            }

            function scrollTo(element, to, duration) {
                let start = element.scrollTop, change = to - start, currentTime = 0, increment = 20;
                const animateScroll = function () {
                    currentTime += increment;
                    element.scrollTop = easeInOutQuad(currentTime, start, change, duration);
                    if (currentTime < duration) {
                        setTimeout(animateScroll, increment);
                    }
                };
                animateScroll();
            }


        },
    },
}

</script>
<style scoped>


</style>
