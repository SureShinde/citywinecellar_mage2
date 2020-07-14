import Abstract from './IndexedDbAbstract';

export default class IndexedDbCatalogRuleProductPrice extends Abstract {
    static className = 'IndexedDbCatalogRuleProductPrice';

    main_table = 'catalogrule_product_price';
    primary_key = 'rule_product_price_id';
    index_table = '';
    index_table_fields = [];
    default_order_by = 'rule_product_price_id';

    /**
     * get not existed ids
     * @param ruleProductPriceIds
     * @return {Promise<Array>}
     */
    async getNotExistedIds(ruleProductPriceIds) {
        let allIds = await this.db[this.main_table].toCollection().primaryKeys();
        let index = 0, len = ruleProductPriceIds.length;
        if (index === len) {
            return allIds;
        }
        let result = allIds.filter(id => {
            if (index === len) {
                return true;
            }
            while (ruleProductPriceIds[index] < id) {
                index++;
                if (index === len) {
                    return true;
                }
            }
            if (ruleProductPriceIds[index] === id) {
                index++;
                return false;
            }
            return true;
        });
        return result;
    }

    /**
     * get data by product ids
     * @param productIds
     * @returns {Promise<*>}
     */
    getByProductIds(productIds) {
        return this.db[this.main_table].where('product_id').anyOf(productIds).toArray();
    }

    /**
     * get catalog rule product price for products
     * @param productIds
     * @returns {Promise<void>}
     */
    async getCatalogRulePriceProducts(productIds) {
        let data = await this.getByProductIds(productIds);
        let catalogRulePrices = {};
        data.forEach(rulePrice => {
            let productId = rulePrice.product_id;
            if (!catalogRulePrices[productId]) {
                catalogRulePrices[productId] = [rulePrice];
            } else {
                catalogRulePrices[productId].push(rulePrice);
            }
        });
        return catalogRulePrices;
    }
}
