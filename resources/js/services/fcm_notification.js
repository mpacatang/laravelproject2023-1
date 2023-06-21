import { ToastNotification } from "@js/services/toast_notification";
import { array_remove } from "@js/shared/array_utils";
import { Notification } from "@js/types/models/notification";
export class FcmNotification {
    static orderListener = [];
    static chatMessageListener = [];
    static chatStatusListener = [];
    static handler = (payload) => {
        console.info(payload);
        let notification = Notification.fromFCM(payload);
        if (notification.data.type === 'order') {
            let order_id = payload.data.order_id;
            if (order_id != null) {
                this.broadcastOrderChange(order_id);
            }
            ToastNotification.showFCM(notification);
        }
        else if (notification.data.type == 'chat_message_notification') {
            if (notification.data.message != null) {
                let data = notification.data.message;
                if (typeof notification.data.message === "string") {
                    data = JSON.parse(notification.data.message);
                }
                this.broadcastChatMessageChange(data);
            }
        }
        else if (notification.data.type == 'chat_message_status_notification') {
            if (notification.data.message != null) {
                let data = notification.data.message;
                if (typeof notification.data.message === "string") {
                    data = JSON.parse(notification.data.message);
                }
                this.broadcastChatStatusChange(data);
            }
        }
        else {
            ToastNotification.showFCM(notification);
        }
    };
    static subscribeOrderChangeListener(listener) {
        this.orderListener.push(listener);
    }
    static subscribeChatMessageListener(listener) {
        this.chatMessageListener.push(listener);
    }
    static subscribeChatStatusListener(listener) {
        this.chatStatusListener.push(listener);
    }
    static unsubscribeOrderChangeListener(listener) {
        this.orderListener = array_remove(this.orderListener, listener);
    }
    static unsubscribeChatMessageListener(listener) {
        this.chatMessageListener = array_remove(this.chatMessageListener, listener);
    }
    static unsubscribeChatStatusListener(listener) {
        this.chatStatusListener = array_remove(this.chatStatusListener, listener);
    }
    static broadcastOrderChange(order_id) {
        for (const call of this.orderListener) {
            call(order_id);
        }
    }
    static broadcastChatMessageChange(message) {
        for (const call of this.chatMessageListener) {
            call(message);
        }
    }
    static broadcastChatStatusChange(message) {
        for (const call of this.chatStatusListener) {
            call(message);
        }
    }
}
