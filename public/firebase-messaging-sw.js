var window = self;

importScripts('https://www.gstatic.com/firebasejs/9.2.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.2.0/firebase-messaging-compat.js');

// importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object


const app = firebase.initializeApp({
    apiKey: "AIzaSyCC_FoblDBTBXcktqL-c7EJTzFWnN_J67M",
    authDomain: "shopy-user.firebaseapp.com",
    projectId: "shopy-user",
    storageBucket: "shopy-user.appspot.com",
    messagingSenderId: "524891909464",
    appId: "1:524891909464:web:8ddad43952ba84397ac92d"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.info(payload);
    if (clients != null) {
        clients
            .matchAll({
                type: "window", includeUncontrolled: true
            })
            .then(windowClients => {
                for (let i = 0; i < windowClients.length; i++) {
                    const windowClient = windowClients[i];
                    windowClient.postMessage({
                        "from": "background",
                        "payload": payload
                    });
                }
            });
    }
    // if (self.fcmHandler) {
    //     self.fcmHandler(payload);
    // }
});
// messaging.onMessage((payload) => {
//     if (self.fcmHandler) {
//         self.fcmHandler(payload);
//     }
// });
