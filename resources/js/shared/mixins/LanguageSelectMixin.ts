import {defineComponent} from "vue";
import i18n from "@js/services/i18n";
import {ILanguage} from "@js/types/other";
import {useLanguageStore} from "@js/services/state/states";

export default defineComponent({
    data() {
        return {
            selected_language: null as ILanguage,
        }
    }, computed: {
        languages(): ILanguage[] {
            return [{
                flag: '/assets/images/flags/english.jpg', language: "en", title: "English", rtl: false,
            }, {
                flag: '/assets/images/flags/french.jpg', language: "fr", title: "French", rtl: false,
            }, {
                flag: '/assets/images/flags/spanish.jpg', language: "es", title: "Spanish", rtl: false,
            }, {
                flag: '/assets/images/flags/arabic.jpg', language: "ar", title: "Arabic", rtl: false,
            }, {
                flag: '/assets/images/flags/chinese.jpg', language: "zh", title: "Chinese", rtl: false,
            }];
        },
    }, methods: {
        setLanguage(language: ILanguage) {
            this.selected_language = language;
            i18n.global.locale = language.language;
            useLanguageStore().updateSelectedLanguage(language.language);
            if (language.rtl) {
                this.setToRTL();
            } else {
                this.setToLTR();
            }
        }, setToRTL() {
            const html = document.getElementsByTagName('html')[0];
            const dir = html.getAttribute('dir') ?? 'ltr';
            if (dir != 'rtl') {
                const rootStyle = document.getElementById('root-style-link');
                if (rootStyle) {
                    rootStyle.setAttribute('href', '/css/app-rtl.css');
                }
                html.setAttribute('dir', 'rtl');
            }
        }, setToLTR() {
            const html = document.getElementsByTagName('html')[0];
            const dir = html.getAttribute('dir') ?? 'ltr';
            if (dir != 'ltr') {
                const rootStyle = document.getElementById('root-style-link');
                if (rootStyle) {
                    rootStyle.setAttribute('href', '/css/app.css');
                }
                html.setAttribute('dir', 'ltr');
            }
        }
    }, created() {
        const setLanguageFromCode = (lang: string) => {
            for (const language of this.languages) {
                if (language.language == lang) {
                    this.selected_language = language;
                    return;
                }
            }
        }
        const store = useLanguageStore();
        setLanguageFromCode(store.getSelectedLanguage());
        store.$subscribe((mut, state) => {
            setLanguageFromCode(store.getSelectedLanguage());
        })
    }
});

