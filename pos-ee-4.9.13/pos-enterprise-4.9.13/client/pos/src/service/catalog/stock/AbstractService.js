import CoreService from "../../CoreService";
import ServiceFactory from "../../../framework/factory/ServiceFactory"
import StockHelper from "../../../helper/StockHelper";
import CustomSaleConstant from "../../../view/constant/custom-sale/CustomSaleConstant";
import ConfigHelper from "../../../helper/ConfigHelper";

export class AbstractStockService extends CoreService {
    static className = 'AbstractStockService';

    /**
     * Get Stock item
     *
     * @param product
     * @return {*}
     */
    getStockItem(product) {
        if (product.stocks && product.stocks.length > 0) {
            return product.stocks[0];
        }
        return null;
    }

    /**
     * Get Product qty
     *
     * @param product
     * @return {*}
     */
    getProductQty(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            return stockItem.qty !== null ? stockItem.qty : 0;
        }
        return 0;
    }

    /**
     * Get qty increment of product to add product
     *
     * @param product
     * @return {number}
     */
    getAddQtyIncrement(product) {
        /*let qtyIncrement = 0;
         let min_sale_qty = 0;*/
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            return parseFloat(this.getQtyIncrement(product));
            /*qtyIncrement = this.getQtyIncrement(product);

             min_sale_qty = this.getMinSaleQty(product);

             return Math.max(qtyIncrement, min_sale_qty);*/
        }
        return 0;
    }

    /**
     * check is enable increment of product
     *
     * @param product
     * @return {boolean}
     */
    isEnableQtyIncrements(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            if (stockItem.use_config_enable_qty_inc) {
                return StockHelper.isEnableQtyIncrements();
            }
            return !!stockItem.enable_qty_increments;
        }
        return false;
    }

    /**
     * Get Qty increment of product
     *
     * @param product
     * @return {*}
     */
    getQtyIncrement(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            if (stockItem.use_config_enable_qty_inc) {
                return parseFloat(StockHelper.isEnableQtyIncrements() ? StockHelper.getQtyIncrement() : 1);
            } else {
                if (stockItem.enable_qty_increments) {
                    return parseFloat(
                            stockItem.use_config_qty_increments ?
                                StockHelper.getQtyIncrement() :
                                stockItem.qty_increments
                        ) || 1;
                } else {
                    return 1;
                }
            }
        }
        return 0;
    }

    /**
     * Get min sale qty of product
     *
     * @param product
     * @return {*}
     */
    getMinSaleQty(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            if (stockItem.use_config_min_sale_qty) {
                return StockHelper.getMinSaleQty();
            } else {
                return stockItem.min_sale_qty;
            }
        }
        return 0;
    }

    /**
     * Get max sale qty of product
     *
     * @param product
     * @return {*}
     */
    getMaxSaleQty(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            if (stockItem.use_config_max_sale_qty) {
                return StockHelper.getMaxSaleQty();
            } else {
                return stockItem.max_sale_qty || 100000000000000000;
            }
        }
        return 0;
    }

    /**
     * Get qty in location of product
     *
     * @param product
     * @return {*}
     */
    getQtyInLocation(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem && stockItem.quantity) {
            return stockItem.quantity;
        }
        return 0;
    }

    /**
     * Get qty to out of stock threshold for product
     *
     * @param product
     * @return {*}
     */
    getOutOfStockThreshold(product) {
        let stockItem = this.getStockItem(product);
        let outOfStockThreshold = 0;
        if (stockItem) {
            if (stockItem.use_config_min_qty) {
                outOfStockThreshold = parseFloat(StockHelper.getOutOfStockThreshold());
            } else {
                outOfStockThreshold = parseFloat(stockItem.min_qty);
            }
            if (ConfigHelper.compareMagentoVersion('2.3.0', '>=')) {
                let backorders = this.getBackorders(product);
                if (
                    (backorders && outOfStockThreshold > 0)
                    || (!backorders && outOfStockThreshold < 0)
                ) {
                    return 0;
                }
                return outOfStockThreshold;
            } else {
                return 0;
            }
        }
        return outOfStockThreshold;
    }

    /**
     * Get isQtyDecimal
     *
     * @param product
     * @return {boolean}
     */
    isQtyDecimal(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            return !!stockItem.is_qty_decimal || stockItem.sku === CustomSaleConstant.SKU;
        }

        return false;
    }

    /**
     * Check product is manage stock
     *
     * @param product
     * @returns {boolean}
     */
    isManageStock(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            return stockItem.use_config_manage_stock ? StockHelper.isManageStock() : stockItem.manage_stock;
        }
        return false;
    }

    /**
     * Check product can back order
     *
     * @param product
     * @return {*}
     */
    getBackorders(product) {
        let stockItem = this.getStockItem(product);
        if (stockItem) {
            if (stockItem.use_config_backorders) {
                return StockHelper.isBackOrder();
            }
            return !!+stockItem.backorders;
        }
        return false;
    }

    /**
     * Verify stock product
     *
     * @param product
     * @return {*}
     */
    verifyStock(product) {

        let stockItem = this.getStockItem(product);
        if (stockItem) {
            if (stockItem.qty === null && this.isManageStock(product)) {
                return false;
            }
            if (stockItem.hasOwnProperty('is_in_stock') && !stockItem.is_in_stock) {
                return false;
            }
            let backorders = this.getBackorders(product);
            let outOfStockThreshold = this.getOutOfStockThreshold(product);

            if (ConfigHelper.compareMagentoVersion('2.3.0', '>=')) {
                if (backorders) {
                    if (!outOfStockThreshold) {
                        return true;
                    }

                    return stockItem.qty > outOfStockThreshold;
                }

                return stockItem.qty > outOfStockThreshold;
            }

            return backorders || stockItem.qty > outOfStockThreshold;
        }
        return true;
    }
}

let abstractStockService = ServiceFactory.get(AbstractStockService);

export default abstractStockService;

