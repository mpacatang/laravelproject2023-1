import {IIdentifierModel} from "@js/types/models/models";

export interface ICustomerFavoriteProduct extends IIdentifierModel {

    customer_id: number,
    product_id: number

}

export interface ICustomerFavoriteShop extends IIdentifierModel {
    customer_id: number,
    shop_id: number

}
