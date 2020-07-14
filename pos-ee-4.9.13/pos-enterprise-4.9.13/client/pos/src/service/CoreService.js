import ResourceModelFactory from "../framework/factory/ResourceModelFactory";
import ObjectManager from "../framework/ObjectManager";

export default class CoreService {

    /**
     * get target Resource Model
     *
     * @return {class}
     */
    getResourceModel(resourceModel) {
        if(!resourceModel) {
            if (!this.compiledResourceModel) {
                this.compiledResourceModel = ResourceModelFactory.get(this.resourceModel);
            }
            return ObjectManager.get(this.compiledResourceModel);
        }
        return ObjectManager.get(ResourceModelFactory.get(resourceModel));
    }

    /**
     * Call ResourceModel request get list object
     * @param {object} queryService
     * @returns {Promise<any>}
     */
    getList(queryService) {
        return this.getResourceModel().getList(queryService);
    }

    /**
     * get data by id
     * @param id
     * @return {*|Promise<any>}
     */
    getById(id) {
        return this.getResourceModel().getById(id);
    }

    /**
     * Clear all data of table in indexedDb
     * @returns {*}
     */
    clear() {
        return this.getResourceModel().clear();
    }

    /**
     * Get Data online
     * @param queryService
     * @param isSync
     * @returns {*|Object|Promise<any>}
     */
    getDataOnline(queryService, isSync = false) {
        return this.getResourceModel().getDataOnline(queryService, isSync);
    }

    /**
     * Get deleted items
     * @param queryService
     * @param isSync
     * @returns {*|Promise<any>|Object}
     */
    getDeleted(queryService, isSync = false) {
        return this.getResourceModel().getDeleted(queryService, isSync);
    }

    /**
     * Call ProductResourceModel request save product to indexedDb
     * @param data
     * @param updateIndex
     * @returns {*|Promise|void|Promise<Promise<any>|Dexie.Promise<Key>>}
     */
    saveToDb(data, updateIndex = false) {
        return this.getResourceModel().saveToDb(data, updateIndex);
    }

    /**
     * Delete items
     * @param ids
     * @returns {Promise|*|void}
     */
    deleteItems(ids) {
        return this.getResourceModel().deleteItems(ids);
    }

    /**
     * get not existed ids
     * @param ids
     * @returns {Promise|*|void}
     */
    getNotExistedIds(ids) {
        return this.getResourceModel().getNotExistedIds(ids);
    }

    /**
     * check need reindex
     * @returns {Promise<*|boolean>}
     */
    needReindex() {
        return this.getResourceModel().needReindex();
    }

    /**
     * Call ProductResourceModel request index data
     * @returns {Promise|*|void}
     */
    reindexTable() {
        return this.getResourceModel().reindexTable();
    }

    /**
     * Get update data
     * @param queryService
     * @param isSync
     * @returns {*|Promise<any>|Object}
     */
    getUpdateData(queryService, isSync = false) {
        return this.getResourceModel().getUpdateData(queryService, isSync);
    }

    /**
     * check need update data
     * @return {boolean}
     */
    needUpdateData() {
        return true;
    }
}
