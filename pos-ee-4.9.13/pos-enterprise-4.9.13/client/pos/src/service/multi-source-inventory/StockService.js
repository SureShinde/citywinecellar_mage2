import CoreService from "./../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import Config from '../../config/Config';
import StoreHelper from '../../helper/StoreHelper';
import LocationHelper from '../../helper/LocationHelper';

export class MultiSourceInventoryStockService extends CoreService {
    static className = 'MultiSourceInventoryStockService';

    /**
     *
     * @param locationId
     * @returns {null}
     */
    getStockIdByLocationId(locationId) {
        let locations = Config.config && Config.config.locations ? Config.config.locations : [];
        let location = locations.find(location => location.location_id === locationId);
        return location && location.stock_id ? location.stock_id : null;
    }

    /**
     * Get stock id from store id
     *
     * @param storeId
     * @returns {null}
     */
    getStockIdByStoreId(storeId) {
        let store = StoreHelper.getStoreFromStoreId(storeId);
        let websiteId = store && typeof store.website_id !== 'undefined' && store.website_id !== null ?
            store.website_id :
            null;
        if (websiteId === null) {
            return null;
        }
        let website = StoreHelper.getWebsiteFromWebsiteId(websiteId);
        let websiteCode = website && website.code ? website.code : null;
        if (websiteCode === null) {
            return null;
        }
        let salesChannels = Config.config && Config.config.sales_channels ? Config.config.sales_channels : [];
        let channel = salesChannels.find(channel => channel.type === 'website' && channel.code === websiteCode);
        return channel && channel.stock_id ? channel.stock_id : null;
    }

    /**
     * Get current stock id
     * 
     * @returns {null}
     */
    getCurrentStockId() {
        return this.getStockIdByLocationId(LocationHelper.getId());
    }

    /**
     *
     * @param order
     * @return {int|null}
     */
    getStockIdByOrder(order) {
        let stockId = null;
        if (order.pos_location_id) {
            stockId = this.getStockIdByLocationId(order.pos_location_id);
        }
        if (!stockId) {
            stockId = this.getStockIdByStoreId(order.store_id);
        }
        return stockId;
    }
}

/**
 * @type {MultiSourceInventoryStockService}
 */
let multiSourceInventoryStockService = ServiceFactory.get(MultiSourceInventoryStockService);

export default multiSourceInventoryStockService;