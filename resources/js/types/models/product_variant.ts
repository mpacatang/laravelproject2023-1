import {IProduct} from "./product";
import {IIdentifierModel} from "@js/types/models/models";

export interface IProductVariant extends IIdentifierModel {
    id: number,
    products: IProduct[]
}

