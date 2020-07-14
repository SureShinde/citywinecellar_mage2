import {Observable} from 'rxjs';
import ActionLogAction from "../../action/ActionLogAction";
import OnHoldOrderService from "../../../service/sales/OnHoldOrderService";
import OnHoldOrderConstant from "../../constant/OnHoldOrderConstant";
import OnHoldOrderAction from "../../action/OnHoldOrderAction";
/**
 * hold order epic
 * @param action$
 * @return {Observable<any>}
 */
export default function cancelHoldOrderEpic(action$) {
    return action$.ofType(OnHoldOrderConstant.CANCEL_ON_HOLD_ORDER)
        .mergeMap(action => Observable.from(OnHoldOrderService.cancelOrder(action.order))
            .mergeMap((response) => {
                return [
                    OnHoldOrderAction.syncDeletedHoldOrderFinish([action.order.increment_id]),
                    OnHoldOrderAction.cancelOrderAfter(response, action.history),
                    ActionLogAction.syncActionLog()
                ];
            }).catch(error => {
                return [
                    ActionLogAction.syncActionLog()
                ];
            })
        );
};