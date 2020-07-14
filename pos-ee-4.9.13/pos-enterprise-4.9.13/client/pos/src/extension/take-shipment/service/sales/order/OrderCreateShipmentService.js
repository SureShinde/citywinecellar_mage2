import {AbstractOrderService} from "../../../../../service/sales/AbstractService";
import ServiceFactory from "../../../../../framework/factory/ServiceFactory";
import OrderItemService from "../../../../../service/sales/order/OrderItemService";
import ShipmentFactoryService from "./ShipmentFactoryService";

export class OrderCreateShipmentService extends AbstractOrderService {
    static className = 'OrderCreateShipmentService';

    /**
     *
     * @param item
     * @param order
     * @return {{order_item_id: *, order_item: *, qty: number, is_qty_decimal: *, qty_left: number}}
     */
    prepareShipmentItemParam(item, order) {
        let qtyLeft = 0;
        if (OrderItemService.isDummy(item, order, true)) {

        } else {
            qtyLeft = OrderItemService.getQtyToShip(item, order);
        }


        qtyLeft = ShipmentFactoryService.castQty(item, qtyLeft);
        let orderItemId = item.tmp_item_id || item.item_id;

        return {
            order_item_id: orderItemId,
            order_item: item,
            qty_left: qtyLeft,
            qty: 0,
            is_qty_decimal: item.is_qty_decimal
        };
    }
}

/** @type {OrderCreateShipmentService} */
let orderCreateShipmentService = ServiceFactory.get(OrderCreateShipmentService);

export default orderCreateShipmentService;