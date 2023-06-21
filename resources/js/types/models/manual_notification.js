export class ManualNotification {
    static validateActions(notification) {
        if (notification.actions != null) {
            for (let action of notification.actions) {
                if (action.text == null || action.text.trim().length == 0 || action.click_action == null || action.click_action.trim().length == 0) {
                    return false;
                }
            }
        }
        return true;
    }
}
