
export default {
    /**
     *
     * @param order
     * @return {boolean}
     */
    canShip(order) {
        if (order) {
            if (this.canUnhold(order) || this.isPaymentReview(order)) {
                return false;
            }
            if (order.is_virtual || this.isCanceled(order)) {
                return false;
            }

            if (this.canShipItem(order)) {
                return true;
            }
        }
        return false;
    },

    /**
     *
     * @param order
     * @return {*}
     */
    canShipItem(order) {
        const OrderItemService = require("../../../../../service/sales/order/OrderItemService").default;

        return this.getAllItems(order).find(item => {
            return OrderItemService.getQtyToShip(item, order) > 0 && !item.is_virtual
        });
    }
}