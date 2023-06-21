import {IIdentifierModel} from "@js/types/models/models";

export interface IUser extends IIdentifierModel {
    first_name: string,
    last_name: string,
    email: string,
    mobile_number: string,
    avatar?: string,
}
