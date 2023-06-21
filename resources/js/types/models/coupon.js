export class Coupon {
    static getDiscountFromOrder(coupon, order) {
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
