import {INotification} from "@js/types/models/notification";
import {createApp} from 'vue'
import {Router} from 'vue-router'

import {useToastNotificationStore} from "@js/services/state/states";
import VToast from "@js/components/VToast.vue";
import i18n from "@js/services/i18n";
import {getUserTypeFromUrl} from "@js/services/state/state_helper";
import {ToastTypes} from "@js/types/other";


export class ToastNotification {
    private static $app: any;
    private static $router: Router;
    private static container = null;

    private static toast;

    static setApp(app: any, router: Router) {
        this.$app = app;
        this.$router = router;
        this.toast = useToastNotificationStore();
        if (this.container == null) {
            this.container = document.createElement('div');
            this.container.id = "vtoast-container";
            document.body.appendChild(this.container);
        }

        let toast = createApp({extends: VToast}, {
            onClose() {
                toast.unmount();
            },
        });
        toast.mount(this.container);

    }


    static success(message = "hurrey!!! done", short = false) {
        this.toast.addToast({
            body: message, duration: short ? 3000 : 5000, type: 'success',
        });
    }

    static error(message = "something went wrong", short = false) {
        this.toast.addToast({
            body: message, icon: 'warning', duration: short ? 3000 : 5500, type: 'error',

        });
    }

    static show({
                    message = "Message",
                    title,
                    indeterminate = false,
                    short = false,
                    type = 'success',
                    hide_progress = false,
                    icon,
                    click_action,
                    action
                }: {
        title?: string,
        message?: string,
        short?: boolean,
        indeterminate?: boolean,
        icon?: string,
        type?: ToastTypes,
        hide_progress?: boolean,
        click_action?: Function,
        action?: {
            text: string, callback?: Function
        }
    }) {
        this.toast.addToast({
            title: title,
            body: message,
            duration: indeterminate ? null : short ? 2000 : 3500,
            type: type,
            icon: icon,
            hide_progress: hide_progress,

            click_action: click_action,
            action: action
        });
    }

    static unknownError() {
        this.error();
    }


    static showFCM(notification: INotification) {
        const user_type = getUserTypeFromUrl(window.location.pathname);
        this.toast.addToast({
            title: notification.title,
            body: notification.body,
            icon: 'local_mall',
            type: 'success',
            click_action: notification.data.order_id != null ? () => {
                this.$router.push({name: user_type+'.orders.edit', params: {id: notification.data.order_id}});
            } : null,
            action: {
                text: i18n.global.t('show')
            }
        });
    }
}

