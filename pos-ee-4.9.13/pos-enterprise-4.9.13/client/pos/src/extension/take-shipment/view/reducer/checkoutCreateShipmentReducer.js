import Constant from '../constant/order/ShippingConstant';

const initialState = {isCreateShipment: false};

/**
 *
 * @param state
 * @param action
 * @return {*}
 */
export default function (state = initialState, action) {
    switch (action.type) {
        case Constant.CHECKOUT_SET_IS_CREATE_SHIPMENT: {
            const {isCreateShipment} = action;
            return {...state, isCreateShipment};
        }
        default:
            return state
    }
}
