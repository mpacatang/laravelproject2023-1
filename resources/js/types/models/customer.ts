import {IFormSelectOption, IIdentifierModel, IModel} from "@js/types/models/models";
import i18n from "@js/services/i18n";

export type PaymentMethodTypes = 'referral' | 'from_admin';

export interface ICustomer extends IIdentifierModel {
    active: boolean,
    first_name: string,
    last_name: string,
    email: string,
    email_verified_at?: string,
    mobile_number: string,
    mobile_number_verified_at?: string,
    referral: string,
    avatar?: string,

}

export interface ICustomerWallet extends IIdentifierModel {
    balance: number,
    customer_id: number,
    transactions?: ICustomerWalletTransaction[]
}


export interface ICustomerWalletTransaction extends IIdentifierModel {
    added: boolean,
    amount: number,
    order_id: number,
    payment_method: PaymentMethodTypes,
    created_at: string,
    customer_wallet_id: number,
}

export interface ICustomerLoyaltyWallet extends IIdentifierModel {
    point: number,
    customer_id: number,
    transactions?: ICustomerLoyaltyTransaction[]
}


export interface ICustomerLoyaltyTransaction extends IIdentifierModel {
    added: boolean,
    point: number,
    order_id?: number,
    created_at: string,
    customer_loyalty_wallet_id: number,
}


export interface ICustomerPreference extends IModel{
    category_ids: number[],
    first_time: boolean
}


export class CustomerWalletTransaction {

    static getPaymentMethodText(transaction: ICustomerWalletTransaction): string {
        return i18n.global.t(transaction.payment_method ?? "-");
    }

}


export class Customer {
    static select_helper(): IFormSelectOption<ICustomer> {
        return {
            option: {
                value: 'id', label: function (customer) {
                    return customer.first_name + " " + customer.last_name + "  [" + customer.mobile_number + "]";
                }
            }
        };
    }
}
