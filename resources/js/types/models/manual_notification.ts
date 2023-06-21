import {IIdentifierModel, IModel} from "@js/types/models/models";


export interface IManualNotificationData extends IModel {
    type: 'order',
    order_id?: number
}

export interface IManualNotificationAction extends IModel {
    text: string,
    click_action: string
}

export interface IManualNotification extends IIdentifierModel {
    data: IManualNotificationData,
    title: string,
    body: string,
    all_customers: boolean,
    all_sellers: boolean,
    all_delivery_boys: boolean,
    created_at?: string,
    schedule_at?: string,
    actions?: IManualNotificationAction[]
}


export class ManualNotification {

    static validateActions(notification: IManualNotification) {
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
