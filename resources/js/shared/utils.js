import _ from 'lodash';
export const chartColors = () => ['#7e50fa', '#10c469', '#ff679b', '#fd7e14', '#B9506C'];
export class DateUtil {
    static now() {
        return new Date();
    }
    static tomorrow() {
        let date = new Date();
        date.setDate(date.getDate() + 1);
        return date;
    }
    static convertUTCDateToLocalDate(date) {
        return date;
        // var newDate = new Date(date);
        // // return newDate;
        // newDate.setMinutes(date.getMinutes() - date.getTimezoneOffset());
        // return newDate;
    }
    static isSameDay(date1, date2) {
        if (!(date1 instanceof Date)) {
            date1 = new Date(date1.toString());
        }
        if (!(date2 instanceof Date)) {
            date2 = new Date(date2.toString());
        }
        return date1.getFullYear() == date2.getFullYear() && date1.getMonth() == date2.getMonth() && date1.getDay() == date2.getDay();
    }
}
export const deepCopy = (object) => JSON.parse(JSON.stringify(object));
export const deepCompare = (first, second) => {
    return _.isEqual(first, second);
};
export const base64 = {
    decode: s => Uint8Array.from(atob(s), c => c.charCodeAt(0)), encode: (b) => btoa(b)
};
export const dateCompareFromString = (a, b) => Date.parse(a) - Date.parse(b);
export const stringToUTCDateString = (a) => a == null ? null : new Date(a).toUTCString();
export class MapUtil {
    static calculateDistance(point1, point2) {
        let lat1 = point1.latitude;
        let lon1 = point1.longitude;
        let lat2 = point2.latitude;
        let lon2 = point2.longitude;
        if ((lat1 == lat2) && (lon1 == lon2)) {
            return 0;
        }
        else {
            var radlat1 = Math.PI * lat1 / 180;
            var radlat2 = Math.PI * lat2 / 180;
            var theta = lon1 - lon2;
            var radtheta = Math.PI * theta / 180;
            var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
            if (dist > 1) {
                dist = 1;
            }
            dist = Math.acos(dist);
            dist = dist * 180 / Math.PI;
            dist = dist * 60 * 1.1515;
            dist = dist * 1609.344;
            // if (unit=="K") { dist = dist * 1.609344 }
            // if (unit=="N") { dist = dist * 0.8684 }
            return dist;
        }
    }
    static getDarkStyle() {
        return [{
                "featureType": "all", "elementType": "geometry", "stylers": [{
                        "color": "#242f3e"
                    }]
            }, {
                "featureType": "all", "elementType": "labels.text.stroke", "stylers": [{
                        "lightness": -80
                    }]
            }, {
                "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [{
                        "color": "#746855"
                    }]
            }, {
                "featureType": "administrative.locality", "elementType": "labels.text.fill", "stylers": [{
                        "color": "#d59563"
                    }]
            }, {
                "featureType": "poi", "elementType": "labels.text.fill", "stylers": [{
                        "color": "#d59563"
                    }]
            }, {
                "featureType": "poi.park", "elementType": "geometry", "stylers": [{
                        "color": "#263c3f"
                    }]
            }, {
                "featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [{
                        "color": "#6b9a76"
                    }]
            }, {
                "featureType": "road", "elementType": "geometry.fill", "stylers": [{
                        "color": "#2b3544"
                    }]
            }, {
                "featureType": "road", "elementType": "labels.text.fill", "stylers": [{
                        "color": "#9ca5b3"
                    }]
            }, {
                "featureType": "road.arterial", "elementType": "geometry.fill", "stylers": [{
                        "color": "#38414e"
                    }]
            }, {
                "featureType": "road.arterial", "elementType": "geometry.stroke", "stylers": [{
                        "color": "#212a37"
                    }]
            }, {
                "featureType": "road.highway", "elementType": "geometry.fill", "stylers": [{
                        "color": "#746855"
                    }]
            }, {
                "featureType": "road.highway", "elementType": "geometry.stroke", "stylers": [{
                        "color": "#1f2835"
                    }]
            }, {
                "featureType": "road.highway", "elementType": "labels.text.fill", "stylers": [{
                        "color": "#f3d19c"
                    }]
            }, {
                "featureType": "road.local", "elementType": "geometry.fill", "stylers": [{
                        "color": "#38414e"
                    }]
            }, {
                "featureType": "road.local", "elementType": "geometry.stroke", "stylers": [{
                        "color": "#212a37"
                    }]
            }, {
                "featureType": "transit", "elementType": "geometry", "stylers": [{
                        "color": "#2f3948"
                    }]
            }, {
                "featureType": "transit.station", "elementType": "labels.text.fill", "stylers": [{
                        "color": "#d59563"
                    }]
            }, {
                "featureType": "water", "elementType": "geometry", "stylers": [{
                        "color": "#17263c"
                    }]
            }, {
                "featureType": "water", "elementType": "labels.text.fill", "stylers": [{
                        "color": "#515c6d"
                    }]
            }, {
                "featureType": "water", "elementType": "labels.text.stroke", "stylers": [{
                        "lightness": -20
                    }]
            }];
    }
}
