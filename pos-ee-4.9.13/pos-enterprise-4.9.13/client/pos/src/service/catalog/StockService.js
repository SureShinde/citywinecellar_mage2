import CoreService from "../CoreService";
import StockResourceModel from "../../resource-model/catalog/StockResourceModel";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import SimpleProductStockService from "./stock/SimpleProductService";
import ConfigurableProductStockService from "./stock/ConfigurableProductService";
import BundleProductStockService from "./stock/BundleProductService";
import NumberHelper from "../../helper/NumberHelper";
import CheckoutHelper from "../../helper/CheckoutHelper";
import StockHelper from "../../helper/StockHelper";
import ConfigHelper from "../../helper/ConfigHelper";

export class StockService extends CoreService {
    static className = 'StockService';
    resourceModel = StockResourceModel;
    stockServices = {
        simple: SimpleProductStockService,
        configurable: ConfigurableProductStockService,
        bundle: BundleProductStockService
    };

    /**
     * Get product stock service by product type
     *
     * @param {object} product
     * @return {*}
     */
    getProductStockService(product = null) {
        if (product) {
            if (typeof product === 'string' && this.stockServices[product]) {
                return this.stockServices[product];
            }
            if (product.type_id && this.stockServices[product.type_id]) {
                return this.stockServices[product.type_id];
            }
        }
        return this.stockServices.simple;

    }

    /**
     * get available qty of config product child
     * @param productId
     * @returns {Promise<any>|Object|*}
     */
    getAvailableQty(productId) {
        return this.getResourceModel().getAvailableQty(productId);
    }

    /**
     * get external stock
     * @param productId
     * @returns {*}
     */
    getExternalStock(productId) {
        return this.getResourceModel().getExternalStock(productId);
    }

    /**
     *
     * @param product
     * @return {{min: number, max: *|number, qtyIncrement: *|number, isQtyDecimal: *|boolean}}
     */
    getStockInfo(product) {
        let productStockService = this.getProductStockService(product);
        let minSaleQty = productStockService.getMinSaleQty(product) * 1;
        let qtyIncrement = productStockService.getQtyIncrement(product) * 1;
        let isQtyDecimal = productStockService.isQtyDecimal(product);
        let maxSaleQty = productStockService.getMaxSaleQty(product);
        /*let minQty = productStockService.getOutOfStockThreshold(product);
        let productQty = productStockService.getProductQty(product);
        // let maxSaleQtyPerQtyIncrement = maxSaleQty % qtyIncrement;
        let backOrder = productStockService.getBackorders(product);*/
        let isManageStock = productStockService.isManageStock(product);
        /**
         * max = Min(maximumQty, available)
         * maximumQty =
         *   - maxSaleQty neu max sale qty chia het cho qty increment hoặc allow to add out of stock product
         *   - 1 so < maxSaleQty va chia het cho qty increment
         *   - la qty increment neu maxSaleQty < qty increment
         * available =
         *   - Available qty - outOfStockThreshold neu no chia het cho qty increment
         *   - int ( (Available qty - outOfStockThreshold) / qty increment ) * qty increment
         * */
        if (CheckoutHelper.isAllowToAddOutOfStockProduct() || !isManageStock) {
            maxSaleQty = Math.max(maxSaleQty, 0);
        } else {
            let backOrder = productStockService.getBackorders(product);
            let minQty = productStockService.getOutOfStockThreshold(product);
            let productQty = productStockService.getProductQty(product);
            if (!backOrder) {
                maxSaleQty = Math.min(maxSaleQty, NumberHelper.minusNumber(productQty, minQty));
            } else {
                if (minQty < 0) {
                    maxSaleQty = Math.max(maxSaleQty, 0);
                    maxSaleQty = Math.min(maxSaleQty, NumberHelper.minusNumber(productQty, minQty));
                } else {
                    maxSaleQty = Math.max(maxSaleQty, 0);
                }
            }
        }
        let max = maxSaleQty % qtyIncrement === 0 ?
            maxSaleQty :
            NumberHelper.multipleNumber(Math.floor(maxSaleQty / qtyIncrement), qtyIncrement);
        let isEnableQtyIncrements = productStockService.isEnableQtyIncrements(product);

        return {
            min: Math.max(minSaleQty, qtyIncrement),
            max,
            minSaleQty,
            maxSaleQty,
            qtyIncrement,
            isQtyDecimal,
            isEnableQtyIncrements
        }
    }

    /**
     * Get list product by product id
     *
     * @param productIds
     * @returns {*|Promise<any>}
     */
    getListByProductIds(productIds) {
        return this.getResourceModel().getResourceOffline().getListByProductIds(productIds);
    }

    /**
     * Retrieve can Manage Stock
     *
     * @param stockItem
     * @return {*}
     */
    getManageStock(stockItem) {
        let isUseConfigManageStock;
        if (typeof stockItem.use_config_manage_stock === 'string') {
            isUseConfigManageStock = parseInt(stockItem.use_config_manage_stock, 10);
        } else {
            isUseConfigManageStock = stockItem.use_config_manage_stock;
        }
        if (isUseConfigManageStock) {
            return StockHelper.isManageStock()
        }
        return stockItem.manage_stock;
    }

    /**
     * Retrieve minimal quantity available for item status in stock
     *
     * @param stockItem
     */
    getMinQty(stockItem) {
        if (stockItem.use_config_min_qty) {
            return StockHelper.getMinQty();
        }
        return stockItem.min_qty;
    }

    /**
     * Is stock item qty decimal
     *
     * @param stockItem
     * @return {boolean}
     */
    isQtyDecimal(stockItem) {
        return !!stockItem.is_qty_decimal;
    }

    /**
     * Check stock item can back order
     *
     * @param stockItem
     * @return {*}
     */
    getBackorders(stockItem) {
        if (stockItem) {
            if (stockItem.use_config_backorders) {
                return StockHelper.isBackOrder();
            }
            return !!+stockItem.backorders;
        }
        return false;
    }

    /**
     *
     * @param stockItem
     * @returns {*}
     */
    getStockItemQty(stockItem) {
        if (!this.getBackorders(stockItem)) {
            return stockItem.qty - Math.max(0, this.getMinQty(stockItem));
        }
        if (ConfigHelper.compareMagentoVersion('2.3.0', '>=')) {
            return stockItem.qty - Math.min(0, this.getMinQty(stockItem));
        }
        return stockItem.qty;
    }

    /**
     * Get stock from product ids
     *
     * @param productIds
     * @return {*|Promise<any>}
     */
    getStockProducts(productIds) {
        return this.getResourceModel().getStockProducts(productIds);
    }
}

/** @type StockService */
let stockService = ServiceFactory.get(StockService);

export default stockService;

