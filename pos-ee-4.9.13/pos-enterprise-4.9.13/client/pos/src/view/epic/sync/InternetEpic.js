import ActionTypes from 'react-redux-internet-connection/lib/react-redux-internet-connection/redux/actionTypes'
import SyncAction from "../../action/SyncAction";

/**
 * Check Internet Online
 * @param action$
 * @returns {Observable<*|{type: string}>}
 */
export default function internetOnline(action$) {
    return action$.ofType(ActionTypes.ON_LINE)
        .mergeMap(action =>
            {
                return [
                    SyncAction.syncData(),
                    SyncAction.syncActionLog()
                ];
            }
        );

}
