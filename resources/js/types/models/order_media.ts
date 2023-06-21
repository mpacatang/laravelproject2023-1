import {IIdentifierModel} from "@js/types/models/models";

export interface IOrderMedia  extends IIdentifierModel {
    id: number,
    order_id: number,
    media: string,
}

