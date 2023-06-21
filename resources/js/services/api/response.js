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
};
export default class Response {
    data;
    errors;
    status;
    constructor(response) {
        this.data = response.data;
        this.status = response.status;
    }
    static _isErrorWithResponseAndStatus(err) {
        return err.response && err.response.status;
    }
    static checkStatusCode(err, statusCode) {
        return this._isErrorWithResponseAndStatus(err) && err.response.status === statusCode;
    }
    static is404(err) {
        return this._isErrorWithResponseAndStatus(err) && err.response.status === 404;
    }
    static isUnauthenticated(error) {
        return this.checkStatusCode(error, statusCode.unauthenticated);
    }
    static isBadRequest(error) {
        return this.checkStatusCode(error, statusCode.badRequest);
    }
    static isNotFound(error) {
        return this.checkStatusCode(error, statusCode.notFound);
    }
    static isInactive(error) {
        return this.checkStatusCode(error, statusCode.inactive);
    }
    static isSelfDeliveryNotActive(error) {
        return this.checkStatusCode(error, statusCode.selfDeliveryNotActive);
    }
    static isMobileNotVerified(error) {
        return this.checkStatusCode(error, statusCode.mobileNotVerified);
    }
    static isEmailNotVerified(error) {
        return this.checkStatusCode(error, statusCode.mobileNotVerified);
    }
    static isShopNotActive(error) {
        return this.checkStatusCode(error, statusCode.shopNotActive);
    }
    static isForbidden(error) {
        return this.checkStatusCode(error, statusCode.forbidden);
    }
    static hasNotShop(error) {
        return this.checkStatusCode(error, statusCode.hasNotShop);
    }
    static hasShopPlanNeedUpgrade(error) {
        return this.checkStatusCode(error, statusCode.shopPlanNeedUpgrade);
    }
    static is422(err) {
        return this._isErrorWithResponseAndStatus(err) && err.response.status === 422;
    }
    static isInstallationFallback(error) {
        return this.checkStatusCode(error, statusCode.installationFallback);
    }
    static isInstallationError(error) {
        return this.checkStatusCode(error, statusCode.installationError);
    }
    static create422(errors) {
        return { response: { data: { errors: errors } } };
    }
    success() {
        return this.status >= 200 && this.status < 300;
    }
    static getHumanReadableError(errors) {
        let error = "";
        for (const [key, value] of Object.entries(errors)) {
            error += (value + "\n");
        }
        return error;
    }
}
