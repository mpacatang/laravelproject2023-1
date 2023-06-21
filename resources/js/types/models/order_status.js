import i18n from '@js/services/i18n';
class OrderStatus {
    /**
     * @deprecated since version 2.0.0
     */
    static getStatusText(status) {
        switch (status) {
            case "place_order":
                break;
            case "payment_done":
                break;
            case "cancel_by_customer":
                break;
            case "cancel_by_shop":
                break;
            case "accepted":
                break;
            case "reject":
                break;
            case "resubmit":
                break;
            case "waiting":
                break;
            case "assign_delivery_boy":
                break;
            case "accept_for_delivery":
                break;
            case "reject_for_delivery":
                break;
            case "order_ready":
                break;
            case "on_the_way":
                break;
            case "delivered":
                break;
            case "reviewed":
                break;
            default:
                return status;
        }
        return i18n.global.t(status);
    }
    // static canReadyOrder(status) {
    //     return status === this.;
    // }
    static canAssignForDelivery(status) {
        return status === "accepted" || status == 'reject_for_delivery' || status == 'assign_delivery_boy';
    }
    static canDeliver(status) {
        return status === 'order_ready';
    }
    static canWaitForDeliveryConfirmation(status) {
        return status === 'assign_delivery_boy';
    }
    static canWaitForDelivery(status) {
        return status === 'accept_for_delivery';
    }
    static canSetEstimateTime(status) {
        return status === 'accepted' || status === 'reject_for_delivery' || status == 'assign_delivery_boy' || status == 'accept_for_delivery';
    }
    static isCancelled(status) {
        return status === 'cancel_by_shop' || status === 'cancel_by_customer';
    }
}
export default OrderStatus;
