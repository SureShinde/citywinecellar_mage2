import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import ProductResourceModel from "../../resource-model/catalog/ProductResourceModel";
import CheckoutHelper from "../../helper/CheckoutHelper";
import ProductTypeConstant from "../../view/constant/ProductTypeConstant";
import StockService from "./StockService";
import CatalogRuleProductPriceService from "./rule/CatalogRuleProductPriceService";

export class ProductService extends CoreService {
    static className = 'ProductService';
    resourceModel = ProductResourceModel;

    /**
     * Call ProductResourceModel request get list product
     * @param searchKey
     * @param pageSize
     * @param currentPage
     * @returns {Object}
     */
    getProductList(queryService) {
        return this.getList(queryService);
    }

    /**
     * Get options product
     *
     * @param productId
     * @return {*|Promise<any>}
     */
    getOptions(productId) {
        return this.getResourceModel().getOptions(productId);
    }

    /**
     * Get options product and stock children product
     *
     * @param productId
     * @return {*|Promise<any>}
     */
    getOptionsAndStockChildrens(productId) {
        return this.getResourceModel().getOptionsAndStockChildrens(productId);
    }

    /**
     * Get stock item to refund from product ids
     *
     * @param productIds
     * @param {string} mode
     * @return {*|Promise<any>}
     */
    getStockItemsToRefund(productIds, mode) {
        return this.getResourceModel().getStockItemsToRefund(productIds, mode);
    }

    /**
     * Get list product ids from response get list product
     * @param response
     * @return {*|Array}
     */
    getProductIdsFromResponse(response) {
        return this.getResourceModel().getProductIdsFromResponse(response);
    }

    /**
     * Add stock for product
     *
     * @param response
     * @param stocks
     */
    addStockProducts(response, stocks) {
        return this.getResourceModel().addStockProducts(response, stocks);
    }

    /**
     * Add catalog rule prices for product
     * @param response
     * @param catalogRulePrices
     * @return {*}
     */
    addCatalogRuleProductPrices(response, catalogRulePrices) {
        return this.getResourceModel().addCatalogRuleProductPrices(response, catalogRulePrices);
    }

    /**
     * get stocks data
     * @param response
     * @param productIds
     * @returns {Promise<*>}
     */
    async getStocksDataForResponse(response, productIds) {
        if (!productIds || !productIds.length) {
            return response;
        } else {
            try {
                let stocks = await StockService.getStockProducts(productIds);
                if (stocks) {
                    this.addStockProducts(response, stocks);
                }
                return response;
            } catch (e) {
                return response;
            }
        }
    }

    /**
     * Get catalog rule prices data for response
     * @param response
     * @param productIds
     * @returns {Promise<*>}
     */
    async getCatalogRulePricesDataForResponse(response, productIds) {
        if (!productIds || !productIds.length) {
            return response;
        } else {
            try {
                let catalogRulePrices = await CatalogRuleProductPriceService.getCatalogRulePriceProducts(productIds);
                if (catalogRulePrices) {
                    this.addCatalogRuleProductPrices(response, catalogRulePrices);
                }
                return response;
            } catch (e) {
                return response;
            }
        }
    }

    /**
     * Call ProductResourceModel request search product by barcode
     * @param code
     * @returns {*|{type: string, code: *}}
     */
    searchByBarcode(code) {
        return this.getResourceModel().searchByBarcode(code);
    }

    /**
     *
     * @param code
     * @param store
     * @returns {*|{type: string, code: *}}
     */
    processBarcode(code, store) {
        let allItems = store.getState().core.checkout.quote.items;
        let isExistProductInCart = false;
        let productToSearch = {};
        allItems.map((item) => {
            if (!item.parent_item_id) {
                if (
                    typeof item.product.pos_barcode !== 'undefined'
                    && item.product.pos_barcode
                    && item.product.pos_barcode.includes(',' + code + ',')
                ) {
                    isExistProductInCart = true;
                    productToSearch = item.product;
                }
            }
            return item;
        });
        if (!isExistProductInCart) {
            return this.searchByBarcode(code);
        } else {
            return new Promise((resolve, reject) => {
                resolve({
                    items: [productToSearch]
                });
            });
        }
    }

    /**
     * Check product is composite
     *
     * @param product
     * @return {boolean}
     */
    isComposite(product) {
        return [
            ProductTypeConstant.CONFIGURABLE,
            ProductTypeConstant.BUNDLE,
            ProductTypeConstant.GROUPED
        ].includes(product.type_id);
    }

    /**
     * @param product
     * @return {boolean}
     */
    isSalable(product) {
        if (CheckoutHelper.isAllowToAddOutOfStockProduct()) {
            return product.status;
        }
        if (!this.isComposite(product)) {
            let productStockService = StockService.getProductStockService(product);
            if (!productStockService.isManageStock(product)) {
                return true;
            }
            return productStockService.verifyStock(product);
        }
        return undefined === product.search_string || product.is_salable === 1;
    }

    /**
     *
     * @param product
     * @returns {*}
     */
    getQty(product) {
        if (!product) return false;

        if (![ProductTypeConstant.SIMPLE, ProductTypeConstant.GIFT_CARD, ProductTypeConstant.VIRTUAL].includes(product.type_id)) {
            return false;
        }
        if (!product.stocks ||  !product.stocks.length) {
            return false;
        }
        /* if out of stock */
        if(!this.isSalable(product)){
            return false;
        }

        let productStockService = StockService.getProductStockService(product);
        if (!productStockService.isManageStock(product)) {
            return false;
        }

        return StockService.getStockItemQty(product.stocks[0]);
    }


}

/** @type ProductService */
let productService = ServiceFactory.get(ProductService);

export default productService;
