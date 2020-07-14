import {Component} from 'react';
import Singleton from "./Singleton"
import Config from '../config/Config';
import SyncContant from "../view/constant/SyncConstant";

export default class AbstractResourceModel extends Component {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            resourceName: '',
            dataType: ''
        }
    }

    /**
     * get resource depend on mode
     *
     * @return: object
     */
    getResource() {
        if (
            Config.dataTypeMode
            && this.state.dataType
            && Config.dataTypeMode[this.state.dataType] === SyncContant.OFFLINE_MODE
        ) {
            return Singleton.getOffline(this.state.resourceName);
        }
        return Singleton.getOnline(this.state.resourceName);
    }

    /**
     * get online resource
     *
     * @return: object
     */
    getResourceOnline() {
        return Singleton.getOnline(this.state.resourceName);
    }

    /**
     * get offline resource
     *
     * @return: object
     */
    getResourceOffline() {
        return Singleton.getOffline(this.state.resourceName);
    }

    /**
     * Call API request get list product
     * @param searchKey
     * @returns {Object}
     */
    getList(searchKey) {
        return this.getResource().getList(searchKey);
    }

    /**
     * get data by id
     * @param id
     * @return {*|Promise<any>}
     */
    getById(id) {
        return this.getResource().getById(id);
    }

    /**
     * Save To IndexedDb
     *
     * @param data
     * @param updateIndex
     * @returns {Promise<Promise<any>|Dexie.Promise<Key>>}
     */
    async saveToDb(data, updateIndex = false) {
        if (updateIndex) {
            await this.getResourceOffline().updateIndexItemsBeforeSave(data);
        }
        return await this.getResourceOffline().bulkPut(data);
    }

    /**
     * check need reindex
     * @returns {*|Promise<*|boolean>}
     */
    needReindex() {
        return this.getResourceOffline().needReindex();
    }

    /**
     * Request index data
     *
     * @returns {Promise|*|void}
     */
    reindexTable() {
        return this.getResourceOffline().reindexTable();
    }

    /**
     * Delete items
     * @param ids
     * @returns {Promise|*|void}
     */
    deleteItems(ids) {
        return this.getResourceOffline().bulkDelete(ids);
    }

    /**
     * get not existed ids
     * @param ids
     * @returns {Promise|*|void}
     */
    getNotExistedIds(ids) {
        return this.getResourceOffline().getNotExistedIds(ids);
    }

    /**
     * Get Data Online
     * @param queryService
     * @param isSync
     * @returns {*|Promise<any>|Object}
     */
    getDataOnline(queryService, isSync = false) {
        return this.getResourceOnline().getList(queryService, isSync);
    }

    /**
     * Get deleted items
     * @param queryService
     * @param isSync
     * @returns {*|Promise<any>|Object}
     */
    getDeleted(queryService, isSync = false) {
        return this.getResourceOnline().getDeleted(queryService, isSync);
    }

    /**
     * Clear table
     * @returns {*}
     */
    clear() {
        return this.getResourceOffline().clear();
    }

    /**
     * Get update data
     * @param queryService
     * @param isSync
     * @returns {*|Promise<any>|Object}
     */
    getUpdateData(queryService, isSync = false) {
        return this.getResourceOnline().getUpdateData(queryService, isSync);
    }
}

