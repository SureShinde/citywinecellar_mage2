import ShippingAction from "../../../../view/action/order/ShippingAction";

export default {
    changeMapDispatch: {
        sortOrder: 1,
        disabled: false,
        after: (result, dispatch) => {
            return {
                ...result,
                setIsCreatingShipment:
                    isCreatingShipment  => dispatch(ShippingAction.setIsCreatingShipment(isCreatingShipment))
            }
        }
    },
}