import {useAdminDataStore} from "@js/services/state/states";
import {RequestCache} from "@js/services/api/cache";

export default {

    data() {
        return {
            selected_module_id: null
        }
    }, methods: {
        onChangeAdminModule(module_id) {

        }
    }, mounted() {
        const store = useAdminDataStore();
        this.selected_module_id = store.module_id;
        store.$subscribe((mutate, state) => {
            if (this.selected_module_id !== state.module_id) {
                this.selected_module_id = state.module_id;
                RequestCache.clear();
                this.onChangeAdminModule(state.module_id);
            }

        })
    }
};

