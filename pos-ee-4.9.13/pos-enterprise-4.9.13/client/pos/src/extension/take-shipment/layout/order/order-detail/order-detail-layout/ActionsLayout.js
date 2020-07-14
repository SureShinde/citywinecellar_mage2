import React from "react";

export default {
    /**
     *  Add take shipment button on order detail screen
     */
    print_before: [function takeShipmentScreen(component) {
        const CreateShipmentButton = require("../../../../view/component/order/order-detail/CreateShipmentButton").default;
        const {canShip} = component.state;
        const {order} = component.props;
        const {setIsCreatingShipment} = component.props;
        return (
            <CreateShipmentButton
                key={'take-shipment-button'}
                order={order}
                canShip={!!canShip}
                afterLoadProductQtys={() => canShip && setIsCreatingShipment(true)}
            />
        );
    }]
}