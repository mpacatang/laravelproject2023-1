import {IShop, Shop} from "./shop";
import {IProductVariant} from "./product_variant";
import {IAddon} from "./addon";
import {IFormSelectOption, IIdentifierModel} from "@js/types/models/models";
import {ICategory, ISubCategory} from "@js/types/models/category";
import {IUnit} from "@js/types/models/unit";
import {IProductOption} from "@js/types/models/product_option";
import {IProductImage} from "@js/types/models/product_image";
import {FoodTypes} from "@js/types/custom_types";
import {ICustomerFavoriteProduct} from "@js/types/models/customer_favorite";
import {getMobileNumberCallURL} from "@js/services/api/location_api";

export interface IProduct extends IIdentifierModel {
    id: number,
    name: string,
    unit_id: number,
    unit_title: string,
    unit?: IUnit,
    slug: string,
    active: boolean,
    need_prescription: boolean,
    description: string,
    shop_id: number,
    rating: number,
    ratings_total: number,
    ratings_count: number,
    selling_count: number,
    shop?: IShop,
    addons?: IAddon[],
    sub_category_id: number,
    category_id: number,
    available_from: string,
    available_to: string,
    sub_category?: ISubCategory,
    category?: ICategory,
    product_variant_id: number,
    food_type: FoodTypes,
    options?: IProductOption[],
    images?: IProductImage[],
    // product?: IProduct,
    variant?: IProductVariant,
    customer_favorite_products?: ICustomerFavoriteProduct[]

}


export interface IProductImport{
    name: string,
    sub_category_id: number,
    price: number,
    stock: number,

    unit_id: number,
    unit_title: string,
    sku: string,
    barcode: string,
    image?: string,
    description?: string

    sub_category: ISubCategory,
    unit?: IUnit,
}


class Product {

    static select_helper(): IFormSelectOption<IProduct> {
        return {
            option: {
                value: 'id', label: 'name'
            },
        };
    }

    static getImageUrl(product: IProduct): string | null {
        return this.getImage(product)?.image;
    }

    static getMinPrice(product: IProduct): number {
        if (product == null || product.options == null) {
            return -1;
        }
        let prices: number[] = [];
        for (const option of product.options) {
            prices.push(option.calculated_price);
        }
        return Math.min(...prices);
    }

    static getMaxPrice(product: IProduct): number {
        let prices: number[] = [];
        for (const option of product.options) {
            prices.push(option.calculated_price);
        }
        return Math.max(...prices);
    }

    static getMinStock(product: IProduct): number{
        let minStock = Infinity;
        for (const option of product.options) {
            if(option.stock<minStock) minStock = option.stock;
        }
        return minStock;
    }

    static hasLowStock(product: IProduct): boolean{
        return Product.getMinStock(product)<=10;
    }

    static optionHasLowStock(product_option: IProductOption): boolean{
        return product_option.stock<=10;
    }

    static getImage(product: IProduct): IProductImage | null {
        const {images} = product;
        if (images != null && images.length > 0) {
            return images[0];
        }
        return null;
    }

    static isFavorite(product: IProduct) {
        return product.customer_favorite_products != null && product.customer_favorite_products.length > 0
    }

    static toggleFavorite(product: IProduct) {
        if (this.isFavorite(product)) {
            product.customer_favorite_products = null;
        } else {
            product.customer_favorite_products ??= [];
            product.customer_favorite_products.push({
                id: -1, product_id: product.id, customer_id: -1
            });
        }
        return product;
    }

    // static getEffectivePrice(product) {
    //     if(product.discount_type==='percent'){
    //
    //     }else{
    //         return product.price - price.
    //     }
    // }


}


export default Product;
