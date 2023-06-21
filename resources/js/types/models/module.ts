import {IFormSelectOption, IIdentifierModel} from "@js/types/models/models";
import {ModuleTypes} from "@js/types/custom_types";
import {BusinessSetting} from "@js/types/models/business_setting";

export interface IModule extends IIdentifierModel {
    type: ModuleTypes,
    active: boolean,
    title: string,
    image: string,
    products_count?: number,
    shops_count?: number
}


export class Module {


    static instance: IModule[] = null;

    static init() {

        const cModules = BusinessSetting.getInstance()?.modules;
        if (cModules) {

            const modules: IModule[] = [{
                id: null, type: 'all', title: 'all', active: true, image: ''
            }, ...cModules];
            this.instance = modules.map((module) => {
                return {
                    ...module, image: '/assets/images/module/' + module.type + ".png"
                }
            })
        }
    }


    static getCachedModules(): IModule[] {
        return this.instance ?? [];
    }

    static select_helper():IFormSelectOption<IModule> {
        return {
            option: {
                label: 'title',
                value: 'id'
            },
        };
    }


    static getModuleFromId(id?: number) {
        for (let cachedModule of this.getCachedModules()) {
            if (cachedModule.id == id) return cachedModule;
        }
        return this.getCachedModules()[0];
    }

    static canChangeProductStock(module: IModule): boolean {
        return module.type != 'food';
    }

    static canChangeProductAddon(module: IModule): boolean {
        return module.type == 'food';
    }

    static canChangeProductAvailability(module: IModule): boolean {
        return module.type == 'food';
    }

    static canChangeProductPrescription(module: IModule): boolean {
        return module.type == 'pharmacy';
    }

    static canChangeProductFoodType(module: IModule): boolean {
        return module.type == 'food';
    }


}
