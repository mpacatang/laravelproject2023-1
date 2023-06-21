import {IShop} from "./shop";
import {IIdentifierModel} from "@js/types/models/models";
import {IProduct} from "@js/types/models/product";

export interface IHomeBanner extends IIdentifierModel {
    active: boolean,
    image: string,
    product_id?: number,
    shop_id?: number,
    shop?: IShop,
    product?: IProduct,
}
