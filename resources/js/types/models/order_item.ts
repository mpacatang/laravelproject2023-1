import {IProduct} from "./product";
import {IProductOption} from "./product_option";
import {IProductVariant} from "./product_variant";
import {IOrderItemAddon} from "./order_item_addon";
import {IIdentifierModel} from "@js/types/models/models";


export interface IOrderItem  extends IIdentifierModel {
    id: number,
    product_id: number,
    product_option_id: number,
    product_variant_id: number,
    order_id: number,
    quantity: number,
    price: number,
    calculated_price: number,
    discount: number,
    discount_type: string,
    product: IProduct,
    product_option: IProductOption,
    product_variant: IProductVariant,
    addons?: IOrderItemAddon[]
}


class OrderItem {


    static getItemTotal(orderItem: IOrderItem) {
        return orderItem.calculated_price * orderItem.quantity;
    }

}

export default OrderItem;
