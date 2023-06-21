export function getLocationMapURL(latitude, longitude) {
    return `https://maps.google.com/maps?q=${latitude}+${longitude}`;
}
export function getLocationDirectionFromMyLocationURL(latitude, longitude) {
    return `https://maps.google.com/?saddr=My%20Location&daddr=${latitude}+${longitude}`;
    // return `https://maps.google.com/maps?q=${latitude}+${longitude}`;
}
export function getLocationDirectionURL(start, end) {
    return `https://maps.google.com/?saddr=${start.latitude}+${start.longitude}&daddr=${end.latitude}+${end.longitude}`;
    // return `https://maps.google.com/maps?q=${latitude}+${longitude}`;
}
export function getMobileNumberCallURL(mobile_number) {
    return `tel:${mobile_number}`;
}
export function getSendEmailURl(mobile_number) {
    return `mailto:${mobile_number}`;
}
