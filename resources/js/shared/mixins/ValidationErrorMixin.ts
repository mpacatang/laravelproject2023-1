import {defineComponent, ref} from "vue";

export default defineComponent({

    data() {
        return {
            errors: ref(null)
        }
    }, methods: {
        errorFor(field: string): any {
            if (this.errors != null) {
                console.log(this.errors[field]);
                let error = this.errors[field];
                if (error != null) {
                    if (typeof error === 'string') {
                        return [error];
                    }
                    return error;
                }
            }
            return null;
        }, addError(field, error) {
            if (this.errors == null) {
                this.errors = {};
            }
            this.errors[field] = [error];
        }, clearAllError() {
            this.errors = {};
        }, hasAnyError() {
            return Object.keys(this.errors).length != 0;
        }

    }
});

