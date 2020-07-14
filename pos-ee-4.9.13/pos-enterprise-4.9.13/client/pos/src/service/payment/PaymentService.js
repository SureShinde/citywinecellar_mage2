import CoreService from "../CoreService";
import PaymentResourceModel from "../../resource-model/payment/PaymentResourceModel";
import ServiceFactory from "../../framework/factory/ServiceFactory"
import PaymentHelper from "../../helper/PaymentHelper";
import PaymentFactory from "../../factory/PaymentFactory";
import PaymentConstant from "../../view/constant/PaymentConstant";
import StoreCreditService from "../store-credit/StoreCreditService";
import NumberHelper from "../../helper/NumberHelper";
import _ from "lodash";
import CurrencyHelper from "../../helper/CurrencyHelper";
import OrderHelper from "../../helper/OrderHelper";

export class PaymentService extends CoreService {
    static className     = 'PaymentService';
           resourceModel = PaymentResourceModel;

    /**
     * Call PaymentResourceModel get all
     *
     * @returns {Object|*|FormDataEntryValue[]|string[]}
     */
    getAll() {
        let paymentResourceModel = this.getResourceModel();
        return paymentResourceModel.getAll();
    }

    async getByCode(code) {
        const list = await this.getAll();

        return list.find(payment => (payment.code === code));
    }

    /**
     * clear all data in indexedDB
     * @return {*}
     */
    clear() {
        return this.getResourceModel().clear();
    }

    /**
     * process single Payment
     * @param payment
     * @param index
     * @param object
     * @return {Promise<{order_increment_id: string|*}>}
     */
    async processSinglePayment(payment, index, object) {
        let isEWallet = PaymentHelper.hasUsingEWallet(payment.method);
        let isSpecialPayment =  PaymentHelper.hasUsingCreditCardForm(payment.method)
            || PaymentHelper.hasUsingTerminal(payment.method)
            || isEWallet;

        /**
         *  if not paid and is special payment
         */
        if (

            !payment.is_paid
            && isSpecialPayment
            && (
                !payment.reference_number
                /**
                 *  accept exception use eWallet and interrupt complete order
                 */
                || (payment.reference_number && isEWallet)
            )
        ) {
            let paymentService = PaymentFactory.createByCode(payment.method);

            // if order => set order
            const isCreditmemo = object.isCreditmemo;
            const isCheckout   = !object.hasOwnProperty('increment_id');

            if (isCreditmemo) {
                paymentService.setCreditmemo(object);
            } else if ( isCheckout) {
                paymentService.setQuote(object);
            }
            else {
                paymentService.setOrder(object);
            }

            let response = await paymentService.setPayment(payment).execute();
            if (response.errorMessage) {
                return {
                    error  : true,
                    message: response.errorMessage,
                    response
                };
            }

            return {
                error: false,
                response
            };
        }

        return {
            error: false
        };

    }

    /**
     * process Payment
     * @param object
     * @return {Promise<{order_increment_id: string|*}>}
     */
    async processPayment(object) {

        let promises = [];
        object.payments.forEach(payment => {
            let process = Promise.resolve({});

            if (
                !payment.reference_number
                && !payment.is_paid
                && PaymentHelper.hasUsingCreditCardForm(payment.method)
            ) {
                let paymentService = PaymentFactory.createByCode(payment.method);

                // if order => set order
                if (object.increment_id) {
                    paymentService.setOrder(object);
                } else {
                    paymentService.setQuote(object);
                }

                process = paymentService.setPayment(payment).execute();
            }
            promises.push(process)
        });

        let responses       = await Promise.all(promises);
        let errors          = [];
        let processPayments = {};

        responses.forEach((response, index) => {
            processPayments[object.payments[index].method + index] = response;
            response.errorMessage && errors.push(response.errorMessage);
        });

        if (errors.length) {
            return {
                error  : true,
                message: errors.join(', '),
                processPayments
            };
        }

        return {
            error: false,
            processPayments
        };

    }

    /**
     * add and check payments
     * @param quote
     * @param payments
     * @param payments_selected
     * @param isUpdate
     * @return {*}
     */
    addAndCheckPayments(quote, payments, payments_selected, isUpdate = false) {
        // check store credit
        let payment_select_store_credit = payments_selected.find(
            (payment) => payment.method === PaymentConstant.STORE_CREDIT
        );
        let payment_store_credit        = payments.find((payment) => payment.code === PaymentConstant.STORE_CREDIT);
        if (payment_store_credit || payment_select_store_credit) {
            if (!isUpdate) {
                return payments;
            } else {
                payments = payments.filter(payment => payment.code !== PaymentConstant.STORE_CREDIT);
            }
        }
        let customer = quote.customer;
        return StoreCreditService.checkAndAddStoreCreditToListPayment(customer, payments);
    }

    /**
     * recalculate payment data in quote if has change before place order
     * @param quote
     */
    recalculatePaymentDataInQuote(quote) {
        let totalPaid = 0;
        quote.payments.forEach(payment =>
            totalPaid = NumberHelper.addNumber(totalPaid, payment.amount_paid, payment.amount_change)
        );

        let changeAmount = NumberHelper.minusNumber(totalPaid, quote.grand_total);
        changeAmount = changeAmount > 0 ? changeAmount : 0;
        let totalChangeAmount = changeAmount;
        if (!changeAmount) {
            return;
        }

        let newCashPayments = quote.payments.filter(payment =>
            payment.method === PaymentConstant.CASH && !payment.is_paid
        );
        newCashPayments = _.orderBy(newCashPayments, 'amount_paid', 'desc');

        newCashPayments.forEach(payment => {
            if (changeAmount > 0) {
                let newAmountPaid = NumberHelper.minusNumber(payment.amount_paid, changeAmount);
                newAmountPaid = newAmountPaid > 0 ? newAmountPaid : 0;
                let newAmountChange = NumberHelper.minusNumber(payment.amount_paid, newAmountPaid);

                payment.amount_paid = CurrencyHelper.roundToFloat(newAmountPaid);
                payment.base_amount_paid = CurrencyHelper.convertAndRoundFloatToBase(newAmountPaid);

                changeAmount = NumberHelper.minusNumber(changeAmount, newAmountChange);
            }
            return null;
        });

        // Save total amount change to cash payment with amount paid > 0
        let cashPayment = newCashPayments.find(payment => payment.amount_paid > 0);
        if (cashPayment) {
            cashPayment.amount_change = CurrencyHelper.roundToFloat(totalChangeAmount);
            cashPayment.base_amount_change = CurrencyHelper.convertAndRoundFloatToBase(totalChangeAmount);
        }

        // Remove zero amount payment
        quote.payments = quote.payments.filter(payment => {
            return !(
                !payment.is_paid
                && payment.amount_paid === 0
                && payment.method === PaymentConstant.CASH
            );
        });
    }

    /**
     * recalculate payment data in order if has change before take payment
     * @param order
     */
    recalculatePaymentDataInOrder(order) {
        let totalPaid = 0;
        order.payments.forEach(payment =>
            totalPaid = NumberHelper.addNumber(totalPaid, payment.amount_paid, payment.amount_change)
        );

        let changeAmount = NumberHelper.minusNumber(totalPaid, order.grand_total);
        changeAmount = changeAmount > 0 ? changeAmount : 0;
        let totalChangeAmount = changeAmount;
        if (!changeAmount) {
            return;
        }

        let newCashPayments = order.payments.filter(payment =>
            payment.method === PaymentConstant.CASH && !payment.is_paid
        );
        newCashPayments = _.orderBy(newCashPayments, 'amount_paid', 'desc');

        newCashPayments.forEach(payment => {
            if (changeAmount > 0) {
                let newAmountPaid = NumberHelper.minusNumber(payment.amount_paid, changeAmount);
                newAmountPaid = newAmountPaid > 0 ? newAmountPaid : 0;
                let newAmountChange = NumberHelper.minusNumber(payment.amount_paid, newAmountPaid);

                payment.amount_paid = CurrencyHelper.roundToFloat(newAmountPaid);
                payment.base_amount_paid = OrderHelper.convertAndRoundToBase(newAmountPaid, order);

                changeAmount = NumberHelper.minusNumber(changeAmount, newAmountChange);
            }
            return null;
        });

        // Save total amount change to cash payment with amount paid > 0
        let cashPayment = newCashPayments.find(payment => payment.amount_paid > 0);
        if (cashPayment) {
            cashPayment.amount_change = CurrencyHelper.roundToFloat(totalChangeAmount);
            cashPayment.base_amount_change = OrderHelper.convertAndRoundToBase(totalChangeAmount, order);
        }

        // Remove zero amount payment
        order.payments = order.payments.filter(payment => {
            return !(
                !payment.is_paid
                && payment.amount_paid === 0
                && payment.method === PaymentConstant.CASH
            );
        });
    }

    /**
     *  Update date from process payment service into order payment
     *
     * @param payment
     * @param response
     */
    updatePaymentFromSuccessResponse(payment, response) {
        if(response.reference_number) {
            payment.reference_number = response.reference_number;
        }
        if(response.cc_last4) {
            payment.cc_last4 = response.cc_last4;
        }
        if(response.card_type) {
            payment.card_type = response.card_type;
        }
        if(response.pos_paypal_invoice_id) {
            payment.pos_paypal_invoice_id = response.pos_paypal_invoice_id;
        }
        if(response.receipt) {
            payment.receipt = response.receipt;
        }
    }
}

/**
 *
 * @type {PaymentService}
 */
let paymentService = ServiceFactory.get(PaymentService);

export default paymentService;
