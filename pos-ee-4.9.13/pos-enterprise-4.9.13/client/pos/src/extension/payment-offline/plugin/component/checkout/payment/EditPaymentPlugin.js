/**
 * Plugin
 */
export default {
    /**
     * plugin to set amount = 0 when payment is pay later
     */
    calculateAmountPaid: {
        payment_offline: {
            sortOrder: 100,
            disabled: false,
            after: function (result, quote, paymentMethod) {
                if (paymentMethod.is_pay_later) {
                    result = 0;
                }
                return result;
            },
        }
    },
    /**
     * plugin to skip step accept amount when payment is pay later and has not reference number
     */
    preparePaymentData: {
        payment_offline: {
            sortOrder: 100,
            disabled: false,
            after: function (result, quote) {
                let {paymentMethod} = this.props;
                if (paymentMethod.is_pay_later) {
                    if (!paymentMethod.is_reference_number) {
                        this.handlePaymentAmount();
                        return false;
                    }
                }
                return result;
            },
        }
    }
};
