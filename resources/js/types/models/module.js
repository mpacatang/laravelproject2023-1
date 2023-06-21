import { BusinessSetting } from "@js/types/models/business_setting";
export class Module {
    static instance = null;
    static init() {
        const cModules = BusinessSetting.getInstance()?.modules;
        if (cModules) {
            const modules = [{
                    id: null, type: 'all', title: 'all', active: true, image: ''
                }, ...cModules];
            this.instance = modules.map((module) => {
                return {
                    ...module, image: '/assets/images/module/' + module.type + ".png"
                };
            });
        }
    }
    static getCachedModules() {
        return this.instance ?? [];
    }
    static select_helper() {
        return {
            option: {
                label: 'title',
                value: 'id'
            },
        };
    }
    static getModuleFromId(id) {
        for (let cachedModule of this.getCachedModules()) {
            if (cachedModule.id == id)
                return cachedModule;
        }
        return this.getCachedModules()[0];
    }
    static canChangeProductStock(module) {
        return module.type != 'food';
    }
    static canChangeProductAddon(module) {
        return module.type == 'food';
    }
    static canChangeProductAvailability(module) {
        return module.type == 'food';
    }
    static canChangeProductPrescription(module) {
        return module.type == 'pharmacy';
    }
    static canChangeProductFoodType(module) {
        return module.type == 'food';
    }
}
