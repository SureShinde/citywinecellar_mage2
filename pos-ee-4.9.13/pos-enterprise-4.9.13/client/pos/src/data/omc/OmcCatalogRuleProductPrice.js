import OmcAbstract from "./OmcAbstract";
import QueryService from "../../service/QueryService";

if (!window.Promise) {
    window.Promise = Promise;
}

export default class OmcCatalogRuleProductPrice extends OmcAbstract {
    static className = 'OmcCatalogRuleProductPrice';

    get_list_api = this.get_list_catalog_rule_product_price_api;

    /**
     * get catalog rule product price ids
     * @param queryService
     * @param isSync
     * @returns {Promise<any>}
     */
    getIds(queryService = {}, isSync = false) {
        let queryParams = this.getQueryParams(Object.assign({}, queryService));
        let opts = {};
        if (isSync) {
            opts.credentials = 'omit';
        }
        return this.get(this.getBaseUrl()
            + this.get_catalog_rule_product_price_ids_api
            + '?' + encodeURI(queryParams.join('&')), opts);
    }

    /**
     * get catalog rule product price for products
     * @param productIds
     * @returns {Promise<void>}
     */
    getCatalogRulePriceProducts(productIds) {
        let queryService = Object.assign({}, QueryService);
        queryService.reset();
        queryService.addFieldToFilter('product_id', productIds, 'in');
        return new Promise((resolve, reject) => {
            this.getList(queryService).then(response => {
                let catalogRulePrices = {};
                if (response.items && response.items.length > 0) {
                    response.items.map(item => {
                        if (!catalogRulePrices[item.product_id]) {
                            catalogRulePrices[item.product_id] = [];
                        }
                        catalogRulePrices[item.product_id].push(item);
                        return item;
                    });
                }
                resolve(catalogRulePrices);
            }).catch(error => {
                resolve({});
            })
        })
    }
}

