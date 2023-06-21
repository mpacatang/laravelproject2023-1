import {IIdentifierModel} from "@js/types/models/models";
import {IShop} from "@js/types/models/shop";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import {IOrder} from "@js/types/models/order";

export interface IShopRevenue extends IIdentifierModel {
    order_amount: number,
    tax: number,
    packaging_charge: number,
    delivery_charge: number,
    revenue: number,
    pay_to_admin: number,
    take_from_admin: number,
    pay_to_delivery_boy: number,
    take_from_delivery_boy: number,
    collected_payment_from_customer: number,
    shop_id: number,
    delivery_boy_id: number,
    shop_payout_id: number,
    order_id: number,
    shop?: IShop,
    shop_payout?: IShopPayout,
    delivery_boy?: IDeliveryBoy,
    order?: IOrder,
    created_at: string,
}

export interface IShopPayout extends IIdentifierModel {
    pay_to_shop: number,
    take_from_shop: number,
    till_date: string,
    created_at: string,
    shop_id: number,
    shop?: IShop
}

export interface IDeliveryBoyRevenue extends IIdentifierModel {
    revenue: number,
    pay_to_admin: number,
    take_from_admin: number,
    pay_to_shop: number,
    take_from_shop: number,
    order_id: number,
    created_at: string,
    delivery_boy_id: number,
    delivery_boy?: IDeliveryBoy,
    delivery_boy_payout_id?: number,
    delivery_boy_payout?: IDeliveryBoyPayout
}

export interface IDeliveryBoyPayout extends IIdentifierModel {
    pay_to_admin: number,
    take_from_admin: number,
    pay_to_shop: number,
    take_from_shop: number,
    till_date: string,
    created_at: string,
    delivery_boy_id: number,
    delivery_boy?: IDeliveryBoy,
}


export interface IAdminRevenue extends IIdentifierModel {
    revenue: number,
    order_id: number,
    created_at: string,
    delivery_charge: number,
    delivery_commission: number,
    order_commission: number,
    coupon_discount: number,
    payment_charge: number,
    collected_payment_from_customer: number,

}

