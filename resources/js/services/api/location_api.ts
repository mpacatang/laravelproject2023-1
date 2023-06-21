import {ILatLng} from "@js/types/models/models";

export function getLocationMapURL(latitude: string, longitude: string) {
    return `https://maps.google.com/maps?q=${latitude}+${longitude}`;
}

export function getLocationDirectionFromMyLocationURL(latitude: string, longitude: string) {
    return `https://maps.google.com/?saddr=My%20Location&daddr=${latitude}+${longitude}`
    // return `https://maps.google.com/maps?q=${latitude}+${longitude}`;
}

export function getLocationDirectionURL(start: ILatLng, end: ILatLng) {
    return `https://maps.google.com/?saddr=${start.latitude}+${start.longitude}&daddr=${end.latitude}+${end.longitude}`
    // return `https://maps.google.com/maps?q=${latitude}+${longitude}`;
}


export function getMobileNumberCallURL(mobile_number: string) {
    return `tel:${mobile_number}`
}

export function getSendEmailURl(mobile_number: string) {
    return `mailto:${mobile_number}`
}

