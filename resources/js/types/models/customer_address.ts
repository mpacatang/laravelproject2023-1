import {IIdentifierModel} from "@js/types/models/models";

export interface ICustomerAddress extends IIdentifierModel {
    type: string,
    address: string,
    selected: boolean,
    landmark: string,
    city: string,
    pincode: number,
    longitude: number,
    latitude: number,
    customer_id: number,
}
