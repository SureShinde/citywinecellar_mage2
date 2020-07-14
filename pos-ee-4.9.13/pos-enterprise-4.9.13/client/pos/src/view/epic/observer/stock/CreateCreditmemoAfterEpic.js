import CreditmemoConstant from '../../../constant/order/CreditmemoConstant';
import {Observable} from 'rxjs';
import ReturnProcessorService from "../../../../service/sales/inventory/order/ReturnProcessorService";

/**
 * Receive action type(PLACE_ORDER_AFTER) and request, response list product
 * @param action$
 */
export default function CreateCreditmemoAfterEpic(action$) {
    return action$.ofType(CreditmemoConstant.CREATE_CREDITMEMO_AFTER)
        .mergeMap(action => {
            let creditmemo = action.creditmemo;
            let order = creditmemo.order;
            let orderToRefund = action.orderToRefund;
            let returnToStockItems = [];
            creditmemo.items.forEach(item => {
                if (item.back_to_stock) {
                    returnToStockItems.push(item.order_item_id);
                }
            });
            if (returnToStockItems.length) {
                ReturnProcessorService.execute(creditmemo, order, orderToRefund, returnToStockItems);
            }

            return Observable.empty();
        });
}