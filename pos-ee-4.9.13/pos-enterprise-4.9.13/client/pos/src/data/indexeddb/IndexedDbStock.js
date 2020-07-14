import Abstract from './IndexedDbAbstract';

export default class IndexedDbStock extends Abstract {
    static className = 'IndexedDbStock';

    main_table = 'stock';
    primary_key = 'item_id';
    offline_id_prefix = '';

    /**
     * Get stock by product id
     *
     * @param productId
     * @return {Promise<any>}
     */
    getStock(productId) {
        return this.get(productId, 'product_id');
    }

    /**
     * Get list stock by product ids
     *
     * @param productIds
     * @return {Promise<any>}
     */
    getListByProductIds(productIds) {
        return new Promise((resolve, reject)=> {
            this.db[this.main_table].where('product_id').anyOf(productIds).toArray()
                .then(items => resolve(items))
                .catch(exception => reject(exception));
        });
    }

    /**
     * Get stock products from product ids
     *
     * @param productIds
     */
    getStockProducts(productIds) {
        return new Promise((resolve, reject) => {
            this.getListByProductIds(productIds).then(items => {
                let stocks = {};
                if (items && items.length > 0) {
                    items.map(stock => {
                        if (!stocks[stock.product_id]) {
                            stocks[stock.product_id] = [];
                        }
                        stocks[stock.product_id].push(stock);
                        return stock;
                    });
                }
                resolve(stocks);
            }).catch(error => {
                reject(error);
            })
        })
    }
}
