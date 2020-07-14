import * as PrintConstant from "../constant/PrintConstant";
import OrderConstant from "../constant/OrderConstant";
import SessionConstant from "../constant/SessionConstant";


/**
 *  initial State for reducer
 *
 * @type {{isOpen: boolean}}
 */
const initState = {
};

/**
 * receive action from Checkout Action
 *
 * @param state
 * @param action
 * @returns {*}
 */
export default function PrintReducer(state = initState, action) {
    switch (action.type) {
        case OrderConstant.PLACE_ORDER_AFTER:
            return {...state, orderData: action.order, isReprint: false, quote: action.quote};
        case PrintConstant.FINISH_PRINT:
            return {
                ...state,
                orderData: null,
                isReprint: false,
                quote: null,
                reportData: null,
                customer: null,
                creditmemo: null
            };
        case OrderConstant.REPRINT_ORDER:
            return {
                ...state,
                orderData: action.order,
                isReprint: true,
                customer: action.customer
            };
        case  OrderConstant.PRINT_CREDITMEMO:
            return {
                ...state,
                creditmemo: action.creditmemo,
                customer: action.customer
            };
        case  SessionConstant.PRINT_REPORT:
            return {
                ...state,
                reportData: action.currentSession};
        default: return state
    }
}
