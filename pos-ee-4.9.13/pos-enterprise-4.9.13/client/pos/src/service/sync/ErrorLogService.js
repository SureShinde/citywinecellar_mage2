import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import ErrorLogResourceModel from "../../resource-model/sync/ErrorLogResourceModel";
import LoadingConstant from "../../view/constant/LoadingConstant";
import {toast} from "react-toastify";
import i18n from "../../config/i18n";
import AppStore from "../../view/store/store";

export class ErrorLogService extends CoreService {
    static className = 'ErrorLogService';
    resourceModel = ErrorLogResourceModel;

    /**
     * get all data in table error log
     *
     * @returns {Object|*|FormDataEntryValue[]|string[]}
     */
    getAllDataErrorLog() {
        return this.getResourceModel().getAllDataErrorLog();
    }

    /**
     * handle errors at loading page
     * If request is failed, recall it.
     * After failed 'MAX_REQUEST_TIME' times, save the error response to error_log table of indexedDb
     * @param error
     * @param type
     * @param loadingErrorLogs
     * @param requestTime
     * @param action
     * @param message
     */
    handleLoadingPageErrors(error, type, loadingErrorLogs, requestTime, action, message) {
        if (typeof error === 'object') {
            error.action_id = `${type}_${error.created_at}`;
            if (window.location.hash.includes('loading') && action.atLoadingPage) {
                loadingErrorLogs[`${error.status}_${error.message}`] = error;
                if (requestTime < LoadingConstant.MAX_REQUEST_TIME) {
                    setTimeout(() => AppStore.dispatch(action), 1000);
                } else {
                    this.saveToDb(Object.values(loadingErrorLogs));
                    // reset data
                    loadingErrorLogs = {};
                    requestTime = 0;
                    toast.error(
                        i18n.translator.translate(message),
                        {
                            className: 'wrapper-messages messages-warning',
                            autoClose: 3000
                        }
                    );
                }
            } else {
                this.saveToDb([error]);
            }
        }
    }
}

/** @type ErrorLogService */
let errorLogService = ServiceFactory.get(ErrorLogService);

export default errorLogService;
