import {StockHelper} from "./StockHelper";

export class CreditmemoHelper extends StockHelper{
    /**
     * Get Stock item
     *
     * @param item
     * @return {*}
     */
    static getStockItem(item) {
        if (Array.isArray(item.product_stocks) && item.product_stocks.length > 0) {
            return item.product_stocks[0];
        }
        return null;
    }

    /**
     * Check product is manage stock
     *
     * @param item
     * @returns {boolean}
     */
    static itemIsManageStock(item) {
        let stockItem = this.getStockItem(item);
        if (stockItem) {
            return (stockItem.use_config_manage_stock && this.isManageStock()) ||
                stockItem.manage_stock;
        }
        return false;
    }
}
