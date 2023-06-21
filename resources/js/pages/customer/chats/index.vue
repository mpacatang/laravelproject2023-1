<template>
    <Layout>

        <div id="chat-page" class="d-flex gap-3">
            <div :class="[{'d-block': show_filter_wrapper}]" class="chat-list-wrapper">
                <Card style="min-width: 270px;">
                    <CardHeader :title="$t('chats')" icon="forum"/>
                    <SimpleBarVue v-if="chats && chats.length>0" class="" style="max-height: 85vh">
                        <div v-for="chat in chats"
                             :class="[{'active': selected_chat?.id===chat.id}]" class="single-chat"
                             @click="onSelectChat(chat)">
                            <NetworkImage
                                :from-text="getLastMessageParticipant(chat)?.user?.first_name + ' ' +getLastMessageParticipant(chat)?.user?.last_name"
                                :src="getLastMessageParticipant(chat)?.user?.avatar" circular
                                size="36"/>
                            <div class="ms-2 flex-grow-1">
                                <p :class="[{'fw-semibold': !lastMessageIsSeen(chat)}]" class="mb-0 fw-medium fw-14">
                                    {{
                                        getLastMessageParticipant(chat)?.user?.first_name + ' ' + getLastMessageParticipant(chat)?.user?.last_name
                                    }}
                                </p>
                                <div v-if="getLastMessage(chat)" class="d-flex align-items-center">
                                    <template v-if="lastMessageIsMy(chat)">
                                        <Icon :color="getLastMessage(chat)?.seen?'primary':null"
                                              :icon="getLastMessage(chat)?.seen?'done_all':'check'"
                                              class="me-1" size="14"></Icon>
                                    </template>
                                    <template v-if="getLastMessage(chat)?.type==='text'">
                                        <span :class="[{'fw-semibold': !lastMessageIsSeen(chat)}]"
                                              class="font-12 flex-grow-1">
                                            {{ getLastMessage(chat)?.message }}
                                        </span>
                                    </template>
                                    <div v-if="getLastMessage(chat)?.type==='image'" class="flex-grow-1">
                                        <Icon icon="image" size="14"></Icon>
                                        <span :class="[{'fw-semibold': !lastMessageIsSeen(chat)}]"
                                              class="font-12 ms-1">{{ $t('image') }}</span>
                                    </div>
                                    <span v-if="!lastMessageIsSeen(chat)" class="text-primary font-20 lh-0">•</span>
                                </div>
                                <span v-else class="font-12">{{ $t('click_to_start_chat') }}</span>
                            </div>
                        </div>
                    </SimpleBarVue>
                    <p v-else class="text-center fw-medium my-3">{{ $t('there_is_no_chat') }}</p>

                </Card>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    <div :class="[{'bg-soft-secondary':show_filter_wrapper, 'bg-soft-purple':!show_filter_wrapper}]"
                         class="p-2 d-block d-lg-none rounded d-flex align-items-center"
                         @click="show_filter_wrapper=!show_filter_wrapper">
                        <Icon :icon="show_filter_wrapper?'close':'forum'" size="20"/>
                    </div>
                </div>
                <Card style="min-width: 300px;">
                    <div class="d-flex border-bottom py-2-5 px-3 align-items-center">
                        <NetworkImage v-if="selected_chat"
                                      :from-text="getLastMessageParticipant(selected_chat)?.user?.first_name + ' ' +getLastMessageParticipant(selected_chat)?.user?.last_name"
                                      :src="getLastMessageParticipant(selected_chat)?.user?.avatar" circular
                                      size="26"/>
                        <p class="ms-2 mb-0 fw-medium text-xmuted flex-grow-1">
                            <template
                                v-if="getLastMessageParticipant(selected_chat)">{{
                                    getLastMessageParticipant(selected_chat)?.user?.first_name + ' ' + getLastMessageParticipant(selected_chat)?.user?.last_name
                                }}
                            </template>

                            ({{
                                selected_chat?.title ?? $t('select_any_chat')
                            }})
                        </p>
                        <Icon class="hover-effect-circular-lg cursor-pointer" icon="edit" size="18"
                              @click="showEditChat"></Icon>

                    </div>

                    <template v-if="selected_chat">
                        <div class="position-relative">
                            <div ref="messages_wrapper" class="p-3"
                                 style="height: 70vh">
                                <div v-for="(message, index) in selected_chat.messages">

                                    <template v-if="getPinnedDateFromIndex(index)">
                                        <div class="text-center mb-2">
                                            <span
                                                class=" bg-light p-0-5 px-2 rounded font-13 fw-medium">
                                                {{ getFormattedDate(getPinnedDateFromIndex(index)) }}
                                            </span>
                                        </div>
                                    </template>
                                    <template v-if="getMessageParticipant(message)?.user_type===this.user_type">
                                        <div class="mb-2 d-flex justify-content-end">
                                            <div class="chat-width text-end">
                                                <template v-if="message.type==='image'">
                                                    <div class="text-end">
                                                        <NetworkImage :src="message.image" rounded size="128" show-window-preview/>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="border p-2 rounded">
                                                        {{ message.message }}
                                                    </div>
                                                </template>
                                                <div class="d-flex align-items-center  mt-1 justify-content-end">
                                                    <Icon :color="message.seen?'bluish-purple':null"
                                                          :icon="message.seen?'done_all':'check'"
                                                          size="14"></Icon>
                                                    <p class="text-end font-12 ms-0-5 mb-0">
                                                        {{ getFormattedTime(message.sent_at) }}</p>
                                                </div>

                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <template v-if="messageLoaded(this.selected_chat,message)"></template>
                                        <div class="mb-2 d-flex align-items-end">
                                            <NetworkImage v-if="canShowParticipant(index)"
                                                          :from-text="getMessageParticipant(message)?.user?.first_name + ' ' +getMessageParticipant(message)?.user?.last_name"
                                                          :src="getMessageParticipant(message)?.user.avatar" circular
                                                          width="24"></NetworkImage>
                                            <div v-else style="width: 24px;"></div>
                                            <div class="ms-1-5">
                                                <template v-if="message.type==='image'">
                                                    <div class="">
                                                        <NetworkImage :src="message.image" rounded size="128" show-window-preview/>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="border chat-width p-2 rounded">
                                                        {{ message.message }}
                                                    </div>
                                                </template>
                                                <p class="font-12 mb-0">
                                                    {{ getFormattedTime(message.sent_at) }}</p>
                                            </div>
                                        </div>
                                    </template>

                                </div>
                            </div>
                            <div class="d-flex border-top px-2-5 p-2 align-items-center">
                                <FormInput
                                    v-model="message"
                                    class="border-none bg-light flex-grow-1"
                                    name="message"
                                    no-label no-spacing
                                />
                                <Icon class="ms-2-5 hover-effect-circular-lg cursor-pointer" icon="image"
                                      size="20" @click="()=>this.show_upload_image_modal=true"></Icon>
                                <Icon class="ms-2-5 hover-effect-circular-lg cursor-pointer" icon="send"
                                      size="20" @click="sendMessage"></Icon>
                            </div>
                            <div v-if="hasMoreMessages" class="load-more-message-wrapper">
                                <div
                                    class="load-more-message"
                                    @click="loadMoreMessage">
                                    {{ $t('load_more') }}
                                    <Icon v-if="!chat_loading" class="ms-1" icon="arrow_upward" size="14"></Icon>
                                    <CircularLoader :loading="chat_loading" class="ms-1-5" stroke="blue"/>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <VModal v-model="show_upload_image_modal" close-btn>
            <Card class="mb-0">
                <CardHeader :title="$t('send_image')" icon="image"></CardHeader>
                <CardBody>
                    <FileUpload
                        ref="image_dropzone"
                        :max-files="1"
                        :placeholder="$t('drop_image_to_send')"
                        name="zip"
                        no-label
                        preview-as-list
                        v-on:file-added="image_helper.onFileUpload"
                        v-on:file-removed="image_helper.onFileRemoved"/>

                    <div class="text-end">
                        <LoadingButton flexed-icon icon="send" @click="sendMessage">
                            {{ $t('send') }}
                        </LoadingButton>
                    </div>
                </CardBody>
            </Card>
        </VModal>

        <VModal v-model="show_edit_chat_modal" close-btn>
            <Card class="mb-0">
                <CardHeader :title="$t('edit_chat')" icon="edit" size="18"></CardHeader>
                <CardBody>
                    <FormInput v-model="chat_title" :label="$t('title')" name="chat_title"/>
                    <div class="text-end">
                        <UpdateButton flexed-icon @click="updateChat">
                            {{ $t('update') }}
                        </UpdateButton>
                    </div>
                </CardBody>
            </Card>
        </VModal>

    </Layout>

</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import {defineComponent} from 'vue';
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {Chat, IChat, IChatMessage, IChatParticipant} from "@js/types/models/chat";
import Request from "@js/services/api/request";
import {IData} from "@js/types/models/data";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import {SimpleBar as SimpleBarVue} from 'simplebar-vue3';
import {ToastNotification} from "@js/services/toast_notification";
import {FcmNotification} from "@js/services/fcm_notification";
import {array_add_unique, array_get_from_id, array_update_unique} from "@js/shared/array_utils";
import {DateUtil} from "@js/shared/utils";
import VModal from "@js/components/VModal.vue";
import {FileUploadHelper} from "@js/types/models/file_upload_helper";
import SimpleBar from 'simplebar';
import UpdateButton from "@js/components/buttons/UpdateButton.vue";
import CircularLoader from "@js/components/CircularLoader.vue";

export default defineComponent({
    components: {CircularLoader, UpdateButton, VModal, Layout, SimpleBarVue, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            simplebar_instance: null,
            page_loading: true,
            chat_loading: false,
            show_filter_wrapper: false,
            chats: [] as IChat[],
            selected_chat: null as IChat,
            message: '',
            chat_title: '',
            sending: false,
            user_type: 'customer',
            show_upload_image_modal: false,
            show_edit_chat_modal: false,
            image_helper: new FileUploadHelper({max: 1})
        }
    },
    computed: {
        hasMoreMessages() {
            return Chat.hasMoreMessages(this.selected_chat);
        }
    },
    methods: {
        showEditChat() {
            this.show_edit_chat_modal = true;
            this.chat_title = this.selected_chat.title.toString();
        },
        lastMessageIsSeen(chat: IChat) {
            let message = this.getLastMessage(chat);
            if (message) {
                let participant = Chat.getParticipantFromMessage(chat, message);
                if (participant && participant.user_type != this.user_type) {
                    return message.seen;
                }
            }
            return true;
        },
        lastMessageIsMy(chat: IChat): boolean {
            let message = this.getLastMessage(chat);
            if (message) {
                let participant = Chat.getParticipantFromMessage(chat, message);
                return participant && participant.user_type === this.user_type;
            }
            return true;
        },
        canShowParticipant(index: number): boolean {
            if (this.selected_chat != null && this.selected_chat.messages != null && this.selected_chat.messages.length > index + 1) {
                return Chat.getParticipantFromMessage(this.selected_chat, this.selected_chat.messages[index + 1]).user_type === this.user_type;
            }
            return true;
        },
        getPinnedDateFromIndex(index: number) {
            if (this.selected_chat?.messages == null) return null;
            if (index == 0 || !DateUtil.isSameDay(this.selected_chat.messages[index].sent_at, this.selected_chat.messages[index - 1].sent_at)) {
                return this.selected_chat.messages[index].sent_at;
            }
            return null;
        },
        onMessageReceived(message: IChatMessage) {
            if (this.selected_chat.id === message.chat_id) {
                this.selected_chat ??= [];
                this.selected_chat.messages = [...this.selected_chat.messages, message];
            } else {
                let chat = array_get_from_id<IChat>(this.chats, message.chat_id)
                if (chat != null) {
                    chat.messages ??= [];
                    chat.messages = [...chat.messages, message];
                }

            }
        },
        onMessageStatus(message: IChatMessage) {
            if (this.selected_chat.id === message.chat_id) {
                this.selected_chat.messages = array_update_unique(this.selected_chat.messages, message);
            } else {
                let chat = array_get_from_id<IChat>(this.chats, message.chat_id)
                if (chat != null) {
                    chat.messages ??= [];
                    chat.messages = array_update_unique(chat.messages, message);
                }

            }
        },
        getLastMessage(chat: IChat): IChatMessage | null {
            return Chat.getLastMessage(chat);
        },
        getLastMessageParticipant(chat: IChat): IChatParticipant | null {
            return Chat.getOtherParticipant(chat, this.user_type);
        },
        async onSelectChat(chat: IChat) {
            this.selected_chat = chat;
            if (chat.messages == null) {
                await this.getChatMessages();
            }
            this.scrollToBottom();
        },
        scrollToBottom(slow = false) {
            if (!this.$refs.messages_wrapper) return;
            if (!this.simplebar_instance) {
                this.simplebar_instance = new SimpleBar(this.$refs.messages_wrapper);
            }
            let simplebar = this.simplebar_instance;

            if (simplebar.getContentElement()) {
                const maxPosition = simplebar.getContentElement().scrollHeight;
                const time = slow ? 20 : 1;

                let scroll_position = simplebar.getScrollElement().scrollTop;
                const interval = setInterval(function () {
                    scroll_position += 5;
                    simplebar.getScrollElement().scrollTop = scroll_position;
                    if (scroll_position > maxPosition - 500) clearInterval(interval);
                }, time);
            }
        },
        loadMoreMessage() {
            this.getChatMessages();
        },
        getMessageParticipant(message: IChatMessage) {
            return Chat.getParticipantFromMessage(this.selected_chat, message);
        },
        async messageLoaded(chat: IChat, message: IChatMessage) {
            let url = customerAPI.chats.message_as_seen(chat.id, message.id);
            Chat.messageLoaded(url, message).then();
            return true;
        },

        async updateChat() {
            try {
                const response = await Request.patchAuth<IData<any>>(customerAPI.chats.update(this.selected_chat.id), {
                    title: this.chat_title
                });
                if (response.success()) {
                    this.show_edit_chat_modal = false;
                    this.selected_chat.title = this.chat_title;
                }
            } catch (e) {
                handleException(e);
            }
        },
        async sendMessage() {
            if (this.message.trim().length == 0 && this.image_helper.getFile() == null) {
                return ToastNotification.error(this.$t('enter_message_or_send_image'));
            }
            try {
                const response = await Request.postAuth<IData<IChatMessage>>(customerAPI.chats.messages(this.selected_chat.id), {
                    message: this.message,
                    image: this.image_helper.getImageDataFile()
                });
                if (response.success()) {
                    this.message = '';
                    this.$refs.image_dropzone.removeAllFiles();
                    this.show_upload_image_modal = false;
                    this.selected_chat.messages ??= [];
                    this.selected_chat.messages = [...this.selected_chat.messages, response.data.data];
                    this.scrollToBottom(true);
                }
            } catch (e) {
                handleException(e);
            }

        },
        async getWithChats() {
            const query = this.$route.query;
            const userType = query['user_type'];
            const id = query['id'];
            if (userType == null || id == null) return;
            const url = customerAPI.chats.with + "?user_type=" + userType + "&&id=" + id;
            try {
                const response = await Request.getAuth<IData<IChat>>(url, {forced: true});
                if (response.success()) {
                    const chat = response.data.data;
                    this.chats = array_add_unique(this.chats, chat);
                    let fChat = array_get_from_id(this.chats, chat.id);
                    await this.onSelectChat(fChat);
                }
            } catch (error) {
                handleException(error);
            }
        },
        async getChats() {
            try {
                const response = await Request.getAuth<IData<IChat[]>>(customerAPI.chats.get, {
                    forced: true
                });
                this.chats = response.data.data;
                this.page_loading = false;
                if (this.chats.length > 0) {
                    await this.onSelectChat(this.chats[0]);
                }
            } catch (error) {
                handleException(error);
            }
        },
        async getChatMessages() {
            this.chat_loading = true;
            let selected_chat = await Chat.loadMoreMessages(this.selected_chat, customerAPI.chats.messages(this.selected_chat.id));
            if (selected_chat != null) {
                this.selected_chat = selected_chat;
            }
            this.chat_loading = false;
        }
    },
    async mounted() {
        FcmNotification.subscribeChatMessageListener(this.onMessageReceived);
        FcmNotification.subscribeChatStatusListener(this.onMessageStatus);
        await this.getChats();
        await this.getWithChats();
    },

    beforeUnmount() {
        FcmNotification.unsubscribeChatMessageListener(this.onMessageReceived);
        FcmNotification.unsubscribeChatStatusListener(this.onMessageStatus);
    }
});

</script>

<style scoped>
</style>

