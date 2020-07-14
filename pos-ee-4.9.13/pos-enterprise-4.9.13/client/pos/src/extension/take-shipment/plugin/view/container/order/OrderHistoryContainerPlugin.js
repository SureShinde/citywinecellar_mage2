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
    changeMapState: {
        sortOrder: 1,
        disabled: false,
        after: (result, state) => {
            const {isCreatingShipment} = state.extension.orderCreateShipmentReducer;
            return {
                ...result,
                isCreatingShipment
            }
        }
    }
}