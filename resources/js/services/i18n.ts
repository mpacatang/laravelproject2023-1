import {createI18n} from 'vue-i18n';
import {useLanguageStore} from "@js/services/state/states";
import NavigatorService from "@js/services/navigator_service";

/**
 * Load locale messages
 *
 * The loaded `JSON` locale messages is pre-compiled by `@intlify/vue-i18n-loader`, which is integrated into `vue-cli-plugin-i18n`.
 * See: https://github.com/intlify/vue-i18n-loader#rocket-i18n-resource-pre-compilation
 */
function loadLocaleMessages() {
    // @ts-ignore
    const locales = require.context('./lang', true, /[A-Za-z\d-_,\s]+\.json$/i);
    const messages = {};
    locales.keys().forEach((key) => {
        const matched = key.match(/([A-Za-z\d-_]+)\./i);
        if (matched && matched.length > 1) {
            const locale = matched[1];
            messages[locale] = locales(key);
        }
    });
    return messages;
}

const setDateTimeFormats = {
    short: {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    },
    long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long',
        hour: 'numeric',
        minute: 'numeric',
    },
};

const dateTimeFormats = {
    en: setDateTimeFormats,
    es: setDateTimeFormats,
    de: setDateTimeFormats,
    'en-GB': setDateTimeFormats,
};

// const code = useLanguageStore().getSelectedLanguage();

export default createI18n({
    locale: process.env.VUE_APP_I18N_LOCALE || "en",
    fallbackLocale: process.env.VUE_APP_I18N_FALLBACK_LOCALE || 'en',
    messages: loadLocaleMessages(),
    dateTimeFormats,
    missing: (locale: any, key: any, vm?: any) => {
        NavigatorService.addTrans(key);
    },
});
