import {IFormSelectOption, IIdentifierModel} from "@js/types/models/models";
import {IModule} from "@js/types/models/module";

export interface ICategory extends IIdentifierModel {
    id: number,
    name: string,
    description: string,
    active: boolean,
    image?: string,
    module_id?: string,
    sub_categories: ISubCategory[],
    module?: IModule
}

export interface ISubCategory extends IIdentifierModel {
    id: number,
    name: string,
    category_id: number,
    active: boolean,
    category?: ICategory,
}

export class Category{

    static select_helper(): IFormSelectOption<ICategory> {
        return {
            option: {
                value: 'id',
                label: 'name'
            },
        };
    }
}

export class SubCategory{

    static select_helper(): IFormSelectOption {
        return {
            option: {
                value: 'id',
                label: 'name'
            },
            optgroup: {
                label: 'name',
                options: 'sub_categories'
            }
        };
    };
}
