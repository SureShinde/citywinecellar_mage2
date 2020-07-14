import SyncConstant from '../constant/SyncConstant';

export default {
    /**
     * set default data in table sync
     * @returns {{type: string}}
     */
    setDefaultSyncDB: () => {
        return {
            type: SyncConstant.SET_DEFAULT_SYNC_DB
        }
    },

    /**
     * set default data in table success
     * @returns {{type: string}}
     */
    setDefaultSyncDBSuccess: () => {
      return {
          type: SyncConstant.SET_DEFAULT_SYNC_DB_SUCCESS
      }
    },

    /**
     * action sync data
     * @return type
     */
    syncData: () => {
        return {
            type: SyncConstant.SYNC_DATA
        }
    },

    /**
     * action sync data product
     * @return type, object
     */
    syncDataWithType: (data = {}) => {
        return {
            type: SyncConstant.SYNC_DATA_WITH_TYPE,
            data: data
        }
    },

    /**
     * sync data finish result
     *
     * @param isSync
     * @returns {{type: string, isSync: string}}
     */
    syncDataFinishResult: (isSync = SyncConstant.ONLINE_MODE) => {
        return {
            type: SyncConstant.CHECK_SYNC_DATA_FINISH_RESULT,
            isSync: isSync
        }
    },

    /**
     * sync action log
     * @returns {{type: string}}
     */
    syncActionLog: () => {
        return {
            type: SyncConstant.SYNC_ACTION_LOG
        }
    },

    /**
     * sync action log success
     * @returns {{type: string}}
     */
    syncActionLogSuccess: () => {
        return {
            type: SyncConstant.SYNC_ACTION_LOG_SUCCESS
        }
    },

    /**
     * action update data
     * @return type
     */
    updateData: () => {
        return {
            type: SyncConstant.UPDATE_DATA
        }
    },

    /**
     * action execute update data
     * @param actions
     * @returns {{type: string, actions: *}}
     */
    executeUpdateData: (actions) => {
        return {
            type: SyncConstant.EXECUTE_UPDATE_DATA,
            actions: actions
        }
    },

    /**
     * action update data with type
     * @param data
     * @param nextActions
     * @returns {{data, nextActions: Array, type: string}}
     */
    updateDataWithType: (data = {}, nextActions = []) => {
        return {
            type: SyncConstant.UPDATE_DATA_WITH_TYPE,
            data: data,
            nextActions: nextActions
        }
    },

    /**
     * action update data finish
     * @param {object} data
     * @param {object} items
     * @return type
     */
    updateDataFinish: (data = {}, items = {}) => {
        return {
            type: SyncConstant.UPDATE_DATA_FINISH,
            data: data,
            items: items,
        }
    },

    /**
     * action delete data finish
     * @param {object} data
     * @param {object} ids
     * @return type
     */
    deleteDataFinish: (data = {}, ids = {}) => {
        return {
            type: SyncConstant.DELETE_DATA_FINISH,
            data: data,
            ids: ids,
        }
    },

    /**
     * action update data finish result
     * @param {object} data
     * @return type
     */
    updateDataFinishResult: (data = {}) => {
        return {
            type: SyncConstant.UPDATE_DATA_FINISH_RESULT,
            data: data,
        }
    },

    /**
     * action change mode
     * @param mode
     * @return {{type: string, mode: *}}
     */
    changeMode: (mode) => {
        return {
            type: SyncConstant.CHANGE_MODE,
            mode: mode,
        }
    },

    /**
     * action change data type's mode
     * @param dataTypeMode
     * @returns {{type: string, dataTypeMode: *}}
     */
    changeDataTypeMode: (dataTypeMode) => {
        return {
            type: SyncConstant.CHANGE_DATA_TYPE_MODE,
            dataTypeMode: dataTypeMode,
        }
    },

    /**
     * Set data type's mode
     *
     * @param dataTypeMode
     * @returns {{type: string, dataTypeMode: *}}
     */
    setDataTypeMode: (dataTypeMode) => {
        return {
            type: SyncConstant.SET_DATA_TYPE_MODE,
            dataTypeMode: dataTypeMode
        }
    },

    /**
     *
     * @returns {{type: string}}
     */
    reloadPage: () => {
        return {
            type: SyncConstant.RELOAD_PAGE
        }
    },
}
