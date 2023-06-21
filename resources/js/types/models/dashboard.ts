import {IProduct} from "./product";
import {IProductOption} from "./product_option";
import {IProductVariant} from "./product_variant";
import {IIdentifierModel, IModel} from "@js/types/models/models";
import {IShop} from "@js/types/models/shop";
import {IAddon} from "@js/types/models/addon";
import {ICart} from "@js/types/models/cart";
import {IShopPlan} from "@js/types/models/shop_plan";
import {IShopReview} from "@js/types/models/review";

export interface IAdminDashboard extends IModel {
    total_revenue: number,
    order_count: number,
    shop_count: number,
    customer_count: number,
    revenues: {
        date: string, revenue: number
    }[],
    shop_plan_data: {
        shop_plan: IShopPlan, count: number
    }[],
    category_product_popular_data: IProduct[],
    most_rated_product_data: IProduct[]
}


export interface ISellerDashboard extends IModel {
    total_revenue: number,
    order_count: number,
    product_count: number,
    shop_plan: IShopPlan,
    revenues: {
        date: string, revenue: number
    }[],
    product_selling_data: IProduct[],
    product_rated_data: IProduct[],
    review_data: IShopReview[]
}

