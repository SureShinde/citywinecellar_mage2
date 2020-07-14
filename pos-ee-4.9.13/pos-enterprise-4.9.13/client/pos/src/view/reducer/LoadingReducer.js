import LoadingConstant from '../constant/LoadingConstant';
import LogoutPopupConstant from "../constant/LogoutPopupConstant";
import ConfigConstant from "../constant/ConfigConstant";
import {fire} from "../../event-bus";
import ColorSwatchConstant from "../constant/ColorSwatchConstant";
import PaymentConstant from "../constant/PaymentConstant";
import ShippingConstant from "../constant/ShippingConstant";
import OrderConstant from "../constant/OrderConstant";
import SessionConstant from "../constant/SessionConstant";

const initialState = {
    finishedList: [],
    count: 0,
    total: 6,
    dataList: [
        ConfigConstant.TYPE_GET_CONFIG,
        ColorSwatchConstant.TYPE_GET_COLOR_SWATCH,
        PaymentConstant.TYPE_GET_PAYMENT,
        ShippingConstant.TYPE_GET_SHIPPING,
        OrderConstant.TYPE_GET_LIST_ORDER_STATUSES,
        SessionConstant.TYPE_GET_CURRENT_SESSION
    ],
};
// event to modify initial state
fire('reducer_loading_define_initial_state_after', {initialState});
initialState.total = initialState.dataList.length;

/**
 * receive action from Loading Action
 *
 * @param state = initialState
 * @param action
 * @returns {*}
 */
const loadingReducer =  function (state = initialState, action) {
    switch (action.type) {
        case LoadingConstant.UPDATE_FINISHED_LIST: {
            let finishedList = state.finishedList;
            if (finishedList.indexOf(action.dataType) < 0) {
                finishedList.push(action.dataType);
            }
            return {...state, finishedList: finishedList, count: finishedList.length};
        }
        case LoadingConstant.RESET_STATE: {
            return initialState;
        }
        case LoadingConstant.CLEAR_DATA_ERROR: {
            return state;
        }
        case LogoutPopupConstant.FINISH_LOGOUT_REQUESTING: {
            return initialState;
        }
        default: {
            return state;
        }
    }
};

export default loadingReducer;
