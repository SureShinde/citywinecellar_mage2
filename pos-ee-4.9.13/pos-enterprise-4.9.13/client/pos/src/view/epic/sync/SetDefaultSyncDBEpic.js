import {Observable} from 'rxjs';
import SyncService from "../../../service/sync/SyncService";
import SyncConstant from "../../constant/SyncConstant";
import SyncAction from "../../action/SyncAction";
import AppStore from "../../store/store";

/**
 * Set default data of syncDb
 * @param action$
 * @returns {Observable<any>}
 */
export default function setDefaultSyncDB(action$) {
    return action$.ofType(SyncConstant.SET_DEFAULT_SYNC_DB)
        .mergeMap(() => {
                SyncService.setDefaultData().then((result) => {
                    if (result) {
                        AppStore.dispatch(SyncAction.setDataTypeMode(SyncService.getDefaultDataTypeMode()));
                    }
                });
                return Observable.of(SyncAction.setDefaultSyncDBSuccess());
            }
        );
}
