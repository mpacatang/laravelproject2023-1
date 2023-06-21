import {IIdentifierModel} from "@js/types/models/models";
import i18n from '@js/services/i18n';

export type IOrderStatusesType =
    'place_order'
    | 'payment_done'
    | 'cancel_by_customer'
    | 'cancel_by_shop'
    | 'accepted'
    | 'reject'
    | 'resubmit'
    | 'waiting'
    | 'assign_delivery_boy'
    | 'accept_for_delivery'
    | 'reject_for_delivery'
    | 'order_ready'
    | 'on_the_way'
    | 'delivered'
    | 'reviewed';


export interface IOrderStatus extends IIdentifierModel {

    id: number,
    order_id: number,
    description: string,
    status: IOrderStatusesType,
    created_at: Date

}


class OrderStatus {


    /**
     * @deprecated since version 2.0.0
     */
    static getStatusText(status: IOrderStatusesType) {

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


    static canAssignForDelivery(status: IOrderStatusesType) {
        return status === "accepted" || status == 'reject_for_delivery' || status == 'assign_delivery_boy';
    }


    static canDeliver(status: IOrderStatusesType) {
        return status === 'order_ready';
    }

    static canWaitForDeliveryConfirmation(status: IOrderStatusesType) {
        return status === 'assign_delivery_boy';
    }

    static canWaitForDelivery(status: IOrderStatusesType) {
        return status === 'accept_for_delivery';
    }

    static canSetEstimateTime(status: IOrderStatusesType) {
        return status === 'accepted' || status === 'reject_for_delivery' || status == 'assign_delivery_boy' || status == 'accept_for_delivery';
    }

    static isCancelled(status: IOrderStatusesType) {
        return status === 'cancel_by_shop' || status === 'cancel_by_customer';
    }


}

export default OrderStatus;
