/**
 * Plugin to update label
 */
import PaymentConstant from "../../../../../view/constant/PaymentConstant";
import PaymentHelper from "../../../../../helper/PaymentHelper";
import CurrencyHelper from "../../../../../helper/CurrencyHelper";

export default {
    /**
     * Print pay later in receipt
     * @param order
     * @returns {*}
     */
    getTemplateDuePayments: function (order) {
        let payments = order.payments;
        let totalDue = CurrencyHelper.roundToFloat(order.total_due, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
        if (payments && totalDue) {
            return (
                payments.map((payment, index) => {
                    if (!payment.amount_paid || payment.amount_paid === 0) {
                        if (payment.type !== PaymentConstant.TYPE_REFUND) {
                            return this.getTemplateDuePayment(payment, index, order);
                        }
                    }
                    return null;
                })
            )
        }
    },
    /**
     * Print pay later (label is Pay Later)
     * @param payment
     * @param index
     * @param order
     * @returns {template|*}
     */
    getTemplateDuePayment: function(payment, index, order) {
        let paymentAmount = this.props.t('Pay Later');
        let title = payment.title;
        let referenceNumber = payment.reference_number;
        if (PaymentHelper.isPaypalDirect(payment.method) && !referenceNumber) {
            title = PaymentHelper.paypalPayViaEmailTitle();
        }

        if (referenceNumber) {
            title = `${title} (${referenceNumber}`;
            if (payment.card_type) {
                title += ` - ${payment.card_type.toUpperCase()}`;
            }
            title += ')';
        } else if (payment.card_type) {
            title = `${title} (${payment.card_type})`;
        }

        return this.getTemplateTotal(
            title,
            paymentAmount,
            index.toString(), false, true
        );
    }
};
