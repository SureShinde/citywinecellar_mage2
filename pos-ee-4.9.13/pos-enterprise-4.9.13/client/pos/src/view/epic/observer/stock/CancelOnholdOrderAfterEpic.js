import OnHoldOrderConstant from '../../../constant/OnHoldOrderConstant';
import {Observable} from 'rxjs';
import NumberHelper from "../../../../helper/NumberHelper";
import LocationHelper from "../../../../helper/LocationHelper";
import ProductAction from "../../../action/ProductAction";
import StockService from "../../../../service/catalog/StockService";
import OrderItemService from "../../../../service/sales/order/OrderItemService";
import OnHoldOrderService from "../../../../service/sales/OnHoldOrderService";
/**
 * Receive action type(PLACE_ORDER_AFTER) and request, response list product
 * @param action$
 */
export default function CancelOnholdOrderAfterEpic(action$, store) {
    return action$.ofType(OnHoldOrderConstant.CANCEL_ON_HOLD_ORDER_AFTER)
        .mergeMap(action => {
            let order = action.order;
            let productIdsQty = getOrderProductIdsQty(order);
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
                    StockService.saveToDb(stocks).then(async () => {
                        if (order.reorder) {
                            let products = await OnHoldOrderService.checkProducts(order);
                            if (OnHoldOrderService.isCheckoutAble(products)) {
                                await OnHoldOrderService.reorder(order, products, action.history, store, true);
                            }
                        }
                    });
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
    order.items.map(item => {
        if (item.product_id) {
            // if (StockService.getProductStockService(item.product).isManageStock(item.product)) {
            if (!productIds[item.product_id]) {
                productIds[item.product_id] = 0;
            }
            // let qty = item.qty_ordered;
            // if (item.parent_item_id) {
            //     let parentItemId = item.parent_item_id;
            //     if (!parentItems[parentItemId]) {
            //         let parentItem = OrderItemService.getParentItem(item, order);
            //         if (parentItem) {
            //             parentItems[parentItemId] = parentItem;
            //         }
            //     }
            //     if (parentItems[parentItemId] && parentItems[parentItemId].item_id) {
            //         qty = NumberHelper.multipleNumber(qty, parentItems[parentItemId].qty);
            //     }
            // }
            let qtyToReturn = OrderItemService.getQtyToReturnCancel(item, order);
            productIds[item.product_id] = NumberHelper.addNumber(productIds[item.product_id], qtyToReturn);
            // }
        }
        return item;
    });
    return productIds;
}
