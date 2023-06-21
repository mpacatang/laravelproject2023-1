import { initializeApp, } from 'firebase/app';
import { getAuth, GoogleAuthProvider, signInWithPopup } from "firebase/auth";
import { deleteToken, getMessaging, getToken, onMessage, } from "firebase/messaging";
import { FcmNotification } from "@js/services/fcm_notification";
let fcmMessaging = null;
export class Firebase {
    static initialized = false;
    static init() {
        const firebaseConfig = {
            apiKey: "AIzaSyCC_FoblDBTBXcktqL-c7EJTzFWnN_J67M",
            authDomain: "shopy-user.firebaseapp.com",
            projectId: "shopy-user",
            storageBucket: "shopy-user.appspot.com",
            messagingSenderId: "524891909464",
            appId: "1:524891909464:web:8ddad43952ba84397ac92d"
        };
        const app = initializeApp(firebaseConfig);
        fcmMessaging = getMessaging();
        onMessage(fcmMessaging, (ons) => {
            FcmNotification.handler(ons);
        });
        if (navigator.serviceWorker != null) {
            navigator.serviceWorker.addEventListener('message', (event) => {
                if (event?.data?.from === 'background') {
                    FcmNotification.handler(event?.data.payload);
                }
            });
        }
        requestNotificationPermission().then();
        this.initialized = true;
    }
    static getFCMToken = async () => {
        try {
            if (fcmMessaging == null) {
                return;
            }
            await requestNotificationPermission();
            await this.deleteFCMToken();
            let currentToken = await getToken(fcmMessaging);
            if (currentToken) {
                return currentToken;
            }
            else {
                return null;
            }
        }
        catch (err) {
            console.warn('An error occurred while retrieving token. ', err);
            return null;
        }
    };
    static deleteFCMToken = async () => {
        const fcmMessaging = getMessaging();
        if (fcmMessaging == null) {
            return;
        }
        try {
            await deleteToken(fcmMessaging);
        }
        catch (e) {
        }
        return true;
    };
    static googleSignIn = async () => {
        const auth = getAuth();
        const provider = new GoogleAuthProvider();
        try {
            const result = await signInWithPopup(auth, provider);
            return result.user;
        }
        catch (e) {
            console.info(e);
            return null;
        }
    };
}
const requestNotificationPermission = async () => {
    try {
        const status = await Notification.requestPermission();
        if (status === 'granted')
            return true;
    }
    catch (e) {
        return false;
    }
};
//In Dev
// class Verifier implements ApplicationVerifier{
//
//     readonly type = "recaptcha";
//     token;
//
//     constructor(token: string) {
//         this.token = token;
//     }
//
//     async verify() {
//         return await this.token;
//     }
// }
