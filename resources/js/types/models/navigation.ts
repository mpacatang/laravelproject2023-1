import {ILatLng, IModel} from "@js/types/models/models";
import Request from "@js/services/api/request";
import {customerAPI} from "@js/services/api/request_url";


export interface INavigationRoute extends IModel {
    weight: number,
    duration: number,
    distance: number,
    points: ILatLng[]
}

export interface INavigation extends IModel {
    routes: INavigationRoute[]
}


export class MapNavigation {

    static decodeRoute(data: any): INavigationRoute {
        let route = data.geometry.coordinates;
        let points: ILatLng[] = [];
        for (const point of route) {
            points.push({
                latitude: point[1],
                longitude: point[0]
            })
        }
        return {
            weight: data.weight,
            distance: data.distance,
            duration: data.duration,
            points: points
        }

    }

    static async getNavigation(starting: ILatLng, destination: ILatLng): Promise<INavigation | null> {
        let body = {
            lat_1: starting.latitude,
            lng_1: starting.longitude,
            lat_2: destination.latitude,
            lng_2: destination.longitude,
        }

        console.log(starting)

        const response = await Request.postAuth<any>(customerAPI.navigation.get_route, body);
        if (response.success()) {
            let routesData = response.data.routes;
            let routes = [];
            for (const routeData of routesData) {
                routes.push(this.decodeRoute(routeData));
            }
            return {
                routes: routes
            };
        } else {
            return null;
        }

    }

    static findBestRoute(navigation: INavigation): INavigationRoute|null {
        let best: INavigationRoute = null ;
        for (const route of navigation.routes) {
            if (best == null) {
                best = route;
            } else if (best.weight < route.weight) {
                best = route;
            }
        }
        return best;
    }
}
