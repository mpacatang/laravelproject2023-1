import {IShop} from "./shop";
import {IIdentifierModel} from "@js/types/models/models";
import {ChargesTypes} from "@js/types/custom_types";

export interface ICoupon extends IIdentifierModel {
    active: boolean
    code: string,
    description: string,
    discount?: number,
    discount_type?: ChargesTypes,
    min_order?: number,
    max_discount?: number,
    delivery_free?: boolean,
    limit?: number,
    first_order: boolean,
    shop_id?: number,
    zone_id?: number,
    module_id?: number,
    shop?: IShop,
    started_at: Date,
    expired_at: Date,
}


export class Coupon {

    static getDiscountFromOrder(coupon: ICoupon, order: number) {
        if (order < coupon.min_order) {
            return 0;
        }
        if (coupon.discount_type == 'percent') {
            let value = (order * coupon.discount / 100);
            if (value > coupon.max_discount) {
                return coupon.max_discount;
            }
            return value;

        }
        return coupon.discount;
    }
}
