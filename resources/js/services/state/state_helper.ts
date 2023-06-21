import {useAdminDataStore, useCustomerDataStore, useLayoutStore, useSellerDataStore,} from "@js/services/state/states";
import {ICart} from "@js/types/models/cart";
import {ICustomerAddress} from "@js/types/models/customer_address";
import {BusinessSetting} from "@js/types/models/business_setting";


//=------------------- Auth Helper --------------------------------------//
export const logoutCustomer = () => {
    const customer_store = useCustomerDataStore();
    customer_store.logout();
    customer_store.clearAddressData();
    customer_store.clearCartData();
    customer_store.clearCheckoutData();
    customer_store.clearModuleData();
}

//=------------------- Layout STATE --------------------------------------//
export const changeTheme = (theme: 'light' | 'dark' | 'auto') => {
    const layoutStore = useLayoutStore();
    layoutStore.updateTheme(theme);
}
export const changeTopbarColor = (color: 'light' | 'dark') => {
    const layoutStore = useLayoutStore();
    layoutStore.updateTopbarColor(color);
}
export const changeLayoutMode = (mode: 'vertical' | 'detached' | 'full') => {
    const layoutStore = useLayoutStore();
    layoutStore.updateLayoutMode(mode);
}

export const toggleLeftbarSmHover = () => {
    const layoutStore = useLayoutStore();
    layoutStore.toggleLeftbarSmHover();
}
export const changeLeftbarSize = (size: 'full' | 'sm-hover') => {
    const layoutStore = useLayoutStore();
    layoutStore.changeLeftbarSize(size);
}

export const toggleLeftbarEnable = (enable=null) => {
    const layoutStore = useLayoutStore();
    layoutStore.toggleLeftbarEnable(enable);
}


export const changeLeftbarColor = (color: 'light' | 'dark') => {
    const layoutStore = useLayoutStore();
    layoutStore.updateLeftbarColor(color);
}

export const openRightbar = () => {
    const layoutStore = useLayoutStore();
    layoutStore.updateShowRightbar(true);
}

export const closeRightbar = () => {
    const layoutStore = useLayoutStore();
    if (layoutStore.layout.show_rightbar == false) return;
    layoutStore.updateShowRightbar(false);
}


export const resetTheme = () => {
    useLayoutStore().resetTheme();
}


export const setTableBordered = (active: boolean) => {
    const layoutStore = useLayoutStore();
    layoutStore.updateTableLayout({...layoutStore.layout.table_layout, bordered: active});
}
export const setTableAsCard = (active: boolean) => {
    const layoutStore = useLayoutStore();
    layoutStore.updateTableLayout({...layoutStore.layout.table_layout, as_card: active});
}
export const setTableTextCenter = (active: boolean) => {
    const layoutStore = useLayoutStore();
    layoutStore.updateTableLayout({...layoutStore.layout.table_layout, center: active});
}


export const setTopbarShow = (enable: boolean) => {
    const layoutStore = useLayoutStore();
    layoutStore.updateFullLayout({...layoutStore.getFullLayoutOption(), show_topbar: enable});
}
export const setLeftbarShow = (enable: boolean) => {
    const layoutStore = useLayoutStore();
    layoutStore.updateFullLayout({...layoutStore.getFullLayoutOption(), show_leftbar: enable});
}

//=------------------- AUTH STATE --------------------------------------//


export const getUserTypeFromUrl = (url: string): 'admin'|'customer'|'seller'|null|string => {
    if(url == null)
        return null;
    let indexes = {
        'admin':  url.indexOf('admin'),
        'seller':  url.indexOf('seller'),
        'customer':  url.indexOf('customer'),
    }

    let least = 1000000;
    let user  = null ;

    for (const [key, value] of Object.entries(indexes)) {
        if(value!=-1 && value<least){
            least = value;
            user = key;
        }
    }

    return user;
}

export const getAuthTokenFromUrl = (url: string): string | null => {
    const user_type = getUserTypeFromUrl(url);
    if(user_type==null)
        return null;
    switch (user_type){
        case "admin":
            return useAdminDataStore().getUserData()?.auth_token;
        case "seller":
            return useSellerDataStore().getUserData()?.auth_token;
        case "customer":
            return useCustomerDataStore().getUserData()?.auth_token;
    }

}


// ----------------- CART STATE --------------------------------------//
export const addCartToState = (cart: ICart) => {
    const customerStore = useCustomerDataStore();
    customerStore.addCart(cart);
}

export const replaceCartToState = (carts: ICart[]) => {
    const customerStore = useCustomerDataStore();
    customerStore.replaceAllCart(carts);
}

export const updateCartToState = (cart: ICart) => {
    const customerStore = useCustomerDataStore();
    customerStore.updateCart(cart);
}


//=------------------- Admin ------------------------//
export const getAdminSelectedModuleId = (): Number => {
    const store = useAdminDataStore();
    return store.module_id;
}

//=------------------- Navigation STATE --------------------------------------//
export const toggleAdminPinnedNavigationToState = (id: string) => {
    const navigationStore = useAdminDataStore();
    navigationStore.togglePinnedNavigation(id);
}
export const toggleSellerPinnedNavigationToState = (id: string) => {
    const navigationStore = useSellerDataStore();
    navigationStore.togglePinnedNavigation(id);
}

//=------------------- Customer ------------------------//
export const getCustomerSelectedModuleId = (): Number => {
    const customerModuleStore = useCustomerDataStore();
    return customerModuleStore.module_id;
}

// ----------------- Address STATE --------------------------------------//
export const getAddressWithSubscribeState = (fn: (addresses: ICustomerAddress[]) => void) => {
    const customerStore = useCustomerDataStore();
    fn(customerStore.addresses);
    customerStore.$subscribe((mut, state) => {
        fn(customerStore.addresses);
    })
}


//=------------------- Business Setting STATE --------------------------------------//

export const getCurrencySymbol = () => {
    return BusinessSetting.getInstance().currency_symbol ?? "$";
}

export const getCurrencyPosition = (): 'left' | 'right' => {
    return BusinessSetting.getInstance().currency_position ?? "left";
}
export const getTimeFormat = (): '12h' | '24h' => {
    return BusinessSetting.getInstance().time_format ?? "12h";
}

export const getDigitAfterDecimal = (): number => {
    return BusinessSetting.getInstance().digit_after_decimal ?? 2;
}
