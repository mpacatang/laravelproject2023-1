export class Notification {
    static fromFCM(data) {
        return {
            title: data.notification?.title,
            body: data.notification?.body,
            data: data.data
        };
    }
}
