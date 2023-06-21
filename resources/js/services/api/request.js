import axios from "axios";
import Response from "./response";
import { getAdminSelectedModuleId, getAuthTokenFromUrl, getCustomerSelectedModuleId } from "@js/services/state/state_helper";
import { RequestCache } from "@js/services/api/cache";
export default class Request {
    // static BASE_URL = 'http://192.168.1.72:8000/api/v1/';
    static BASE_URL = '/api/v1/';
    static async get(url, config, option = { forced: false, without_base: false }) {
        await this.sleep();
        const fUrl = option.without_base ? this.getCleanedUrl(url) : this.BASE_URL + this.getCleanedUrl(url);
        if (!option.forced) {
            const data = RequestCache.get(fUrl);
            if (data != null) {
                return new Response(JSON.parse(data));
            }
        }
        const rData = await axios.get(fUrl, config);
        const response = new Response(rData);
        if (response.success()) {
            RequestCache.set(fUrl, JSON.stringify(rData));
        }
        return response;
    }
    static async post(url, data, config) {
        await this.sleep();
        return new Response(await axios.post(this.BASE_URL + this.getCleanedUrl(url), data, config));
    }
    static async patch(url, data, config) {
        await this.sleep();
        return new Response(await axios.patch(this.BASE_URL + this.getCleanedUrl(url), data, config));
    }
    static async put(url, data, config) {
        await this.sleep();
        return new Response(await axios.put(this.BASE_URL + this.getCleanedUrl(url), config));
    }
    static async delete(url, config) {
        await this.sleep();
        return new Response(await axios.delete(this.BASE_URL + this.getCleanedUrl(url), config));
    }
    static getAuth(url, option = { forced: false, without_base: false }) {
        return this.get(url, this._getAuthHeaderConfig(url), option);
    }
    static postAuth(url, data, config) {
        return this.post(url, data, this._getAuthHeaderConfig(url, config));
    }
    static patchAuth(url, data, config) {
        return this.patch(url, data, this._getAuthHeaderConfig(url, config));
    }
    static putAuth(url, data, config) {
        return this.put(url, data, this._getAuthHeaderConfig(url, config));
    }
    static deleteAuth(url, config) {
        return this.delete(url, this._getAuthHeaderConfig(url, config));
    }
    static _getAuthHeaderConfig(url, config) {
        const token = getAuthTokenFromUrl(url);
        return {
            ...config, headers: {
                Authorization: 'Bearer ' + token ?? "",
            }
        };
    }
    static getImageData(imageData) {
        if (imageData == null)
            return null;
        return imageData.toString().split(";base64,").pop();
    }
    static sleep() {
        return;
        return new Promise(resolve => setTimeout(resolve, 1000));
    }
    static getCleanedUrl(url) {
        return url.endsWith('/') ?
            url.slice(0, -1) :
            url;
    }
    /**
     * @deprecated since version 1.0.1. Use addParameters instead
     */
    static addCustomerModule(url) {
        let id = getCustomerSelectedModuleId();
        return id != null ? url + "?module_id=" + id : url;
    }
    static addParameters(url, { addModule, parameters }) {
        let fUrl = url + "?";
        if (addModule) {
            let id = getCustomerSelectedModuleId();
            fUrl = id != null ? fUrl + "module_id=" + id : fUrl;
        }
        if (parameters != null) {
            for (const [key, value] of Object.entries(parameters)) {
                fUrl += (key + "=" + value + "&&");
            }
        }
        return fUrl;
    }
    static addAdminModule(url) {
        let id = getAdminSelectedModuleId();
        return url + "?" + (id != null ? "module_id=" + id + "&" : "");
    }
    static getClearBody(body) {
        let newBody = {};
        Object.entries(body).map(([key, val]) => {
            if (val != null) {
                newBody[key] = val;
            }
        });
        return newBody;
    }
}
