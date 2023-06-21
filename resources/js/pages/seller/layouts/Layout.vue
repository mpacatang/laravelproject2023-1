<template>
    <div>
        <div class="wrapper">

            <TopBar :layout="layout"/>

            <LeftBar :layout="layout"/>
            <div class="content-page">
                <div class="content">
                    <div class="container-fluid mt-3">
                        <slot/>
                    </div>
                </div>
                <Footer/>
            </div>
        </div>
        <RightBar :layout="layout"/>
        <div id="custom-offcanvas-backdrop"></div>
    </div>
</template>


<script lang="ts">
import {defineComponent} from "vue";
import {ILayout, useLayoutStore, useSellerDataStore} from "@js/services/state/states";
import TopBar from "./TopBar.vue";
import LeftBar from "./LeftBar.vue";
import Footer from "./Footer.vue";
import RightBar from "./RightBar.vue";
import {changeLeftbarSize, setLeftbarShow, setTopbarShow, toggleLeftbarEnable} from "@js/services/state/state_helper";
import Request from "@js/services/api/request";
import {IData} from "@js/types/models/data";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";

export default defineComponent({
    components: {TopBar, LeftBar, Footer, RightBar},
    computed: {
        // ...layoutComputed,
    }, data() {
        return {
            layout: useLayoutStore().getLayout() as ILayout,
            html: null as HTMLElement,
            backdrop_element: null as HTMLElement
        }
    },


    methods: {
        addClass(className: string){
            this.html.classList.add(className);
        },
        removeClass(className: string){
            this.html.classList.remove(className);
        },
        showLeftbar(show: boolean) {
            if(show){
                this.addAttributeToHTML('show-leftbar');
            }else{
                this.removeAttributeToHTML('show-leftbar');
            }
        },
        showTopbar(show: boolean){
            if(show){
                this.addAttributeToHTML('show-topbar');
            }else{
                this.removeAttributeToHTML('show-topbar');
            }
        },
        adjustLayout(firstTime = false) {
            let theme = this.layout.theme;
            if (this.layout.theme === 'auto') {
                theme = this.getSystemTheme();
            }
            this.changeTheme(theme);
            this.html.setAttribute('data-layout-mode', this.layout.mode);

            let full_layout = this.layout.full_layout;
            this.removeBackdrop();
            if (this.layout.mode=='full') {
                this.showLeftbar(full_layout.show_leftbar);
                this.showTopbar(full_layout.show_topbar);
                if (full_layout.show_leftbar || full_layout.show_topbar) {
                    this.addBackdrop();
                }
            }


            if(firstTime) {
                if (window.innerWidth < 767) {
                    changeLeftbarSize('full');
                }
            }

            if(window.innerWidth<767){
                if(this.layout.leftbar.enable){
                    this.addBackdrop();
                }
            }


            if(this.layout.leftbar.enable){
                this.addClass('sidebar-enable')
            }else{
                this.removeClass('sidebar-enable');
            }

            this.html.setAttribute('data-sidenav-size', this.layout?.leftbar?.mode??'full');

        },
        getSystemTheme(): 'light' | 'dark' {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
            return 'light';
        },
        changeTheme(theme: 'light' | 'dark') {
            this.html.setAttribute('data-theme', theme);
            if (theme === 'dark') {
                this.html.setAttribute('data-topbar-color', 'dark');
                this.html.setAttribute('data-sidenav-color', 'dark');

            } else {
                if (this.layout.topbar.color) {
                    this.html.setAttribute('data-topbar-color', this.layout.topbar.color);
                }
                if (this.layout.leftbar.color) {
                    this.html.setAttribute('data-sidenav-color', this.layout.leftbar.color);
                }
            }
        },
        addAttributeToHTML(attr: string) {
            this.html.setAttribute(attr, '');
        }, removeAttributeToHTML(attr: string) {
            this.html.removeAttribute(attr,);
        },
        addBackdrop() {
            this.html.classList.add('show-backdrop');
            const self = this;
            document.getElementById('custom-offcanvas-backdrop')?.addEventListener('click', function (e) {
                self.removeBackdrop();
                setTopbarShow(false);
                setLeftbarShow(false);
                toggleLeftbarEnable(false);
            })
        },
        removeBackdrop() {
            this.html.classList.remove('show-backdrop');
        },
        async getInitData() {
            const store = useSellerDataStore();
            if (store.loaded)
                return;


            try {
                const response = await Request.getAuth<IData<any>>(sellerAPI.init.get);
                if (response.success()) {
                    const data = response.data;
                    if (data['seller'] != null) {
                        store.updateUserData(data['seller'])
                    }
                    if (data['shop'] != null) {
                        store.updateShopData(data['shop'])
                    }
                    if (data['module'] != null) {
                        store.updateModuleData(data['module'])
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
        this.html = document.getElementsByTagName('html')[0];
        let layoutStore = useLayoutStore();
        const self = this;
        this.adjustLayout(true);
        layoutStore.$subscribe((mut, state) => {
            self.layout = layoutStore.getLayout();
            self.adjustLayout();
        });
    },
    created() {
        this.getInitData();
    },
})
</script>

<style scoped>
</style>
