
export default {
    /**
     * Plugin to add payment offline (without pay later) to take payment
     */
    template: {
        payment_offline: {
            sortOrder: 100,
            disabled: false,
            before: function () {
                let payments = this.state.items;
                let take_payments = [];
                payments.forEach(function(payment) {
                    if (!payment.is_pay_later) {
                        take_payments.push(payment);
                    }
                });
                this.state.items = take_payments;
            },
        }
    }
};
