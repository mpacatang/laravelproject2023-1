import {IModel} from "@js/types/models/models";
import {ICustomer} from "@js/types/models/customer";

export interface IRazorpayData extends IModel {
    enable: boolean,
}
export interface IStripeData extends IModel {
    enable: boolean,
}
export interface IPaypalData extends IModel {
    enable: boolean,
}

export interface IFlutterwaveData extends IModel {
    enable: boolean,
}

export interface IPaystackData extends IModel {
    enable: boolean,
}

export interface ICashfreeData extends IModel {
    enable: boolean,
}

export interface IMercadoData extends IModel {
    enable: boolean,
    public_key: string
}

export interface IPaymentData extends IModel {
    currency_code: string,
    customer: ICustomer,
    business: {
        name: string
    }
    razorpay: IRazorpayData,
    stripe: IStripeData,
    paypal: IPaypalData,
    flutterwave: IFlutterwaveData,
    paystack: IPaystackData,
    cashfree: ICashfreeData,
    mercado: IMercadoData,

}
