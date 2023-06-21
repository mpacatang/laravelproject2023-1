<template>
    <div>
        <template v-for="toast in toasts" :key="toast.id">

            <div
                :class="[getToastType(toast),
                {'show': toast.state!=='closed', 'clickable': toast.click_action}] "
                :style="{minWidth: getMinWidth(toast)+'px', maxWidth: getMaxWidth(toast)+'px'}"
                class="vtoast" @click="onToastClick(toast)"
                @mouseleave="()=>onMouseLeave(toast)"

                @mouseover="()=>onMouseOver(toast)"
            >
                <div class="toast-content position-relative">
                    <div class="text-center icon">
                        <Icon :icon="getIcon(toast)"></Icon>
                    </div>
                    <div class="flex-grow-1">
                        <p v-if="toast.title" class="my-0 title">{{ toast.title }}</p>
                        <p class="my-0 body">{{ toast.body }}</p>
                    </div>

                    <div v-if="toast.action" class="action">
                        <span class="action-text"
                              @click="onToastActionClick(toast)">{{ toast.action.text }}</span>
                    </div>

                    <Icon v-if="!toast.hide_close_icon" :icon="getCloseIcon(toast)" class="close-icon" size="18"
                          @click.prevent="closeToast(toast)"></Icon>

                </div>
                <div v-if="!toast.hide_progress && toast.duration"
                     :style="{animationDuration: toast.duration+'ms', animationPlayState: toast.animation_play_state}"
                     class="vprogress-bar"></div>
            </div>
        </template>
    </div>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import Icon from "@js/components/Icon.vue";


import {useToastNotificationStore} from "@js/services/state/states";
import {IToast} from "@js/types/other";

interface ToastInternal extends IToast {
    remain_duration?: number,
    animation_play_state: 'paused' | 'running',
    state: 'closed' | 'show'
}

export default defineComponent({
    components: {Icon},
    props: {},
    data() {
        const store = useToastNotificationStore();
        return {
            store,
            toasts: [] as ToastInternal[],
        }
    },
    computed: {},
    watch: {},

    methods: {
        getIcon(toast: ToastInternal) {
            return toast.icon ?? 'info';
        },
        getCloseIcon(toast: ToastInternal) {
            return toast.close_icon ?? 'close';
        },

        getToastType(toast: ToastInternal) {
            return toast.type ?? 'success';
        },
        getMinWidth(toast: ToastInternal) {
            return toast.minWidth ?? 350;
        },
        getMaxWidth(toast: ToastInternal) {
            return toast.maxWidth ?? 450;
        },
        onMouseOver(toast: ToastInternal) {
            toast.animation_play_state = 'paused';
        },
        onMouseLeave(toast: ToastInternal) {
            toast.animation_play_state = 'running';
        },
        onChangeToasts(newVal: IToast[]) {
            let toasts: ToastInternal[] = [];

            newVal.forEach((toast) => {
                if (this.toasts.find((t) => t.id == toast.id) == null) {
                    toasts.push({
                        ...toast,
                        remain_duration: null,
                        animation_play_state: 'running',
                        state: 'show'
                    });
                }
            });
            for (const toast of toasts) {
                this.setTimeoutOnToast(toast);
                this.toasts.push(toast)
            }

            // this.toasts.push(...toasts);

        },
        onToastActionClick(toast: IToast) {
            if (toast.action?.callback) {
                this.closeToast(toast);
                toast.action?.callback();
            }
        },
        onToastClick(toast: ToastInternal) {
            if (toast.click_action) {
                this.closeToast(toast);
                toast.click_action();
            }
        },
        setTimeoutOnToast(toast: ToastInternal) {
            if (toast.duration != null) {
                if (toast.remain_duration == null)
                    toast.remain_duration = toast.duration;

                const fade_duration = toast.duration * 0.2;

                const self = this;
                const time = setInterval(() => {
                    if(toast.animation_play_state=='running'){
                        toast.remain_duration -= 10;
                        if (toast.remain_duration < fade_duration) {
                            this.toasts = this.toasts.map((t) => {
                                if (t.id == toast.id) {
                                    return {
                                        ...t,
                                        state: 'closed'
                                    }
                                }
                                return t;
                            })
                            clearInterval(time);
                            setTimeout(() => {
                                self.closeToast(toast);
                            }, toast.duration * 0.2);
                        }
                    }
                }, 10);


            }
            // let interval = setInterval(() => {
            //
            //     if (toast.remain_duration < 0) {
            //         clearInterval(interval);
            //         this.closeToast(toast);
            //     }
            // }, 400)
        },
        closeToast(toast) {
            this.store.removeToast(toast);
            this.toasts = this.toasts.filter((t) => t.id !== toast.id);
        },

    },
    mounted() {
        const self = this;
        this.store.$subscribe((mut, state) => {
            this.onChangeToasts(state.toasts);
        });
    }
});
</script>
