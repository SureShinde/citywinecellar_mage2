import Abstract from './IndexedDbAbstract';
import QueryService from "../../service/QueryService";
import Singleton from "../../resource-model/Singleton";

export default class IndexedDbProduct extends Abstract {
    static className = 'IndexedDbProduct';

    main_table = 'product';
    primary_key = 'id';
    index_table = 'product_index';
    index_table_fields = [
        'id',
        'name',
        'search_string',
        'category_ids',
        'pos_barcode'
    ];
    index_fields = [
        'sku',
        'name',
        'pos_barcode'
    ];
    offline_id_prefix = 'product';
    default_order_by = 'name';

    IndexDbStock = Singleton.getOffline('Stock');
    IndexedDbCatalogRuleProductPrice = Singleton.getOffline('CatalogRuleProductPrice');


    /**
     * Get list item in indexed DB with QueryService object
     *
     * @param {object} queryService
     * @return {Promise<any>}
     */
    getList(queryService = {}) {
        let productRequest = super.getList(queryService);
        // return productRequest;
        return new Promise((resolve, reject) => {
            productRequest.then(response => {
                let productIds = this.getProductIdsFromResponse(response);
                if (!productIds.length) {
                    resolve(response);
                } else {
                    let requestStock = this.IndexDbStock.getStockProducts(productIds);
                    let requestCatalogRulePrices = this.IndexedDbCatalogRuleProductPrice.getCatalogRulePriceProducts(productIds);
                    Promise.all([requestStock, requestCatalogRulePrices]).then(result => {
                        let stocks = result[0];
                        let catalogRulePrices = result[1];
                        if (stocks) {
                            this.addStockProducts(response, stocks);
                        }
                        if (catalogRulePrices) {
                            this.addCatalogRuleProductPrices(response, catalogRulePrices);
                        }
                        resolve(response);
                    }).catch(error => {
                        resolve(response);
                    });
                }
            }).catch(error => {
                reject(error);
            });
        });
    }

    /**
     * Get list product ids from response get list product
     *
     * @param response
     * @return {array}
     */
    getProductIdsFromResponse(response) {
        let productIds = [];
        response.items.map(product => {
            productIds.push(product.id);
            productIds.push(...this.addChildrenProductIdsFromResponse(product));
            return product;
        });
        return productIds;
    }

    addChildrenProductIdsFromResponse(product) {
        let productIds = [];
        if (product.children_products && product.children_products.length > 0) {
            product.children_products.map(child => {
                if (child.id) {
                    productIds.push(child.id);
                }
                return child;
            })
        }
        return productIds;
    }

    /**
     * Add stock for product
     *
     * @param response
     * @param stocks
     */
    addStockProducts(response, stocks) {
        response.items.map(product => {
            if (stocks[product.id]) {
                product.stocks = stocks[product.id];
            }
            if (product.children_products && product.children_products.length > 0) {
                product.children_products.map(childProduct => {
                    if (stocks[childProduct.id]) {
                        childProduct.stocks = stocks[childProduct.id];
                    }
                    return childProduct;
                });
            }
            return product;
        });
        return response;
    }

    /**
     * Get stock item to refund from product ids
     *
     * @param productIds
     */
    getStockItemsToRefund(productIds) {
        return this.IndexDbStock.getStockProducts(productIds);
    }

    /**
     * Search by barcode
     *
     * @param code
     * @returns {Promise<any>}
     */
    searchByBarcode(code) {
        let queryService = QueryService.reset();
        queryService.setOrder('name').setPageSize(1).setCurrentPage(1);
        queryService.addFieldToFilter('pos_barcode', ',' + code + ',', 'like');
        return this.getList(queryService);
    }

    /**
     * Add catalog rule prices for product
     * @param response
     * @param catalogRulePrices
     * @return {*}
     */
    addCatalogRuleProductPrices(response, catalogRulePrices) {
        response.items.map(product => {
            if (catalogRulePrices[product.id]) {
                product.catalogrule_prices = catalogRulePrices[product.id];
            }
            if (product.children_products && product.children_products.length > 0) {
                product.children_products.map(childProduct => {
                    if (catalogRulePrices[childProduct.id]) {
                        childProduct.catalogrule_prices = catalogRulePrices[childProduct.id];
                    }
                    return childProduct;
                });
            }
            return product;
        });
        return response;
    }
}
