<template>
    <div id="topbar" class="navbar-custom">
        <div class="navbar-header">
            <div class="d-flex align-items-center gap-2">
                <router-link :to="{name: 'admin.dashboard'}" class="topbar-logo">
                    <Logo/>
                </router-link>
                <button class="button-toggle-menu" @click="toggleLeftbarHover">
                    <i class="fe fe-menu"></i>
                </button>
            </div>

            <div class="d-flex align-items-center justify-content-center">
                <div class="dropdown dropdown-center topbar-item">
                    <a aria-expanded="false" aria-haspopup="false"
                       class="topbar-button w-100 py-1 px-2 rounded dropdown-toggle arrow-none  hover-effect-rounded"
                       data-bs-toggle="dropdown" href="#" role="button">
                        <img :alt="selected_module.title" :src="selected_module.image" width="34">
                        <span class="ms-2 fw-semibold d-none d-sm-flex  font-16">{{ $t(selected_module.title) }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-animated">
                        <div v-for="module in remain_module"
                             class="d-flex justify-content-start align-items-center gap-3 cursor-pointer dropdown-item"
                             @click="onSelectModule(module)">
                            <img :alt="module.title" :src="module.image" width="28">
                            <span class="font-15 fw-medium">{{ $t(module.title) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2">
                <div class="topbar-item dropdown d-none d-lg-flex">
                    <a aria-expanded="false" aria-haspopup="false"
                       class="topbar-button dropdown-toggle arrow-none  hover-effect-circular" data-bs-toggle="dropdown"
                       href="#" role="button">
                        <img :src="selected_language.flag" alt="admin-image" height="16" v-if="selected_language">
                    </a>
                    <div class="dropdown-menu dropdown-menu-animated">
                        <a v-for="language in languages" class="dropdown-item" href="javascript:void(0);"
                           @click="()=>setLanguage(language)">
                            <img :src="language.flag" alt="admin-image" class="me-2" height="12">
                            <span class="align-middle">{{ language.title }}</span>
                        </a>


                    </div>
                </div>


                <div class="topbar-item d-none d-md-flex">
                    <router-link :to="{name: 'admin.chats.index'}"
                                 class="topbar-button  hover-effect-circular">
                        <Icon icon="forum" size="24"></Icon>
                    </router-link>
                </div>


                <div class="topbar-item d-none d-md-flex">
                    <div :class="[{'fullscreen-enable': fullscreen_enable}]"
                         class="topbar-button  hover-effect-circular" @click="onFullScreen">
                        <Icon v-if="!fullscreen_enable" icon="fullscreen" size="30"></Icon>
                        <Icon v-else icon="fullscreen_exit" size="30"></Icon>
                    </div>
                </div>

                <div class="dropdown topbar-item">
                    <a aria-expanded="false" aria-haspopup="false"
                       class="topbar-button dropdown-toggle arrow-none hover-effect-circular" data-bs-toggle="dropdown"
                       href="#" role="button" @click="onNotificationShow">
                        <i class="fe fe-bell"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg  dropdown-menu-animated">

                        <PageLoading :loading="notification_loading">

                            <div class="noti-title px-2 py-1">
                                <h5 class="m-0">
                                <span class="float-end">
                                    <small>{{ $t('clear') }}</small>
                                </span>{{ $t('notification') }}
                                </h5>
                            </div>
                            <hr class="dashed">
                            <template v-for="notification in getNotifications">
                                <div class="py-0 px-2 d-flex align-items-start">
                                    <VItem align-items="center" circular class="p-1-5" color="success" display="flex"
                                           soft>
                                        <Icon icon="local_mall" size="18"></Icon>
                                    </VItem>
                                    <div class="flex-grow-1 ms-2">
                                        <div class="fw-medium">
                                            {{ notification.title }}
                                        </div>

                                        <span class="font-13">{{ notification.body }}</span>
                                        <div class="text-end font-12 text-muted">
                                            {{ getDifferenceDateTimeAgo(notification.created_at) }}
                                        </div>
                                    </div>
                                </div>
                                <hr class="dashed">
                            </template>

                            <span class="d-block cursor-pointer text-center text-primary px-2 py-1">
                                {{ $t('view_all') }}
                                <i class="fe fe-arrow-right"></i>
                            </span>
                        </PageLoading>

                    </div>
                </div>


                <div class="d-none d-sm-flex topbar-item ">
                    <Tooltip :tooltip="$t(layout.theme??' ')" position="bottom">
                        <div class="topbar-button  hover-effect-circular" @click="toggleTheme">
                            <Icon v-if="layout.theme==='light'" icon="light_mode" msr></Icon>
                            <Icon v-if="layout.theme==='dark'" icon="dark_mode" msr></Icon>
                            <Icon v-if="layout.theme==='auto'" icon="night_sight_auto" msr></Icon>
                        </div>
                    </Tooltip>
                </div>

                <div class="topbar-item">
                    <div class="topbar-button  hover-effect-circular" @click="toggleRightbar">
                        <Icon icon="settings" type="msr"></Icon>
                    </div>
                </div>

                <template v-if="admin">
                    <div class="dropdown topbar-item">
                        <a class="topbar-button w-100 py-1 px-2 rounded dropdown-toggle arrow-none d-flex align-items-center gap-1  hover-effect-rounded"
                           data-bs-toggle="dropdown" href="#">
                            <NetworkImage :src="admin.avatar"  circular size="32" :from-text="admin.first_name+' '+admin.last_name"/>
                            <span class="font-16 d-none d-md-block">{{ admin.first_name }} <i class="mdi mdi-chevron-down"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                            <!-- item-->
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow m-0">{{ admin.first_name }} {{ admin.last_name }}</h6>
                            </div>

                            <!-- item-->
                            <router-link :to="{name: 'admin.profile'}" class="dropdown-item notify-item">
                                <i class="fe fe-user me-2"></i>
                                <span>{{ $t('my_account') }}</span>
                            </router-link>

                            <!-- item-->
                            <router-link :to="{name: 'admin.chats.index'}" class="dropdown-item notify-item">
                                <i class="fe fe-message-square me-2"></i>
                                <span>{{ $t('chats') }}</span>
                            </router-link>


                            <hr class="dashed">

                            <!-- item-->
                            <div class="dropdown-item notify-item cursor-pointer" @click="logout">
                                <i class="fe fe-log-out me-2"></i>
                                <span>Logout</span>
                            </div>

                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="topbar-hide-enable" @click="toggleTopbarHide">
            <Icon icon="expand_more"></Icon>
        </div>
    </div>

</template>


<script lang="ts">
import {ILayout, useAdminDataStore, useLayoutStore} from "@js/services/state/states";
import {
    changeLeftbarSize,
    changeTheme,
    openRightbar,
    setTopbarShow,
    toggleLeftbarEnable,
    toggleLeftbarSmHover
} from "@js/services/state/state_helper";
import Icon from "@js/components/Icon.vue";
import {defineComponent, PropType} from 'vue';
import FormInput from "@js/components/form/FormInput.vue";
import Button from "@js/components/buttons/Button.vue";
import NetworkImage from "@js/components/NetworkImage.vue";
import {IAdmin} from "@js/types/models/admin";
import {IModule, Module} from "@js/types/models/module";
import VItem from "@js/components/VItem.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Logo from "@js/components/Logo.vue";
import {adminAPI} from "@js/services/api/request_url";
import Request from "@js/services/api/request";
import {handleException} from "@js/services/api/handle_exception";
import {INotification} from "@js/types/models/notification";
import {IData} from "@js/types/models/data";
import {ToastNotification} from "@js/services/toast_notification";
import PageLoading from "@js/components/PageLoading.vue";
import LanguageSelectMixin from "@js/shared/mixins/LanguageSelectMixin";
import Tooltip from "@js/components/Tooltip.vue";

export default defineComponent({
    components: {Tooltip, PageLoading, Logo, VItem, NetworkImage, Button, FormInput, Icon},
    mixins: [UtilMixin, LanguageSelectMixin],
    props: {
        layout: {
            type: Object as PropType<ILayout>,
            required: true
        }
    },
    data() {
        return {

            admin: {} as IAdmin,

            fullscreen_enable: false,
            selected_module: {} as IModule,

            notifications: null as INotification[],
            notification_loading: false,
        }
    },
    computed: {
        getNotifications() {
            if (this.notifications == null) {
                return 0;
            }
            return this.notifications.slice(0, 6)
        },

        remain_module() {
            const self = this
            return Module.getCachedModules().filter((module) => {
                return self.selected_module.id != module.id;
            })
        },
        // ...authComputed,
        // ...layoutComputed,
    },
    methods: {
        toggleMenu() {
            this.$parent.toggleMenu()
        },

        toggleLeftbarHover() {
            if (window.innerWidth < 767) {
                toggleLeftbarEnable();
            } else {
                toggleLeftbarSmHover()
            }
        },
        toggleRightSidebar() {
            this.$parent.toggleRightSidebar()
        },

        async onNotificationShow() {
            if (this.notifications == null) {
                this.notification_loading = true;

                let store = useAdminDataStore();
                if (store.notifications == null) {
                    try {
                        const response = await Request.getAuth<IData<INotification[]>>(adminAPI.notifications.get);
                        if (response.success()) {
                            store.updateNotifications(response.data.data);
                        } else {
                            ToastNotification.unknownError();
                        }
                    } catch (error) {
                        handleException(error);
                    } finally {
                    }
                }
                this.notifications = store.notifications;
                this.notification_loading = false;

            }
        },
        async logout() {

            try {
                const response = await Request.postAuth(adminAPI.auth.logout, {});
            } catch (error) {
                handleException(error);
            } finally {
            }
            useAdminDataStore().logout();

            this.$router.push({'name': 'admin.login'});
        },
        toggleTopbarHide() {
            const navigationStore = useLayoutStore();
            let full_layout = navigationStore.getFullLayoutOption();
            setTopbarShow(!full_layout.show_topbar)

        },
        toggleTheme() {
            switch (this.layout.theme) {
                case "light":
                    changeTheme('dark');
                    break;
                case "dark":
                    changeTheme('auto');
                    break;
                case "auto":
                    changeTheme('light');
                    break;
            }
        },
        toggleRightbar() {
            openRightbar()
        },
        onFullScreen() {
            if (!this.fullscreen_enable) {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
            this.fullscreen_enable = !this.fullscreen_enable;
        },
        onSelectModule(module: IModule) {
            if (this.selected_module.id != module.id) {
                this.selected_module = module;
                useAdminDataStore().updateModuleId(module.id);
            }
        },
    },

    mounted() {
        const self = this;
        const authStore = useAdminDataStore();

        this.admin = authStore.getUserData()?.data;

          authStore.$subscribe((mut, state) => {
            self.admin = authStore.getUserData()?.data;
        });
        document.addEventListener('fullscreenchange', (event) => {
            this.fullscreen_enable = !window.screenTop && !window.screenY;
        });


        this.selected_module = Module.getModuleFromId(authStore.module_id);

    }
});
</script>

<style scoped>


</style>
