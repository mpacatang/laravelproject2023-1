import { defineStore } from 'pinia';
import { useStorage } from '@vueuse/core';
import { array_toggle, array_update_unique } from "@js/shared/array_utils";
import { RequestCache } from "@js/services/api/cache";
export const useAdminDataStore = defineStore('admin_data_store', {
    state: () => {
        return {
            loaded: false,
            module_id: useStorage('admin_selected_module_id', null),
            notifications: null,
            user_data: useStorage('admin_auth_data', {}),
            pinned_navigations: useStorage('admin_pinned_navigations_data', [])
        };
    }, actions: {
        setLoaded() {
            this.loaded = true;
        }, isLoggedIn() {
            return this.user_data != null && this.user_data.data != null;
        }, updateNotifications(notifications) {
            this.notifications = notifications;
        }, togglePinnedNavigation(id) {
            this.pinned_navigations = array_toggle(this.pinned_navigations, id);
        }, getPinnedNavigations() {
            return this.pinned_navigations ?? [];
        }, getUserData() {
            return this.user_data;
        }, updateUserData(data) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.data = data;
        }, updateAuthToken(token) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.auth_token = token;
        }, updateModuleId(module_id) {
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
            loaded: false,
            notifications: null,
            user_data: useStorage('seller_auth_data', {}),
            shop: null,
            pinned_navigations: useStorage('seller_pinned_navigations_data', []),
            module: null,
        };
    }, actions: {
        setLoaded() {
            this.loaded = true;
        }, isLoggedIn() {
            return this.user_data != null && this.user_data.data != null;
        }, updateNotifications(notifications) {
            this.notifications = notifications;
        }, togglePinnedNavigation(id) {
            this.pinned_navigations = array_toggle(this.pinned_navigations, id);
        }, getUserData() {
            return this.user_data;
        }, updateUserData(data) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.data = data;
        }, updateAuthToken(token) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.auth_token = token;
        }, updateShopData(shop) {
            this.shop = shop;
        }, updateModuleData(module) {
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
            loaded: false,
            user_data: useStorage('customer_auth_data', {}),
            addresses: null,
            addresses_loaded: false,
            module_id: useStorage('customer_selected_module_id', null),
            notifications: null,
            carts: [],
            checkout_shop_id: useStorage('customer_checkout_shop_id', null),
            preference: useStorage('customer_preference', {})
        };
    }, actions: {
        getUserData() {
            return this.user_data;
        },
        getPreference() {
            return {
                category_ids: this.preference.category_ids ?? [],
                first_time: this.preference.first_time ?? true
            };
        },
        isLoggedIn() {
            return this.user_data != null && this.user_data.data != null;
        },
        updateNotifications(notifications) {
            this.notifications = notifications;
        },
        setLoaded() {
            this.loaded = true;
        }, updateUserData(data) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.data = data;
        }, updateAuthToken(token) {
            if (this.user_data == null) {
                this.user_data = {};
            }
            this.user_data.auth_token = token;
        }, updateModuleId(module_id) {
            this.module_id = module_id;
        }, updateCategoryPreference(category_ids) {
            const pref = this.getPreference();
            pref.category_ids = category_ids;
            pref.first_time = false;
            this.preference = pref;
        }, updateAddresses(addresses) {
            this.addresses = addresses;
            this.addresses_loaded = true;
        }, clearAddressData() {
            this.addresses = null;
        }, addCart(newCart) {
            for (const cart of this.carts) {
                if (cart.id == newCart.id) {
                    return;
                }
            }
            this.carts.push(newCart);
        }, replaceAllCart(carts) {
            this.carts = carts;
            this.loaded = true;
        }, updateCart(cart) {
            this.carts = array_update_unique(this.carts, cart);
            this.loaded = true;
        }, removeCart(cartId) {
            this.carts = this.carts.filter((ca) => ca.id != cartId);
        }, clearCartData() {
            this.carts = [];
        }, updateCheckoutShopId(shop_id) {
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
                }, show_rightbar: false, leftbar: {
                    color: 'light', mode: 'full'
                }, table_layout: {}, full_layout: {},
            })
        };
    }, actions: {
        getLayout() {
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
        }, getDefault() {
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
        updateTheme(theme) {
            this.layout = { ...this.layout, theme: theme, };
            switch (theme) {
                case 'dark':
                    this.layout = { ...this.layout, is_dark: true };
                    break;
                case 'light':
                    this.layout = { ...this.layout, is_dark: false };
                    break;
                case 'auto':
                    this.adjustTheme();
                    break;
            }
        }, updateLayoutMode(mode) {
            this.layout = { ...this.layout, mode: mode };
        }, updateTopbarColor(color) {
            let layout = this.getLayout();
            layout.topbar.color = color;
            this.layout = layout;
        }, updateLeftbarColor(color) {
            this.layout = { ...this.layout, leftbar: { ...this.layout.leftbar, color: color } };
        }, updateShowRightbar(show = false) {
            this.layout = { ...this.layout, show_rightbar: show };
        }, updateIsDark(is_dark) {
            this.layout = { ...this.layout, is_dark: is_dark };
        }, updateTableLayout(table_layout) {
            this.layout = { ...this.layout, table_layout: table_layout };
        }, updateFullLayout(full_layout) {
            this.layout = { ...this.layout, full_layout: full_layout };
        }, changeLeftbarSize(size) {
            let leftbar = this.getLeftbar();
            leftbar.mode = size;
            this.layout = { ...this.layout, ...{ leftbar: leftbar } };
        }, toggleLeftbarEnable(enable = null) {
            let leftbar = this.getLeftbar();
            leftbar.enable = enable ?? !leftbar.enable;
            this.layout = { ...this.layout, ...{ leftbar: leftbar } };
        }, toggleLeftbarSmHover() {
            let leftbar = this.getLeftbar();
            leftbar.mode = leftbar.mode == 'full' ? 'sm-hover' : 'full';
            this.layout = { ...this.layout, ...{ leftbar: leftbar } };
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
                }
                else {
                    this.updateIsDark(false);
                }
            }
        }, getTableLayout() {
            return this.layout?.table_layout ?? {
                as_card: false, bordered: false, center: false
            };
        }, getFullLayoutOption() {
            return this.layout?.full_layout ?? {
                show_topbar: false, show_leftbar: false
            };
        }, getLeftbar() {
            return this.layout?.leftbar ?? {
                color: 'light', mode: 'full'
            };
        }
    },
});
///=========================== Language store ==========================================//
export const useLanguageStore = defineStore('language_store', {
    state: () => {
        return {
            selected_language: useStorage('selected_language', null)
        };
    }, actions: {
        getSelectedLanguage() {
            return this.selected_language ?? "en";
        },
        updateSelectedLanguage(language) {
            this.selected_language = language;
        }
    },
});
///=========================== Toast store ==========================================//
export const useToastNotificationStore = defineStore('toast_notification_store', {
    state: () => {
        return {
            toasts: []
        };
    }, actions: {
        addToast(toast) {
            this.toasts.push({ ...toast, id: Math.random().toString(36).substring(2, 7) });
        }, removeToast(toast) {
            this.toasts = this.toasts.filter((t) => t.id !== toast.id);
        }
    },
});
