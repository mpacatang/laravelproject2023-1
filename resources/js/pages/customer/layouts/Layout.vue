<template>
    <div v-if="false" id="customer-layout">

        <TopBar1 :hide-search="hideSearch"/>


        <div class="content-page mt-2-5 mt-md-3">
            <div class="content">
                <div class="wrapper">
                    <slot/>
                </div>
            </div>
        </div>

        <div v-if="can_hide_footer" class="d-flex p-2-5 px-3 justify-content-between border-top align-items-center"
             @click="show_footer=!show_footer">
            <h5 class="m-0 fw-medium">More about {{ appName }}</h5>
            <Icon :icon="show_footer?'expand_less':'expand_more'"></Icon>
        </div>
        <Footer v-show="show_footer"/>
    </div>
    <div id="customer-layout-2">

        <TopBar2 :hide-search="hideSearch">
            <slot name="topbar_extra"></slot>
        </TopBar2>


        <div class="content-page mt-2-5">
            <div class="content">
                <div class="wrapper">
                    <slot/>
                </div>
            </div>
        </div>

        <div v-if="can_hide_footer" class="d-flex p-2-5 mt-3 px-3 justify-content-between border-top align-items-center"
             @click="show_footer=!show_footer">
            <h5 class="m-0 fw-medium">More about {{ appName }}</h5>
            <Icon :icon="show_footer?'expand_less':'expand_more'"></Icon>
        </div>
        <Footer class="mt-3" v-show="show_footer"/>
    </div>
</template>

<script lang="ts">


import {defineComponent} from "vue";
import {ILayout, useCustomerDataStore, useLayoutStore} from "@js/services/state/states";
import {IModule, Module} from "@js/types/models/module";
import Icon from "@js/components/Icon.vue";
import VItem from "@js/components/VItem.vue";
import Request from "@js/services/api/request";
import {IData} from "@js/types/models/data";
import {customerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import Footer from "@js/components/Footer.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import Layout from "@js/pages/admin/layouts/Layout.vue";
import TopBar1 from "@js/pages/customer/layouts/TopBar1.vue";
import TopBar2 from "@js/pages/customer/layouts/TopBar2.vue";

export default defineComponent({
    components: {
        TopBar2,
        TopBar1,
         Layout, Footer, VItem, Icon},
    mixins: [UtilMixin],
    props: {
        hideSearch: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            layout: {} as ILayout,
            selected_module: {} as IModule,
            show_footer: true,
            can_hide_footer: false
        }
    },
    computed: {

        remain_module() {
            const self = this
            return Module.getCachedModules().filter((module) => {
                return self.selected_module.id != module.id;
            })
        }
    },
    methods: {
        adjustLayout() {
            let html = document.getElementsByTagName('html')[0];
            let theme = this.layout.theme;
            if (this.layout.theme === 'auto') {
                theme = this.getSystemTheme();
            }
            html.setAttribute('data-theme', theme);
            html.setAttribute('data-topbar-color', theme);
            html.removeAttribute('data-layout-mode');
        },
        getSystemTheme() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
            return 'light';
        },
        watch() {
            const self = this;
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    self.adjustLayout();
                });
            }
        },
        async getInitData() {
            const store = useCustomerDataStore();
            if (store.loaded)
                return;

            try {
                const response = await Request.getAuth<IData<any>>(customerAPI.init.get);
                if (response.success()) {
                    const data = response.data;

                    if (data['customer'] != null) {
                        store.updateUserData(data['customer'])
                    }
                    if (data['customer_addresses'] != null) {
                        store.updateAddresses(data['customer_addresses'])
                    }
                    if (data['carts'] != null) {
                        store.replaceAllCart(data['carts'])
                    }

                }
                store.setLoaded();
            } catch (error) {
                handleException(error);
            } finally {
            }


        },


    },
    mounted() {
        let layoutStore = useLayoutStore();
        this.layout = layoutStore.layout;
        const self = this;
        this.adjustLayout();

        if (window.screen.width < 576) {
            this.show_footer = false;
            this.can_hide_footer = true;
        }

        layoutStore.$subscribe((mut, state) => {
            self.layout = state.layout;
            self.adjustLayout();
        });
        this.watch();
        let store = useCustomerDataStore();
        this.selected_module = Module.getModuleFromId(store.module_id);


    },
    created(){
        this.getInitData();
    }

})
</script>

<style scoped>

.collapsed span {
    visibility: hidden;
}

</style>
