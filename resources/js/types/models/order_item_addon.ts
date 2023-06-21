import {IAddon} from "./addon";
import {IOrderItem} from "./order_item";
import {IIdentifierModel} from "@js/types/models/models";


export interface IOrderItemAddon  extends IIdentifierModel {
    id: number,
    price: number,
    quantity: number,
    addon_id: number,
    order_item_id: number,
    addon: IAddon,
    order_item?: IOrderItem
}


export default class OrderItemAddon {

    static getAddonTotal(addon: IOrderItemAddon): number {
        return addon.price * addon.quantity;
    }
}
