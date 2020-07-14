import ShippingAction from "../../../../../view/action/order/ShippingAction";

export default {
    changeMapDispatch: {
        sortOrder: 1,
        disabled: false,
        after: (result, dispatch) => {
            return {
                ...result,
                setCheckoutIsCreateShipment:
                    isCreatingShipment  => dispatch(ShippingAction.setCheckoutIsCreateShipment(isCreatingShipment))
            }
        }
    },
}