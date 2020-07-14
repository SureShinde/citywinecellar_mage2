import React, {Fragment} from "react";
import StripeTerminalConstant from "../../../../../../view/constant/payment/StripeTerminalConstant";
import moment from "moment";

/**
 *  Get Date String from order payment
 *
 * @param payment
 * @return {string}
 */
function getPaymentDate(payment) {
    if (!payment.payment_date) {
        return '';
    }
    const DateTimeHelper = require('../../../../../../../../helper/DateTimeHelper').default;
    const formatted = moment(
        DateTimeHelper.convertDatabaseDateTimeToLocalDate(payment.payment_date)).format('L')
    ;
    return `(${formatted})`
}

export default {
    template: {
        changeOnTemplate: {
            sortOrder: 1,
            disabled: false,
            /**
             *  Change payment detail for stripe terminal
             *
             * @param proceed
             * @return {*}
             */
            around: function (proceed) {
                let {payment, order} = this.props;

                if (payment.method !== StripeTerminalConstant.CODE) {
                    return proceed();
                }

                let StripeTerminalService = require("../../../../../../service/payment/type/StripeTerminalService").default;
                let OrderHelper = require("../../../../../../../../helper/OrderHelper").default;

                let {title, card_type, cc_last4, reference_number} = payment;
                let date = getPaymentDate(payment);
                let totalAmount = OrderHelper.formatPrice(payment.amount_paid, order);
                let hasCardInformation = card_type || cc_last4;
                let paymentCardInformation = StripeTerminalService.getOrderPaymentDetail(payment, '', true, false);

                return (
                    <Fragment>
                        <li>
                            <div className="title">{ title } <span>{ date }</span></div>
                            <div className="value">
                                {totalAmount}
                            </div>
                        </li>
                        {
                            hasCardInformation ? (
                                <li>
                                    <div className="value">{paymentCardInformation}</div>
                                </li>
                            ): null
                        }
                        <li>
                            <div className="value">{reference_number}</div>
                        </li>
                    </Fragment>
                );
            }
        }
    },
}