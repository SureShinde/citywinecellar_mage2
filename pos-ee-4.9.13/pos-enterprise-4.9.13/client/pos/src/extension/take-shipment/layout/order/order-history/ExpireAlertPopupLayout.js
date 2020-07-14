import React from "react";
// import OrderCreateShipment from "../../../view/component/order/order-detail/OrderCreateShipment";

export default {
    /**
     *  Add take shipment screen into order history screen
     */
    order_refund_expire_alert: [function takeShipmentButton(component) {
        const {isCreatingShipment, setIsCreatingShipment} = component.props;

        if (!isCreatingShipment) {
            return null;
        }
        const OrderCreateShipment = require("../../../view/component/order/order-detail/OrderCreateShipment").default;

        return (<OrderCreateShipment key="order-create-shipment"
                                     order={component.state.currentOrder}
                                     cancelCreateShipment={() => {
                                             setIsCreatingShipment(false)
                                     }}/>)
    }]
}