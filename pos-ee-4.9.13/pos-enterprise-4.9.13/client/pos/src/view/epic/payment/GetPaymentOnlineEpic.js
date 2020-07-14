import PaymentConstant from '../../constant/PaymentConstant';
import LoadingAction from "../../action/LoadingAction";
import {Observable} from 'rxjs';
import PaymentService from "../../../service/payment/PaymentService";
import SyncService from "../../../service/sync/SyncService";
import AppStore from "../../store/store";
import ErrorLogService from "../../../service/sync/ErrorLogService";

/**
 * Get payment online epic
 * @param action$
 * @returns {Observable<any>}
 */
export default function getPaymentOnline(action$) {
    let requestTime = 0;
    let loadingErrorLogs = {};
    return action$.ofType(PaymentConstant.GET_PAYMENT_ONLINE)
        .mergeMap((action) => {
            requestTime++;
            return Observable.from(SyncService.getPayment())
                .mergeMap((response) => {
                    PaymentService.clear().then(() => {
                        PaymentService.saveToDb(response.items);
                    });

                    requestTime = 0;
                    if (action.atLoadingPage) {
                        AppStore.dispatch(LoadingAction.updateFinishedList(PaymentConstant.TYPE_GET_PAYMENT));
                    }
                    return [];
                }).catch(error => {
                    let message = "Failed to get payment data. Please contact technical support.";
                    ErrorLogService.handleLoadingPageErrors(
                        error,
                        PaymentConstant.TYPE_GET_PAYMENT,
                        loadingErrorLogs,
                        requestTime,
                        action,
                        message
                    );
                    return Observable.empty();
                })
        });
}
