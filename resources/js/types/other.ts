import {IModel} from "@js/types/models/models";



///// =================== Toast ====================================//
export type ToastTypes = 'success' | 'error';

export interface IToast {
    id?: string,
    type?: ToastTypes,
    title?: string,
    body: string,
    maxWidth?: number,
    minWidth?: number,
    duration?: number,
    hide_progress?: boolean,
    icon?: string,
    close_icon?: string,
    hide_close_icon?: boolean,
    click_action?: Function,
    action?: {
        text: string, callback?: Function
    }
}


///// =================== Language ====================================//

export interface ILanguage{
    flag: string,
    language: string,
    title: string,
    rtl: boolean
}



