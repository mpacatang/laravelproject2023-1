export class Cart {
    static calculateTotal(cart) {
        return (cart.product_option.calculated_price * cart.quantity) + this.calculateAddonTotal(cart);
    }
    static calculateAddonTotal(cart) {
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
    static fromCarts(carts) {
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
    static _findInList(list, cart) {
        for (const shopCart of list) {
            if (shopCart.shop.id == cart.shop_id) {
                return shopCart;
            }
        }
        return null;
    }
}
