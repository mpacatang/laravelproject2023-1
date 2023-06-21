import {IShop} from "./shop";
import {IProductImage} from "./product_image";
import {IIdentifierModel} from "@js/types/models/models";
import {ChargesTypes} from "@js/types/custom_types";

export interface IProductOption extends IIdentifierModel {
    id: number,
    sku: string,
    barcode: string,
    stock: number,
    price: number,
    discount: number,
    calculated_price: number,
    discount_type: ChargesTypes,
    option_value: string,
    product_id: string

}
