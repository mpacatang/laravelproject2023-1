import { useCustomerDataStore } from "@js/services/state/states";
export default class NavigatorService {
    static router = null;
    static setApp(router) {
        this.router = router;
    }
    static addTrans(text) {
        let transObject = localStorage.getItem('trans');
        let trans = [];
        if (transObject != null) {
            trans = JSON.parse(transObject);
        }
        trans = trans.filter((item, index) => trans.indexOf(item) === index);
        trans.push(text);
        localStorage.setItem('trans', JSON.stringify(trans));
    }
    static resetTrans() {
        localStorage.setItem('trans', JSON.stringify([]));
    }
    static getUnTrans() {
        try {
            let transObject = localStorage.getItem('trans');
            let trans = "";
            if (transObject != null) {
                let newTrans = JSON.parse(transObject);
                for (const t of newTrans) {
                    let n = t.replaceAll("_", " ");
                    n = n.charAt(0).toUpperCase() + n.slice(1);
                    trans += ('"' + t + '": "' + n + '",\n');
                }
                console.info(trans);
            }
        }
        catch (e) {
        }
    }
    static getCustomerFirstRoute() {
        const store = useCustomerDataStore();
        return store.getPreference().first_time ? 'customer.preference' : 'customer.home';
    }
    static async copyToClipboard(text) {
        function fallbackCopyTextToClipboard(text) {
            let success = false;
            const textArea = document.createElement("textarea");
            textArea.value = text;
            // Avoid scrolling to bottom
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                success = document.execCommand('copy');
            }
            catch (err) {
            }
            document.body.removeChild(textArea);
            return success;
        }
        if (!navigator.clipboard) {
            return fallbackCopyTextToClipboard(text);
        }
        await navigator.clipboard.writeText(text);
        return true;
    }
    static goBackOrRoute(route) {
        if (window.history?.state?.back != null) {
            this.router.go(-1);
        }
        this.router.replace(route);
    }
}
export class DocsNavigation {
    static base = "http://localhost:5173/";
    static goToFirebase() {
        this._to('setup/firebase');
    }
    static goToBackend() {
        this._to('setup/backend');
    }
    static goToRazorpay() {
        this._to('payments/razorpay');
    }
    static goToStripe() {
        this._to('payments/stripe');
    }
    static goToPaypal() {
        this._to('payments/paypal');
    }
    static goToFlutterwave() {
        this._to('payments/flutterwave');
    }
    static _to(url) {
        window.open(this.base + url, "_blank");
    }
    static goToMail() {
        this._to('mail');
    }
    static goToSMS() {
        this._to('sms/twilio');
    }
    static goToOtherSetup() {
        this._to('other_setups/map');
    }
}
export function goToBuyNow() {
    window.open("https://codecanyon.net/item/shopy-multivendor-ecommerce-food-grocery-pharmacy-one-stop-solution/42363182", "_blank");
}
