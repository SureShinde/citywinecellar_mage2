import {Observable} from 'rxjs';
import SyncConstant from "../../constant/SyncConstant";
import SyncAction from "../../action/SyncAction";

/**
 * Receive action type(UPDATE_DATA) and update data from server
 * @param action$
 */
export default function executeUpdateData(action$) {
    return action$.ofType(SyncConstant.EXECUTE_UPDATE_DATA)
        .mergeMap(action => {
            if (!window.navigator.onLine || !action.actions.length) {
                return Observable.empty();
            }
            // get first data that need to be update
            let currentAction = action.actions.shift();
            // dispatch updateDataWithType action to update that data
            // with params are that data and list of next data type that need to update
            return Observable.of(SyncAction.updateDataWithType(currentAction, action.actions));
        });
}
