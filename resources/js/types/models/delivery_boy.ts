import {IShop} from "./shop";
import {IFormSelectOption, IIdentifierModel, ILatLng} from "@js/types/models/models";
import {DeliveryBoyIdentityTypes} from "@js/types/custom_types";
import {IZone} from "@js/types/models/zone";

export interface IDeliveryBoy extends IIdentifierModel, ILatLng {
    id: number,
    active: boolean,
    approved: boolean,
    archived: boolean,

    first_name: string,
    last_name: string,
    mobile_number: string,
    identity_number: string,
    vehicle_number: string,
    identity_type: DeliveryBoyIdentityTypes,
    email: string,
    password?: string,
    shop_id?: number,
    zone_id?: number,
    avatar?: string,
    identity_image?: string,
    salary_based: boolean,
    bank_name?: string,
    account_number?: string,
    active_for_delivery: boolean,
    shop?: IShop,
    zone?: IZone
}


export class DeliveryBoy {

    static select_helper(): IFormSelectOption<IDeliveryBoy> {
        return {
            option: {
                value: 'id', label: function (delivery_boy) {
                    return delivery_boy.first_name + " " + delivery_boy.last_name + " [" + delivery_boy.mobile_number + "]";
                }
            }
        };
    }
}
