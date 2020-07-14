import {Observable} from 'rxjs';
import SessionConstant from "../../constant/SessionConstant";
import SessionService from "../../../service/session/SessionService";
import SessionAction from "../../action/SessionAction";
import Config from "../../../config/Config";
import SyncConstant from "../../constant/SyncConstant";

/**
 * search hold order epic
 *
 * @param action$
 * @returns {Observable<any>}
 */
export default function getListSessionEpic(action$) {
    return action$.ofType(SessionConstant.GET_LIST_SESSION)
        .mergeMap(action => {
                let requestMode = Config.dataTypeMode[SyncConstant.TYPE_SESSION];
                return Observable.from(
                    SessionService.getList(action.queryService)
                ).mergeMap(response => {
                    let sessions = response.items;
                    if (sessions && sessions.length && requestMode === SyncConstant.ONLINE_MODE) {
                        return Observable.from(
                            SessionService.prepareUpdateSessionData(sessions)
                        ).mergeMap(updatedSessions => {
                            return Observable.of(SessionAction.getListSessionResult(
                                updatedSessions,
                                response.search_criteria,
                                response.total_count,
                                requestMode
                            ));
                        });
                    }
                    return Observable.of(SessionAction.getListSessionResult(
                        sessions,
                        response.search_criteria,
                        response.total_count,
                        requestMode
                    ));
                }).catch(error => Observable.of(SessionAction.getListSessionResult([])))

            }
        );
}
