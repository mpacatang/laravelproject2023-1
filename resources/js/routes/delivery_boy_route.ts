import {IRoute, routeGroup} from "./routes";
import SelfRegistration from "../pages/delivery_boys/registration.vue";


const other: IRoute[] = [{
    path: "registration/", component: SelfRegistration, name: "registration",
},]

const route: IRoute[] = [...other,];


export default routeGroup(route, '/delivery_boy/', 'delivery_boy');
