import dateFormat from 'dateformat';
import {DateUtil} from "@js/shared/utils";
import {
    getCurrencyPosition,
    getCurrencySymbol,
    getDigitAfterDecimal,
    getTimeFormat
} from "@js/services/state/state_helper";
import {Images} from "@js/shared/constant";
import {BusinessSetting} from "@js/types/models/business_setting";

export default {
    data() {
        const categoryPlaceholder = Images.categoryPortraitPlaceholder;
        const shopPortraitPlaceholder = Images.shopPortraitPlaceholder;
        const shopLandscapePlaceholder = Images.shopLandscapePlaceholder;
        const productPortraitPlaceholder = Images.productPortraitPlaceholder;
        const productLandscapePlaceholder = Images.productLandscapePlaceholder;
        const appName = BusinessSetting.instance?.business_name ?? "Shopy";
        const purchaseLink = "https://codecanyon.net/item/shopy-multivendor-ecommerce-food-grocery-pharmacy-one-stop-solution/42363182";
        return {
            categoryPlaceholder,
            shopLandscapePlaceholder,
            shopPortraitPlaceholder,
            productPortraitPlaceholder,
            productLandscapePlaceholder,
            appName,
            purchaseLink,
        }
    }, computed: {
        isDemo() {
            return BusinessSetting.instance?.demo ?? false;
        },
    }, methods: {
        addExternalJS(src: string, callback: Function=null) {
            let s,
                r;
            r = false;
            s = document.createElement('script');
            s.type = 'text/javascript';
            s.src = src;
            s.onload = s.onreadystatechange = function () {
                //console.log( this.readyState ); //uncomment this line to see which ready states are called.
                if (!r && (!this.readyState || this.readyState == 'complete')) {
                    r = true;
                    callback && callback();
                }
            };
            document.head.appendChild(s)
        },
        goToBuyShopy() {
            window.open(this.purchaseLink, '_blank')
        },

        setTitle(title: string) {
            document.title = title + " | " + this.appName;
        },
        $t(text: string) {
            const autoTrans = () => {
                let trans = text.replaceAll("_", " ");
                return (trans.charAt(0).toUpperCase() + trans.slice(1));
            }
            try {
                let trans = this.$i18n.t(text);
                return trans == text ? autoTrans() : trans;
            } catch (e) {
                return "";
            }
        },

        simplebar_scroll(simplebar, fast = false) {
            const maxPosition = simplebar.getContentElement().scrollHeight;
            const time = fast ? 6 : 12;

            let scrollPosition = simplebar.getScrollElement().scrollTop;
            const interval = setInterval(function () {
                scrollPosition += 2;
                simplebar.getScrollElement().scrollTop = scrollPosition;
                if (scrollPosition > maxPosition) clearInterval(interval);
            }, time);
        },

        getFormattedTimeFromTime(time: string, params?: { second?: boolean, minute?: boolean, hour?: boolean, meridiem?: boolean, separator?: string }): string {

            params = {
                ...{
                    second: true, minute: true, hour: true, meridiem: true, separator: ":"
                }, ...params,
            };
            if (time == null) return '';
            let times = time.split(":");


            let hour = "0", minute = "0", second = "0";


            if (times.length == 3) {
                hour = times[0];
                minute = times[1];
                second = times[2];
            } else if (times.length == 2) {
                hour = times[0];
                minute = times[1];
            } else {
                hour = times[0];
            }
            let meridiem = 'AM';
            if (params.meridiem) {
                if (parseInt(time) > 12) {
                    meridiem = 'PM';
                    hour = (parseInt(time) - 12).toString();
                }
            }

            let timeText = "";
            if (params.hour) timeText += hour;
            if (params.minute) {
                if (params.hour) timeText += params.separator
                timeText += minute
            }
            if (params.second) {
                if (params.minute) timeText += params.separator
                timeText += second;
            }
            if (params.meridiem) {
                timeText += (" " + meridiem);
            }


            return timeText;
        },
        getFormattedDateTime(date: String | Date, formatter?: { month?: string }) {
            if(date==null)
                return "-";
            if (!(date instanceof Date)) {
                date = new Date(date.toString());
            }

            const monthFormat = formatter?.month ?? "mmmm";

            const timeFormat = getTimeFormat();
            let format = "dd " + monthFormat + ", yyyy H:MM";
            if (timeFormat === '12h') {
                format = "dd " + monthFormat + ", yyyy h:MM TT";
            }
            return dateFormat(DateUtil.convertUTCDateToLocalDate(date), format);
        },
        getFormattedDate(date, params?: { date?: boolean | undefined, month?: boolean | undefined, year?: boolean | undefined, short?: boolean | undefined } | undefined) {
            try {
                if (!(date instanceof Date)) {
                    date = new Date(date.toString());
                }
                let formatter = "";
                const dateFormatter = params?.short ? "dd" : "dd";
                const monthFormatter = params?.short ? "mmm" : "mmmm";
                const yearFormatter = params?.short ? "yy" : "yyyy";
                if (params != null) {
                    if (params.date == null || params.date) {
                        formatter += (dateFormatter + " ");
                    }
                    if (params.month == null || params.month) {
                        formatter += (monthFormatter + ((params.year == null || params.year) ? ", " : ""));
                    }
                    if (params.year == null || params.year) {
                        formatter += (yearFormatter);
                    }
                } else {
                    formatter = dateFormatter + " " + monthFormatter + ", " + yearFormatter;
                }

                return dateFormat(DateUtil.convertUTCDateToLocalDate(date), formatter);
            } catch (e) {
                return "-";
            }
        },
        getFormattedTime(date) {
            if (!(date instanceof Date)) {
                date = new Date(date.toString());
            }
            const timeFormat = getTimeFormat();
            let format = "H:MM";
            if (timeFormat === '12h') {
                format = "h:MM TT";
            }
            return dateFormat(DateUtil.convertUTCDateToLocalDate(date), format);
        },
        getPrecise(number): number {
            const digits = 2;
            let parsed = parseFloat(number);
            for (let i = digits; i >= 0; i--) {
                let cur = parseFloat(parsed.toFixed(i));
                if (cur === parsed) {
                    return cur;
                }
            }
            return parseFloat(number.toFixed(digits));
        },
        getPreciseCurrency(currency): number {
            const digits = getDigitAfterDecimal();
            let parsed = parseFloat(currency);
            for (let i = digits; i >= 0; i--) {
                let cur = parseFloat(parsed.toFixed(i));
                if (cur === parsed) {
                    return cur;
                }
            }
            return parseFloat(currency.toFixed(digits));
        },
        getDifferenceDateTimeAgo(date: string): string {
            const diff = Math.abs(Date.parse(date) - Date.now()) / 1000;
            const days = Math.floor(diff / (60 * 60 * 24));

            if (days > 1) {
                return days + " " + this.$t("days_ago");
            }
            if (days > 0) {
                return days + " " + this.$t("day_ago");
            }
            const hours = Math.floor(diff / (60 * 60));

            if (hours > 1) {
                return hours + " " + this.$t("hours_ago");
            }
            if (hours > 0) {
                return hours + " " + this.$t("hour_ago");
            }
            const minutes = Math.floor(diff / (60));

            if (minutes > 1) {
                return minutes + " " + this.$t("minutes_ago");
            }
            if (minutes > 0) {
                return minutes + " " + this.$t("minute_ago");
            }

            if (diff > 20) {
                return diff + " " + this.$t("seconds_ago");
            }

            return this.$t('just_now');
        },
        getDurationInText(duration: number): string {
            const days = Math.floor(duration / (60 * 60 * 24));

            if (days > 1) {
                return days + " " + this.$t("days");
            }
            if (days > 0) {
                return days + " " + this.$t("day");
            }
            const hours = Math.floor(duration / (60 * 60));

            if (hours > 1) {
                return hours + " " + this.$t("hours");
            }
            if (hours > 0) {
                return hours + " " + this.$t("hour");
            }
            const minutes = Math.floor(duration / (60));

            if (minutes > 1) {
                return minutes + " " + this.$t("minutes");
            }
            if (minutes > 0) {
                return minutes + " " + this.$t("minute");
            }

            return duration + " " + this.$t("seconds");
        },
        getFormattedCurrency(amount?: number, option?: { currencySpace: boolean, default?: string }) {

            const sign = getCurrencySymbol();
            const position = getCurrencyPosition();

            let currencySpace = option?.currencySpace ?? true;
            if (amount == null) {
                return option?.default ?? "-";
            }


            return ((amount < 0) ? "- " : "") + (position == 'left' ? (sign + (currencySpace ? " " : "")) : "") + this.getPrecise(Math.abs(amount)) + (position == 'right' ? ((currencySpace ? " " : "") + sign) : "");

        },

    }
};

