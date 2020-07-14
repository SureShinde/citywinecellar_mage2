import {Observable} from 'rxjs';
import ActionLogAction from "../../action/ActionLogAction";
import OrderAction from "../../action/OrderAction";
import OrderConstant from "../../constant/OrderConstant";
import TakePaymentService from "../../../service/sales/order/TakePaymentService";

/**
 * take payment epic
 * @param action$
 * @returns {*}
 */
export default function takePaymentEpic(action$) {
    return action$.ofType(OrderConstant.TAKE_PAYMENT)
        .mergeMap(action => Observable.from(TakePaymentService.takePayment(action.order))
            .mergeMap((response) => {
                return [
                    OrderAction.takePaymentResult(response.order, response.createInvoice),
                    OrderAction.syncActionUpdateDataFinish([response.order]),
                    ActionLogAction.syncActionLog()
                ];
            }).catch(error => {
                return [
                    ActionLogAction.syncActionLog()
                ];
            })
        );
};
