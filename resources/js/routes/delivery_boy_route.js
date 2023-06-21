import { routeGroup } from "./routes";
import SelfRegistration from "../pages/delivery_boys/registration.vue";
const other = [{
        path: "registration/", component: SelfRegistration, name: "registration",
    },];
const route = [...other,];
export default routeGroup(route, '/delivery_boy/', 'delivery_boy');
