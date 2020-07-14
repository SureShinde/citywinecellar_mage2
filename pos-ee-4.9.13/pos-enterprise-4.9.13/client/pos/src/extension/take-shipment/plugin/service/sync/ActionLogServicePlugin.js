import ShippingConstant from "../../../view/constant/order/ShippingConstant";

export default {
    canBeSavedToServer: {
        take_shipment: {
            sortOrder: 100,
            disabled: false,
            after: (result, data) => {
                return result || data.action_type === ShippingConstant.REQUEST_ORDER_CREATE_SHIPMENT;
            }
        }
    }
}
