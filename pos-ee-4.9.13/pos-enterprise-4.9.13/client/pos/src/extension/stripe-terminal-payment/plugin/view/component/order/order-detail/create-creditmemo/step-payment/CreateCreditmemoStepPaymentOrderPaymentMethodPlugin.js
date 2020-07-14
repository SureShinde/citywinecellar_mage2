import React from "react";
import StripeTerminalConstant from "../../../../../../../view/constant/payment/StripeTerminalConstant";

export default {
    getPaymentDescription: {
        changeGetPaymentDescription: {
            sortOrder: 1,
            disabled: false,
            /**
             *  Change payment detail for stripe terminal
             *
             * @param proceed
             * @param payment
             * @return {*}
             */
            around: function (proceed, payment) {
                if (
                    payment.method === StripeTerminalConstant.CODE
                    || payment.code === StripeTerminalConstant.CODE
                    ) {
                    const StripeTerminalService = require('../../../../../../../service/payment/type/StripeTerminalService').default;
                    return <span className="des">{StripeTerminalService.getOrderPaymentDetail(payment, '', true)}</span>
                }

                return proceed(payment);
            }
        }
    },
}