import StripeTerminalConstant from "../../../../view/constant/payment/StripeTerminalConstant";

export default {
    getTemplatePayment: {
        changeOnGetTemplatePayment: {
            sortOrder: 1,
            disabled: false,
            /**
             *
             * @param proceed
             * @param payment
             * @param index
             * @param order
             * @param titlePrefix
             * @return {*}
             */
            around: function (proceed, payment, index, order, titlePrefix = '') {
                if (
                    payment.method !== StripeTerminalConstant.CODE
                ) {
                   return proceed(payment, index, order, titlePrefix);
                }
                let StripeTerminalService = require("../../../../service/payment/type/StripeTerminalService").default;
                let CurrencyHelper = require("../../../../../../helper/CurrencyHelper").default;
                let paymentAmount = CurrencyHelper.format(payment.amount_paid, order.order_currency_code);

                if (!this.props.creditmemo) {
                    let title = StripeTerminalService.getOrderPaymentDetail(payment, titlePrefix);
                    const { reference_number } = payment;
                    title += ` ${reference_number}`;

                    return this.getTemplateTotal(
                        title,
                        paymentAmount,
                        index.toString(), false, true
                    );
                }

                const { creditmemo } = this.props;

                let parentPayment = creditmemo.order.payments.find(orderPayment => {
                    return payment.parent_increment_id === orderPayment.increment_id;
                });

                if (!parentPayment) {
                    return proceed(payment, index, order, titlePrefix);
                }

                let title = StripeTerminalService.getOrderPaymentDetail(parentPayment, titlePrefix);

                return this.getTemplateTotal(
                    title,
                    paymentAmount,
                    index.toString(), false, true
                );
            }
        }
    },
}