import { defineComponent } from "vue";
export default defineComponent({
    data() {
        return {
            selected_delivery_time_type: 'minutes'
        };
    }, computed: {
        delivery_time_types() {
            return [{
                    value: 'minutes', label: this.$t('minutes')
                }, {
                    value: 'hours', label: this.$t('hours')
                }, {
                    value: 'days', label: this.$t('days')
                },];
        },
        delivery_time_type_helper() {
            return {
                option: {
                    value: 'value', label: 'label'
                },
            };
        }
    }, methods: {
        onChangeDeliveryTypeType(id) {
            this.selected_delivery_time_type = id;
        },
    }
});
