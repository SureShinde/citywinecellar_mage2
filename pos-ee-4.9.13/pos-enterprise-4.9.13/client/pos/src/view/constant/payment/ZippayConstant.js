export default {
    API_KEY: "api_key",
    API_URL: "api_url",
    LOCATIONS: "locations",

    /** Constant transaction result from zippay terminal response */
    TRANSACTION_STATUS_PENDING          : 'PENDING',
    TRANSACTION_STATUS_APPROVED         : 'APPROVED',
    TRANSACTION_STATUS_DECLINED         : 'DECLINED',
    TRANSACTION_STATUS_REVERSED         : 'REVERSED',
    TRANSACTION_STATUS_CANCELLED        : 'CANCELLED',
    TRANSACTION_STATUS_EXPIRED          : 'EXPIRED',
    /** Message */
    MESSAGE_TRANSACTION_STATUS_EXPIRED  : 'The order has timed out. Please start the process again.',
    MESSAGE_TRANSACTION_STATUS_DECLINED : 'The transaction has been declined, please check available funds.',
    MESSAGE_TRANSACTION_STATUS_CANCELLED: 'The purchase has been cancelled.',
    UNKNOWN_EXCEPTION_MESSAGE           : 'Connection failed. Please contact admin to check the configuration of API.',


    PROCESS_PAYMENT: '[ZIPPAY] PROCESS_PAYMENT',
}
