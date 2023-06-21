import { routeGroup } from "./routes";
import Login from "@js/pages/admin/auth/login.vue";
import Profile from "@js/pages/admin/auth/profile.vue";
import NotFoundError from "@js/pages/admin/errors/not_found.vue";
import Dashboard from "@js/pages/admin/dashboard.vue";
import Shops from "@js/pages/admin/shops/index.vue";
import ShopMap from "@js/pages/admin/shops/on_map.vue";
import CreateShop from "@js/pages/admin/shops/create.vue";
import EditShop from "@js/pages/admin/shops/manage/show.vue";
import ShopPayouts from "@js/pages/admin/shops/payouts/index.vue";
import CreateShopPayout from "@js/pages/admin/shops/payouts/create.vue";
import ShopPlans from "@js/pages/admin/shop_plans/index.vue";
import CreateShopPlan from "@js/pages/admin/shop_plans/create.vue";
import EditShopPlan from "@js/pages/admin/shop_plans/edit.vue";
import Sellers from "@js/pages/admin/sellers/index.vue";
import CreateSeller from "@js/pages/admin/sellers/create.vue";
import EditSeller from "@js/pages/admin/sellers/edit.vue";
import Categories from "@js/pages/admin/categories/index.vue";
import CreateCategory from "@js/pages/admin/categories/create.vue";
import EditCategory from "@js/pages/admin/categories/edit.vue";
import Units from "../pages/admin/units/index.vue";
import CreateUnit from "../pages/admin/units/create.vue";
import EditUnit from "../pages/admin/units/edit.vue";
import Banners from "@js/pages/admin/home_banners/index.vue";
import CreateBanner from "@js/pages/admin/home_banners/create.vue";
import EditBanner from "@js/pages/admin/home_banners/edit.vue";
import Products from "@js/pages/admin/products/index.vue";
import CreateProduct from "@js/pages/admin/products/create.vue";
import EditProduct from "@js/pages/admin/products/edit.vue";
import ProductReview from "@js/pages/admin/products/reviews.vue";
import ImportProduct from "@js/pages/admin/products/import.vue";
import CreatePOS from "@js/pages/admin/pos/create.vue";
import Orders from "@js/pages/admin/orders/index.vue";
import EditOrder from "@js/pages/admin/orders/edit.vue";
import OrderInvoice from "@js/pages/admin/orders/invoice.vue";
import AssignDeliveryToOrder from "@js/pages/admin/orders/assign_delivery.vue";
import DeliveryBoys from "@js/pages/admin/delivery_boys/index.vue";
import CreateDeliveryBoy from "@js/pages/admin/delivery_boys/create.vue";
import EditDeliveryBoy from "@js/pages/admin/delivery_boys/edit.vue";
import SubCategories from "@js/pages/admin/sub_categories/index.vue";
import EditSubCategory from "@js/pages/admin/sub_categories/edit.vue";
import CreateSubCategory from "@js/pages/admin/sub_categories/create.vue";
// import Products from "@js/pages/admin/products/index.vue";
// import EditProduct from "@js/pages/admin/products/edit.vue";
import Customers from "@js/pages/admin/customers/index.vue";
import CreateCustomer from "@js/pages/admin/customers/create.vue";
import EditCustomer from "@js/pages/admin/customers/edit.vue";
import EditHomeLayout from "@js/pages/admin/edit_home_layout.vue";
import Modules from "@js/pages/admin/modules/index.vue";
import EditModule from "@js/pages/admin/modules/edit.vue";
import Addons from "@js/pages/admin/addons/index.vue";
import CreateAddon from "@js/pages/admin/addons/create.vue";
import EditAddon from "@js/pages/admin/addons/edit.vue";
import Zones from "@js/pages/admin/zones/index.vue";
import CreateZone from "@js/pages/admin/zones/create.vue";
import EditZone from "@js/pages/admin/zones/edit.vue";
import Coupons from "@js/pages/admin/coupons/index.vue";
import CreateCoupon from "@js/pages/admin/coupons/create.vue";
import EditCoupon from "@js/pages/admin/coupons/edit.vue";
import AdminRevenues from "@js/pages/admin/revenues/admin_revenues.vue";
import ShopRevenues from "@js/pages/admin/shops/revenues.vue";
import DeliveryBoyRevenues from "@js/pages/admin/delivery_boys/revenues.vue";
import DeliveryBoyPayouts from "@js/pages/admin/delivery_boys/payouts/index.vue";
import CreateDeliveryBoyPayout from "@js/pages/admin/delivery_boys/payouts/create.vue";
import DeliveryBoyReviews from "@js/pages/admin/delivery_boys/reviews/index.vue";
import ShopReviews from "@js/pages/admin/shops/reviews/index.vue";
import CustomerWalletTransactions from "@js/pages/admin/customers/wallets/transactions.vue";
import SystemSettingSetting from "@js/pages/admin/setups/system_setting.vue";
import AppSettingSetting from "@js/pages/admin/setups/app_setting.vue";
import PaymentSettingSetting from "@js/pages/admin/setups/payment_setting.vue";
import SmsSettingSetting from "@js/pages/admin/setups/sms_setting.vue";
import OtherSettingSetting from "@js/pages/admin/setups/other_setting.vue";
import InfoSettingSetting from "@js/pages/admin/setups/info_setting.vue";
import MailSetting from "@js/pages/admin/setups/mail_setting.vue";
import ManualNotifications from "@js/pages/admin/manual_notifications/index.vue";
import CreateManualNotification from "@js/pages/admin/manual_notifications/create.vue";
import EditManualNotification from "@js/pages/admin/manual_notifications/edit.vue";
import FileManager from "@js/pages/admin/file_managers/index.vue";
import AdminRoles from "@js/pages/admin/roles/index.vue";
import CreateAdminRole from "@js/pages/admin/roles/create.vue";
import EditAminRole from "@js/pages/admin/roles/edit.vue";
import Employees from "../pages/admin/employees/index.vue";
import CreateEmployee from "../pages/admin/employees/create.vue";
import EditEmployee from "../pages/admin/employees/edit.vue";
import Reports from "../pages/admin/reports/report.vue";
import Subscribers from "@js/pages/admin/subscribers/index.vue";
import OrderSetting from "@js/pages/admin/setups/order_setting.vue";
import Chats from "@js/pages/admin/chats/index.vue";
const auth = [{
        path: "login/", component: Login, name: "login"
    }, {
        path: "profile/", component: Profile, name: "profile"
    },];
const modules = [{
        path: "modules/", component: Modules, name: "modules.index",
    }, {
        path: "modules/:id/", component: EditModule, name: "modules.edit"
    }];
const categories = [{
        path: "categories/", component: Categories, name: "categories.index",
    }, {
        path: "categories/create/", component: CreateCategory, name: "categories.create",
    }, {
        path: "categories/:id/", component: EditCategory, name: "categories.edit"
    }, {
        path: "sub_categories/", component: SubCategories, name: "sub_categories.index",
    }, {
        path: "sub_categories/create/", component: CreateSubCategory, name: "sub_categories.create",
    }, {
        path: "sub_categories/:id/", component: EditSubCategory, name: "sub_categories.edit"
    },
];
const zones = [{
        path: "zones/", component: Zones, name: "zones.index",
    }, {
        path: "zones/create/", component: CreateZone, name: "zones.create",
    }, {
        path: "zones/:id/", component: EditZone, name: "zones.edit"
    },];
const shops = [{
        path: "shops/", component: Shops, name: "shops.index",
    }, {
        path: "shops/create/", component: CreateShop, name: "shops.create"
    }, {
        path: "shops/on_map/", component: ShopMap, name: "shops.on_map"
    }, {
        path: "shops/:id/", component: EditShop, name: "shops.edit"
    }, {
        path: "shop_reviews/", component: ShopReviews, name: "shop_reviews.index",
    }, {
        path: "shop_revenues/", component: ShopRevenues, name: "shop_revenues.index",
    }, {
        path: "shop_payouts/", component: ShopPayouts, name: "shop_payouts.index",
    }, {
        path: "shop_payouts/create/", component: CreateShopPayout, name: "shop_payouts.create",
    },
    {
        path: "shop_plans/", component: ShopPlans, name: "shop_plans.index",
    }, {
        path: "shop_plans/create/", component: CreateShopPlan, name: "shop_plans.create",
    }, {
        path: "shop_plans/:id/", component: EditShopPlan, name: "shop_plans.edit",
    },
];
const sellers = [{
        path: "sellers/", component: Sellers, name: "sellers.index",
    }, {
        path: "sellers/create/", component: CreateSeller, name: "sellers.create"
    }, {
        path: "sellers/:id/", component: EditSeller, name: "sellers.edit"
    },
];
const customers = [
    {
        path: "customers/", component: Customers, name: "customers.index",
    }, {
        path: "customers/create/", component: CreateCustomer, name: "customers.create",
    }, {
        path: "customers/:id/", component: EditCustomer, name: "customers.edit",
    }, {
        path: "customer_wallets/", component: CustomerWalletTransactions, name: "customer_wallets.index",
    }, {
        path: "home_layout/edit/", component: EditHomeLayout, name: "home_layouts.edit",
    },
];
const deliveryBoys = [{
        path: "delivery_boys/", component: DeliveryBoys, name: "delivery_boys.index",
    }, {
        path: "delivery_boys/create/", component: CreateDeliveryBoy, name: "delivery_boys.create"
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
const coupons = [{
        path: "coupons/", component: Coupons, name: "coupons.index",
    }, {
        path: "coupons/create/", component: CreateCoupon, name: "coupons.create"
    }, {
        path: "coupons/:id/", component: EditCoupon, name: "coupons.edit"
    }];
const banners = [{
        path: "home_banners/", component: Banners, name: "home_banners.index",
    }, {
        path: "home_banners/create/", component: CreateBanner, name: "home_banners.create",
    }, {
        path: "home_banners/:id/", component: EditBanner, name: "home_banners.edit"
    },];
const products = [{
        path: "products/", component: Products, name: "products.index",
    },
    {
        path: "products/import", component: ImportProduct, name: "products.import"
    }, {
        path: "products/create/", component: CreateProduct, name: "products.create",
    }, {
        path: "products/:id/", component: EditProduct, name: "products.edit"
    }, {
        path: "products/:id/reviews", component: ProductReview, name: "products.reviews"
    }, {
        path: "addons/", component: Addons, name: "addons.index",
    }, {
        path: "addons/create/", component: CreateAddon, name: "addons.create",
    }, {
        path: "addons/:id/", component: EditAddon, name: "addons.edit"
    },
];
const orders = [{
        path: "pos/create", component: CreatePOS, name: "pos.create",
    }, {
        path: "orders/", component: Orders, name: "orders.index",
    }, {
        path: "orders/:id/", component: EditOrder, name: "orders.edit"
    }, {
        path: "orders/:id/invoice/", component: OrderInvoice, name: "orders.invoice"
    }, {
        path: "orders/:id/assign_delivery/", component: AssignDeliveryToOrder, name: "orders.assign_delivery"
    },];
const setups = [{
        path: "setups/app_settings", component: AppSettingSetting, name: "setups.app_settings",
    }, {
        path: "setups/payment_settings", component: PaymentSettingSetting, name: "setups.payment_settings",
    }, {
        path: "setups/sms_settings", component: SmsSettingSetting, name: "setups.sms_settings",
    }, {
        path: "setups/other_settings", component: OtherSettingSetting, name: "setups.other_settings",
    }, {
        path: "setups/info_settings", component: InfoSettingSetting, name: "setups.info_settings",
    }, {
        path: "setups/order_settings", component: OrderSetting, name: "setups.order_settings",
    }, {
        path: "setups/mail_settings", component: MailSetting, name: "setups.mail_settings",
    }, {
        path: "setups/system_settings", component: SystemSettingSetting, name: "setups.system_settings",
    },];
const file_manager = [{
        path: "file_manager", component: FileManager, name: "file_manager.index",
    },];
const errors = [{
        path: "not_found/", component: NotFoundError, name: "errors.not_found"
    }, {
        path: ":pathMatch(.*)*", component: NotFoundError, name: "fallback"
    }];
const units = [{
        path: "units/", component: Units, name: "units.index"
    }, {
        path: "units/create/", component: CreateUnit, name: "units.create"
    }, {
        path: "units/:id/", component: EditUnit, name: "units.edit"
    },];
const roles = [{
        path: "roles/", component: AdminRoles, name: "roles.index"
    }, {
        path: "roles/create/", component: CreateAdminRole, name: "roles.create"
    }, {
        path: "roles/:id/", component: EditAminRole, name: "roles.edit"
    },];
const subscribers = [{
        path: "subscribers/", component: Subscribers, name: "subscribers.index"
    },];
const admins = [{
        path: "employees/", component: Employees, name: "employees.index"
    }, {
        path: "employees/create/", component: CreateEmployee, name: "employees.create"
    }, {
        path: "employees/:id/", component: EditEmployee, name: "employees.edit"
    },];
const reports = [{
        path: "reports/", component: Reports, name: "reports.report"
    },];
const manual_notifications = [{
        path: "manual_notifications/", component: ManualNotifications, name: "manual_notifications.index",
    }, {
        path: "manual_notifications/create/", component: CreateManualNotification, name: "manual_notifications.create",
    }, {
        path: "manual_notifications/:id/", component: EditManualNotification, name: "manual_notifications.edit",
    },];
const chats = [{
        path: "chats/", component: Chats, name: "chats.index",
    },];
const system = [{
        path: "/", component: Dashboard, name: "entry"
    }, {
        path: "dashboard/", component: Dashboard, name: "dashboard"
    },
    {
        path: "revenues/", component: AdminRevenues, name: "revenues.index",
    },];
const routes = [];
routes.push(...auth);
routes.push(...modules);
routes.push(...shops);
routes.push(...sellers);
routes.push(...coupons);
routes.push(...deliveryBoys);
routes.push(...zones);
routes.push(...categories);
routes.push(...banners);
routes.push(...products);
routes.push(...orders);
routes.push(...customers);
routes.push(...setups);
routes.push(...file_manager);
routes.push(...units);
routes.push(...roles);
routes.push(...subscribers);
routes.push(...admins);
routes.push(...reports);
routes.push(...manual_notifications);
routes.push(...system);
routes.push(...chats);
routes.push(...errors);
export default routeGroup(routes, '/admin/', 'admin');
