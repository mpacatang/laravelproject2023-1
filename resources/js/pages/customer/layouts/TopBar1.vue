<template>
    <div>
        <div class="navbar-custom shadow-none border-bottom">
            <div class="container-fluid navbar-header d-flex">

                <div class="w-33">
                    <router-link :to="{name: 'customer.home'}">

                        <Logo/>

                    </router-link>
                </div>

                <div class="d-none w-33 d-sm-flex align-items-center justify-content-center gap-2">
                    <div class="topbar-item" @click="showChooseAddress">
                        <div class="topbar-button w-100 p-1 rounded gap-1">
                            <Icon icon="home_pin" size="sm" />
                            <div v-if="selected_address">
                                <p class="mb-0 max-lines font-14" style="max-width: 130px">{{
                                        selected_address.address
                                    }}</p>
                            </div>
                            <div v-else>
                                <p class="mb-0 font-14 text-nowrap">{{ $t('select_an_address') }}</p>
                            </div>
                            <Icon color="primary" icon="expand_more" />
                        </div>
                    </div>
                    <div class="topbar-item d-none d-lg-flex">
                        <div v-if="!hideSearch">
                            <form class="app-search" v-on:submit.prevent="onSearchDone">
                                <FormInput v-model="search" :placeholder="$t('search')" name="search" no-spacing noLabel></FormInput>
                                <Icon class="search-icon" icon="search" size="xs"></Icon>
                                <Icon :class="[{'d-none': !show_search_cancel}]" class="search-widget-icon-close" enterkeyhint="done" icon="cancel" size="xs" @click="onSearchCancel"></Icon>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="w-33 d-flex align-items-center justify-content-end">
                    <div class="d-flex align-items-center justify-content-end gap-2" >
                        <div class="dropdown topbar-item" v-if="isLoggedIn">
                            <div class="topbar-button dropdown-toggle arrow-none hover-effect-circular" @click="goToCart">
                                <i class="fe fe-shopping-cart "></i>
                                <span v-if="cart_count>0" class="badge bg-danger rounded-circle topbar-item-icon-badge">{{
                                        cart_count
                                    }}</span>
                            </div>
                        </div>

                        <div class="dropdown d-none d-xl-flex topbar-item">
                            <a aria-expanded="false" aria-haspopup="false" class="topbar-button dropdown-toggle arrow-none  hover-effect-circular" data-bs-toggle="dropdown" href="#" role="button">
                                <img :src="selected_language.flag" alt="customer-image" height="16">
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated">

                                <a v-for="language in languages" class="dropdown-item" href="javascript:void(0);"
                                   @click="setLanguage(language)">
                                    <img :src="language.flag" alt="customer-image" class="me-2" height="12">
                                    <span class="align-middle">{{ language.title }}</span>
                                </a>

                            </div>
                        </div>

                        <div v-if="isLoggedIn" class="dropdown topbar-item">
                            <a aria-expanded="false" aria-haspopup="false"
                               class="topbar-button dropdown-toggle arrow-none hover-effect-circular"
                               data-bs-toggle="dropdown"
                               href="#" role="button" @click="onNotificationShow">
                                <i class="fe fe-bell"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-lg  dropdown-menu-animated">

                                <PageLoading :loading="notification_loading">

                                    <div class="noti-title px-2 py-1">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-medium">{{ $t('notification') }}</span>
                                            <small>{{ $t('clear') }}</small>
                                        </div>

                                    </div>
                                    <hr class="dashed">
                                    <template v-for="notification in getNotifications">
                                        <div class="py-0 px-2 d-flex align-items-start">
                                            <VItem align-items="center" circular class="p-1-5" color="success"
                                                   display="flex"
                                                   soft>
                                                <Icon icon="local_mall" size="18"></Icon>
                                            </VItem>
                                            <div class="flex-grow-1 ms-2">
                                                <div class="fw-medium">
                                                    {{ notification.title }}
                                                </div>

                                                <span class="font-13">{{ notification.body }}</span>
                                                <div class="text-end font-12 text-muted">{{
                                                        getDifferenceDateTimeAgo(notification.created_at)
                                                    }}
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


                        <div class="d-none d-xl-flex topbar-item">
                            <Tooltip :tooltip="$t(layout.theme??' ')" position="bottom">
                            <div class="topbar-button   hover-effect-circular" @click="toggleTheme">
                                    <Icon v-if="layout.theme==='light'" icon="light_mode" msr></Icon>
                                    <Icon v-if="layout.theme==='dark'" icon="dark_mode" msr></Icon>
                                    <Icon v-if="layout.theme==='auto'" icon="night_sight_auto" msr></Icon>
                            </div>
                            </Tooltip>
                        </div>

                        <template v-if="customer">
                            <div class="dropdown topbar-item">
                                <a class="topbar-button w-100 py-1 px-2 rounded dropdown-toggle arrow-none d-flex align-items-center gap-1  hover-effect-rounded"
                                   data-bs-toggle="dropdown" href="#">
                                    <NetworkImage :from-text="customer.first_name + ' ' +customer.last_name" :src="customer.avatar" circular
                                                  size="32"/>
                                    <span class="font-16 d-none d-md-block">{{ customer.first_name }} <i class="mdi mdi-chevron-down"></i></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                                    <router-link :to="{name: 'customer.profile'}" class="dropdown-item notify-item">
                                        <Icon class="me-2" color="teal" icon="manage_accounts" size="20"/>
                                        <span class="text-teal fw-medium">{{ $t('my_account') }}</span>
                                    </router-link>

                                    <hr class="dashed"/>

                                    <router-link :to="{name: 'customer.favorites.index'}" class="dropdown-item">
                                        <Icon class="me-2" icon="favorite" size="20" />
                                        <span class="fw-medium">{{ $t('favorites') }}</span>
                                    </router-link>

                                    <router-link :to="{name: 'customer.orders.index'}" class="dropdown-item ">
                                        <Icon class="me-2" icon="local_mall" size="20" />
                                        <span class="fw-medium">{{ $t('orders') }}</span>
                                    </router-link>

                                    <router-link :to="{name: 'customer.addresses.index'}" class="dropdown-item">
                                        <Icon class="me-2" icon="home_pin" size="20"/>
                                        <span class="fw-medium">{{ $t('addresses') }}</span>
                                    </router-link>

                                    <hr class="dashed"/>


                                    <router-link :to="{name: 'customer.wallets.index'}" class="dropdown-item">
                                        <Icon class="me-2" icon="account_balance_wallet" size="20" />
                                        <span class="fw-medium">{{ $t('wallet') }}</span>
                                    </router-link>


                                    <router-link :to="{name: 'customer.loyalty_wallets.index'}" class="dropdown-item">
                                        <Icon class="me-2" icon="card_membership" size="20" />
                                        <span class="fw-medium">{{ $t('loyalty_wallet') }}</span>
                                    </router-link>

                                    <hr class="dashed"/>

                                    <!-- item-->
                                    <div class="dropdown-item notify-item cursor-pointer" @click="logout">
                                        <Icon class="me-2" color="danger" icon="logout" size="20"/>
                                        <span class="text-danger fw-medium">{{ $t('logout') }}</span>
                                    </div>


                                </div>
                            </div>

                        </template>
                        <template v-else>
                            <div class="dropdown topbar-item align-items-center">
                                <div class="nav-link">
                                    <Button class="px-3" color="primary" radius="pill" variant="fill" @click="goToLogin">{{ $t('login') }}
                                    </Button>
                                </div>
                            </div>
                        </template>


                        <!--            <li class="dropdown notification-list">-->
                        <!--                <a class="topbar-button right-bar-toggle" href="javascript:void(0);">-->
                        <!--                    <i class="fe fe-settings noti-icon"></i>-->
                        <!--                </a>-->
                        <!--            </li>-->

                    </div>
                </div>
            </div>


        </div>
        <VModal v-model="show_choose_address_modal">
            <Card class="mb-0">
                <CardHeader :title="$t('select_address')" icon="home_pin" type="msr">
                    <div class="float-end" >
                        <Icon class="cursor-pointer" icon="refresh" size="20" color="secondary" @click="()=>getAddresses(true)"></Icon>
                    </div>

                </CardHeader>


                <CardBody>
                    <PageLoading :loading="loading">
                        <div v-if="selected_address" class="border mb-2 p-2 px-2-5 rounded">
                            <p class="fw-medium mb-0-5">{{ selected_address.address }}</p>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0  text-muted">
                                    {{ selected_address.city }}
                                    - {{ selected_address.pincode }}
                                </p>
                                <span class="text-muted">{{ $t('selected') }}</span>
                            </div>
                        </div>

                        <SimpleBar style="max-height: 600px">
                            <div v-for="address in remaining_address" class="border mb-2 p-2 px-2-5 rounded">
                                <p class="fw-medium mb-0-5">{{ address.address }}</p>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0  text-muted">{{ address.city }} - {{ address.pincode }}</p>
                                    <span class="text-primary fw-medium font-14 cursor-pointer" @click="selectAddress(address)">{{ $t('select') }}</span>
                                </div>
                            </div>

                        </SimpleBar>

                        <div class="text-center">
                            <Button color="primary" variant="soft" @click="goToAddAddress" flexed-icon>
                                <Icon icon="add" size="20" class="me-1-5"></Icon>
                                {{ $t('add_address') }}
                            </Button>
                        </div>
                    </PageLoading>
                </CardBody>

            </Card>
        </VModal>
    </div>
</template>

<script lang="ts">
import {SimpleBar} from 'simplebar-vue3';
import {ICustomer} from "@js/types/models/customer";
import {defineComponent} from 'vue';
import {changeTheme, getAddressWithSubscribeState, logoutCustomer,} from "@js/services/state/state_helper";
import {ILayout, useCustomerDataStore, useLayoutStore} from "@js/services/state/states";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {ICustomerAddress} from "@js/types/models/customer_address";
import Request from "@js/services/api/request";
import {IData} from "@js/types/models/data";
import {customerAPI} from "@js/services/api/request_url";
import {Components} from "@js/components/components";
import VModal from "@js/components/VModal.vue";
import {handleException} from "@js/services/api/handle_exception";
import Logo from "@js/components/Logo.vue";
import Tooltip from "@js/components/Tooltip.vue";
import {INotification} from "@js/types/models/notification";
import {ToastNotification} from "@js/services/toast_notification";
import LanguageSelectMixin from "@js/shared/mixins/LanguageSelectMixin";

export default defineComponent({
    components: {Tooltip, Logo, VModal, SimpleBar, ...Components},
    mixins: [UtilMixin, LanguageSelectMixin],
    props: {
        hideSearch: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            loading: false,

            addresses: null as ICustomerAddress[],
            search: null,
            show_search_cancel: false,
            cart_count: 0,
            customer: {} as ICustomer,
            layout: {} as ILayout,
            show_choose_address_modal: false,

            notifications: null as INotification[],
            notification_loading: false,
        }
    },
    watch: {
        '$route.query': {
            immediate: true,
            handler(newVal) {
                if (newVal['q'] != null) {
                    this.search = newVal['q'];
                }
            }
        },
        search(newVal: string) {
            this.show_search_cancel = newVal != null && newVal?.length != 0;
        }
    },
    computed: {
        isLoggedIn() {
            return useCustomerDataStore().isLoggedIn()
        },
        getNotifications() {
            if (this.notifications == null) {
                return 0;
            }
            return this.notifications.slice(0, 6)
        },

        selected_address(): ICustomerAddress | null {
            for (const address of this.addresses ?? []) {
                if (address.selected) {
                    return address;
                }
            }
            return null
        },
        remaining_address(): ICustomerAddress[] {
            return (this.addresses ?? []).filter(function (item) {
                return !item.selected;
            })
        },
    },
    methods: {
        toggleMenu() {
            this.$parent.toggleMenu()
        },
        toggleRightSidebar() {
            this.$parent.toggleRightSidebar()
        },

        goToLogin() {
            this.$router.push({'name': 'customer.login'});
        },
        goToCart() {
            this.$router.push({'name': 'customer.carts.index'});
        },
        async logout() {
            try {
                const response = await Request.postAuth(customerAPI.auth.logout, {});
            } catch (error) {
                handleException(error);
            } finally {
            }
            logoutCustomer();
            this.$router.push({'name': 'customer.login'});
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
        onSearchDone() {
            if (this.$route.name == 'customer.search') {
                let query = this.$route.query;
                this.$router.replace({query: {...query, q: this.search}, silent: true})
            } else {
                this.$router.push({name: 'customer.search', query: {q: this.search}});
            }
        },
        goToAddAddress() {
            this.show_choose_address_modal = false;
            this.$router.push({name: 'customer.addresses.index'});
        },
        onSearchCancel() {
            this.search = '';
            this.onSearchDone();
        },
        showChooseAddress() {
            this.show_choose_address_modal = true;
        },
        async onNotificationShow() {
            if (this.notifications == null) {
                this.notification_loading = true;

                let store = useCustomerDataStore();
                if (store.notifications == null) {
                    try {
                        const response = await Request.getAuth<IData<INotification[]>>(customerAPI.notifications.get);
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
        async selectAddress(address: ICustomerAddress) {
            let store = useCustomerDataStore();
            let addresses: ICustomerAddress[] = this.addresses.map(function (oldAddress) {
                return {
                    ...oldAddress,
                    selected: oldAddress.id == address.id
                }
            });
            try {
                const response = await Request.patchAuth<IData<ICustomerAddress[]>>(customerAPI.addresses.selected(address.id))
                if (response.success()) {
                    store.updateAddresses(addresses);
                    this.addresses = store.addresses;
                }
            } catch (error) {
                handleException(error);
            }
            this.show_choose_address_modal = false;
        },
        async getAddresses(forced = false) {
            const self = this;
            // getAddressWithSubscribeState((addresses: ICustomerAddress[]) => {
            //     self.addresses = addresses;
            // });

            if (this.customer != null) {
                let store = useCustomerDataStore();
                if (!store.addresses_loaded || forced) {
                    const self = this;
                    this.loading = true;
                    try {
                        const response = await Request.getAuth<IData<ICustomerAddress[]>>(customerAPI.addresses.get);
                        if (response.success()) {
                            self.addresses = response.data.data;
                            store.updateAddresses(response.data.data);
                        }
                    } catch (error) {
                        this.show_choose_address_modal = false;
                        handleException(error);
                    } finally {
                        this.loading = false;
                    }
                } else {
                    getAddressWithSubscribeState((addresses: ICustomerAddress[]) => {
                        self.addresses = addresses;
                    });
                }

            }
        },
    },
    created() {
    },
    mounted() {
        const self = this;
        const store = useCustomerDataStore();

        this.customer = store.getUserData()?.data;
        store.$subscribe((mut, state) => {
            self.customer = store.getUserData()?.data;
        });

        // const address_store = useCustomerAddressStore();
        // this.addresses = address_store.addresses;
        this.getAddresses();
        this.cart_count = store.carts?.length;

        store.$subscribe((mutation, state) => {
            self.cart_count = state.carts?.length;
        });
        let layoutStore = useLayoutStore();
        this.layout = layoutStore.layout;
        layoutStore.$subscribe((mut, state) => {
            self.layout = state.layout;
        });
        const query = this.$route.query;
        this.search = query['q'];
        this.show_search_cancel = this.search != null && this.search?.length != 0;


    }

});
</script>

<style scoped>
.navbar-custom {
    /*padding-left: 140px;*/
    /*padding-right: 140px;*/
}

</style>
