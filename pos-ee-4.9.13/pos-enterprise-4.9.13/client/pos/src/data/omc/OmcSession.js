import OmcAbstract from "./OmcAbstract";
import PosService from "../../service/PosService";

export default class OmcSession extends OmcAbstract {
    static className = 'OmcSession';

    get_list_api = this.get_list_session_api;

    /**
     * get path shift save
     * @returns string
     */
    getPathShiftSave() {
        return this.shift_save_api;
    }

    /**
     * get path cash transaction save
     * @returns string
     */
    getPathCashTransactionSave() {
        return this.cash_transaction_save_api;
    }

    /**
     * Get list via api
     * @param queryService
     * @param isSync
     * @returns {Promise<any>}
     */
    getList(queryService, isSync = false) {
        queryService.addFieldToFilter('pos_id', PosService.getCurrentPosId(), 'eq');
        return super.getList(queryService, isSync);
    }
}
