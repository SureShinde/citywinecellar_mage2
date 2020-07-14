import LogoutPopupConstant from "../../constant/LogoutPopupConstant";
import QuoteConstant from "../../constant/checkout/QuoteConstant";
import CheckoutConstant from "../../constant/CheckoutConstant";
import cloneDeep from 'lodash/cloneDeep';
import QuoteService from "../../../service/checkout/QuoteService";
import PaymentConstant from "../../constant/PaymentConstant";
import PaymentService from "../../../service/payment/PaymentService";

/**
 * Receive action from Quote, Payment, Checkout  Action
 *
 * @param state
 * @param action
 * @returns {*}
 */
const quoteReducer = function (state = cloneDeep(QuoteService.initialQuoteReducerState), action) {
    switch (action.type) {
        case QuoteConstant.SET_QUOTE:
            return {...action.quote};
        case QuoteConstant.SET_PAYMENTS:
            return {...state, payments: action.payments};
        case LogoutPopupConstant.FINISH_LOGOUT_REQUESTING: {
            let quote = cloneDeep(QuoteService.initialQuoteReducerState);
            return {...quote, id: new Date().getTime()};
        }
        case CheckoutConstant.CHECKOUT_TO_SELECT_PAYMENTS:
            if(action.initPayments) {
                return {...state, payments: []};
            }
            return state;
        case CheckoutConstant.CHECKOUT_PROCESS_SINGLE_PAYMENT_RESULT:{
            const { result, index } = action;
            let payments            = [...state['payments']];
            payments[index].status  = PaymentConstant.PROCESS_PAYMENT_SUCCESS;
            if (result.response) {
                PaymentService.updatePaymentFromSuccessResponse(payments[index], result.response)
            }
            return {...state, payments: payments};
        }
        case CheckoutConstant.CHECKOUT_PROCESS_SINGLE_PAYMENT_ERROR: {
            const { result, index }      = action;
            let payments                 = [...state['payments']];
            payments[index].status       = PaymentConstant.PROCESS_PAYMENT_ERROR;
            payments[index].errorMessage = result.message;

            if (result.response && result.response.reference_number) {
                payments[index].reference_number = result.response.reference_number;
            }

            return {...state, payments: payments};
        }

        case PaymentConstant.PAYMENT_STATUS_TO_PROCESSING: {
            if (!Object.keys(state['payments']).length) return state;
            const {index} = action;
            let payments = [...state['payments']];
            payments[index].status = PaymentConstant.PROCESS_PAYMENT_PROCESSING;
            return {...state, payments: payments};
        }
        case PaymentConstant.PAYMENTS_STATUS_TO_PENDING: {
            if (!Object.keys(state['payments']).length) return state;
            let payments = [...state['payments']];
            payments.forEach((payment) => {
                if (
                    payment.status === PaymentConstant.PROCESS_PAYMENT_NEW
                    || payment.status === PaymentConstant.PROCESS_PAYMENT_ERROR
                ) {
                    return payment.status = PaymentConstant.PROCESS_PAYMENT_PENDING;
                }
            });
            return {...state, payments: payments};
        }
        default:
            return state;
    }
};

export default quoteReducer;

