export default class OrderItemAddon {
    static getAddonTotal(addon) {
        return addon.price * addon.quantity;
    }
}
