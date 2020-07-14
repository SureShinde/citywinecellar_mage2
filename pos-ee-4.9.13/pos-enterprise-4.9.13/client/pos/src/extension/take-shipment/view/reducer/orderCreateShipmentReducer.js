import Constant from '../constant/order/ShippingConstant';

const initialState = {
    isCreatingShipment: false,
    productQtys: false
};

/**
 *
 * @param state
 * @param action
 * @return {*}
 */
export default function (state = initialState, action) {
    switch (action.type) {
        case Constant.ORDER_SET_IS_CREATING_SHIPMENT: {
            const {isCreatingShipment} = action;
            return {...state, isCreatingShipment};
        }
        case Constant.ORDER_CREATE_SHIPMENT_LOAD_PRODUCT_QTYS_AFTER: {
            const {productQtys} = action;
            return {...state, productQtys};
        }
        default:
            return state
    }
};
