import {IShop} from "./shop";
import {IIdentifierModel} from "@js/types/models/models";

export interface IFile extends IIdentifierModel {
    name: string,
    path: string,
    type: 'file' | 'folder',
    storage_path: string,
    url?: string,
}



