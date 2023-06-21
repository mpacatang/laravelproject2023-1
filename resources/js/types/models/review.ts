import {IIdentifierModel} from "@js/types/models/models";
import {IDeliveryBoy} from "@js/types/models/delivery_boy";
import {ICustomer} from "@js/types/models/customer";
import {IOrder} from "@js/types/models/order";
import {IShop} from "@js/types/models/shop";
import {IProduct} from "@js/types/models/product";

export interface IDeliveryBoyReview extends IIdentifierModel {
    order_id: number,
    delivery_boy_id: number,
    customer_id: number,
    rating: number,
    review: string,
    delivery_boy?: IDeliveryBoy,
    updated_at: string
    customer?: ICustomer,
    order?: IOrder,

}

export interface IShopReview extends IIdentifierModel {
    shop_id: number,
    customer_id: number,
    rating: number,
    review: string,
    shop?: IShop,
    customer?: ICustomer,
    updated_at: string,

}

export interface IProductReview extends IIdentifierModel {

    customer_id: number,
    product_id: number,
    order_id: number,
    shop_id: number,
    product_option_id: number,
    rating: number,
    review?: String,
    updatedAt?: string,
    customer?: ICustomer,
    product?: IProduct,
    updated_at: string,

}
