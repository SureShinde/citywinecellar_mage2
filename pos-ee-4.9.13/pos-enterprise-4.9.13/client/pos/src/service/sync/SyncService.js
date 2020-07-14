import SyncResourceModel from "../../resource-model/sync/SyncResourceModel";
import LocalStorageHelper from "../../helper/LocalStorageHelper";
import Config from "../../config/Config";
import CoreService from "../CoreService";
import ConfigService from "../config/ConfigService";
import ActionLogResourceModel from "../../resource-model/sync/ActionLogResourceModel";
import QueryService from "../QueryService";
import PaymentService from "../payment/PaymentService";
import ShippingService from "../shipping/ShippingService";
import ColorSwatchService from "../config/ColorSwatchService";
import ServiceFactory from "../../framework/factory/ServiceFactory"
import TaxService from "../tax/TaxService";

class SyncService extends CoreService {
    static className = 'SyncService';
    resourceModel = SyncResourceModel;

    /**
     * Call SyncResourceModel get all
     *
     * @returns {Object|*|FormDataEntryValue[]|string[]}
     */
    getAll() {
        return this.getResourceModel().getAll();
    }

    /**
     * Call SyncResourceModel set default data
     * @returns {*|void|Promise<*|null>}
     */
    setDefaultData() {
        return this.getResourceModel().setDefaultData();
    }

    /**
     * Get default sync data
     */
    getDefaultData() {
        return this.getResourceModel().getDefaultData();
    }

    /**
     * Check has sync pending
     * @returns {boolean}
     */
    async hasSyncPending() {
        let actionLogResource = this.getResourceModel(ActionLogResourceModel);
        let results = await actionLogResource.getAllDataActionLog();
        return results.length > 0;
    }

    /**
     * Call ConfigResourceModel request get config
     * @returns {*}
     */
    getConfig() {
        let queryService = QueryService.reset();
        queryService.setPageSize(200).setCurrentPage(1);
        return ConfigService.getResourceModel().getDataOnline(queryService);
    }

    /**
     * Call ColorSwatchResourceModel request get color swatch
     * @returns {*}
     */
    getColorSwatch() {
        let queryService = QueryService.reset();
        queryService.setPageSize(200).setCurrentPage(1);
        return ColorSwatchService.getResourceModel().getDataOnline(queryService);
    }

    /**
     * Call PaymentResourceModel request get payments
     * @returns {*}
     */
    getPayment() {
        let queryService = QueryService.reset();
        return PaymentService.getResourceModel().getDataOnline(queryService);
    }

    /**
     * Call ShippingResourceModel request get payments
     * @returns {*}
     */
    getShipping() {
        let queryService = QueryService.reset();
        return ShippingService.getResourceModel().getDataOnline(queryService);
    }

    /**
     * Call TaxResourceModel request get tax rate list
     * @returns {*}
     */
    getTaxRate() {
        let queryService = QueryService.reset();
        queryService.setPageSize(300).setCurrentPage(1);
        return TaxService.getResourceModel().getDataOnline(queryService);
    }

    /**
     * Call TaxResourceModel request get tax rule list
     * @returns {*}
     */
    getTaxRule() {
        let queryService = QueryService.reset();
        queryService.setPageSize(300).setCurrentPage(1);
        return TaxService.getRuleResourceModel().getDataOnline(queryService);
    }

    /**
     * Save mode to local storage
     * @param mode
     */
    saveMode(mode) {
        //Set mode to local storage
        LocalStorageHelper.set(LocalStorageHelper.MODE, mode);
        // Change mode in config
        Config.mode = mode;
    }

    /**
     * get Mode from local storage
     * @returns {*|string}
     */
    getMode() {
        return LocalStorageHelper.get(LocalStorageHelper.MODE);
    }

    /**
     * get data type's mode from local storage
     * @returns {{}}
     */
    getDataTypeMode() {
        let dataTypeMode = LocalStorageHelper.get(LocalStorageHelper.DATA_TYPE_MODE);
        return dataTypeMode ? JSON.parse(dataTypeMode) : {};
    }

    /**
     * Get default data type mode
     * @returns {*|{}|{}}
     */
    getDefaultDataTypeMode() {
        return this.getResourceModel().getDefaultDataTypeMode();
    }

    /**
     * Save data type's mode to local storage
     * @param dataTypeMode
     */
    saveDataTypeMode(dataTypeMode) {
        //Set data type's mode to local storage
        LocalStorageHelper.set(LocalStorageHelper.DATA_TYPE_MODE, JSON.stringify(dataTypeMode));
        // Change data type's mode in config
        Config.dataTypeMode = dataTypeMode;
    }

    /**
     * save need sync to localStorage
     * @param flg
     */
    saveNeedSync(flg){
        LocalStorageHelper.set(LocalStorageHelper.NEED_SYNC, flg);
    }

    /**
     * get need sync from localStorage
     * @returns {*|string}
     */
    getNeedSync(){
        return LocalStorageHelper.get(LocalStorageHelper.NEED_SYNC);
    }

    /**
     * save need sync to localStorage
     * @param flg
     */
    saveNeedSyncSession(flg){
        LocalStorageHelper.set(LocalStorageHelper.NEED_SYNC_SESSION, flg);
    }

    /**
     * get need sync from localStorage
     * @returns {*|string}
     */
    getNeedSyncSession(){
        return LocalStorageHelper.get(LocalStorageHelper.NEED_SYNC_SESSION);
    }

    /**
     * Clear Data of sync Table in indexedDb
     * @returns {*}
     */
    clear() {
        return this.getResourceModel().clear();
    }

    /**
     * Reset items's data of sync table in indexedDb
     * @param items
     * @returns {*}
     */
    resetData(items) {
        return this.getResourceModel().resetData(items);
    }
}
/**
 * @type {SyncService}
 */
let syncService = ServiceFactory.get(SyncService);

export default syncService;
