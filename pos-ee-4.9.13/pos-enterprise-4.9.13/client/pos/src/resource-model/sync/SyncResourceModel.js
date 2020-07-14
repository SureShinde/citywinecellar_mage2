import AbstractResourceModel from "../AbstractResourceModel";

export default class SyncResourceModel extends AbstractResourceModel {
    static className = 'SyncResourceModel';
    /**
     * Constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {resourceName : 'Sync'};
    }

    /**
     * Call get all sync items
     *
     * @returns {Object|*|FormDataEntryValue[]|string[]}
     */
    getAll() {
        return this.getResourceOffline().getAll();
    }

    /**
     * Call Set default data of Sync Table
     * @returns {*|void|Promise<*|null>|Promise<*|null>}
     */
    setDefaultData() {
        return this.getResourceOffline().setDefaultData();
    }

    /**
     * Reset items's data of sync table in indexedDb
     * @param items
     * @returns {*}
     */
    resetData(items) {
        return this.getResourceOffline().resetData(items);
    }

    /**
     * Get default data type mode
     * @returns {*|{}}
     */
    getDefaultDataTypeMode() {
        return this.getResourceOffline().getDefaultDataTypeMode();
    }

    /**
     * Get default sync data
     */
    getDefaultData() {
        return this.getResourceOffline().getDefaultData();
    }
}
