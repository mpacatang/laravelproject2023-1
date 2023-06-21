class OrderPayment {
    static getStatusText(status) {
        if (status === 'unpaid') {
            return "Unpaid";
        }
        else if (status === 'paid') {
            return "Paid";
        }
        else {
            return "Other";
        }
    }
    static getTypeText(type) {
        if (type === 'cash_on_delivery') {
            return "Cash on Delivery";
        }
        else if (type === 'wallet') {
            return "Wallet";
        }
        else {
            return "Other";
        }
    }
}
export default OrderPayment;
