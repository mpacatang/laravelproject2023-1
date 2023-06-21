export default {
    data() {
        return {
            food_types: [{
                value: 'veg', label: "Veg"
            }, {
                value: 'non_veg', label: "Non Veg"
            },{
                value: 'vegan', label: "Vegan"
            }, ], food_type_helper: {
                option: {
                    value: 'value', label: 'label'
                },
            }, selected_food_type: null
        }
    }, methods: {
    }
};

