import ConfigHelper from "./ConfigHelper";
import ConfigConstant from "../view/constant/catalog/stock/ConfigConstant";

export class StockHelper {
    /**
     * Get settings is manage stock
     *
     * @return {boolean}
     */
    static isManageStock() {
        return ConfigHelper.getConfig(ConfigConstant.XML_PATH_MANAGE_STOCK) === '1';
    }

    /**
     * Get settings is back order stock
     *
     * @return {boolean}
     */
    static isBackOrder() {
        return ConfigHelper.getConfig(ConfigConstant.XML_PATH_BACKORDERS) === '1';
    }

    /**
     * Get settings max sale qty stock
     *
     * @return {number}
     */
    static getMaxSaleQty() {
        let maxSaleQty = ConfigHelper.getConfig(ConfigConstant.XML_PATH_MAX_SALE_QTY);
        return parseFloat(maxSaleQty) || 100000000000000000;
    }

    /**
     * Get settings min sale qty stock
     *
     * @return {number}
     */
    static getMinSaleQty() {
        return ConfigHelper.getConfig(ConfigConstant.XML_PATH_MIN_SALE_QTY);
    }

    /**
     * Get settings out of stock threshold qty stock
     *
     * @return {number}
     */
    static getOutOfStockThreshold() {
        let minQty = ConfigHelper.getConfig(ConfigConstant.XML_PATH_MIN_QTY);
        return (minQty !== null && minQty !== '') ? parseFloat(minQty) : 0;
    }

    /**
     * Get settings enable qty increment stock
     *
     * @return {boolean}
     */
    static isEnableQtyIncrements() {
        return ConfigHelper.getConfig(ConfigConstant.XML_PATH_ENABLE_QTY_INCREMENTS) === '1';
    }

    /**
     * Get settings qty increment stock
     *
     * @return {number}
     */
    static getQtyIncrement() {
        return parseFloat(ConfigHelper.getConfig(ConfigConstant.XML_PATH_QTY_INCREMENTS)) || 1;
    }

    /**
     * Check if is possible subtract value from item qty
     * @return {boolean}
     */
    static canSubtractQty() {
        return ConfigHelper.getConfig(ConfigConstant.XML_PATH_CAN_SUBTRACT) === '1';
    }

    /**
     * Retrieve can Back in stock
     *
     * @return {boolean}
     */
    static getCanBackInStock() {
        return ConfigHelper.getConfig(ConfigConstant.XML_PATH_CAN_BACK_IN_STOCK) === '1';
    }

    /**
     * @return {*|null}
     */
    static getMinQty() {
        return +ConfigHelper.getConfig(ConfigConstant.XML_PATH_MIN_QTY);
    }
}

export default StockHelper;