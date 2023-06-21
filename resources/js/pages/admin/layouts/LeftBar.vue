<template>

    <div id="leftside-menu" class="leftside-menu">
        <router-link :to="{name: 'admin.dashboard'}">
            <Logo/>
        </router-link>
        <button :title="layout.leftbar?.mode==='sm-hover'?$t('full'):$t('minimize')" aria-label="Show Full Sidebar"
                class="btn button-sm-hover p-0" type="button">
            <Icon v-if="layout.leftbar?.mode==='sm-hover'" icon="switch_left" type="msr" @click="toggleLeftbarSmHover"/>
            <Icon v-else icon="switch_right" type="msr" @click="toggleLeftbarSmHover"/>
        </button>

        <SimpleBar id="leftside-menu-container" class="h-100" style="overflow-x: hidden">

            <ul class="side-nav pt-1-5 pb-2">
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
                                <a :id="'leftmenu_'+item.id" :class="item.classList" :href="`#collapse_`+item.id"
                                   class="side-nav-link" data-bs-toggle="collapse">
                                    <Icon :icon="item.icon" class="icon" size="20" type="msr"></Icon>
                                    <span class="ms-2">{{ item.title }}</span>
                                    <span class="menu-arrow"></span>
                                    <span v-if="item.pinnable" class="menu-pin" @click.prevent="togglePin(item)"></span>
                                </a>
                                <div :id="'collapse_'+item.id" class="collapse">
                                    <ul class="side-nav-second-level">
                                        <template v-for="subitem in item.children" :key="subitem.title">
                                            <li class="side-nav-item">
                                                <router-link :id="'leftmenu_'+subitem.id" :to="{name: subitem.route}"
                                                             class="side-nav-link">
                                                    <span> {{ subitem.title }} </span>
                                                </router-link>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                            </template>
                            <template v-else>
                                <router-link :id="'leftmenu_'+item.id"
                                             :class="item.classList" :to="item.route!=null?{name: item.route}:item.href"
                                             class="side-nav-link">
                                    <Icon :icon="item.icon" class="icon" size="20" type="mso"></Icon>
                                    <span class="ms-2"> {{ item.title }} </span>
                                    <span v-if="item.pinnable" class="menu-pin" @click.prevent="togglePin(item)"></span>

                                </router-link>
                            </template>
                        </li>
                    </template>


                </template>

                <li v-if="isDemo" class="text-center mt-2-5 d-md-block d-none buy-now">
                    <Button color="bluish-purple" @click="goToBuyShopy" flexed-icon class="fw-medium" radius="sm">
                        <Icon icon="shopping_cart_checkout" class="me-1-5" size="16"></Icon>
                        Buy Now
                    </Button>
                </li>
            </ul>

            <!--            <div>dd</div>-->
            <!--            <div class="side-nav-extra-item">-->
            <!--                <hr class="dashed mt-0"/>-->
            <!--                <p class="text-center my-2-5">-->
            <!--                    Looking for-->
            <!--                    <router-link :to="{name:'seller.dashboard'}"-->
            <!--                                 class="text-bluish-purple fw-medium">Seller Panel-->
            <!--                    </router-link>-->
            <!--                    ->-->
            <!--                </p>-->
            <!--            </div>-->


        </SimpleBar>
        <!--        <div class="side-nav-extra-item">-->
        <!--            <hr class="dashed my-0"/>-->
        <!--            <router-link :to="{}" class="side-nav-link">-->
        <!--                <Icon class="icon" icon="description" size="20" type="mso"></Icon>-->
        <!--                <span class="text"> {{ $t('documentation') }} -></span>-->
        <!--            </router-link>-->
        <!--        </div>-->

        <div class="leftbar-hide-enable" @click="toggleLeftbarHide">
            <Icon icon="chevron_right" size="md"></Icon>
        </div>
    </div>

</template>


<script lang="ts">

import Icon from "@js/components/Icon.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {Collapse} from "bootstrap";
import {SimpleBar} from 'simplebar-vue3';
import {IMenuItem} from "@js/types/models/models";
import {ILayout, useAdminDataStore, useLayoutStore} from "@js/services/state/states";
import {
    setLeftbarShow,
    toggleAdminPinnedNavigationToState, toggleLeftbarEnable,
    toggleLeftbarSmHover
} from "@js/services/state/state_helper";
import {array_remove} from "@js/shared/array_utils";
import Logo from "@js/components/Logo.vue";
import {PropType} from "vue";
import {adminPermissions, isAdminPermission} from "@js/services/permissions";
import Button from "@js/components/buttons/Button.vue";
import {BusinessSetting} from "@js/types/models/business_setting";

export default {
    components: {Button, Logo, Icon, SimpleBar},
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

        menuItems(): IMenuItem[] {
            let items: IMenuItem[] = [
                {
                    title: this.$t('system'),
                    isHeader: true
                },
                {
                    id: 'modules',
                    title: this.$t('modules'),
                    icon: 'view_module',
                    route: 'admin.modules.index',
                    pinnable: true,
                    permission: adminPermissions.modules
                },
                {
                    id: 'zones',
                    title: this.$t('zones'),
                    icon: 'mode_of_travel',
                    route: 'admin.zones.index',
                    pinnable: true,
                    permission: adminPermissions.zones
                },

                {
                    id: 'units',
                    title: this.$t('units'),
                    icon: 'interests',
                    route: 'admin.units.index',
                    pinnable: true,
                    permission: adminPermissions.units,

                },
                {
                    id: 'shop_plans',
                    title: this.$t('shop_plans'),
                    icon: 'view_carousel',
                    route: 'admin.shop_plans.index',
                    pinnable: true,
                    permission: adminPermissions.shop_plans
                },

                {
                    title: this.$t('items'),
                    isHeader: true
                },
                {
                    id: 'categories_menu',
                    title: this.$t('categories'),
                    icon: 'category',
                    pinnable: true,
                    permission: adminPermissions.categories,
                    children: [
                        {
                            id: 'categories',
                            title: this.$t('categories'),
                            icon: 'folder',
                            route: 'admin.categories.index',
                        },
                        {
                            id: 'sub_categories',
                            title: this.$t('sub_categories'),
                            icon: 'grid_view',
                            route: 'admin.sub_categories.index',
                        },
                    ]
                },
                {
                    id: 'coupons',
                    title: this.$t('coupons'),
                    icon: 'confirmation_number',
                    route: 'admin.coupons.index',
                    pinnable: true,
                    permission: adminPermissions.coupons
                },

                {
                    id: 'addons',
                    title: this.$t('addons'),
                    icon: 'extension',
                    route: 'admin.addons.index',
                    pinnable: true,
                    permission: adminPermissions.addons
                },
                {
                    id: 'products',
                    title: this.$t('products'),
                    icon: 'inventory_2',
                    route: 'admin.products.index',
                    pinnable: true,
                    permission: adminPermissions.products
                },

                {
                    id: 'orders',
                    title: this.$t('orders'),
                    icon: 'receipt_long',
                    route: 'admin.orders.index',
                    pinnable: true,
                    permission: adminPermissions.orders
                },

                {
                    title: this.$t('manage_shops'),
                    isHeader: true
                },
                {
                    id: 'sellers',
                    title: this.$t('sellers'),
                    icon: 'manage_accounts',
                    route: 'admin.sellers.index',
                    pinnable: true,
                    permission: adminPermissions.sellers
                },
                {
                    id: 'shops',
                    title: this.$t('shops'),
                    icon: 'storefront',
                    route: 'admin.shops.index',
                    pinnable: true,
                    permission: adminPermissions.shops
                },
                {
                    id: 'pos',
                    title: this.$t('POS'),
                    icon: 'local_convenience_store',
                    route: 'admin.pos.create',
                    pinnable: true,
                    permission: adminPermissions.pos
                },
                {
                    id: 'shop_other_menu',
                    title: this.$t('other'),
                    icon: 'add_business',
                    pinnable: true,
                    permission: adminPermissions.shops,
                    children: [
                        {
                            id: 'shop_revenues',
                            title: this.$t('revenues'),
                            icon: 'widgets',
                            route: 'admin.shop_revenues.index',
                        },
                        {
                            id: 'shop_payouts',
                            title: this.$t('payouts'),
                            icon: 'widgets',
                            route: 'admin.shop_payouts.index',
                        },
                        {
                            id: 'shop_reviews',
                            title: this.$t('reviews'),
                            icon: 'widgets',
                            route: 'admin.shop_reviews.index',
                        },

                    ]
                },

                {
                    title: this.$t('manage_customers'),
                    isHeader: true
                },

                {
                    id: 'customers',
                    title: this.$t('customers'),
                    icon: 'person',
                    route: 'admin.customers.index',
                    pinnable: true,
                    permission: adminPermissions.customers
                },

                {
                    id: 'customer_wallets',
                    title: this.$t('wallet'),
                    icon: 'account_balance_wallet',
                    route: 'admin.customer_wallets.index',
                    permission: adminPermissions.customers,

                },
                {
                    id: 'home_layouts',
                    title: this.$t('home_layout'),
                    icon: 'view_comfy',
                    route: 'admin.home_layouts.edit',
                    pinnable: true,
                    permission: adminPermissions.home_layouts
                },
                {
                    title: this.$t('manage_delivery_boys'),
                    isHeader: true
                },
                {
                    id: 'delivery_boys',
                    title: this.$t('delivery_boys'),
                    icon: 'local_shipping',
                    route: 'admin.delivery_boys.index',
                    pinnable: true,
                    permission: adminPermissions.delivery_boys

                },

                {
                    id: 'delivery_boy_other_menu',
                    title: this.$t('other'),
                    icon: 'electric_moped',
                    pinnable: true,
                    permission: adminPermissions.delivery_boys,

                    children: [
                        {
                            id: 'delivery_boy_reviews',
                            title: this.$t('reviews'),
                            route: 'admin.delivery_boy_reviews.index',
                        },
                        {
                            id: 'delivery_boy_revenues',
                            title: this.$t('revenues'),
                            route: 'admin.delivery_boy_revenues.index',
                        },
                        {
                            id: 'delivery_boy_payouts',
                            title: this.$t('payouts'),
                            route: 'admin.delivery_boy_payouts.index',
                        },


                    ]
                },
                {
                    title: this.$t('manage_employees'),
                    isHeader: true,
                },
                {
                    id: 'roles',
                    title: this.$t('roles'),
                    icon: 'assignment_ind',
                    route: 'admin.roles.index',
                    pinnable: true,
                    permission: adminPermissions.employees

                }, {
                    id: 'employees',
                    title: this.$t('employees'),
                    icon: 'person_4',
                    route: 'admin.employees.index',
                    pinnable: true,
                    permission: adminPermissions.employees
                },
                {
                    title: this.$t('business'),
                    isHeader: true,
                },

                {
                    id: 'home_banners',
                    title: this.$t('banners'),
                    icon: 'view_carousel',
                    route: 'admin.home_banners.index',
                    pinnable: true,
                    permission: adminPermissions.home_banners,

                },
                {
                    id: 'manual_notifications',
                    title: this.$t('notifications'),
                    icon: 'campaign',
                    route: 'admin.manual_notifications.index',
                    pinnable: true,
                },
                {
                    id: 'revenues',
                    title: this.$t('revenues'),
                    icon: 'account_balance_wallet',
                    route: 'admin.revenues.index',
                    pinnable: true,
                    permission: adminPermissions.revenues
                },

                {
                    id: 'reports',
                    title: this.$t('reports'),
                    icon: 'request_page',
                    route: 'admin.reports.report',
                    pinnable: true,
                    permission: adminPermissions.reports
                },

                {
                    id: 'subscribers',
                    title: this.$t('subscribers'),
                    icon: 'mail',
                    route: 'admin.subscribers.index',
                    pinnable: true,
                    permission: adminPermissions.subscribers
                },


                {
                    title: this.$t('management'),
                    isHeader: true,
                },
                {
                    id: 'file_manager',
                    title: this.$t('file_manager'),
                    icon: 'folder',
                    route: 'admin.file_manager.index',
                    pinnable: true,
                    permission: adminPermissions.file_managers,
                },
                {
                    id: 'business_setup_menu',
                    title: this.$t('setups'),
                    icon: 'tune',
                    pinnable: true,
                    permission: adminPermissions.business_settings,
                    children: [
                        {
                            id: 'info_settings',
                            title: this.$t('information'),
                            route: 'admin.setups.info_settings',
                        },
                        {
                            id: 'system_settings',
                            title: this.$t('system'),
                            route: 'admin.setups.system_settings',
                        },

                        {
                            id: 'order_settings',
                            title: this.$t('order'),
                            route: 'admin.setups.order_settings',
                        },
                        {
                            id: 'mobile_app_settings',
                            title: this.$t('mobile_app'),
                            route: 'admin.setups.app_settings',
                        }, {
                            id: 'payment_settings',
                            title: this.$t('payment'),
                            route: 'admin.setups.payment_settings',
                        }, {
                            id: 'sms_settings',
                            title: this.$t('SMS'),
                            route: 'admin.setups.sms_settings',
                        },
                        {
                            id: 'mail_settings',
                            title: this.$t('mail'),
                            route: 'admin.setups.mail_settings',
                        },
                        {
                            id: 'other_settings',
                            title: this.$t('other'),
                            route: 'admin.setups.other_settings',
                        },

                    ]
                },

            ];
            let list: IMenuItem[] = [];

            for (const item of items) {
                if (item.permission == null || isAdminPermission(item.permission)) {
                    list.push(item);
                }
            }

            return list;
        },
        getMenuItems(): IMenuItem[] {
            let items: IMenuItem[] = [];

            if (isAdminPermission(adminPermissions.dashboard)) {
                items.push({
                    id: 'dashboard',
                    title: this.$t('dashboard'),
                    icon: 'dashboard',
                    route: 'admin.dashboard',
                },)
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

        },
    },
    created() {
        const navigationStore = useAdminDataStore();
        this.pinned = navigationStore.pinned_navigations ?? [];
    },
    mounted: function () {
        this.activateCollapse();
        const navigationStore = useAdminDataStore();
        // this.pinned = navigationStore.pinned;
        const self = this;
        navigationStore.$subscribe((mut, state) => {
            self.pinned = navigationStore.pinned_navigations ?? [];
            self.activateMenuItems();
        });
        this.activateMenuItems();
        // window.test =  this.activateMenuItems;
    },
    methods: {
        toggleLeftbarSmHover() {
            if (window.innerWidth < 767) {
                toggleLeftbarEnable();
            } else {
                toggleLeftbarSmHover();
            }

        },
        togglePin(item) {
            toggleAdminPinnedNavigationToState(item.id)
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

            let route: string | null = this.$route.name;
            if (route != null) {
                let route2: string = route.replace('create', 'index');
                route2 = route2.replace('edit', 'index');
                route2 = route2.replace('on_map', 'index');
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
                                    if (parentElement.parentElement.querySelector('.collapse')) {
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
                    } else {
                        if (menuItem.route === route || menuItem.route === route2) {
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
