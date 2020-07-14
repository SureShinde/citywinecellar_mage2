import AbstractResourceModel from "../AbstractResourceModel";
import Singleton from "../Singleton";
import SyncConstant from "../../view/constant/SyncConstant";

export default class ProductResourceModel extends AbstractResourceModel {
    static className = 'ProductResourceModel';

    /**
     * Constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            resourceName: 'Product',
            dataType: SyncConstant.TYPE_PRODUCT
        };
    }


    /**
     * Get options product
     *
     * @param productId
     * @return {*}
     */
    getOptions(productId) {
        return this.getResource().getOptions(productId);
    }

    /**
     * Get options product and stock of children
     *
     * @param productId
     * @return {*}
     */
    getOptionsAndStockChildrens(productId) {
        return this.getResourceOnline().getOptionsAndStockChildrens(productId);
    }

    /**
     * Get list product ids from response get list product
     * @param response
     * @return {*|Array}
     */
    getProductIdsFromResponse(response) {
        return this.getResourceOffline().getProductIdsFromResponse(response);
    }

    /**
     * Add stock for product
     *
     * @param response
     * @param stocks
     */
    addStockProducts(response, stocks) {
        return this.getResourceOffline().addStockProducts(response, stocks);
    }

    /**
     * Add catalog rule prices for product
     * @param response
     * @param catalogRulePrices
     * @return {*}
     */
    addCatalogRuleProductPrices(response, catalogRulePrices) {
        return this.getResourceOffline().addCatalogRuleProductPrices(response, catalogRulePrices);
    }

    /**
     * Get stock item to refund from product ids
     *
     * @param productIds
     * @param {string} mode
     * @return {*|Promise<any>}
     */
    getStockItemsToRefund(productIds, mode) {
        let resource = this.getResource();
        if (mode === SyncConstant.OFFLINE_MODE) {
            if (Singleton.getOffline(this.state.resourceName)) {
                resource = Singleton.getOffline(this.state.resourceName);
            }
        } else if (mode === SyncConstant.ONLINE_MODE) {
            if (Singleton.getOnline(this.state.resourceName)) {
                resource = Singleton.getOnline(this.state.resourceName)
            }
        }
        return resource.getStockItemsToRefund(productIds, mode);
    }

    /**
     * Search by barcode
     * @param code
     * @returns {*|{type: string, code: *}}
     */
    searchByBarcode(code) {
        return this.getResource().searchByBarcode(code);
    }
}
