import {ToastNotification} from "@js/services/toast_notification";
import {array_remove} from "@js/shared/array_utils";
import order from "@js/types/models/order";
import {Notification} from "@js/types/models/notification";
import {IChatMessage} from "@js/types/models/chat";

export class FcmNotification {

    private static orderListener: ((order_id: number) => void)[] = [];
    private static chatMessageListener: ((message: IChatMessage) => void)[] = [];
    private static chatStatusListener: ((message: IChatMessage) => void)[] = [];

    static handler = (payload: any) => {
        console.info(payload);
        let notification = Notification.fromFCM(payload);
        if (notification.data.type === 'order') {
            let order_id = payload.data.order_id;
            if (order_id != null) {
                this.broadcastOrderChange(order_id);
            }
            ToastNotification.showFCM(notification);
        } else if (notification.data.type == 'chat_message_notification') {
            if (notification.data.message != null) {
                let data: any = notification.data.message;
                if (typeof notification.data.message === "string") {
                    data = JSON.parse(notification.data.message);
                }
                this.broadcastChatMessageChange(data);
            }
        } else if (notification.data.type == 'chat_message_status_notification') {
            if (notification.data.message != null) {
                let data: any = notification.data.message;
                if (typeof notification.data.message === "string") {
                    data = JSON.parse(notification.data.message);
                }
                this.broadcastChatStatusChange(data);
            }
        } else {
            ToastNotification.showFCM(notification);
        }

    }

    static subscribeOrderChangeListener(listener: (order_id: number) => void) {
        this.orderListener.push(listener);
    }

    static subscribeChatMessageListener(listener: (message: IChatMessage) => void) {
        this.chatMessageListener.push(listener);
    }
    static subscribeChatStatusListener(listener: (message: IChatMessage) => void) {
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


    private static broadcastOrderChange(order_id: number) {
        for (const call of this.orderListener) {
            call(order_id);
        }
    }

    private static broadcastChatMessageChange(message: IChatMessage) {
        for (const call of this.chatMessageListener) {
            call(message);
        }
    }
    private static broadcastChatStatusChange(message: IChatMessage) {
        for (const call of this.chatStatusListener) {
            call(message);
        }
    }
}
