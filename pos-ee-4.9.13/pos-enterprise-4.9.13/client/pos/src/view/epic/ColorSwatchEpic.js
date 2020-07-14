import ColorSwatchAction from "../action/ColorSwatchAction";
import LoadingAction from "../action/LoadingAction";
import {Observable} from 'rxjs';
import ColorSwatchService from "../../service/config/ColorSwatchService";
import SyncService from "../../service/sync/SyncService";
import ColorSwatchConstant from "../constant/ColorSwatchConstant";
import AppStore from "../store/store";
import ErrorLogService from "../../service/sync/ErrorLogService";

/**
 * Receive action type(GET_COLOR_SWATCH) and request, response data color swatch
 * @param action$
 * @returns {Observable<* | type | configs | {type: string, configs: (module.exports.configs|{recommended, all})}>}
 */
export default action$ => {
    let requestTime = 0;
    let loadingErrorLogs = {};
    return action$.ofType(ColorSwatchConstant.GET_COLOR_SWATCH)
        .mergeMap((action) => {
            requestTime++;
            return Observable.from(SyncService.getColorSwatch())
                .mergeMap((response) => {
                    ColorSwatchService.saveToLocalStorage(response.items);

                    requestTime = 0;
                    if (action.atLoadingPage) {
                        AppStore.dispatch(LoadingAction.updateFinishedList(ColorSwatchConstant.TYPE_GET_COLOR_SWATCH));
                    }
                    return [
                        ColorSwatchAction.getColorSwatchResult(response.items)
                    ];
                }).catch(error => {
                    let message = "Failed to get color swatch data. Please contact technical support.";
                    ErrorLogService.handleLoadingPageErrors(
                        error,
                        ColorSwatchConstant.TYPE_GET_COLOR_SWATCH,
                        loadingErrorLogs,
                        requestTime,
                        action,
                        message
                    );
                    return Observable.of(ColorSwatchAction.getColorSwatchError(error));
                })
        });
}
