import AbstractResourceModel from "../AbstractResourceModel";
import SyncConstant from "../../view/constant/SyncConstant";

export default class CatalogRuleProductPriceResourceModel extends AbstractResourceModel {
    static className = 'CatalogRuleProductPriceResourceModel';

    /**
     * Constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            resourceName : 'CatalogRuleProductPrice',
            dataType: SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE
        };
    }

    /**
     * get not existed ids
     * @param ruleProductPriceIds
     * @return {*|Promise|Promise<Array>}
     */
    getNotExistedIds(ruleProductPriceIds) {
        return this.getResourceOffline().getNotExistedIds(ruleProductPriceIds);
    }

    /**
     * get catalog rule product price ids
     * @param queryService
     * @param isSync
     * @returns {*|Promise<any>}
     */
    getIds(queryService = {}, isSync = false) {
        return this.getResourceOnline().getIds(queryService, isSync);
    }

    /**
     * get catalog rule product price for products
     * @param productIds
     * @returns {*|Promise<void>}
     */
    getCatalogRulePriceProducts(productIds) {
        return this.getResource().getCatalogRulePriceProducts(productIds);
    }
}
