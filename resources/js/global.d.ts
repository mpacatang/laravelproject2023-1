import '@types/googlemaps';


declare global {
    interface Window {
        Apex: any;
        google: typeof google;
        Razorpay: any;
        flutter_inappwebview: any|null,
        fcmHandler: Function,
        MercadoPago: any
    }
}
