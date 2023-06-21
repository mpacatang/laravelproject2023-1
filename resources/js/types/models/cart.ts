import {IProduct} from "./product";
import {IProductOption} from "./product_option";
import {IProductVariant} from "./product_variant";
import {IIdentifierModel} from "@js/types/models/models";
import {IShop} from "@js/types/models/shop";
import {ICartAddon} from "@js/types/models/cart_addon";

export interface ICart extends IIdentifierModel {
    id: number,
    product_id: number,
    product_option_id: number,
    product_variant_id: number,
    quantity: number,
    product: IProduct,
    product_option: IProductOption,
    product_variant: IProductVariant,
    shop?: IShop,
    shop_id: number,
    addons?: ICartAddon[]
}

export interface IShopCart {
    shop?: IShop,
    carts?: ICart[]
}

export class Cart {

    static calculateTotal(cart: ICart) {
        return (cart.product_option.calculated_price * cart.quantity) + this.calculateAddonTotal(cart);
    }

    static calculateAddonTotal(cart: ICart) {
        let total = 0;
        for (const addon of cart.addons) {
            if (addon.addon != null) {
                total += addon.quantity * addon.addon.price;
            }
        }
        return total;
    }
}


export class ShopCart {

    static fromCarts(carts: ICart[]) {
        let list = [];
        for (const cart of carts) {
            let shopCart = ShopCart._findInList(list, cart);
            if (shopCart == null) {
                shopCart = {
                    shop: cart.shop, carts: []
                };
                list.push(shopCart);
            }
            shopCart.carts.push(cart);
        }
        return list;
    }

    private static _findInList(list: IShopCart[], cart: ICart) {
        for (const shopCart of list) {
            if (shopCart.shop.id == cart.shop_id) {
                return shopCart;
            }
        }
        return null;
    }
}


