import LoadingConstant from '../constant/LoadingConstant';

export default {
    /**
     * action update finished list
     *
     * @param dataType
     * @returns {{dataType: *, type: string}}
     */
    updateFinishedList: (dataType) => {
        return {
            type: LoadingConstant.UPDATE_FINISHED_LIST,
            dataType: dataType
        }
    },

    /**
     * Reset State
     * @returns {{type: string}}
     */
    resetState: () => {
        return {
            type: LoadingConstant.RESET_STATE
        }
    },

    /**
     * Clear Data
     * @returns {{type: string}}
     */
    clearData: () => {
        return {
            type: LoadingConstant.CLEAR_DATA
        }
    },

    /**
     * Clear data success
     * @returns {{type: string}}
     */
    clearDataSuccess: () => {
        return {
            type: LoadingConstant.CLEAR_DATA_SUCCESS
        }
    },

    /**
     * Clear data error
     * @returns {{type: string}}
     */
    clearDataError: (error) => {
        return {
            type: LoadingConstant.CLEAR_DATA_ERROR,
            error
        }
    }
}
