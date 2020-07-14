import OmcAbstract from "./OmcAbstract";
import QueryService from "../../service/QueryService";

export default class OmcStock extends OmcAbstract {
    static className = 'OmcStock';
    get_list_api = this.get_list_stock_api;

    /**
     * get available qty from server
     * @param productId
     * @returns {Promise<any>}
     */
    getQty(productId) {
        // let params = {
        //     product_id: productId
        // };
        let url = this.getBaseUrl() + this.get_available_qty_api;
        if (productId) {
            url += '?product_id=' + productId;
        }
        return this.get(url);
    }

    /**
     * get external stock from server
     * @param product_id
     * @returns {Promise.<any>}
     */
    getExternalStock(product_id) {
        let url = this.getBaseUrl() + this.get_external_stock_api;
        if (product_id) {
            url += '/' + product_id;
        }
        return this.get(url);
    }



    /**
     * Get list via api
     *
     * @param {object} queryService
     * @return {Promise<any>}
     */
    getStockItemsToRefund(queryService = {}) {
        let query = Object.assign({}, queryService);
        let queryParams = this.getQueryParams(query);
        return this.get(this.getBaseUrl() + this.get_stock_items_to_refund_api
            + '?' + encodeURI(queryParams.join('&')));
    }

    /**
     * Get stock products from product ids
     *
     * @param productIds
     */
    getStockProducts(productIds) {
        let queryService = Object.assign({}, QueryService);
        queryService.reset();
        queryService.addFieldToFilter('stock_item_index.product_id', productIds, 'in');
        return new Promise((resolve, reject) => {
            this.getList(queryService).then(response => {
                let stocks = {};
                if (response.items && response.items.length > 0) {
                    response.items.map(stock => {
                        if (!stocks[stock.product_id]) {
                            stocks[stock.product_id] = [];
                        }
                        stocks[stock.product_id].push(stock);
                        return stock;
                    });
                }
                resolve(stocks);
            }).catch(error => {
                resolve({});
            })
        })
    }
}

