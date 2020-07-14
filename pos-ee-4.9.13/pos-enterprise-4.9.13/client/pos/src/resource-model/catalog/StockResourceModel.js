import AbstractResourceModel from "../AbstractResourceModel";
import SyncConstant from "../../view/constant/SyncConstant";

export default class StockResourceModel extends AbstractResourceModel {
    static className = 'StockResourceModel';

    /**
     * Constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            resourceName : 'Stock',
            dataType: SyncConstant.TYPE_STOCK
        };
    }

    /**
     * get available qty
     * @param productId
     */
    getAvailableQty(productId) {
        return this.getResourceOnline().getQty(productId);
    }

    /**
     * get external stock
     * @param productId
     * @returns {*}
     */
    getExternalStock(productId) {
        return this.getResourceOnline().getExternalStock(productId);
    }

    /**
     * Get stock from product ids
     *
     * @param productIds
     * @return {*|Promise<any>}
     */
    getStockProducts(productIds) {
        return this.getResource().getStockProducts(productIds);
    }
}
