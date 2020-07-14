/**
 * Plugin to disable edit payment amount when payment is pay later
 */
export default {
    editPayment: {
        payment_offline: {
            sortOrder: 100,
            disabled: false,
            around: function (proceed, paymentData) {
                if (paymentData.is_pay_later) {
                    if (!paymentData.is_reference_number) {
                        return false;
                    }
                }
                this.props.selectPayment(paymentData, this.state.remain);
            },
        }
    }
};
