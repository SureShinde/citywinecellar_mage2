import CoreService from "../../../CoreService";
import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import NumberHelper from "../../../../helper/NumberHelper";
import StockManagementService from "../../../catalog/StockManagementService";
import OrderItemService from "../../../sales/order/OrderItemService";
import ModuleHelper from "../../../../helper/ModuleHelper";
import MultiSourceInventoryStockService from "../../../multi-source-inventory/StockService";

export class ReturnProcessorService extends CoreService {
    static className = 'ReturnProcessorService';

    execute(creditmemo, order, orderToRefund, returnToStockItems = [], isAutoReturn = false) {
        let isMSIEnable = ModuleHelper.enableModuleMSI();
        let currentStockId = null,
            orderStockId = null;
        if (isMSIEnable) {
            currentStockId = MultiSourceInventoryStockService.getCurrentStockId();
            orderStockId = MultiSourceInventoryStockService.getStockIdByOrder(orderToRefund);
        }
        let itemsToUpdate = {};

        if (creditmemo && creditmemo.items && creditmemo.items.length) {
            creditmemo.items.forEach(item => {
                let productId = item.product_id;
                let orderItem = item.order_item;
                let parentItemId = orderItem.parent_item_id;
                let qty = item.qty;
                if (isAutoReturn || this.canReturnItem(item, qty, parentItemId, returnToStockItems)) {
                    if (isMSIEnable && currentStockId !== orderStockId) {
                        let orderItem = OrderItemService.getOrderItemByOrderItemId(orderToRefund, item.order_item_id);
                        let nonShippedQty = OrderItemService.getNonShippedQtyToReturn(orderItem, orderToRefund);
                        let returnQty = Math.max(NumberHelper.minusNumber(qty, nonShippedQty), 0);
                        this.setItemsToUpdateQty(itemsToUpdate, productId, returnQty);
                    } else {
                        this.setItemsToUpdateQty(itemsToUpdate, productId, qty);
                    }
                    if (!OrderItemService.isDummy(orderItem, order)) {
                        let childrenItems = OrderItemService.getChildrenItems(orderItem, order);
                        if (childrenItems && childrenItems.length) {
                            childrenItems.forEach(children => {
                                let parentItemId = children.parent_item_id;
                                if (isAutoReturn || this.canReturnItem(children, qty, parentItemId, returnToStockItems)) {
                                    let qtyIncrement = children.qty_ordered / orderItem.qty_ordered;
                                    let qtyToReturn = NumberHelper.multipleNumber(qtyIncrement, qty);
                                    let productId = children.product_id;
                                    if (isMSIEnable && currentStockId !== orderStockId) {
                                        let orderItem = OrderItemService.getOrderItemByOrderItemId(
                                            orderToRefund, children.item_id
                                        );
                                        let nonShippedQty = OrderItemService.getNonShippedQtyToReturn(
                                            orderItem, orderToRefund
                                        );
                                        let returnQty = Math.max(NumberHelper.minusNumber(qtyToReturn, nonShippedQty), 0);
                                        this.setItemsToUpdateQty(itemsToUpdate, productId, returnQty);
                                    } else {
                                        this.setItemsToUpdateQty(itemsToUpdate, productId, qtyToReturn);
                                    }
                                }
                            });
                        }
                    }
                }
            })
        }
        if (!Object.keys(itemsToUpdate).length) {
            return this;
        }
        StockManagementService.backItemQty(itemsToUpdate);
    }

    /**
     * @param item
     * @param qty
     * @param parentItemId
     * @param returnToStockItems
     * @return {boolean | *}
     */
    canReturnItem(item, qty, parentItemId, returnToStockItems = []) {
        return (returnToStockItems.includes(item.order_item_id) || returnToStockItems.includes(parentItemId)) && qty;
    }

    /**
     * Set items to update
     * @param itemsToUpdate
     * @param productId
     * @param qty
     * @returns {*}
     */
    setItemsToUpdateQty(itemsToUpdate, productId, qty) {
        if (itemsToUpdate[productId]) {
            itemsToUpdate[productId] = NumberHelper.addNumber(itemsToUpdate[productId], qty);
        } else {
            itemsToUpdate[productId] = qty;
        }
        return itemsToUpdate;
    }
}

/**
 *
 * @type {ReturnProcessorService}
 */
let returnProcessorService = ServiceFactory.get(ReturnProcessorService);

export default returnProcessorService;