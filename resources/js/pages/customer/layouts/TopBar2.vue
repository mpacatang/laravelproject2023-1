<template>
    <div id="topbar">
        <div class="top-banner d-none d-sm-flex">
            <div class="d-flex align-items-center">
                <Icon class="me-2" icon="call" size="18"/>
                <span>{{ getData.mobile_number }}</span>
            </div>
            <router-link :to="{name:'customer.coupons.index'}" class="d-flex nav-link">
                <span class="fw-medium" style="letter-spacing: 0.2px">New offers available</span>
                <hr class="vr m-0 mx-1-5 my-0-5">
                <span class="fw-medium text-purple ls-0-5">
                {{ $t('coupons') }}
                </span>
            </router-link>

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a aria-expanded="false" aria-haspopup="false"
                       class="dropdown-toggle nav-link d-flex align-items-center arrow-none" data-bs-toggle="dropdown"
                       href="#" role="button">
                        {{ selected_language.title }}
                        <Icon class="ms-1" icon="expand_more" size="16"></Icon>
                    </a>
                    <div class="dropdown-menu dropdown-menu-animated  mt-1">
                        <a v-for="language in languages" class="dropdown-item" href="javascript:void(0);"
                           @click="setLanguage(language)">
                            <img :src="language.flag" alt="customer-image" class="me-2" height="12">
                            <span class="align-middle">{{ language.title }}</span>
                        </a>

                    </div>
                </div>
                <div class="align-items-center d-none d-md-flex ms-3">
                    <hr class="vr m-0 me-3 my-0-5">
                    <a class="me-3 nav-link" href="">{{ $t('contact_us') }}</a>
                    <a class="me-3 nav-link" href="">{{ $t('about_us') }}</a>
                    <a class="me-3 nav-link" href="">{{ $t('FAQ') }}</a>
                </div>
            </div>
        </div>
        <div class="banner ">
            <router-link :to="{name:'customer.home'}">
                <Logo class="lh-0"/>
            </router-link>
            <div class="address-bar">
                <div v-if="isLoggedIn" class="d-flex align-items-center py-1 cursor-pointer" @click="showChooseAddress">
                    <Icon class="" icon="home_pin" size="19"/>
                    <div class="align-items-center d-none d-sm-flex ms-2">
                        <div v-if="selected_address">
                            <p class="mb-0 max-lines-1 font-14 fw-medium" style="max-width: 130px">
                                {{ selected_address.address }}
                            </p>
                        </div>
                        <div v-else>
                            <p class="mb-0 font-14 text-nowrap">{{ $t('select_an_address') }}</p>
                        </div>
                        <Icon class="ms-1-5" icon="expand_more" size="16"/>
                    </div>
                </div>
                <hr v-if="isLoggedIn" class="vr m-0 mx-2">
                <div class="py-1 d-flex align-items-center">
                    <form class=" me-3" v-on:submit.prevent="onSearchDone">
                        <input v-model="search" class="input-search" placeholder="Search..."/>
                    </form>
                    <Icon class="" icon="search" size="18" @click="onSearchDone"></Icon>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="cursor-pointer">
                    <div v-if="isLoggedIn" class="dropdown topbar-item ">
                        <a class="topbar-button nav-link dropdown-toggle arrow-none d-flex align-items-center hover-effect-rounded-lg"
                           data-bs-toggle="dropdown" href="#">
                            <NetworkImage :from-text="customer.first_name + ' ' +customer.last_name"
                                          :src="customer.avatar"
                                          circular size="32"/>
                            <div class="ms-1-5 d-none d-lg-block ">
                                <p class="mb-n1 font-11 text-muted fw-medium">Hi, {{ customer.first_name }}</p>
                                <span class="mt-0 fw-medium font-17">{{ $t('account') }}</span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-animated mt-2 profile-dropdown">
                            <router-link :to="{name: 'customer.profile'}" class="dropdown-item notify-item">
                                <Icon class="me-2" color="teal" icon="manage_accounts" size="20"/>
                                <span class="text-teal fw-medium">{{ $t('my_account') }}</span>
                            </router-link>

                            <hr class="dashed"/>

                            <router-link :to="{name: 'customer.chats.index'}" class="dropdown-item">
                                <Icon class="me-2" icon="chat_bubble" size="20"/>
                                <span class="fw-medium">{{ $t('chats') }}</span>
                            </router-link>

                            <router-link :to="{name: 'customer.favorites.index'}" class="dropdown-item">
                                <Icon class="me-2" icon="favorite" size="20"/>
                                <span class="fw-medium">{{ $t('favorites') }}</span>
                            </router-link>

                            <router-link :to="{name: 'customer.orders.index'}" class="dropdown-item ">
                                <Icon class="me-2" icon="local_mall" size="20"/>
                                <span class="fw-medium">{{ $t('orders') }}</span>
                            </router-link>

                            <router-link :to="{name: 'customer.addresses.index'}" class="dropdown-item">
                                <Icon class="me-2" icon="home_pin" size="20"/>
                                <span class="fw-medium">{{ $t('addresses') }}</span>
                            </router-link>

                            <hr class="dashed"/>


                            <router-link :to="{name: 'customer.wallets.index'}" class="dropdown-item">
                                <Icon class="me-2" icon="account_balance_wallet" size="20"/>
                                <span class="fw-medium">{{ $t('wallet') }}</span>
                            </router-link>


                            <router-link :to="{name: 'customer.loyalty_wallets.index'}" class="dropdown-item">
                                <Icon class="me-2" icon="card_membership" size="20"/>
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
                    <router-link v-else :to="{name:'customer.login'}"
                                 class="d-flex align-items-center nav-link hover-effect-rounded-lg">
                        <i class="fe fe-user font-22"></i>
                        <div class="ms-1-5  d-none d-md-block">
                            <p class="mb-n1 font-11 text-muted fw-medium">{{ $t('sign_in') }}</p>
                            <span class="mt-0 fw-medium font-17">{{ $t('account') }}</span>
                        </div>


                    </router-link>
                </div>

                <template v-if="isLoggedIn">
                    <router-link :to="{name: 'customer.favorites.index'}"
                                 class="fs-16 icon cursor-pointer d-none d-sm-block ms-3">
                        <i class="fe fe-heart font-22"></i>
                    </router-link>
                    <router-link :to="{name: 'customer.carts.index'}"
                                 class="fs-16 icon d-none d-sm-block ms-3 cursor-pointer">
                        <div class="position-relative">
                            <i class="fe fe-shopping-cart font-22"></i>
                            <span v-if="cart_count>0"
                                  class="bg-danger rounded-circle d-flex align-items-center text-white"
                                  style="position: absolute;top: -4px;right: -9px; font-size: 10px; padding: 0 5px">
                            {{ cart_count }}
                        </span>
                        </div>
                    </router-link>
                </template>

                <div class="d-none d-lg-block ms-3">
                    <Tooltip :tooltip="$t(layout.theme??' ')" position="bottom">
                        <div class="cursor-pointer" style="margin-bottom: 4px" @click="toggleTheme">
                            <Icon v-if="layout.theme==='light'" icon="light_mode" msr size="24"></Icon>
                            <Icon v-if="layout.theme==='dark'" icon="dark_mode" msr size="24"></Icon>
                            <Icon v-if="layout.theme==='auto'" icon="night_sight_auto" msr size="24"></Icon>
                        </div>
                    </Tooltip>
                </div>

            </div>
        </div>
        <slot></slot>
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
import {BusinessSetting} from "@js/types/models/business_setting";

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
        getData() {
            return BusinessSetting.instance;
        },
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
