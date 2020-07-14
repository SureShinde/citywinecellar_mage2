export default {
    CLEAR_DATA        : '[PAYMENT] CLEAR_DATA',
    GET_PAYMENT_ONLINE: '[PAYMENT] GET_PAYMENT_ONLINE',

    TYPE_GET_PAYMENT: 'get_payment',

    GET_LIST_PAYMENT       : '[PAYMENT] GET_LIST_PAYMENT',
    GET_LIST_PAYMENT_RESULT: '[PAYMENT] GET_LIST_PAYMENT_RESULT',

    SELECT_PAYMENT                                 : '[PAYMENT] SELECT_PAYMENT',
    CANCEL_PAYMENT                                 : '[PAYMENT] CANCEL_PAYMENT',
    BACK_TO_ADD_PAYMENT                            : '[PAYMENT] BACK_TO_ADD_PAYMENT',
    BACK_PAYMENT                                   : '[PAYMENT] BACK_PAYMENT',
    SWITCH_PAGE                                    : '[PAYMENT] SWITCH_PAGE',
    RESET_STATE                                    : '[PAYMENT] RESET_STATE',
    ADD_PAYMENT                                    : '[PAYMENT] ADD_PAYMENT',
    UPDATE_STORE_CREDIT_WHEN_CHANGE_SHIPPING_METHOD: '[PAYMENT] UPDATE_STORE_CREDIT_WHEN_CHANGE_SHIPPING_METHOD',
    UPDATE_PAYMENT_LIST                            : '[PAYMENT] UPDATE_PAYMENT_LIST',

    PAYMENT_PAGE_SELECT_PAYMENT: '[PAYMENT] PAYMENT_PAGE_SELECT_PAYMENT',
    PAYMENT_PAGE_EDIT_PAYMENT  : '[PAYMENT] PAYMENT_PAGE_EDIT_PAYMENT',
    PAYMENT_PAGE_COMPLETE_ORDER: '[PAYMENT] PAYMENT_PAGE_COMPLETE_ORDER',

    PAYMENT_TYPE_OFFLINE        : '0', // origin offline
    PAYMENT_TYPE_CREDITCARD_FORM: '1', // credit card form
    PAYMENT_TYPE_REDIRECT       : '2', // redirect
    PAYMENT_TYPE_TERMINAL       : '3', // terminal
    PAYMENT_TYPE_EWALLET        : '4', // eWallet

    PAYMENT_IS_REFERENCE_NUMBER: 1,
    PAYMENT_CAN_DUE            : 1,

    AUTHORIZENET_INTEGRATION         : 'authorizenet_integration',
    BAMBORA_INTEGRATION              : 'bambora_integration',
    TYRO_INTEGRATION                 : 'tyro_integration',
    CASH                             : 'cashforpos',
    CREDIT_CARD                      : 'ccforpos',
    PAYPAL_INTEGRATION               : 'paypal_integration',
    PAYPAL_DIRECTPAYMENT_INTERGRATION: 'ppdirectpayment_integration',
    STRIPE_INTEGRATION               : 'stripe_integration',
    ZIPPAY_INTEGRATION               : 'zippay_integration',
    STORE_CREDIT                     : 'store_credit',
    PROCESS_PAYMENT_NEW              : 'PROCESS_PAYMENT_NEW',
    PROCESS_PAYMENT_PENDING          : 'PROCESS_PAYMENT_PENDING',
    PROCESS_PAYMENT_PROCESSING       : 'PROCESS_PAYMENT_PROCESSING',
    PROCESS_PAYMENT_SUCCESS          : 'PROCESS_PAYMENT_SUCCESS',
    PROCESS_PAYMENT_ERROR            : 'PROCESS_PAYMENT_ERROR',
    PROCESSED_PAYMENT                : 'PROCESSED_PAYMENT',

    PAYMENT_STATUS_TO_PROCESSING: '[PAYMENT] PAYMENT_STATUS_TO_PROCESSING',
    PAYMENTS_STATUS_TO_PENDING  : '[PAYMENT] PAYMENT_STATUS_TO_PENDING',
    START_PROCESS_SINGLE_PAYMENT: '[PAYMENT] START_PROCESS_SINGLE_PAYMENT',

    TYPE_CHECKOUT: 0,
    TYPE_REFUND  : 1,

    LOST_INTERNET_STATUS            : 'LOST_INTERNET',
    TIMEOUT_STATUS                  : 'TIMEOUT',
    LOST_INTERNET_CONNECTION_MESSAGE: 'Connection failed. ' +
        'You must connect to a Wi-Fi or cellular data network to use this payment method',
    TIME_OUT_EXCEPTION_MESSAGE      : 'The order has timed out. Please start the process again.',

    PAYMENT_TYPE : 'payment_type',
    FLAT_PAYMENTS : 'flat_payments',
    CREDIT_CARD_FORM_PAYMENTS : 'credit_card_form_payments',
    E_WALLET_PAYMENTS : 'e_wallet_payments',
    TERMINAL_PAYMENTS : 'terminal_payments',
    INTERNET_TERMINAL_PAYMENTS : 'internet_terminal_payments',
    PREVENT_CANCEL_ORDER_RULE_PAYMENTS : 'prevent_cancel_order_rule_payments',

    REFUND_PAYMENT_TYPE : 'refund_payment_type',
    ACCEPTED_PAYMENTS : 'accepted_payments',
    USE_TRANSACTION_PAYMENTS : 'use_transaction_payments',
}
