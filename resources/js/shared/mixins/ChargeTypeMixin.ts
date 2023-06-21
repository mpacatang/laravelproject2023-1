export default {
    data() {
        return {
            charge_types: [{
                value: 'percent', label: "Percent"
            }, {
                value: 'amount', label: "Amount"
            },], charge_type_helper: {
                option: {
                    value: 'value', label: 'label'
                },
            }, selected_discount_type: null
        }
    }, methods: {
        onChangeDiscountType(id) {
            this.selected_discount_type = id;
        },
    }
};

