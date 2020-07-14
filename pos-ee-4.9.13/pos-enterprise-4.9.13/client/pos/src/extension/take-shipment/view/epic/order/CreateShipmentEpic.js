import ShippingConstant from "../../constant/order/ShippingConstant";
import {Observable} from 'rxjs';
import OrderService from "../../../../../service/sales/OrderService";
import ActionLogService from "../../../../../service/sync/ActionLogService";
import SyncConstant from "../../../../../view/constant/SyncConstant";
import ActionLogAction from "../../../../../view/action/ActionLogAction";
import cloneDeep from 'lodash/cloneDeep';
import OrderAction from "../../../../../view/action/OrderAction";
import OrderItemService from "../../../../../service/sales/order/OrderItemService";
import NumberHelper from "../../../../../helper/NumberHelper";
import StockHelper from "../../../../../helper/StockHelper";
import ShippingAction from "../../action/order/ShippingAction";
import StateResolverService from "../../../service/sales/order/StateResolverService";
import StockService from "../../../../../service/catalog/StockService";
import ProductAction from "../../../../../view/action/ProductAction";
import LocationService from "../../../../../service/LocationService";
import PaymentHelper from "../../../../../helper/PaymentHelper";

/**
 * Edit order shipping
 *
 * @param action$
 * @returns {Observable<any>}
 */
export default function CreateShipmentEpic(action$) {
    return action$.ofType(ShippingConstant.ORDER_CREATE_SHIPMENT)
        .mergeMap((action) => {
            let {
                order,
                itemsToShip,
                note,
                tracks
            } = action;

            let newOrder = cloneDeep(order);

            let apiParams = {
                request_increment_id: PaymentHelper.generateIncrement(),
                order_increment_id: newOrder.increment_id,
                items_to_ship: [],
                note,
                tracks: tracks
            };

            let productToShip = {};
            let orderItems = newOrder.items;
            orderItems.forEach(orderItem => {
                let orderItemId = orderItem.tmp_item_id || orderItem.item_id;
                if (itemsToShip[orderItemId] !== undefined) {
                    let qtyToShip = itemsToShip[orderItemId] || 0;
                    qtyToShip = Math.min(qtyToShip, OrderItemService.getQtyToShip(orderItem, order));
                    orderItem.qty_shipped = NumberHelper.addNumber(orderItem.qty_shipped, qtyToShip);
                    apiParams.items_to_ship.push({
                        order_item_id: orderItemId,
                        qty_to_ship: qtyToShip
                    });

                    if (!productToShip[orderItem.product_id]) {
                        productToShip[orderItem.product_id] = 0;
                    }
                    productToShip[orderItem.product_id] += qtyToShip;
                }
            });

            newOrder.state = StateResolverService.getStateForOrder(newOrder);
            newOrder.status = StateResolverService.getStatusForOrderByState(newOrder.state);

            if (order.pos_location_id === LocationService.getCurrentLocationId()) {
                return createShipmentSameLocation(newOrder, apiParams, productToShip);
            }

            return createShipmentDifferenceLocation(newOrder, apiParams, productToShip);
        }).catch(() => {
            return Observable.empty();
        });
}

/**
 *
 * @param newOrder
 * @param apiParams
 * @param productToShip
 * @return {Observable<*>}
 */
const createShipmentSameLocation = (newOrder, apiParams, productToShip) => {
    let orderResource = OrderService.getResourceModel();
    let getStockPromise = new Promise(async resolve => {
        await orderResource.saveToDb([newOrder]);
        resolve(await StockService.getListByProductIds(Object.keys(productToShip).map(Number)));
    });
    return Observable.fromPromise(getStockPromise).mergeMap(stocks => {
        stocks = stocks.map(stock => {
            let isManageStock = stock.use_config_manage_stock ? StockHelper.isManageStock() : stock.manage_stock;
            if (!isManageStock) {
                return stock;
            }
            if (productToShip[stock.product_id]) {
                if (typeof stock.quantity !== 'undefined') {
                    stock.quantity = parseFloat(stock.quantity) - parseFloat(productToShip[stock.product_id]);
                }
            }
            return stock;
        });
        return Observable.fromPromise(StockService.saveToDb(stocks)).mergeMap(() => {
            let apiUrl = ShippingConstant.REQUEST_ORDER_CREATE_SHIPMENT_ENDPOINT;

            return Observable.fromPromise(ActionLogService.createDataActionLog(
                ShippingConstant.REQUEST_ORDER_CREATE_SHIPMENT, apiUrl, SyncConstant.METHOD_POST, apiParams
            )).mergeMap(() => {
                return [
                    ShippingAction.createShipmentAfter(newOrder),
                    OrderAction.syncActionUpdateDataFinish([newOrder]),
                    ActionLogAction.syncActionLog()
                ];
            })
        });
    });
};

/**
 *
 * @param newOrder
 * @param apiParams
 * @param productToShip
 * @return {Observable<*>}
 */
const createShipmentDifferenceLocation = (newOrder, apiParams, productToShip) => {
    let orderResource = OrderService.getResourceModel();
    let getStockPromise = new Promise(async resolve => {
        await orderResource.saveToDb([newOrder]);
        resolve(await StockService.getListByProductIds(Object.keys(productToShip).map(Number)));
    });
    return Observable.fromPromise(getStockPromise).mergeMap(stocks => {
        stocks = stocks.map(stock => {
            let isManageStock = stock.use_config_manage_stock ? StockHelper.isManageStock() : stock.manage_stock;
            if (!isManageStock) {
                return stock;
            }

            if (productToShip[stock.product_id]) {
                stock.qty = parseFloat(stock.qty) - parseFloat(productToShip[stock.product_id]);
                if (typeof stock.quantity !== 'undefined') {
                    stock.quantity = parseFloat(stock.quantity) - parseFloat(productToShip[stock.product_id]);
                }
            }
            return stock;
        });
        return Observable.fromPromise(StockService.saveToDb(stocks)).mergeMap(() => {
            let apiUrl = ShippingConstant.REQUEST_ORDER_CREATE_SHIPMENT_ENDPOINT;
            let addShipmentLogPromise = new Promise(async resolve => {
                await StockService.saveToDb(stocks);
                resolve(await ActionLogService.createDataActionLog(
                    ShippingConstant.REQUEST_ORDER_CREATE_SHIPMENT, apiUrl, SyncConstant.METHOD_POST, apiParams
                ));
            });
            return Observable.fromPromise(addShipmentLogPromise).mergeMap(() => {
                return [
                    ShippingAction.createShipmentAfter(newOrder),
                    OrderAction.syncActionUpdateDataFinish([newOrder]),
                    ProductAction.syncActionUpdateStockDataFinish(stocks),
                    ActionLogAction.syncActionLog()
                ];
            })

        });
    });
};
