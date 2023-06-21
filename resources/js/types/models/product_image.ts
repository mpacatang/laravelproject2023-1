import {IShop} from "./shop";
import {IIdentifierModel} from "@js/types/models/models";

export interface IProductImage extends IIdentifierModel {
    id: number,
    image: string,
    product_id: number
}


