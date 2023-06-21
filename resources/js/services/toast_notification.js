import { createApp } from 'vue';
import { useToastNotificationStore } from "@js/services/state/states";
import VToast from "@js/components/VToast.vue";
import i18n from "@js/services/i18n";
import { getUserTypeFromUrl } from "@js/services/state/state_helper";
export class ToastNotification {
    static $app;
    static $router;
    static container = null;
    static toast;
    static setApp(app, router) {
        this.$app = app;
        this.$router = router;
        this.toast = useToastNotificationStore();
        if (this.container == null) {
            this.container = document.createElement('div');
            this.container.id = "vtoast-container";
            document.body.appendChild(this.container);
        }
        let toast = createApp({ extends: VToast }, {
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
    static show({ message = "Message", title, indeterminate = false, short = false, type = 'success', hide_progress = false, icon, click_action, action }) {
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
    static showFCM(notification) {
        const user_type = getUserTypeFromUrl(window.location.pathname);
        this.toast.addToast({
            title: notification.title,
            body: notification.body,
            icon: 'local_mall',
            type: 'success',
            click_action: notification.data.order_id != null ? () => {
                this.$router.push({ name: user_type + '.orders.edit', params: { id: notification.data.order_id } });
            } : null,
            action: {
                text: i18n.global.t('show')
            }
        });
    }
}
