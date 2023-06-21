import {defineStore} from 'pinia'
import {useStorage} from '@vueuse/core';
import {ICustomer, ICustomerPreference} from "@js/types/models/customer";
import {ISeller} from "@js/types/models/seller";
import {ICart} from "@js/types/models/cart";
import {array_toggle, array_update_unique} from "@js/shared/array_utils";
import {ICustomerAddress} from "@js/types/models/customer_address";
import {IAdmin} from "@js/types/models/admin";
import {INotification} from "@js/types/models/notification";
import {IShop} from "@js/types/models/shop";
import {IToast} from "@js/types/other";
import {IModule} from "@js/types/models/module";
import {RequestCache} from "@js/services/api/cache";

export interface ITableLayoutOption {
    center: boolean,
    as_card: boolean,
    bordered: boolean
}

export interface IFullLayoutOption {
    show_leftbar: boolean,
    show_topbar: boolean
}

export interface ILeftbarOption {
    color: 'light' | 'dark',
    mode: 'full' | 'sm-hover',
    enable: boolean
}

export interface ITopbarOption {
    color: 'light' | 'dark',
}

export interface ILayout {
    theme: 'light' | 'dark' | 'auto',
    is_dark: boolean,
    mode: 'vertical' | 'detached' | 'full',
    topbar: ITopbarOption,
    leftbar: ILeftbarOption,
    show_rightbar: boolean,
    table_layout: ITableLayoutOption,
    full_layout: IFullLayoutOption

}

export interface IUserData<T> {
    data: T,
    auth_token: string,
    loaded: false
}


export const useAdminDataStore = defineStore('admin_data_store', {
    state: () => {
        return {
            loaded: false as boolean,
            module_id: useStorage<number | null>('admin_selected_module_id', null),
            notifications: null as INotification[] | null,
            user_data: useStorage<IUserData<IAdmin> | null>('admin_auth_data', {} as null),
            pinned_navigations: useStorage<string[]>('admin_pinned_navigations_data', [],)
        }
    }, actions: {
        setLoaded() {
            this.loaded = true;
        }, isLoggedIn() {
            return this.user_data != null && this.user_data.data != null;
        }, updateNotifications(notifications: INotification[]) {
            this.notifications = notifications;
        }, togglePinnedNavigation(id: string) {
            this.pinned_navigations = array_toggle(this.pinned_navigations, id);
        }, getPinnedNavigations() {
            return this.pinned_navigations ?? [];
        }, getUserData(): IUserData<IAdmin> {
            return this.user_data;
        }, updateUserData(data: IAdmin) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.data = data;
        }, updateAuthToken(token: string) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.auth_token = token;
        }, updateModuleId(module_id: number) {
            this.module_id = module_id;
        }, logout() {
            this.user_data = null;
            RequestCache.clear();
        },

    },
});


export const useSellerDataStore = defineStore('seller_data_store', {
    state: () => {
        return {
            loaded: false as boolean,
            notifications: null as INotification[] | null,
            user_data: useStorage<IUserData<ISeller> | null>('seller_auth_data', {} as null),
            shop: null as IShop | null,
            pinned_navigations: useStorage<string[]>('seller_pinned_navigations_data', [],),
            module: null as IModule | null,

        }
    }, actions: {
        setLoaded() {
            this.loaded = true;
        }, isLoggedIn() {
            return this.user_data != null && this.user_data.data != null;
        }, updateNotifications(notifications: INotification[]) {
            this.notifications = notifications;
        }, togglePinnedNavigation(id: string) {
            this.pinned_navigations = array_toggle(this.pinned_navigations, id);
        }, getUserData(): IUserData<ISeller> {
            return this.user_data;
        }, updateUserData(data: ISeller) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.data = data;
        }, updateAuthToken(token: string) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.auth_token = token;
        }, updateShopData(shop: IShop) {
            this.shop = shop;
        }, updateModuleData(module: IModule) {
            this.module = module;
        }, logout() {
            this.user_data = null;
            this.shop = null;
            this.module = null;
            this.loaded = false;
            this.notification = null;
            RequestCache.clear();
        },

    },
});


export const useCustomerDataStore = defineStore('admin_data_store', {
    state: () => {
        return {
            loaded: false as boolean,
            user_data: useStorage<IUserData<ICustomer> | null>('customer_auth_data', {} as null),
            addresses: null as ICustomerAddress[] | null,
            addresses_loaded: false as boolean,
            module_id: useStorage<number | null>('customer_selected_module_id', null),
            notifications: null as INotification[] | null,
            carts: [] as ICart[],
            checkout_shop_id: useStorage<number>('customer_checkout_shop_id', null),
            preference: useStorage<ICustomerPreference | null>('customer_preference', {} as null)
        }
    }, actions: {
        getUserData(): IUserData<ICustomer> {
            return this.user_data;
        },
        getPreference(): ICustomerPreference {
            return {
                category_ids: this.preference.category_ids ?? [],
                first_time: this.preference.first_time ?? true
            }
        },
        isLoggedIn() {
            return this.user_data != null && this.user_data.data != null;
        },
        updateNotifications(notifications: INotification[]) {
            this.notifications = notifications;
        },
        setLoaded() {
            this.loaded = true;
        }, updateUserData(data: IAdmin) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.data = data;
        }, updateAuthToken(token: string) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.auth_token = token;
        }, updateModuleId(module_id: number) {
            this.module_id = module_id;
        },  updateCategoryPreference(category_ids: number[]) {
            const pref = this.getPreference();
            pref.category_ids=category_ids;
            pref.first_time=false;
            this.preference = pref;
        }, updateAddresses(addresses: ICustomerAddress[]) {
            this.addresses = addresses;
            this.addresses_loaded = true;
        }, clearAddressData() {
            this.addresses = null;
        }, addCart(newCart: ICart) {
            for (const cart of this.carts) {
                if (cart.id == newCart.id) {
                    return;
                }
            }
            this.carts.push(newCart);
        }, replaceAllCart(carts: ICart[]) {
            this.carts = carts;
            this.loaded = true;
        }, updateCart(cart: ICart) {
            this.carts = array_update_unique(this.carts, cart);
            this.loaded = true;
        }, removeCart(cartId: number) {
            this.carts = this.carts.filter((ca) => ca.id != cartId);
        }, clearCartData() {
            this.carts = [];
        }, updateCheckoutShopId(shop_id: number) {
            this.checkout_shop_id = shop_id;
        }, clearCheckoutData() {
            this.checkout_shop_id = null;
        }, clearModuleData() {
            this.module_id = null;
        }, logout() {
            this.user_data = null;
            this.checkout_shop_id = null;
            RequestCache.clear();
            const pref = this.getPreference();
            pref.category_ids = [];
            pref.first_time = true;
            this.preference = pref;
        },

    },
});

///=========================== Layout store ==========================================//


export const useLayoutStore = defineStore('layout_store', {


    state: () => {
        return {
            layout: useStorage('layout_data', {
                theme: 'light', is_dark: false, mode: 'vertical', topbar: {
                    color: 'light'
                } as ITopbarOption, show_rightbar: false, leftbar: {
                    color: 'light', mode: 'full'
                } as ILeftbarOption, table_layout: {} as ITableLayoutOption, full_layout: {} as IFullLayoutOption,
            } as ILayout)
        }
    }, actions: {
        getLayout(): ILayout {
            const cfg = this.getDefault();
            return {
                theme: this.layout.theme ?? cfg.theme,
                is_dark: this.layout.is_dark ?? cfg.is_dark,
                mode: this.layout.mode ?? cfg.mode,
                topbar: {
                    color: this.layout.topbar?.color ?? cfg.topbar.color
                },
                show_rightbar: this.layout.show_rightbar ?? cfg.show_rightbar,
                leftbar: {
                    color: this.layout?.leftbar?.color ?? cfg.leftbar.color,
                    mode: this.layout?.leftbar?.mode ?? cfg.leftbar.mode,
                    enable: this.layout?.leftbar?.enable ?? cfg.leftbar.enable
                },
                full_layout: {
                    show_leftbar: this.layout.full_layout?.show_leftbar ?? cfg.full_layout.show_leftbar,
                    show_topbar: this.layout.full_layout?.show_topbar ?? cfg.full_layout.show_topbar,
                },
                table_layout: {
                    as_card: this.layout.table_layout?.as_card ?? cfg.table_layout.as_card,
                    center: this.layout.table_layout?.center ?? cfg.table_layout.center,
                    bordered: this.layout.table_layout?.bordered ?? cfg.table_layout.bordered,
                }
            };
        }, resetTheme() {
            this.layout = this.getDefault();
        }, getDefault(): ILayout {
            return {
                theme: 'light', is_dark: false, mode: 'vertical', topbar: {
                    color: 'light'
                }, show_rightbar: false, leftbar: {
                    color: 'light', mode: 'full', enable: false
                }, full_layout: {
                    show_leftbar: false, show_topbar: false,
                }, table_layout: {
                    as_card: true, center: false, bordered: false,
                }
            };
        },

        updateTheme(theme: 'light' | 'dark' | 'auto') {
            this.layout = {...this.layout, theme: theme,};
            switch (theme) {
                case 'dark':
                    this.layout = {...this.layout, is_dark: true};
                    break;
                case 'light':
                    this.layout = {...this.layout, is_dark: false};
                    break;
                case 'auto':
                    this.adjustTheme();
                    break;
            }
        }, updateLayoutMode(mode: 'vertical' | 'detached' | 'full') {
            this.layout = {...this.layout, mode: mode};
        }, updateTopbarColor(color: 'light' | 'dark') {
            let layout = this.getLayout();
            layout.topbar.color = color
            this.layout = layout;
        }, updateLeftbarColor(color: 'light' | 'dark') {
            this.layout = {...this.layout, leftbar: {...this.layout.leftbar, color: color}};
        }, updateShowRightbar(show: boolean = false) {
            this.layout = {...this.layout, show_rightbar: show};
        }, updateIsDark(is_dark: boolean) {
            this.layout = {...this.layout, is_dark: is_dark};
        }, updateTableLayout(table_layout: ITableLayoutOption) {
            this.layout = {...this.layout, table_layout: table_layout};
        }, updateFullLayout(full_layout: IFullLayoutOption) {
            this.layout = {...this.layout, full_layout: full_layout};
        }, changeLeftbarSize(size: 'full' | 'sm-hover') {
            let leftbar = this.getLeftbar();
            leftbar.mode = size;
            this.layout = {...this.layout, ...{leftbar: leftbar}};
        }, toggleLeftbarEnable(enable=null) {
            let leftbar = this.getLeftbar();
            leftbar.enable = enable?? !leftbar.enable;
            this.layout = {...this.layout, ...{leftbar: leftbar}};
        }, toggleLeftbarSmHover() {
            let leftbar = this.getLeftbar();
            leftbar.mode = leftbar.mode == 'full' ? 'sm-hover' : 'full';
            this.layout = {...this.layout, ...{leftbar: leftbar}};
        }, init() {
            const self = this;
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                self.adjustTheme();
            });
            this.adjustTheme();
        }, adjustTheme() {
            if (this.layout.theme === 'auto') {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    this.updateIsDark(true);
                } else {
                    this.updateIsDark(false);
                }
            }
        }, getTableLayout(): ITableLayoutOption {
            return this.layout?.table_layout ?? {
                as_card: false, bordered: false, center: false
            }
        }, getFullLayoutOption(): IFullLayoutOption {
            return this.layout?.full_layout ?? {
                show_topbar: false, show_leftbar: false
            }
        }, getLeftbar(): ILeftbarOption {
            return this.layout?.leftbar ?? {
                color: 'light', mode: 'full'
            }
        }
    },

})


///=========================== Language store ==========================================//


export const useLanguageStore = defineStore('language_store', {


    state: () => {
        return {
            selected_language: useStorage('selected_language', null as string)
        }
    }, actions: {
        getSelectedLanguage() {
            return this.selected_language ?? "en";
        },
        updateSelectedLanguage(language: string) {
            this.selected_language = language;
        }
    },

})


///=========================== Toast store ==========================================//

export const useToastNotificationStore = defineStore('toast_notification_store', {
    state: () => {
        return {
            toasts: [] as IToast[] | null
        }
    }, actions: {
        addToast(toast: IToast) {
            this.toasts.push({...toast, id: Math.random().toString(36).substring(2, 7)})
        }, removeToast(toast: IToast) {
            this.toasts = this.toasts.filter((t) => t.id !== toast.id);
        }

    },
})
