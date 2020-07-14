import React from "react";

export default {
    /**
     *  Add create shipment toggle button on complete order screen
     */
    complete_order_button_before: [function createShipmentButton(component) {
        const {quote} = component.props;
        if (quote.is_virtual) {
            component.props.setCheckoutIsCreateShipment(false);
            return null;
        }
        const CreateShipment = require("../../view/component/checkout/complete-order/CreateShipment").default;

        return (<CreateShipment key={'create-shipment-toggle'}/>)
    }]
}