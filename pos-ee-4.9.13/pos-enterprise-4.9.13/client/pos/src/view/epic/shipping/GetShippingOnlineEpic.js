import ShippingConstant from '../../constant/ShippingConstant';
import LoadingAction from "../../action/LoadingAction";
import {Observable} from 'rxjs';
import ShippingService from "../../../service/shipping/ShippingService";
import SyncService from "../../../service/sync/SyncService";
import Config from "../../../config/Config";
import AppStore from "../../store/store";
import ErrorLogService from "../../../service/sync/ErrorLogService";

/**
 * Get shipping online epic
 * @param action$
 * @returns {Observable<any>}
 */
export default function getShippingOnline(action$) {
    let requestTime = 0;
    let loadingErrorLogs = {};
    return action$.ofType(ShippingConstant.GET_SHIPPING_ONLINE)
        .mergeMap((action) => {
            requestTime++;
            return Observable.from(SyncService.getShipping())
                .mergeMap((response) => {
                    ShippingService.clear().then(() => {
                        ShippingService.saveToDb(response.items);
                        Config.shipping_methods = response.items;
                    });
                    requestTime = 0;
                    if (action.atLoadingPage) {
                        AppStore.dispatch(LoadingAction.updateFinishedList(ShippingConstant.TYPE_GET_SHIPPING));
                    }
                    return [];
                }).catch(error => {
                    let message = "Failed to get shipping data. Please contact technical support.";
                    ErrorLogService.handleLoadingPageErrors(
                        error,
                        ShippingConstant.TYPE_GET_SHIPPING,
                        loadingErrorLogs,
                        requestTime,
                        action,
                        message
                    );
                    return Observable.empty();
                })
        });
}
