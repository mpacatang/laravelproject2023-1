import {IFormSelectOption, IIdentifierModel} from "@js/types/models/models";
import {ISeller} from "@js/types/models/seller";
import {IProduct} from "@js/types/models/product";
import {IModule, Module} from "@js/types/models/module";
import {ChargesTypes} from "@js/types/custom_types";
import {IZone} from "@js/types/models/zone";
import {IShopPlan} from "@js/types/models/shop_plan";
import {ICustomerFavoriteShop} from "@js/types/models/customer_favorite";

export interface IShop extends IIdentifierModel {
    active: boolean,
    approved: boolean,
    archived: boolean,
    name: string,
    email: string,
    mobile_number: string,
    license_number: string,
    description: string,
    open: boolean,
    address: string,
    city: string,
    state: string,
    country: string,
    pincode: number,
    latitude: number,
    longitude: number,
    open_for_delivery: boolean,
    take_away: boolean,
    self_delivery: boolean,
    min_delivery_time: number,
    max_delivery_time: number,
    delivery_time_type: 'minutes' | 'hours' | 'days',
    module_id: number,
    delivery_range: number,
    min_order_amount: number,
    minimum_delivery_charge: number,
    rating: number,
    ratings_total: number,
    ratings_count: number,
    delivery_charge_multiplier: number,
    need_prescription: boolean,
    cover_image?: string,
    logo?: string,
    zone_id?: number,
    shop_plan_id?: number,
    packaging_charge: number,
    tax: number,
    tax_type: ChargesTypes,
    admin_commission: number,
    admin_commission_type: ChargesTypes,

    timings?: IShopTime[],
    sellers?: ISeller[],
    products?: IProduct[],
    module?: IModule,
    zone?: IZone,
    shop_plan?: IShopPlan,
    owner?: ISeller,
    customer_favorite_shops?: ICustomerFavoriteShop[]

}


export interface IShopTime {
    id: number,
    day: string,
    open_at: string,
    close_at: string
}


export class Shop {


    static select_helper(): IFormSelectOption<IShop> {
        return {
            option: {
                label: 'name', value: 'id',
            }
        };
    };

    static getMapPinFromShop(shop: IShop): string {
        const module = this.getModule(shop);
        const name = module?.type ?? 'ecommerce';
        return `/assets/images/map/${name}_pin.png`;
    }

    static getModule(shop: IShop): IModule {
        return shop.module ?? (Module.getModuleFromId(shop.module_id));
    }


    static canChangeShopTiming(shop: IShop): boolean {
        return true;
        // const module =  this.getModule(shop);
        // return module.type=='food';
    }


    static getDays() {
        return ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
    }

    static isFavorite(shop: IShop) {
        return shop.customer_favorite_shops != null && shop.customer_favorite_shops.length > 0
    }

    static toggleFavorite(shop: IShop) {
        if (this.isFavorite(shop)) {
            shop.customer_favorite_shops = null;
        } else {
            shop.customer_favorite_shops ??= [];
            shop.customer_favorite_shops.push({
                id: -1, shop_id: shop.id, customer_id: -1
            });
        }
        return shop;
    }

    static getOwner(shop: IShop): ISeller | null {
        return shop.sellers?.find((s) => s.is_owner);
    }

    static calculateTaxFromOrder(shop: IShop, order: number) {
        if (shop.tax_type == 'percent') {
            return (shop.tax * order / 100);
        }
        return shop.tax;
    }

    static calculateAdminCommissionFromOrder(shop: IShop, order: number) {
        if (shop.admin_commission_type == 'percent') {
            return (shop.admin_commission * order / 100);
        }
        return shop.admin_commission;
    }
}

export class ShopTime{

    public static formatShopTime(time: IShopTime): IShopTime {
        if (time.open_at == null || time.close_at == null) {
            return time;
        }
        return {
            ...time, open_at: ShopTime.formatTime(time.open_at), close_at: ShopTime.formatTime(time.close_at),
        }
    }

    private static formatTime(time: string): string {
        time = time.toLowerCase();

        let meridian = time.includes("am") ? "am" : (time.includes('pm') ? 'pm' : null);
        if (meridian == null) {
            return time.trim();
        }
        time = time.replace(meridian, '').trim();

        let times = time.split(":");
        if (times.length > 0 && meridian == 'pm') {
            times[0] = (parseInt(times[0])<12?parseInt(times[0])+12:times[0]).toString();
        }

        return times.join(":");
    }


}
