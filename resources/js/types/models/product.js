class Product {
    static select_helper() {
        return {
            option: {
                value: 'id', label: 'name'
            },
        };
    }
    static getImageUrl(product) {
        return this.getImage(product)?.image;
    }
    static getMinPrice(product) {
        if (product == null || product.options == null) {
            return -1;
        }
        let prices = [];
        for (const option of product.options) {
            prices.push(option.calculated_price);
        }
        return Math.min(...prices);
    }
    static getMaxPrice(product) {
        let prices = [];
        for (const option of product.options) {
            prices.push(option.calculated_price);
        }
        return Math.max(...prices);
    }
    static getMinStock(product) {
        let minStock = Infinity;
        for (const option of product.options) {
            if (option.stock < minStock)
                minStock = option.stock;
        }
        return minStock;
    }
    static hasLowStock(product) {
        return Product.getMinStock(product) <= 10;
    }
    static optionHasLowStock(product_option) {
        return product_option.stock <= 10;
    }
    static getImage(product) {
        const { images } = product;
        if (images != null && images.length > 0) {
            return images[0];
        }
        return null;
    }
    static isFavorite(product) {
        return product.customer_favorite_products != null && product.customer_favorite_products.length > 0;
    }
    static toggleFavorite(product) {
        if (this.isFavorite(product)) {
            product.customer_favorite_products = null;
        }
        else {
            product.customer_favorite_products ??= [];
            product.customer_favorite_products.push({
                id: -1, product_id: product.id, customer_id: -1
            });
        }
        return product;
    }
}
export default Product;
