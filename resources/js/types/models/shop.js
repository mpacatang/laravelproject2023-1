import { Module } from "@js/types/models/module";
export class Shop {
    static select_helper() {
        return {
            option: {
                label: 'name', value: 'id',
            }
        };
    }
    ;
    static getMapPinFromShop(shop) {
        const module = this.getModule(shop);
        const name = module?.type ?? 'ecommerce';
        return `/assets/images/map/${name}_pin.png`;
    }
    static getModule(shop) {
        return shop.module ?? (Module.getModuleFromId(shop.module_id));
    }
    static canChangeShopTiming(shop) {
        return true;
        // const module =  this.getModule(shop);
        // return module.type=='food';
    }
    static getDays() {
        return ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    }
    static isFavorite(shop) {
        return shop.customer_favorite_shops != null && shop.customer_favorite_shops.length > 0;
    }
    static toggleFavorite(shop) {
        if (this.isFavorite(shop)) {
            shop.customer_favorite_shops = null;
        }
        else {
            shop.customer_favorite_shops ??= [];
            shop.customer_favorite_shops.push({
                id: -1, shop_id: shop.id, customer_id: -1
            });
        }
        return shop;
    }
    static getOwner(shop) {
        return shop.sellers?.find((s) => s.is_owner);
    }
    static calculateTaxFromOrder(shop, order) {
        if (shop.tax_type == 'percent') {
            return (shop.tax * order / 100);
        }
        return shop.tax;
    }
    static calculateAdminCommissionFromOrder(shop, order) {
        if (shop.admin_commission_type == 'percent') {
            return (shop.admin_commission * order / 100);
        }
        return shop.admin_commission;
    }
}
export class ShopTime {
    static formatShopTime(time) {
        if (time.open_at == null || time.close_at == null) {
            return time;
        }
        return {
            ...time, open_at: ShopTime.formatTime(time.open_at), close_at: ShopTime.formatTime(time.close_at),
        };
    }
    static formatTime(time) {
        time = time.toLowerCase();
        let meridian = time.includes("am") ? "am" : (time.includes('pm') ? 'pm' : null);
        if (meridian == null) {
            return time.trim();
        }
        time = time.replace(meridian, '').trim();
        let times = time.split(":");
        if (times.length > 0 && meridian == 'pm') {
            times[0] = (parseInt(times[0]) < 12 ? parseInt(times[0]) + 12 : times[0]).toString();
        }
        return times.join(":");
    }
}
