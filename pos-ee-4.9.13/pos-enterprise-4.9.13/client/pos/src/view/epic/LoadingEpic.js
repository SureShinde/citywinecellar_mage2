import {combineEpics} from "redux-observable";
import LoadingConstant from '../constant/LoadingConstant';
import ProductService from "../../service/catalog/ProductService";
import LoadingAction from "../action/LoadingAction";
import SyncService from "../../service/sync/SyncService";
import SyncConstant from "../constant/SyncConstant";
import StockService from "../../service/catalog/StockService";
import OrderService from "../../service/sales/OrderService";
import CustomerService from "../../service/customer/CustomerService";
import SessionService from "../../service/session/SessionService";
import SyncAction from "../action/SyncAction";
import Appstore from "../store/store";
import {fire} from "../../event-bus";

/**
 * Clear data of table in indexedDb
 * @param action$
 * @returns {Observable<any>}
 */
function clearDataEpic(action$) {
    return action$.ofType(LoadingConstant.CLEAR_DATA)
        .mergeMap(async function () {
            let needSync = SyncService.getNeedSync();
            let needSyncSession = SyncService.getNeedSyncSession();
            let promises = [];
            let changeDataTypeMode = {};
            if (needSync === '1') {
                // Change mode to online
                changeDataTypeMode = {
                    [SyncConstant.TYPE_PRODUCT]: SyncConstant.ONLINE_MODE,
                    [SyncConstant.TYPE_STOCK]: SyncConstant.ONLINE_MODE,
                    [SyncConstant.TYPE_ORDER]: SyncConstant.ONLINE_MODE,
                    [SyncConstant.TYPE_CATEGORY]: SyncConstant.ONLINE_MODE,
                    [SyncConstant.TYPE_CUSTOMER]: SyncConstant.ONLINE_MODE,
                };
                promises.push(
                    ProductService.clear(),
                    StockService.clear(),
                    OrderService.clear(),
                    CustomerService.clear()
                );
            }
            if (needSyncSession === '1') {
                // Change mode to online
                changeDataTypeMode[SyncConstant.TYPE_SESSION] = SyncConstant.ONLINE_MODE;
                SessionService.removeCurrentSession();
                promises.push(
                    SessionService.clear()
                );
            }

            /* Event clear data before */
            let eventDataBefore = {
                changeDataTypeMode: changeDataTypeMode,
                promises: promises
            };
            fire('epic_loading_clear_data_before', eventDataBefore);
            changeDataTypeMode = eventDataBefore.changeDataTypeMode;
            promises = eventDataBefore.promises;

            promises.push(
                SyncService.resetData(Object.keys(changeDataTypeMode)),
            );

            if (changeDataTypeMode && Object.keys(changeDataTypeMode).length) {
                Appstore.dispatch(SyncAction.changeDataTypeMode(changeDataTypeMode));
            }

            try {
                await Promise.all(promises);
                SyncService.saveNeedSync(0);
                SyncService.saveNeedSyncSession(0);
                return LoadingAction.clearDataSuccess();
            } catch (e) {
                return LoadingAction.clearDataError(e);
            }
        });
}

export const loadingEpic = combineEpics(
    clearDataEpic
);

export default loadingEpic;
