import {IModel} from "@js/types/models/models";
import {IChatMessage} from "@js/types/models/chat";


export interface INotificationData extends IModel {
    type: 'order'|'chat_message_notification'|'chat_message_status_notification',
    order_id?: number,
    message?: IChatMessage|string
}


export interface INotification extends IModel {
    data: INotificationData,
    title: string,
    body: string,
    created_at?: string,

}

export class Notification {
    static fromFCM(data: any): INotification {
        return {
            title: data.notification?.title,
            body: data.notification?.body,
            data: data.data
        }
    }
}
