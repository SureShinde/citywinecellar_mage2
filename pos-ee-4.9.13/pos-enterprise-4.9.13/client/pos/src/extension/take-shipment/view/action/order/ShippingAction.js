import ShippingConstant from '../../constant/order/ShippingConstant';

export default {
    /**
     *
     * @param order
     * @param itemsToShip
     * @param note
     * @param tracks
     * @return {{note: *, type: string, itemsToShip, tracks: *, order: *}}
     */
    createShipment(order, itemsToShip = {}, note = null, tracks = null) {
        return {
            type: ShippingConstant.ORDER_CREATE_SHIPMENT,
            order,
            itemsToShip,
            note,
            tracks
        }
    },

    /**
     *
     * @param order
     * @return {{type: string, order: *}}
     */
    createShipmentAfter(order) {
        return {
            type: ShippingConstant.ORDER_CREATE_SHIPMENT_AFTER,
            order
        }
    },

    /**
     *
     * @param order
     * @return {{type: string, order: *}}
     */
    startLoadProductQtys(order) {
        return {
            type: ShippingConstant.ORDER_CREATE_SHIPMENT_LOAD_PRODUCT_QTYS_BEGIN,
            order,
        }
    },

    /**
     *
     * @param productQtys
     * @return {{productQtys: *, type: string}}
     */
    finishLoadProductQtys(productQtys) {
        return {
            type: ShippingConstant.ORDER_CREATE_SHIPMENT_LOAD_PRODUCT_QTYS_AFTER,
            productQtys
        }
    },


    /**
     *
     * @param isCreatingShipment
     * @return {{isCreatingShipment: *, type: string}}
     */
    setIsCreatingShipment(isCreatingShipment) {
        return {
            type: ShippingConstant.ORDER_SET_IS_CREATING_SHIPMENT,
            isCreatingShipment,
        }
    },

    /**
     *
     * @param isCreateShipment
     * @return {{isCreateShipment: *, type: string}}
     */
    setCheckoutIsCreateShipment(isCreateShipment) {
        return {
            type: ShippingConstant.CHECKOUT_SET_IS_CREATE_SHIPMENT,
            isCreateShipment,
        }
    },
}