import OrderStatus from "./order_status";
import i18n from "@js/services/i18n";
class Order {
    static getStatusText(order) {
        return OrderStatus.getStatusText(this.getLastStatus(order).status);
        // if (status === 'place_order') {
        //     return "Place order";
        // }
    }
    static getTypeText(order) {
        return i18n.global.t(order.order_type);
    }
    static getPaymentStatusText(status) {
        if (status === 'unpaid') {
            return "Unpaid";
        }
        else if (status === 'paid') {
            return "Paid";
        }
        else {
            return "Other";
        }
    }
    static getPaymentText(order) {
        let payment = order.payments.at(order.payments.length - 1).payment_type;
        if (payment === 'cash_on_delivery') {
            return "Cash on Delivery";
        }
        else if (payment === 'wallet') {
            return 'Wallet';
        }
        else if (payment === 'cash') {
            return 'Cash';
        }
        else if (payment === 'card') {
            return 'Card';
        }
    }
    static getTotalCharges(order) {
        return order.order_commission + order.delivery_commission + order.payment_charge + order.tax + order.packaging_charge;
    }
    static getPaymentTextFromOrder(order) {
        return this.getPaymentText(order);
    }
    static getPaymentStatusTextFromOrder(order) {
        return this.getPaymentStatusText(order.payments.at(order.payments.length - 1).payment_status);
    }
    static getLastStatus(order) {
        return order.statuses[order.statuses.length - 1];
    }
    static getPaymentStatusFromOrder(order) {
        return (order.payments.at(order.payments.length - 1).payment_status);
    }
    static isPOS(order) {
        return order.order_type === 'pos';
    }
    static isSelfPickup(order) {
        return order.order_type === 'self_pickup';
    }
    static isDelivery(order) {
        return order.order_type === 'delivery';
    }
    static isUnpaid(order) {
        return Order.getPaymentStatusFromOrder(order) === 'unpaid';
    }
    static canCustomerCancel(order) {
        let status = order.statuses[order.statuses.length - 1].status;
        // let payment = order.payments[order.payments.length - 1];
        // if (payment.payment_status == 'unpaid' && payment.payment_type == 'wallet') {
        //     return false;
        // }
        return status === 'place_order' || status === 'resubmit' || status === 'payment_done' || status === 'reject';
    }
    static isCancelled(order) {
        let status = this.getLastStatus(order).status;
        return status === 'cancel_by_shop' || status === 'cancel_by_customer';
    }
    static needToPay(order) {
        let status = order.statuses[order.statuses.length - 1].status;
        let payment = order.payments[order.payments.length - 1];
        return (payment.payment_status == 'unpaid' && payment.payment_type == 'wallet') && !OrderStatus.isCancelled(status);
    }
    static canWaitForPayment(order) {
        let payment = order.payments[order.payments.length - 1];
        return payment.payment_status == 'unpaid' && payment.payment_type == 'wallet';
    }
    static canAccept(order) {
        let status = order.statuses[order.statuses.length - 1].status;
        let payment = order.payments[order.payments.length - 1];
        if (payment.payment_status == 'unpaid' && payment.payment_type == 'wallet') {
            return false;
        }
        return status === 'place_order' || status === 'resubmit' || status === 'payment_done';
    }
    static canWaitResubmit(order) {
        return Order.getLastStatus(order).status === 'reject';
    }
    static canAssignForOtherDelivery(order) {
        let status = Order.getLastStatus(order).status;
        return Order.isDelivery(order) && (status === "accepted" || status == 'reject_for_delivery' || status == 'assign_delivery_boy' || status == 'order_ready');
    }
    static canAssignForDelivery(order) {
        return this.canAssignForOtherDelivery(order) && order.assign_delivery_boy_id == null;
    }
    static canReadyForDelivery(order) {
        return Order.canAssignForDelivery(order) && order.delivery_boy_id != null;
    }
    static canOrderReady(order) {
        let status = this.getLastStatus(order).status;
        return (this.isPOS(order) &&
            order.statuses.find((status) => status.status == 'order_ready') == null
            || (this.isSelfPickup(order) &&
                order.statuses.find((status) => status.status == 'accepted') != null &&
                status != 'reviewed' &&
                status != 'delivered' &&
                status != 'order_ready')
            || (this.isDelivery(order) &&
                order.statuses.find((status) => status.status == 'order_ready') == null
                && !this.canAccept(order) && !this.canWaitResubmit(order) && !order.complete));
    }
    static canOrderDeliver(order) {
        let status = this.getLastStatus(order).status;
        return (this.isPOS(order) && status === 'order_ready') || (this.isSelfPickup(order) && status === 'order_ready');
    }
    static canWaitForDeliveryConfirmation(order) {
        let status = this.getLastStatus(order).status;
        return status === 'assign_delivery_boy';
    }
    static canWaitForDeliveryBoy(order) {
        let status = this.getLastStatus(order).status;
        return status === 'accept_for_delivery';
    }
    static canSetEstimateTime(order) {
        let status = this.getLastStatus(order).status;
        return (status === 'accepted' || status === 'reject_for_delivery' || status == 'assign_delivery_boy' || status == 'accept_for_delivery') && order.statuses.find((status) => status.status == 'order_ready') == null;
    }
    static canWaitForDeliverOrder(order) {
        let status = this.getLastStatus(order).status;
        return status === 'on_the_way';
    }
    static canCustomerReview(order) {
        let status = this.getLastStatus(order).status;
        return status === 'delivered' || status === 'reviewed';
    }
}
export default Order;
