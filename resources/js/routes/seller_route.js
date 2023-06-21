import { routeGroup } from "./routes";
import SelfRegistration from "../pages/seller/shops/self_registration.vue";
import Login from "../pages/seller/auth/login.vue";
import Dashboard from "../pages/seller/dashboard.vue";
import Products from "../pages/seller/products/index.vue";
import EditProduct from "../pages/seller/products/edit.vue";
import CreateProduct from "../pages/seller/products/create.vue";
import ImportProduct from "../pages/seller/products/import.vue";
import Addons from "../pages/seller/addons/index.vue";
import CreateAddon from "../pages/seller/addons/create.vue";
import EditAddon from "../pages/seller/addons/edit.vue";
import DeliveryBoys from "../pages/seller/delivery_boys/index.vue";
import CreateDeliveryBoy from "../pages/seller/delivery_boys/create.vue";
import EditDeliveryBoy from "../pages/seller/delivery_boys/edit.vue";
import Orders from "../pages/seller/orders/index.vue";
import EditOrder from "../pages/seller/orders/edit.vue";
import OrderInvoice from "../pages/seller/orders/invoice.vue";
import AssignDeliveryToOrder from "../pages/seller/orders/assign_delivery.vue";
import CreatePOS from "../pages/seller/pos/create.vue";
import Coupons from "../pages/seller/coupons/index.vue";
import NotFoundError from "../pages/seller/errors/not_found.vue";
import HasNotShopError from "../pages/seller/errors/has_not_shop.vue";
import ForbiddenError from "../pages/seller/errors/forbidden.vue";
import InactiveError from "../pages/seller/errors/inactive.vue";
import ShopNotActiveError from "../pages/seller/errors/shop_not_active.vue";
import SelfDeliveryNotActiveError from "../pages/seller/errors/self_delivery_not_active.vue";
import Categories from "../pages/seller/categories/index.vue";
import SubCategories from "../pages/seller/sub_categories/index.vue";
import EditShop from "../pages/seller/shops/edit.vue";
import EditShopSetting from "../pages/seller/shops/settings.vue";
import EditShopPlan from "../pages/seller/shops/plan.vue";
import ShopRevenues from "../pages/seller/shops/payments/shop_revenues.vue";
import ShopPayouts from "../pages/seller/shops/payments/shop_payout.vue";
import ShopReviews from "../pages/seller/shops/reviews/index.vue";
import Employees from "../pages/seller/employees/index.vue";
import CreateEmployee from "../pages/seller/employees/create.vue";
import EditEmployee from "../pages/seller/employees/edit.vue";
import SellerRoles from "../pages/seller/roles/index.vue";
import CreateSellerRole from "../pages/seller/roles/create.vue";
import EditSellerRole from "../pages/seller/roles/edit.vue";
import Profile from "@js/pages/seller/auth/profile.vue";
import DeliveryBoyRevenues from "@js/pages/seller/delivery_boys/revenues.vue";
import DeliveryBoyPayouts from "@js/pages/seller/delivery_boys/payouts/index.vue";
import CreateDeliveryBoyPayout from "@js/pages/seller/delivery_boys/payouts/create.vue";
import DeliveryBoyReviews from "@js/pages/seller/delivery_boys/reviews/index.vue";
import ProductReview from "@js/pages/seller/products/reviews.vue";
import Report from "@js/pages/seller/reports/report.vue";
import Chats from "@js/pages/seller/chats/index.vue";
const auth = [{
        path: "login/", component: Login, name: "login"
    }, {
        path: "profile/", component: Profile, name: "profile"
    },];
const other = [{
        path: "not_found/", component: NotFoundError, name: "errors.not_found"
    }, {
        path: "has_not_shop/", component: HasNotShopError, name: "errors.has_not_shop"
    }, {
        path: "forbidden/", component: ForbiddenError, name: "errors.forbidden"
    }, {
        path: "inactive/", component: InactiveError, name: "errors.inactive"
    }, {
        path: "shop_not_active/", component: ShopNotActiveError, name: "errors.shop_not_active"
    }, {
        path: "self_delivery_not_active/", component: SelfDeliveryNotActiveError, name: "errors.self_delivery_not_active"
    }, {
        path: ":pathMatch(.*)*", component: NotFoundError, name: "fallback"
    }];
const categories = [{
        path: "categories/", component: Categories, name: "categories.index",
    }, {
        path: "sub_categories/", component: SubCategories, name: "sub_categories.index",
    },];
const shops = [{
        path: "self_registration/", component: SelfRegistration, name: "shops.self_registration",
    }, {
        path: "shops/", component: EditShop, name: "shops.edit",
    }, {
        path: "shops/settings/", component: EditShopSetting, name: "shops.settings",
    }, {
        path: "shops/plan/", component: EditShopPlan, name: "shops.plan",
    }, {
        path: "shops/reviews/", component: ShopReviews, name: "shops.reviews",
    },];
const products = [{
        path: "products/", component: Products, name: "products.index",
    }, {
        path: "products/create", component: CreateProduct, name: "products.create",
    }, {
        path: "products/import", component: ImportProduct, name: "products.import"
    }, {
        path: "products/:id/", component: EditProduct, name: "products.edit"
    }, {
        path: "products/:id/reviews", component: ProductReview, name: "products.reviews"
    },];
const addons = [{
        path: "addons/", component: Addons, name: "addons.index",
    }, {
        path: "addons/create", component: CreateAddon, name: "addons.create",
    }, {
        path: "addons/:id/", component: EditAddon, name: "addons.edit"
    },];
const orders = [{
        path: "orders/", component: Orders, name: "orders.index",
    }, {
        path: "orders/:id/", component: EditOrder, name: "orders.edit"
    }, {
        path: "orders/:id/invoice/", component: OrderInvoice, name: "orders.invoice"
    }, {
        path: "orders/:id/assign_delivery/", component: AssignDeliveryToOrder, name: "orders.assign_delivery"
    },];
const delivery_boys = [{
        path: "delivery_boys/", component: DeliveryBoys, name: "delivery_boys.index",
    }, {
        path: "delivery_boys/create/", component: CreateDeliveryBoy, name: "delivery_boys.create",
    }, {
        path: "delivery_boys/:id/", component: EditDeliveryBoy, name: "delivery_boys.edit"
    }, {
        path: "delivery_boy_revenues/", component: DeliveryBoyRevenues, name: "delivery_boy_revenues.index",
    }, {
        path: "delivery_boy_payouts/", component: DeliveryBoyPayouts, name: "delivery_boy_payouts.index",
    }, {
        path: "delivery_boy_payouts/create/", component: CreateDeliveryBoyPayout, name: "delivery_boy_payouts.create",
    }, {
        path: "delivery_boy_reviews/", component: DeliveryBoyReviews, name: "delivery_boy_reviews.index",
    },];
const roles = [{
        path: "roles/", component: SellerRoles, name: "roles.index"
    }, {
        path: "roles/create/", component: CreateSellerRole, name: "roles.create"
    }, {
        path: "roles/:id/", component: EditSellerRole, name: "roles.edit"
    },];
const sellers = [{
        path: "employees/", component: Employees, name: "employees.index"
    }, {
        path: "employees/create/", component: CreateEmployee, name: "employees.create"
    }, {
        path: "employees/:id/", component: EditEmployee, name: "employees.edit"
    },];
const pos = [{
        path: "pos/create", component: CreatePOS, name: "pos.create"
    },];
const system = [{
        path: "/", component: Dashboard, name: "entry"
    }, {
        path: "dashboard/", component: Dashboard, name: "dashboard"
    },];
const coupons = [{
        path: "coupons/", component: Coupons, name: "coupons.index"
    },];
const revenues = [{
        path: "shop_revenues/", component: ShopRevenues, name: "shop_revenues.index"
    }, {
        path: "payouts/", component: ShopPayouts, name: "shop_payouts.index"
    },];
const reports = [{
        path: "reports/", component: Report, name: "reports.report"
    },];
const chats = [
    {
        path: "chats/", component: Chats, name: "chats.index",
    },
];
const route = [...auth, ...other,];
route.push(...pos);
route.push(...orders);
route.push(...shops);
route.push(...addons);
route.push(...products);
route.push(...delivery_boys);
route.push(...categories);
route.push(...coupons);
route.push(...revenues);
route.push(...chats);
route.push(...roles);
route.push(...sellers);
route.push(...reports);
route.push(...system);
//
// if (isPermission(sellerPermissions.categories)) {
//     route.push(...categories);
// }
// if (isPermission(sellerPermissions.shops)) {
//     route.push(...shops);
// }
// if (isPermission(sellerPermissions.products)) {
//     route.push(...products);
// }
//
// if (isPermission(sellerPermissions.addons)) {
//     route.push(...addons);
// }
// if (isPermission(sellerPermissions.orders)) {
//     route.push(...orders);
// }
//
// if (isPermission(sellerPermissions.delivery_boys)) {
//     route.push(...delivery_boys);
// }
//
// if (isPermission(sellerPermissions.roles)) {
//     route.push(...roles);
// }
//
// if (isPermission(sellerPermissions.sellers)) {
//     route.push(...sellers);
// }
//
// if (isPermission(sellerPermissions.pos)) {
//     route.push(...pos);
// }
//
// if (isPermission(sellerPermissions.dashboard)) {
//     route.push(...dashboard);
// }
//
// if (isPermission(sellerPermissions.revenues)) {
//     route.push(...revenues);
// }
//
//
export default routeGroup(route, '/seller/', 'seller');
