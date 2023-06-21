import {IModel} from "@js/types/models/models";

export const statusCode = {
    unauthenticated: 401,
    forbidden: 403,
    notFound: 404,
    inactive: 310,
    selfDeliveryNotActive: 312,
    shopPlanNeedUpgrade: 313,
    mobileNotVerified: 321,
    emailNotVerified: 320,
    shopNotActive: 311,
    hasNotShop: 309,
    installationError: 350,
    installationFallback: 351,

    badRequest: 400
}


export default class Response<T extends IModel> {

    data: T;
    errors: any;
    status: any;

    constructor(response: any) {
        this.data = response.data as T;
        this.status = response.status;
    }

    static _isErrorWithResponseAndStatus(err: any) {
        return err.response && err.response.status;
    }

    static checkStatusCode(err: any, statusCode: number) {
        return this._isErrorWithResponseAndStatus(err) && err.response.status === statusCode;
    }

    static is404(err: any) {
        return this._isErrorWithResponseAndStatus(err) && err.response.status === 404;
    }

    static isUnauthenticated(error: any) {
        return this.checkStatusCode(error, statusCode.unauthenticated)
    }

    static isBadRequest(error: any) {
        return this.checkStatusCode(error, statusCode.badRequest)
    }


    static isNotFound(error: any) {
        return this.checkStatusCode(error, statusCode.notFound)
    }


    static isInactive(error: any) {
        return this.checkStatusCode(error, statusCode.inactive)
    }

    static isSelfDeliveryNotActive(error: any) {
        return this.checkStatusCode(error, statusCode.selfDeliveryNotActive)
    }

    static isMobileNotVerified(error: any) {
        return this.checkStatusCode(error, statusCode.mobileNotVerified)
    }

    static isEmailNotVerified(error: any) {
        return this.checkStatusCode(error, statusCode.mobileNotVerified)
    }

    static isShopNotActive(error: any) {
        return this.checkStatusCode(error, statusCode.shopNotActive)
    }

    static isForbidden(error: any) {
        return this.checkStatusCode(error, statusCode.forbidden)
    }

    static hasNotShop(error: any) {
        return this.checkStatusCode(error, statusCode.hasNotShop)
    }

    static hasShopPlanNeedUpgrade(error: any) {
        return this.checkStatusCode(error, statusCode.shopPlanNeedUpgrade)
    }

    static is422(err: any) {
        return this._isErrorWithResponseAndStatus(err) && err.response.status === 422;
    }

    static isInstallationFallback(error: any) {
        return this.checkStatusCode(error, statusCode.installationFallback)
    }

    static isInstallationError(error: any) {
        return this.checkStatusCode(error, statusCode.installationError)
    }

    static create422(errors: any) {
        return {response: {data: {errors: errors}}};
    }

    success() {
        return this.status >= 200 && this.status < 300;
    }

    static getHumanReadableError(errors: any) {
        let error = "";
        for (const [key, value] of Object.entries(errors)) {
            error += (value + "\n");
        }
        return error;
    }
}
