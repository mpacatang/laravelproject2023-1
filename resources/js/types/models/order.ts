import {IOrderItem} from "./order_item";
import {IOrderPayment, IOrderStatuses} from "./order_payment";
import {ICustomerAddress} from "./customer_address";
import OrderStatus, {IOrderStatus} from "./order_status";
import {IOrderMedia} from "./order_media";
import {ICustomer} from "./customer";
import {IIdentifierModel} from "@js/types/models/models";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import {IShop} from "@js/types/models/shop";
import i18n from "@js/services/i18n";

export type IOrderTypes = 'self_pickup' | 'delivery' | 'pos';


export interface IOrder extends IIdentifierModel {
    id: number,
    shop_id: number,
    order_type: IOrderTypes,
    otp: string,
    invoice_otp: string,
    order_amount: number,
    tax: number,
    packaging_charge: number,
    order_commission: number,
    delivery_charge: number,
    delivery_commission: number,
    coupon_discount: number,
    payment_charge: number,
    total: number,
    notes: string,
    assign_delivery_boy_id?: number,
    delivery_boy_id?: number,
    ready_at: Date,
    created_at: Date,
    items?: IOrderItem[],
    payments: IOrderPayment[],
    address?: ICustomerAddress,
    statuses: IOrderStatus[],
    medias?: IOrderMedia[],
    customer: ICustomer,
    assign_delivery_boy?: IDeliveryBoy,
    delivery_boy?: IDeliveryBoy,
    shop?: IShop,
    complete: boolean


}


class Order {



    static getStatusText(order: IOrder) {
        return OrderStatus.getStatusText(this.getLastStatus(order).status);
        // if (status === 'place_order') {
        //     return "Place order";
        // }
    }


    static getTypeText(order: IOrder) {
        return i18n.global.t(order.order_type);

    }

    static getPaymentStatusText(status) {
        if (status === 'unpaid') {
            return "Unpaid";
        } else if (status === 'paid') {
            return "Paid";
        } else {
            return "Other";
        }
    }

    static getPaymentText(order: IOrder) {
        let payment = order.payments.at(order.payments.length - 1).payment_type
        if (payment === 'cash_on_delivery') {
            return "Cash on Delivery";
        } else if (payment === 'wallet') {
            return 'Wallet';
        } else if (payment === 'cash') {
            return 'Cash';
        } else if (payment === 'card') {
            return 'Card';
        }
    }

    static getTotalCharges(order: IOrder) {
        return order.order_commission + order.delivery_commission + order.payment_charge + order.tax + order.packaging_charge;
    }

    static getPaymentTextFromOrder(order: IOrder) {
        return this.getPaymentText(order);
    }

    static getPaymentStatusTextFromOrder(order: IOrder) {
        return this.getPaymentStatusText(order.payments.at(order.payments.length - 1).payment_status);
    }

    static getLastStatus(order: IOrder) {
        return order.statuses[order.statuses.length - 1];
    }

    static getPaymentStatusFromOrder(order: IOrder): IOrderStatuses {
        return (order.payments.at(order.payments.length - 1).payment_status);
    }

    static isPOS(order: IOrder) {
        return order.order_type === 'pos';
    }

    static isSelfPickup(order: IOrder) {
        return order.order_type === 'self_pickup';
    }

    static isDelivery(order: IOrder) {
        return order.order_type === 'delivery';
    }

    static isUnpaid(order: IOrder) {
        return Order.getPaymentStatusFromOrder(order) === 'unpaid'
    }

    static canCustomerCancel(order: IOrder) {
        let status = order.statuses[order.statuses.length - 1].status;
        // let payment = order.payments[order.payments.length - 1];
        // if (payment.payment_status == 'unpaid' && payment.payment_type == 'wallet') {
        //     return false;
        // }
        return status === 'place_order' || status === 'resubmit' || status === 'payment_done' || status === 'reject';
    }

    static isCancelled(order: IOrder) {
        let status = this.getLastStatus(order).status;
        return status === 'cancel_by_shop' || status === 'cancel_by_customer';
    }

    static needToPay(order: IOrder) {
        let status = order.statuses[order.statuses.length - 1].status;
        let payment = order.payments[order.payments.length - 1];
        return (payment.payment_status == 'unpaid' && payment.payment_type == 'wallet') && !OrderStatus.isCancelled(status);
    }


    static canWaitForPayment(order: IOrder) {
        let payment = order.payments[order.payments.length - 1];
        return payment.payment_status == 'unpaid' && payment.payment_type == 'wallet';
    }

    static canAccept(order: IOrder) {
        let status = order.statuses[order.statuses.length - 1].status;
        let payment = order.payments[order.payments.length - 1];
        if (payment.payment_status == 'unpaid' && payment.payment_type == 'wallet') {
            return false;
        }
        return status === 'place_order' || status === 'resubmit' || status === 'payment_done';
    }

    static canWaitResubmit(order: IOrder) {
        return Order.getLastStatus(order).status === 'reject'
    }

    static canAssignForOtherDelivery(order: IOrder) {
        let status = Order.getLastStatus(order).status;
        return Order.isDelivery(order) && (status === "accepted" || status == 'reject_for_delivery' || status == 'assign_delivery_boy' || status == 'order_ready')
    }

    static canAssignForDelivery(order: IOrder) {
        return this.canAssignForOtherDelivery(order) && order.assign_delivery_boy_id == null;
    }

    static canReadyForDelivery(order: IOrder) {
        return Order.canAssignForDelivery(order) && order.delivery_boy_id != null
    }

    static canOrderReady(order: IOrder) {
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

    static canOrderDeliver(order: IOrder) {
        let status = this.getLastStatus(order).status;
        return (this.isPOS(order) && status === 'order_ready') || (
            this.isSelfPickup(order) && status === 'order_ready');
    }

    static canWaitForDeliveryConfirmation(order: IOrder) {
        let status = this.getLastStatus(order).status;
        return status === 'assign_delivery_boy';
    }

    static canWaitForDeliveryBoy(order: IOrder) {
        let status = this.getLastStatus(order).status;
        return status === 'accept_for_delivery';
    }

    static canSetEstimateTime(order: IOrder) {
        let status = this.getLastStatus(order).status;
        return (status === 'accepted' || status === 'reject_for_delivery' || status == 'assign_delivery_boy' || status == 'accept_for_delivery') && order.statuses.find((status) => status.status == 'order_ready') == null;
    }

    static canWaitForDeliverOrder(order: IOrder) {
        let status = this.getLastStatus(order).status;
        return status === 'on_the_way';
    }


    static canCustomerReview(order: IOrder) {
        let status = this.getLastStatus(order).status;
        return status === 'delivered' || status === 'reviewed';

    }
}

export default Order;
