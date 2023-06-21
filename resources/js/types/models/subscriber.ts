import {IShop} from "./shop";
import {IIdentifierModel} from "@js/types/models/models";

export interface ISubscriber extends IIdentifierModel {
    email: string,
    created_at: string

}
