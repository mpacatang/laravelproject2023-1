import i18n from "@js/services/i18n";
export class CustomerWalletTransaction {
    static getPaymentMethodText(transaction) {
        return i18n.global.t(transaction.payment_method ?? "-");
    }
}
export class Customer {
    static select_helper() {
        return {
            option: {
                value: 'id', label: function (customer) {
                    return customer.first_name + " " + customer.last_name + "  [" + customer.mobile_number + "]";
                }
            }
        };
    }
}
