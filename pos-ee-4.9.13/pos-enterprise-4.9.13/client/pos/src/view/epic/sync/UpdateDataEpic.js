import {Observable} from 'rxjs';
import Config from "../../../config/Config";
import SyncService from "../../../service/sync/SyncService";
import SyncConstant from "../../constant/SyncConstant";
import SyncAction from "../../action/SyncAction";
import SessionHelper from "../../../helper/SessionHelper";
import {fire} from "../../../event-bus";

/**
 * Receive action type(UPDATE_DATA) and update data from server
 * @param action$
 */
export default function updateData(action$) {
    let counter = 0;
    return action$.ofType(SyncConstant.UPDATE_DATA)
        .mergeMap(action => {
            if (!window.navigator.onLine) {
                return Observable.empty();
            }
            return Observable.from(SyncService.getAll())
                .mergeMap(result => {
                    let actions = [];
                    let data = result.filter(dataType => dataType.total >= 0 && dataType.count >= dataType.total);

                    for (let syncData of data) {
                        if (syncData.updating && !Config.updateDataFirstLoad) {
                            continue;
                        }

                        if (
                            syncData.type === SyncConstant.TYPE_SESSION
                            && !SessionHelper.isEnableSession()
                        ) {
                            continue;
                        }

                        // Read config
                        let path = 'webpos/offline/' + syncData.type;
                        if (syncData.type === SyncConstant.TYPE_STOCK) {
                            path += '_item';
                        }
                        path += '_time';

                        let time = 10;
                        let configTime = Config.config.settings.filter(x => x.path === path)[0];
                        if (configTime) {
                            time = parseInt(configTime.value, 10);
                        }
                        if (syncData.type === SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE) {
                            time = 60;
                        } else if (syncData.type === SyncConstant.TYPE_CATEGORY) {
                            time = 60;
                        }

                        let eventDataBefore = {
                            time: time,
                            syncData: syncData
                        };
                        /* Event update data before */
                        fire('epic_update_data_before', eventDataBefore);
                        time = eventDataBefore.time;

                        if (0 === counter % time) {
                            // Update Data
                            syncData.updating = true;
                            actions.push(syncData);
                        }
                    }
                    counter++; // increase counter every minute
                    Config.updateDataFirstLoad = false;

                    // Save updating to database
                    actions.length && SyncService.saveToDb(actions);

                    // Dispatch executeUpdateData action to start update data
                    return Observable.of(SyncAction.executeUpdateData(actions));
                })
                .catch(() => Observable.empty());
        });
}
