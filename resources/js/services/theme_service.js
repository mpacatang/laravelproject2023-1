export function setLightTheme() {
    let html = document.getElementsByTagName('html')[0];
    html?.setAttribute('data-theme', 'light');
}
