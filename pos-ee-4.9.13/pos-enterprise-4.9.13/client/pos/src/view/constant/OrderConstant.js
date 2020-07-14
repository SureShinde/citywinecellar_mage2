export default {
    PLACE_ORDER_AFTER: '[ORDER] PLACE_ORDER_AFTER',
    SEARCH_ORDER: '[ORDER] SEARCH_ORDER',
    SEARCH_ORDER_RESULT: '[ORDER] SEARCH_ORDER_RESULT',
    SEARCH_ORDER_ERROR: '[ORDER] SEARCH_ORDER_ERROR',
    REPRINT_ORDER: '[ORDER] REPRINT_ORDER',

    PRINT_CREDITMEMO: '[ORDER] PRINT_CREDITMEMO',

    TAKE_PAYMENT: '[ORDER] TAKE_PAYMENT',
    TAKE_PAYMENT_RESULT: '[ORDER] TAKE_PAYMENT_RESULT',

    SEND_EMAIL: '[ORDER] SEND_EMAIL',
    SEND_EMAIL_RESULT: '[ORDER] SEND_EMAIL_RESULT',

    ADD_COMMENT: '[ORDER] ADD_COMMENT',
    ADD_COMMENT_RESULT: '[ORDER] ADD_COMMENT_RESULT',

    CANCEL: '[ORDER] CANCEL',
    CANCEL_RESULT: '[ORDER] CANCEL_RESULT',
    CANCEL_ORDER_AFTER: '[ORDER] CANCEL_ORDER_AFTER',

    TAKE_PAYMENT_PROCESS_PAYMENT: '[ORDER] TAKE_PAYMENT_PROCESS_PAYMENT',
    TAKE_PAYMENT_PROCESS_PAYMENT_RESULT: '[ORDER] TAKE_PAYMENT_PROCESS_PAYMENT_RESULT',
    TAKE_PAYMENT_PROCESS_PAYMENT_ERROR: '[ORDER] TAKE_PAYMENT_PROCESS_PAYMENT_ERROR',
    TAKE_PAYMENT_PROCESS_SINGLE_PAYMENT_RESULT: '[ORDER] TAKE_PAYMENT_PROCESS_SINGLE_PAYMENT_RESULT',
    TAKE_PAYMENT_PROCESS_SINGLE_PAYMENT_ERROR: '[ORDER] TAKE_PAYMENT_PROCESS_SINGLE_PAYMENT_ERROR',

    REMOVE_PAYMENT: '[ORDER] REMOVE_PAYMENT',
    ADD_PAYMENT: '[ORDER] ADD_PAYMENT',

    SYNC_ACTION_UPDATE_DATA_FINISH: '[ORDER] SYNC_ACTION_UPDATE_DATA_FINISH',
    RESET_UPDATED_ORDERS_LIST: '[ORDER] RESET_UPDATED_ORDERS_LIST',
    SYNC_DELETED_ORDER_FINISH: '[ORDER] SYNC_DELETED_ORDER_FINISH',

    // Config Path
    XML_PATH_CONFIG_SYNC_ORDER_TIME: "webpos/offline/order_time",
    XML_PATH_CONFIG_SYNC_ORDER_SINCE: "webpos/offline/order_since",

    ORDER_SINCE_24H: '24h',
    ORDER_SINCE_7_DAYS: '7days',
    ORDER_SINCE_MONTH: 'month',
    ORDER_SINCE_YTD: 'YTD',
    ORDER_SINCE_2_YTD: '2YTD',

    PAGE_SIZE: 15,

    GET_LIST_ORDER_STATUSES: '[ORDER] GET_LIST_ORDER_STATUSES',
    TYPE_GET_LIST_ORDER_STATUSES: 'get_list_order_statuses',
}
