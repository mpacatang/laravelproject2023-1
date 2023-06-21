import {IIdentifierModel} from "@js/types/models/models";
import {IAddon} from "@js/types/models/addon";
import {ICart} from "@js/types/models/cart";

export interface ICartAddon extends IIdentifierModel {
    id: number,
    addon_id: number,
    cart_id: number,
    quantity: number,
    addon?: IAddon,
    cart?: ICart
}

