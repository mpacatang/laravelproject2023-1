import { useAdminDataStore, useSellerDataStore } from "@js/services/state/states";
export const adminPermissions = {
    zones: 'zones',
    products: 'products',
    shop_plans: 'shop_plans',
    shops: 'shops',
    sellers: 'sellers',
    customers: 'customers',
    business_settings: 'business_settings',
    modules: 'modules',
    home_layouts: 'home_layouts',
    home_banners: 'home_banners',
    file_managers: 'file_managers',
    units: 'units',
    delivery_boys: 'delivery_boys',
    orders: 'orders',
    pos: 'pos',
    addons: 'addons',
    categories: 'categories',
    sub_categories: 'sub_categories',
    revenues: 'revenues',
    reports: 'reports',
    roles: 'roles',
    employees: 'employees',
    dashboard: 'dashboard',
    coupons: 'coupons',
    subscribers: 'subscribers',
};
export const sellerPermissions = {
    products: 'products',
    report: 'report',
    shops: 'shops',
    delivery_boys: 'delivery_boys',
    orders: 'orders',
    pos: 'pos',
    addons: 'addons',
    categories: 'categories',
    revenues: 'revenues',
    roles: 'roles',
    employees: 'employees',
    dashboard: 'dashboard',
    coupons: 'coupons',
    reviews: 'reviews',
};
export function isSellerPermission(permission) {
    return checkSellerPermission(permission);
}
function checkSellerPermission(permission) {
    let user = useSellerDataStore().getUserData()?.data;
    if (user == null)
        return false;
    if (user.is_owner) {
        return true;
    }
    if (user.role == null || user.role.permissions == null) {
        return false;
    }
    let permissions = user.role.permissions;
    return permissions.includes(permission);
}
export function isAdminPermission(permission) {
    return checkAdminPermission(permission);
}
function checkAdminPermission(permission) {
    let user = useAdminDataStore().getUserData()?.data;
    if (user == null)
        return false;
    if (user.is_super) {
        return true;
    }
    if (user.role == null || user.role.permissions == null) {
        return false;
    }
    let permissions = user.role.permissions;
    return permissions.includes(permission);
}
