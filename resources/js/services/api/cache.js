import Request from "@js/services/api/request";
export class RequestCache {
    static storing_time = 1000; //In seconds
    static data = {};
    static get(url) {
        const data = (this.data)[url];
        if (data != null) {
            if (data.timeout > Date.now()) {
                return data.cache_data;
            }
            this.remove(url);
        }
        return null;
    }
    static set(url, cachedData) {
        const timeout = Date.now() + (this.storing_time * 1000);
        (this.data)[url] = {
            cache_data: cachedData, timeout: timeout
        };
    }
    static remove(url) {
        delete (RequestCache.data)[url];
    }
    static removeSimilar(url) {
        url = Request.getCleanedUrl(url);
        for (const k of Object.keys(this.data)) {
            if (k.includes(url)) {
                this.remove(k);
            }
        }
    }
    static clear() {
        this.data = {};
    }
}
