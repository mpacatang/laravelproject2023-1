import {IFormSelectOption, IIdentifierModel} from "@js/types/models/models";
import Zones from "@js/pages/admin/zones/index.vue";

export interface IZone extends IIdentifierModel {
    active: boolean;
    name: string,
    coordinates: any
}


export class Zone{
    static select_helper(): IFormSelectOption<IZone> {
        return {
            option: {
                label: 'name',
                value: 'id'
            },
        };
    }
}

