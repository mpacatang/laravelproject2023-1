import { Module } from "@js/types/models/module";
export class BusinessSetting {
    static instance = null;
    static getInstance() {
        if (this.instance != null) {
            let bs = this.instance;
            return {
                ...bs,
                minimum_delivery_charge: parseFloat(bs.minimum_delivery_charge?.toString()),
                delivery_charge_multiplier: parseFloat(bs.delivery_charge_multiplier?.toString()),
            };
        }
        return null;
    }
    static init() {
        const bs = document.querySelector('meta[type=business_setting]');
        const content = bs?.getAttribute('content');
        if (content != null) {
            this.instance = JSON.parse(content);
            bs.remove();
        }
        Module.init();
    }
    static calculateOrderCommissionFromOrder(setting, order) {
        if (setting.order_commission_type === 'percent') {
            return (setting.order_commission * order / 100);
        }
        return setting.order_commission;
    }
    static calculateDeliveryCommissionFromOrder(setting, order) {
        if (setting.delivery_commission_type === 'percent') {
            return (setting.delivery_commission * order / 100);
        }
        return setting.delivery_commission;
    }
    static calculatePaymentChargeFromOrder(setting, order) {
        if (setting.payment_charge_type === 'percent') {
            return (setting.payment_charge * order / 100);
        }
        return setting.payment_charge;
    }
    static calculateChargeFromAmountAndType(amount, charge, type) {
        if (type === 'amount' || charge === 0) {
            return charge;
        }
        return (charge * amount / 100);
    }
}
