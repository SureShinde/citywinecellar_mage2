import OrderConstant from '../../../constant/OrderConstant';
import {Observable} from 'rxjs';
import NumberHelper from "../../../../helper/NumberHelper";
import LocationHelper from "../../../../helper/LocationHelper";
import ProductAction from "../../../action/ProductAction";
import StockService from "../../../../service/catalog/StockService";
import OrderItemService from "../../../../service/sales/order/OrderItemService";
import ModuleHelper from "../../../../helper/ModuleHelper";
import MultiSourceInventoryStockService from "../../../../service/multi-source-inventory/StockService";

/**
 * Receive action type(PLACE_ORDER_AFTER) and request, response list product
 * @param action$
 */
export default function CancelOrderAfterEpic(action$) {
    return action$.ofType(OrderConstant.CANCEL_ORDER_AFTER)
        .mergeMap(action => {
            let order = action.order;
            let orderToCancel = action.orderToCancel;

            let isMSIEnable = ModuleHelper.enableModuleMSI();
            let currentStockId = null,
                orderStockId = null;
            if (isMSIEnable) {
                currentStockId = MultiSourceInventoryStockService.getCurrentStockId();
                orderStockId = MultiSourceInventoryStockService.getStockIdByOrder(orderToCancel);
                if (currentStockId !== orderStockId) {
                    console.log('a');
                    return Observable.empty();
                }
            }

            let productIdsQty = getOrderProductIdsQty(orderToCancel);
            if (!order.pos_fulfill_online || LocationHelper.isPrimaryLocation()) {
                return Observable.from(
                    StockService.getListByProductIds(Object.keys(productIdsQty).map(Number))
                ).mergeMap(stocks => {
                    stocks.map(stock => {
                        if (productIdsQty[stock.product_id]) {
                            stock.qty = parseFloat(stock.qty) + parseFloat(productIdsQty[stock.product_id])
                        }
                        return stock;
                    });
                    StockService.saveToDb(stocks);
                    return Observable.of(ProductAction.syncActionUpdateStockDataFinish(stocks));
                }).catch(error => {
                    console.log(error);
                    return Observable.empty();
                })
            }
            return Observable.empty();
        });
}
/**
 * Get order product ids qty
 *
 * @param order
 * @return {{}}
 */
function getOrderProductIdsQty(order) {
    let productIds = {};
    order.items.forEach(item => {
        if (item.product_id) {
            if (!productIds[item.product_id]) {
                productIds[item.product_id] = 0;
            }
            let qtyToReturn = OrderItemService.getQtyToReturnCancel(item, order);
            productIds[item.product_id] = NumberHelper.addNumber(productIds[item.product_id], qtyToReturn);
        }
    });
    return productIds;
}
