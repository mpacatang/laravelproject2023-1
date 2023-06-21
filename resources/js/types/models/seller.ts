import {IFormSelectOption, IIdentifierModel} from "@js/types/models/models";
import {IShop} from "@js/types/models/shop";
import {sellerPermissions} from "@js/services/permissions";

export interface ISeller extends IIdentifierModel {
    active: boolean,
    first_name: string,
    last_name: string,
    avatar?: string,
    email: string,
    mobile_number: string,
    password?: string,
    is_owner: boolean,
    shop_id?: number,
    role_id?: number,
    bank_name?: string,
    account_number?: string,
    role?: ISellerRole,
    shop?: IShop
}


export interface ISellerRole extends IIdentifierModel {
    id: number,
    active: boolean,
    title: string,
    permissions: string,
    shop_id: number,
    shop?: IShop

}

export class Seller {
    static select_helper(): IFormSelectOption<ISeller> {
        return {
            option: {
                label: (item) => item.first_name + " " + item.last_name, value: 'id'
            },
        };
    }
}

export class SellerRole {

    static select_helper(): IFormSelectOption<ISellerRole> {
        return {
            option: {
                value: 'id', label: 'title'
            },
        };
    }

    static allRoles = [{
        'name': sellerPermissions.products, 'value': 'products'
    },{
        'name': sellerPermissions.report, 'value': 'report'
    }, {
        'name': sellerPermissions.shops, 'value': 'shops'
    }, {
        'name': sellerPermissions.delivery_boys, 'value': 'delivery boys'
    }, {
        'name': sellerPermissions.orders, 'value': 'orders'
    }, {
        'name': sellerPermissions.pos, 'value': 'pos'
    }, {
        'name': sellerPermissions.addons, 'value': 'addons'
    }, {
        'name': sellerPermissions.revenues, 'value': 'revenues'
    }, {
        'name': sellerPermissions.employees, 'value': 'employees'
    }, {
        'name': sellerPermissions.dashboard, 'value': 'dashboard'
    }, {
        'name': sellerPermissions.reviews, 'value': 'reviews'
    }, {
        'name': sellerPermissions.categories, 'value': 'categories'
    }, {
        'name': sellerPermissions.roles, 'value': 'roles'
    },];

    static _getSelectableRoles = SellerRole.allRoles.map(function (role) {
        return {
            ...role, 'active': false
        }
    });

    static getSelectableRoles(selectedPermissions: string[]) {
        let permissions = JSON.parse(JSON.stringify(this._getSelectableRoles));

        for (const permission of permissions) {
            for (const selectedPermission of selectedPermissions) {
                if (permission.value === selectedPermission) {
                    permission.active = true;
                    break;
                }
            }
        }
        return permissions;
    }
}
