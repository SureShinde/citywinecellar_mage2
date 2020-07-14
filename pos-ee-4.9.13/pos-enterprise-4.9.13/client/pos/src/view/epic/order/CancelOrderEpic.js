import {Observable} from 'rxjs';
import cloneDeep from 'lodash/cloneDeep';
import ActionLogAction from "../../action/ActionLogAction";
import OrderAction from "../../action/OrderAction";
import OrderConstant from "../../constant/OrderConstant";
import OrderService from "../../../service/sales/OrderService";


/**
 * checkout place order epic
 * @param action$
 * @returns {*}
 */
export default function cancel(action$) {
    return action$.ofType(OrderConstant.CANCEL)
        .mergeMap(action => {
            let orderToCancel = cloneDeep(action.order);
            return Observable.from(OrderService.cancel(action.order, action.comment, action.notify, action.visibleOnFront))
                .mergeMap((response) => {
                    return [
                        OrderAction.cancelResult(response),
                        OrderAction.cancelOrderAfter(response, orderToCancel),
                        OrderAction.syncActionUpdateDataFinish([response]),
                        ActionLogAction.syncActionLog()
                    ];
                }).catch(error => {
                    console.log(error);
                    return [
                        ActionLogAction.syncActionLog()
                    ];
                })
        });
};
