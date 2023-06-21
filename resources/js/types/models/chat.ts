import {IIdentifierModel} from "@js/types/models/models";
import {IPagination, Pagination} from "@js/types/models/pagination";
import {IUser} from "@js/types/models/user";
import Request from "@js/services/api/request";
import {IData} from "@js/types/models/data";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {array_add_all_unique} from "@js/shared/array_utils";

export type IParticipantUserType = 'guest' | 'admin' | 'seller' | 'customer' | 'delivery_boy';
export type IMessageType = 'image' | 'text' | 'other';

export interface IChat extends IIdentifierModel {
    title: string,
    participants: IChatParticipant[],
    messages: IChatMessage[],
    pagination?: IPagination,
    latest_message?: IChatMessage

}

export interface IChatParticipant extends IIdentifierModel {
    user_id: number,
    chat_id: number,
    user_type: IParticipantUserType,
    user?: IUser,
    chat?: IChat

}

export interface IChatMessage extends IIdentifierModel {
    type: IMessageType,
    message?: string,
    image?: string,
    seen: boolean,
    chat_id: number,
    chat_participant_id: number,
    sent_at: string,

    chat?: IChat,
    participant?: IChatParticipant

}


export class Chat {


    static getLastMessage(chat: IChat): IChatMessage | null {
        if (chat.messages == null || chat.messages.length == 0) {
            return chat.latest_message;
        }
        return chat.messages[chat.messages.length - 1];
    }

    static getLastMessageParticipant(chat: IChat): IChatParticipant | null {
        let l_message = Chat.getLastMessage(chat);
        if (l_message != null) {
            return this.getParticipantFromMessage(chat, l_message);
        }
        return null;
    }
    static getOtherParticipant(chat: IChat, user_type: IParticipantUserType): IChatParticipant | null {
        return chat?.participants?.find((p)=>p.user_type!=user_type);
    }

    static getParticipantFromMessage(chat: IChat, chat_message: IChatMessage): IChatParticipant | null {
        return chat.participants?.find((p) => p.id == chat_message.chat_participant_id);
    }

    static hasMoreMessages(chat: IChat): boolean {
        return chat.pagination != null && Pagination.hasNext(chat.pagination);
    }

    static async messageLoaded(url: string, message: IChatMessage) {
        if (message.seen) return;
        try {
            await Request.patchAuth <IData<any>>(url);
            message.seen = true;
        } catch (error) {

        }
    }

    static async loadMoreMessages(chat: IChat, fUrl: string): Promise<IChat | null> {
        try {
            let response;
            if (chat.pagination != null) {
                if (!Pagination.hasNext(chat.pagination)) return null
                const url = Pagination.nextPage(chat.pagination);
                response = await Request.getAuth <IData<any>>(url, {forced: true, without_base: true});
            } else {
                response = await Request.getAuth <IData<any>>(fUrl, {forced: true});
            }
            chat.messages ??= [];
            chat.messages = [...response.data.data.reverse(), ...chat.messages];
            if (response.data.meta != null) {
                chat.pagination = response.data.meta;
            }
            return chat;
        } catch (error) {
            handleException(error);
        }
    }

}
