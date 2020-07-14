import PaymentConstant from "../view/constant/PaymentConstant";
import CustomPrefixHelper from "./CustomPrefixHelper";
import Config from "../config/Config";

const PROCESSED_STATUS = [
  PaymentConstant.PROCESSED_PAYMENT,
  PaymentConstant.PROCESS_PAYMENT_SUCCESS,
  PaymentConstant.PROCESS_PAYMENT_ERROR
];


export default {
    PROCESSED_STATUS: PROCESSED_STATUS,

    /**
     *
     * @param type
     * @returns {*}
     */
    getAllPaymentType(type) {
        return Config.config[PaymentConstant.PAYMENT_TYPE][type];
    },

    /**
     *
     * @param method
     * @return {boolean}
     */
    isFlatPayment(method) {
        return this.getAllPaymentType(PaymentConstant.FLAT_PAYMENTS).split(',').indexOf(method) !== -1
    },
    /**
     *
     * @param method
     * @return {boolean}
     */
    isPaypalDirect(method) {
        return method === PaymentConstant.PAYPAL_DIRECTPAYMENT_INTERGRATION
    },
    /**
     *
     * @return {string}
     */
    paypalPayViaEmailTitle() {
        return 'PayPal pay via email';
    },
    /**
     *
     * @param object
     * @return {boolean}
     */
    hasUsingGatewayPayment(object) {
        return object.payments.find(payment => {
            return this.hasUsingCreditCardForm(payment.method) && !payment.type
        });
    },
    /**
     *
     * @param method
     * @return {boolean}
     */
    hasUsingCreditCardForm(method) {
        return this.getAllPaymentType(PaymentConstant.CREDIT_CARD_FORM_PAYMENTS).split(',').indexOf(method) !== -1;
    },

    /**
     *
     * @param method
     * @return {boolean}
     */
    hasUsingTerminal(method) {
        return this.getAllPaymentType(PaymentConstant.TERMINAL_PAYMENTS).split(',').indexOf(method) !== -1;
    },

    /**
     *
     * @param method
     * @return {boolean}
     */
    hasUsingInternetTerminal(method) {
        return this.getAllPaymentType(PaymentConstant.INTERNET_TERMINAL_PAYMENTS).split(',').indexOf(method) !== -1;
    },

    /**
     *
     * @param method
     * @return {boolean}
     */
    hasUsingEWallet(method) {
        return this.getAllPaymentType(PaymentConstant.E_WALLET_PAYMENTS).split(',').indexOf(method) !== -1;
    },

    /**
     *
     * @param method
     * @returns {boolean}
     */
    hasPreventCancelOrderRule(method) {
        return this.getAllPaymentType(PaymentConstant.PREVENT_CANCEL_ORDER_RULE_PAYMENTS).split(',').indexOf(method) !== -1;
    },

    /**
     * remove dump data
     * @param order
     * @return {{payments}}
     */
    filterOrderData(order) {
        if (!order.payments.length) {
            let newOrder = {...order};
            delete newOrder['payments'];
            return newOrder;
        }
        return {...order, payments: this.filterPaymentData(order.payments)};
    },
    /**
     * remove dump data
     * @param payments
     * @return {{payments}}
     */
    filterPaymentData(payments) {
        return payments.map(payment => {
            /**  remove cc information */
            delete payment['cc_owner'];
            delete payment['cc_number'];
            delete payment['cc_type'];
            delete payment['cc_exp_month'];
            delete payment['cc_exp_year'];
            delete payment['cc_cid'];
            delete payment['email'];
            delete payment['isCardMode'];
            delete payment['status'];
            delete payment['errorMessage'];
            delete payment['last4Digit'];
            delete payment['orderPayment'];
            delete payment['amount_refunded'];
            // delete payment.is_pay_later;
            return payment;
        });
    },
    /**
     * remove dump data
     * @param payment
     * @return {{payments}}
     */
    filterPaypalPaymentData(payment) {
        delete payment['card_type'];
        delete payment['isCardMode'];
        delete payment['email'];
        delete payment['is_pay_later'];
        delete payment['status'];
        delete payment['errorMessage'];
        delete payment['last4Digit'];
        delete payment['shift_increment_id'];
        delete payment['orderPayment'];
        delete payment['amount_refunded'];
        delete payment['increment_id'];
        return payment;
    },
    /**
     *
     * @param processPayments
     * @param removedIndex
     * @return {*}
     */
    resortPayment(processPayments, removedIndex) {
        let newProcessPayments = {};
        Object.keys(processPayments).forEach(key => {
            let method   = key.substr(0, key.length - 1);
            let keyIndex = key.substr(key.length - 1) * 1;

            if (keyIndex < removedIndex) {
                newProcessPayments[key] = processPayments[key];
            }

            if (keyIndex > removedIndex) {
                newProcessPayments[method + (keyIndex - 1)] = processPayments[key];
            }
        });

        return newProcessPayments;
    },
    /**
     *
     * @param processPayments
     * @return {boolean}
     */
    hasPaidViaGatewaySuccess(processPayments) {
        let isCustomerPaidViaGateway = false;
        Object.values(processPayments).forEach(payment => {
            isCustomerPaidViaGateway |= payment.status === PaymentConstant.PROCESS_PAYMENT_SUCCESS;
        });

        return isCustomerPaidViaGateway;
    },
    /**
     *
     * @param processPayments
     * @return {boolean}
     */
    isWaitingProcessPaymentComplete(processPayments) {
        let isWaitingProcessPaymentComplete = false;
        Object.values(processPayments).forEach(payment => {
            isWaitingProcessPaymentComplete |= [
                PaymentConstant.PROCESS_PAYMENT_PENDING,
                PaymentConstant.PROCESS_PAYMENT_PROCESSING
            ].indexOf(payment.status) !== -1;
        });
        return isWaitingProcessPaymentComplete;
    },
    /**
     *
     * @param processPayments
     * @return {*|boolean}
     */
    hasPaidOrWaitingGatewayPayment(processPayments) {
        return this.hasPaidViaGatewaySuccess(processPayments) || this.isWaitingProcessPaymentComplete(processPayments);
    },
    /**
     *
     * @param processPayments
     * @return {boolean}
     */
    isSuccessAll(processPayments) {
        let isProcessedAllPayment = true;
        Object.values(processPayments).forEach(payment => {
            isProcessedAllPayment &= [
                PaymentConstant.PROCESSED_PAYMENT,
                PaymentConstant.PROCESS_PAYMENT_SUCCESS
            ].indexOf(payment.status) !== -1;
        });

        return isProcessedAllPayment
    },
    /**
     *
     * @param processPayments
     * @return {boolean}
     */
    hasErrorProcessPayment(processPayments) {
        let hasErrorProcessPayment = false;
        Object.values(processPayments).forEach(payment => {
            hasErrorProcessPayment |= [
                PaymentConstant.PROCESS_PAYMENT_ERROR
            ].indexOf(payment.status) !== -1;
        });
        return hasErrorProcessPayment;
    },

    /**
     *
     * @param object
     * @param refCode
     * @return {boolean | {}}
     */
    getPaymentByRefCode(object, refCode) {
        if (!object.payments) return false;
        if (!object.payments.length) return false;
        return object.payments.find(payment => payment.reference_number === refCode);
    },

    /**
     *
     * @param index
     * @return {string}
     */
    generateIncrement(index) {
        let incrementId = [];
        if (CustomPrefixHelper.getUseCustomPrefix()) {
            incrementId.push(CustomPrefixHelper.getCustomPrefix());
        }
        if (index) {
            incrementId.push(index);
        }

        let currentTimestamp = new Date().getTime();
        incrementId.push(Config.pos_id);
        incrementId.push(parseInt(currentTimestamp / 1000, 10));
        return incrementId.join('-');
    },

    /**
     *
     * @param object
     * @param seeder
     * @param payment
     * @return {*}
     */
    generateRefCode(object, seeder, payment) {
        let refCode = seeder;
        let counter = 1;
        let subFix = '';
        object.payments.forEach((objectPayment, index) => {
            if (objectPayment.method !== payment.method) {
                return;
            }

            if (objectPayment.increment_id === payment.increment_id) {
                subFix = `-${counter}`;
            }

            counter++;
        });

        return refCode + subFix;
    },

    /**
     *
     * @param refCode
     * @return {string}
     */
    convertRefCodeToIncrementOrderId(refCode) {
        if (!refCode || !refCode.length) return '';

        let refCodeArray = refCode.split('-');

        if (refCodeArray.length === 1) {
            //online order
            return refCodeArray[0];
        }

        if (CustomPrefixHelper.getUseCustomPrefix()) {
            refCode = refCode.replace(`${CustomPrefixHelper.getUseCustomPrefix()}-`, '');
            // custom prefix + pos id + time spam
            return [refCodeArray[0], refCodeArray[1], refCodeArray[2]].join('-');
        }

        //pos id + time spam
        return [refCodeArray[0], refCodeArray[1]].join('-');
    },

    /**
     *
     * @param object
     * @param {boolean} skipCheckPaymentType
     */
    isRequireInternet(object, skipCheckPaymentType) {
        return object.payments.find(payment => {
            let isEWallet = this.hasUsingEWallet(payment.method);
            let requireInternet = (
                    this.hasUsingCreditCardForm(payment.method)
                    || isEWallet
                    || this.hasUsingInternetTerminal(payment.method)
                )
                && (
                    !payment.reference_number
                    || (
                        payment.reference_number
                        && isEWallet
                    )
                );

            if (skipCheckPaymentType) {
                return requireInternet;
            }

            return payment.type !== PaymentConstant.TYPE_REFUND && requireInternet
        });
    }
}
