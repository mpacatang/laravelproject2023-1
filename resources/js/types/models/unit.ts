import {IFormSelectOption, IIdentifierModel} from "@js/types/models/models";

export interface IUnit extends IIdentifierModel {
    active: boolean,
    type: string,
    title: string,
    description: string,
}


export class Unit{
    static select_helper(): IFormSelectOption {
        return {
            option: {
                label: 'title',
                value: 'id'
            }
        };
    }
}
