import {Observable} from 'rxjs';
import SessionConstant from "../../constant/SessionConstant";
import SessionService from "../../../service/session/SessionService";
import QueryService from "../../../service/QueryService";
import AppStore from "../../store/store";
import LoadingAction from "../../action/LoadingAction";
import ErrorLogService from "../../../service/sync/ErrorLogService";
import Config from "../../../config/Config";
import SyncConstant from "../../constant/SyncConstant";

/**
 * search hold order epic
 *
 * @param action$
 * @returns {Observable<any>}
 */
export default function getCurrentSessionEpic(action$) {
    let requestTime = 0;
    let loadingErrorLogs = {};
    return action$.ofType(SessionConstant.GET_CURRENT_SESSION)
        .mergeMap(action => {
            requestTime++;
            let queryService = QueryService.reset();
            queryService.setOrder('opened_at', 'DESC').setPageSize(SessionConstant.PAGE_SIZE).setCurrentPage(1);
            // queryService.addFieldToFilter('staff_id', UserService.getStaffId(), 'eq');
            queryService.addFieldToFilter('status', SessionConstant.SESSION_OPEN, 'eq');
            return Observable.from(SessionService.getList(queryService))
                .mergeMap(response => {
                    if (response.items.length) {
                        SessionService.saveCurrentSession(response.items[0]);
                        SessionService.saveToDb(response.items);
                    }
                    requestTime = 0;
                    if (action.atLoadingPage) {
                        AppStore.dispatch(LoadingAction.updateFinishedList(SessionConstant.TYPE_GET_CURRENT_SESSION));
                    }
                    return Observable.empty();
                }).catch(error => {
                    if (
                        Config.dataTypeMode
                        && Config.dataTypeMode[SyncConstant.TYPE_SESSION] === SyncConstant.ONLINE_MODE
                    ) {
                        let message = "Failed to get current session data. Please contact technical support.";
                        ErrorLogService.handleLoadingPageErrors(
                            error,
                            SessionConstant.TYPE_GET_CURRENT_SESSION,
                            loadingErrorLogs,
                            requestTime,
                            action,
                            message
                        );
                    }
                    return Observable.empty();
                })

            }
        );
}
