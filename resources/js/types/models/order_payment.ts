import {IIdentifierModel} from "@js/types/models/models";



export type IOrderPaymentTypes =
    'cash_on_delivery'
    | 'wallet' | 'card' | 'cash';

export type IOrderStatuses =
    'unpaid'
    | 'paid';


export interface IOrderPayment extends IIdentifierModel {
    id: number,
    payment_type: IOrderPaymentTypes,
    payment_status: IOrderStatuses,
    total_payment: number,
    payment: number
    order_id: number

}


class OrderPayment {

    static getStatusText(status: IOrderStatuses) {
        if (status === 'unpaid') {
            return "Unpaid";
        } else if (status === 'paid') {
            return "Paid";
        } else {
            return "Other";
        }
    }

    static getTypeText(type: IOrderPaymentTypes) {
        if (type === 'cash_on_delivery') {
            return "Cash on Delivery";
        } else if (type === 'wallet') {
            return "Wallet";
        } else {
            return "Other";
        }
    }


}

export default OrderPayment;
