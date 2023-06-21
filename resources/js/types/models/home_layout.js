import Request from "../../services/api/request";
import i18n from "@js/services/i18n";
export class HomeLayout {
    static getTextFromType(type) {
        if (type == 'recommended_products') {
            return i18n.global.t('products');
        }
        return i18n.global.t(type);
    }
    static getIcon(layout) {
        switch (layout.type) {
            case "home_banner":
                return 'view_carousel';
            case 'featured_shops':
                return "storefront";
            case "popular_products":
                return "moving";
            case "latest_products":
                return "new_releases";
            case "featured_products":
                return "dns";
            case "recommended_products":
                return "workspace_premium";
        }
    }
    static isProduct(type) {
        return type === 'featured_products' || type === 'popular_products' || type === 'latest_products' || type === 'recommended_products';
    }
    static isShop(type) {
        return type === 'featured_shops';
    }
    static isFeaturedProduct(type) {
        return type === 'featured_products';
    }
    static isRecommendedProduct(layout) {
        return layout.type === 'recommended_products';
    }
    static isLatestProduct(type) {
        return type === 'latest_products';
    }
    static isPopularProduct(type) {
        return type === 'popular_products';
    }
    static isOther(type) {
        return type === 'other';
    }
    static isBanner(type) {
        return type === 'home_banner';
    }
    static toJSON(params) {
        let list = [];
        let layouts = params?.layouts;
        layouts.forEach(function (layout) {
            if (HomeLayout.isShop(layout.type) || HomeLayout.isBanner(layout.type) || HomeLayout.isFeaturedProduct(layout.type)) {
                let ids = [];
                layout.items?.forEach(function (item) {
                    ids.push(item.id);
                });
                list.push({
                    'type': layout.type, 'active': layout.active, 'ids': ids,
                });
            }
            else if (HomeLayout.isOther(layout.type)) {
                let images = [];
                params?.images?.forEach(function (image) {
                    images.push(Request.getImageData(image.dataURL));
                });
                list.push({
                    'type': layout.type, 'active': layout.active, 'images': images,
                });
            }
            else {
                list.push({
                    'type': layout.type, 'active': layout.active
                });
            }
        });
        return list;
    }
}
