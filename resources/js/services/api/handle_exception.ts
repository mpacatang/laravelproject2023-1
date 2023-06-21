import Response from "./response";

import {ToastNotification} from "../toast_notification";
import {getUserTypeFromUrl} from "@js/services/state/state_helper";
import {useAdminDataStore, useCustomerDataStore, useSellerDataStore} from "@js/services/state/states";
import NavigatorService from "@js/services/navigator_service";

export function handleException(error) {
    let router = NavigatorService.router;
    let route = router.currentRoute.value;
    const role = getUserTypeFromUrl(route.fullPath) ?? 'customer';
    if (Response.isUnauthenticated(error)) {
        const nRoute = `${role}.login`;
        if (role == 'admin') {
            useAdminDataStore().logout();
        } else if (role == 'seller') {
            useSellerDataStore().logout();
        } else if (role == 'customer') {
            useCustomerDataStore().logout();
        }
        if (route.name !== nRoute) {
            let fn = router.replace;
            if(role== 'customer'){
                fn = router.push
            }
            fn({name: `${role}.login`, query: {redirectTo: route.fullPath}})
        }
        return true;
    } else if (Response.isNotFound(error)) {
        router.push({name: `${role}.errors.not_found`})
        return true;
    } else if (Response.isForbidden(error)) {
        router.push({name: `${role}.errors.forbidden`})
        return true;
    } else if (Response.hasNotShop(error)) {
        router.push({name: `${role}.errors.has_not_shop`})
        return true;
    } else if (Response.isInactive(error)) {
        router.push({name: `${role}.errors.inactive`})
        return true;
    } else if (Response.isShopNotActive(error)) {
        router.push({name: `${role}.errors.shop_not_active`})
        return true;
    } else if (Response.isMobileNotVerified(error)) {
        router.push({name: `${role}.verification`})
        return true;
    } else if (Response.isEmailNotVerified(error)) {
        router.push({name: `${role}.verification`})
        return true;
    } else if (Response.isSelfDeliveryNotActive(error)) {
        router.push({name: `${role}.errors.self_delivery_not_active`})
        return true;
    } else if (Response.isBadRequest(error)) {
        ToastNotification.error(error.response.data.error)
    } else {
        ToastNotification.error("Something wrong")
    }


}
