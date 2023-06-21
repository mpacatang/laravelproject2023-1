import {IShop} from "./shop";
import {IFormSelectOption, IIdentifierModel} from "@js/types/models/models";

export interface IAddon extends IIdentifierModel {
    name: string,
    description: string,
    price: number,
    image?: string,
    active: boolean,
    shop_id: number
    shop?: IShop,

}


export class Addon {

    static select_helper(): IFormSelectOption {
        return {
            option: {
                label: 'name',
                value: 'id'
            }
        };
    };
}
