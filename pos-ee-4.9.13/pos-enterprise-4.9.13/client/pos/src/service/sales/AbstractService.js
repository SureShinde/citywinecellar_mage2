import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import ProductTypeConstant from "../../view/constant/ProductTypeConstant";

export class AbstractOrderService extends CoreService {
    static className = 'AbstractOrderService';

    /**
     * Get order item from product id
     *
     * @param order
     * @param productId
     */
    getItemsByProductId(order, productId) {
        return order.items.filter(item => item.product_id === productId);
    }

    /**
     * Get children items from order item parent id
     *
     * @param parentItem
     * @param order
     * @return {mixed[]|boolean}
     */
    getChildrenItems(parentItem, order) {
        if (parentItem.type_id === ProductTypeConstant.SIMPLE ||
            parentItem.product_type === ProductTypeConstant.SIMPLE) {
            return false;
        }
        return order.items.filter(item => +item.parent_item_id === +parentItem.item_id);
    }

    /**
     * Get parent item id from order item id
     *
     * @param childItem
     * @param order
     */
    getParentItem(childItem, order) {
        if (!childItem.parent_item_id) {
            return false;
        }
        return order.items.find(item => (+item.item_id === +childItem.parent_item_id));
    }

    /**
     * Get order item by order item id
     *
     * @param order
     * @param orderItemId
     * @returns {*}
     */
    getOrderItemByOrderItemId(order, orderItemId) {
        if (order && order.items && order.items.length) {
            return order.items.find(item => +item.item_id === +orderItemId);
        }
        return null;
    }
}

/**
 *
 * @type {AbstractOrderService}
 */
let abstractOrderService = ServiceFactory.get(AbstractOrderService);

export default abstractOrderService;