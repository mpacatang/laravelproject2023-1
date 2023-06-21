import * as VueRouter from 'vue-router';
import Landing from "@js/pages/landing.vue";

//Routes
import sellerRoute from "@js/routes/seller_route";
import adminRoute from "@js/routes/admin_route";
import customerRoute from "@js/routes/customer_route";
import deliveryRoute from "@js/routes/delivery_boy_route";
import installationRoute from "@js/routes/installation_route";
export function routeGroup(routes, prefix, name) {
    return routes.map(route => {
        return {
            path: prefix + route.path,
            component: route.component,
            name: route.name != null ? name + "." + route.name : undefined,
            children: route.children != null ? routeGroup(route.children, prefix, name) : undefined
            // name: route.name
        };
    });
}
const otherRoutes = [{
        path: "/", component: Landing, name: "any"

    }, {
        path: "/landing", component: Landing, name: "landing"
    },];
const allRoutes = [...adminRoute, ...sellerRoute, ...customerRoute, ...installationRoute, ...deliveryRoute, ...otherRoutes];
export default VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        else {
            return { top: 0 };
        }
    },
    routes: allRoutes
});
