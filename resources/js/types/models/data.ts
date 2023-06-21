import {IModel} from "@js/types/models/models";
import {IPagination} from "@js/types/models/pagination";

export interface IData<T extends IModel> {
    data: T,
    meta?: IPagination
}


