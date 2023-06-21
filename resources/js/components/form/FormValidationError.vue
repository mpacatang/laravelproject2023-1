<template>
    <div>
        <div v-for="(error, index) in getErrors" :key="key(index)" class="invalid-feedback">
            • {{ error }}
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
    props: {
        errors: {
            type: Object,
            required: true
        },
        name: {
            type: String,
            required: true
        },
    },
    methods: {
        key(index: string) {
            return `v_e_${index}_${Math.random()}`
        },
        errorFor(field) {
            if (this.errors != null) {
                let error = this.errors[field];
                if (error != null) {
                    if (typeof error === 'string') {
                        return [error];
                    }
                    return error;
                }
            }
            return null;
        },
    },
    computed: {

        getErrors() {
            if (this.errors != null) {
                let error = this.errors[this.name];
                if (error != null) {
                    if (typeof error === 'string') {
                        return [error];
                    }
                    return error;
                }
            }
            return null;
        }
    }

});
</script>

<style scoped>
.form-control.is-invalid ~ div > .invalid-feedback {
    display: block;
}

div.is-invalid ~ div > .invalid-feedback {
    display: block;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #f35d5d;
}


</style>
