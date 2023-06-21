import {IIdentifierModel} from "@js/types/models/models";
import {ISeller} from "@js/types/models/seller";
import {IProduct} from "@js/types/models/product";
import {IModule} from "@js/types/models/module";
import {ChargesTypes} from "@js/types/custom_types";
import {IShop} from "@js/types/models/shop";

export interface IShopPlan extends IIdentifierModel {
    active: boolean,
    title: string,
    sub_title: string,
    price: number,
    products: number,
    admin_commission: number,
    admin_commission_type: ChargesTypes,

}


export interface IShopPlanHistory extends IIdentifierModel {
    started_at: string,
    ended_at?: string,
    shop_plan_id: number,
    price: number
    shop_id: number,
    shop?: IShop,
    shop_plan?: IShopPlan,
    duration?: number
}

