import Config from "../config/Config";

export default {
    /**
     * Get store from store id
     *
     * @param storeId
     * @returns {*}
     */
    getStoreFromStoreId(storeId) {
        let stores = Config.config && Config.config.stores ? Config.config.stores : [];
        return stores.find(store => store.id === storeId);
    },

    /**
     * Get website from website id
     *
     * @param websiteId
     * @returns {*}
     */
    getWebsiteFromWebsiteId(websiteId) {
        let websites = Config.config && Config.config.websites ? Config.config.websites : [];
        return websites.find(website => website.id === websiteId);
    }
}
