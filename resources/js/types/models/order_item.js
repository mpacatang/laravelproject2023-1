class OrderItem {
    static getItemTotal(orderItem) {
        return orderItem.calculated_price * orderItem.quantity;
    }
}
export default OrderItem;
