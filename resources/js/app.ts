///============= Bootstrapped Dependencies =================//
import NavigatorService from "@js/services/navigator_service";
/// ================ Vue Core  =======================//
import {createApp} from "vue";
import {createPinia} from 'pinia'


/// ================ Services  =======================//
import {useLanguageStore, useLayoutStore} from "@js/services/state/states";
import {ToastNotification} from "./services/toast_notification";

import router from './routes/routes';
import {Firebase} from "@js/firebase";
import {BusinessSetting} from "@js/types/models/business_setting";
import i18n from "./services/i18n";

try {
    require('bootstrap');
    require('dateformat');
    require('simplebar')
} catch (e) {
}


const app = createApp({})

/// ================ Services  =======================//

try {
    Firebase.init();
} catch (e) {
    console.info("Error catch in firebase init:" + e);
}

//Pinia
const pinia = createPinia()

app.use(pinia);
app.use(router)

app.use(i18n);

i18n.global.locale = useLanguageStore().getSelectedLanguage();
//Layout
useLayoutStore().init();
//Toast
ToastNotification.setApp(app, router);
NavigatorService.setApp(router);
BusinessSetting.init();

// NavigatorService.resetTrans();

// NavigatorService.getUnTrans();
/// ================ Mount  =======================//
app.mount('#app');


/// ================ Global Error Handler  =======================//

// app.config.errorHandler = (err, vm, info) => {
//     console.info(err);
//     vm.$router.push({name: 'landing'})
//     console.info(info)
//     // err: error trace
//     // vm: component in which error
//     // info: Vue specific error information such as lifecycle hooks, events etc.
//
//     // TODO: Perform any custom logic or log to server
//
// };

/// ================ Testing  =======================//

const test = () => {

}


test();
