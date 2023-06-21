export default {
    data() {
        return {
            selected_identity_type: null
        }
    }, computed: {
        identity_type_helper() {
            return {
                option: {
                    value: 'value', label: 'label'
                },
            };
        }, identity_types() {
            return [{
                value: 'passport', label: "Passport"
            }, {
                value: 'driving_license', label: "Driving License"
            }, {
                value: 'other', label: "Other"
            }];
        }
    }, methods: {
        onChangeIdentityType(id) {
            this.selected_identity_type = id;
        },
    }
};

