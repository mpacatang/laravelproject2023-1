import {IRoute, routeGroup} from "./routes";
import Step_1 from "@js/pages/installations/step_1.vue";
import Step_2 from "@js/pages/installations/step_2.vue";
import Step_3 from "@js/pages/installations/step_3.vue";
import Step_4 from "@js/pages/installations/step_4.vue";
import Step_5 from "@js/pages/installations/step_5.vue";


const route: IRoute[] = [
    {
        path: "step_1/", component: Step_1, name: "step_1"
    },
    {
        path: "step_2/", component: Step_2, name: "step_2"
    },
    {
        path: "step_3/", component: Step_3, name: "step_3"
    },
    {
        path: "step_4/", component: Step_4, name: "step_4"
    },
    {
        path: "step_5/", component: Step_5, name: "step_5"
    },
];


export default routeGroup(route, '/installations/', 'installations');
