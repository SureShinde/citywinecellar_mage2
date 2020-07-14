import React, {Fragment} from "react";
import StripeTerminalConstant from "../../../../../../../view/constant/payment/StripeTerminalConstant";

/**
 *
 * @param payment
 * @return {*}
 */
const getAdditionalInfo = (payment) => {
    let {card_type, cc_last4} = payment.orderPayment;

    let cardInformation = '';

    if (card_type || cc_last4) {
        const cardInformationItems = [cc_last4, card_type].filter(data => data);
        cardInformation = `(${cardInformationItems.join(' - ')})`
    }

    return (
        <span className="additional-info">
            {cardInformation}
        </span>
    )
};

/**
 *
 * @param payment
 * @param order
 * @param translator
 * @return {*}
 */
const getPaidInfo = (payment, order, translator) => {
    const OrderHelper = require("../../../../../../../../../helper/OrderHelper").default;

    return (
        <span className="additional-info">
            {translator('Paid')}:&nbsp;
            {OrderHelper.formatPrice(payment.orderPayment.amount_paid, order)}
        </span>
    )
};

/**
 *
 * @param payment
 * @param order
 * @param translator
 * @return {*}
 */
const getRefundedInfo = (payment, order, translator) => {
    const OrderHelper = require("../../../../../../../../../helper/OrderHelper").default;

    return (
        <span className="additional-info">
            {translator('Refunded')}:&nbsp;
            {OrderHelper.formatPrice(payment.amount_refunded, order)}
        </span>
    )
};

export default {
    getLabelSelectedPaymentRefundContent: {
        changeGetLabelSelectedPaymentRefundContent: {
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
                if (payment.method !== StripeTerminalConstant.CODE) {
                    return proceed(payment);
                }

                const { order, t } = this.props;

                return (
                    <span className="label">
                        {payment.orderPayment
                            ? (
                                <Fragment>
                                    <span className="title">
                                        {t(payment.title)}
                                    </span>
                                    {getAdditionalInfo(payment)}
                                    {getPaidInfo(payment, order, t)}
                                    {getRefundedInfo(payment, order, t)}
                                </Fragment>
                            )
                            : t(payment.title)
                        }
                    </span>
                )
            }
        }
    },
    getPaymentDescriptionContent: {
        changeGetPaymentDescriptionContent: {
            sortOrder: 1,
            disabled: false,
            /**
             *  Change payment detail for stripe terminal
             *
             * @param proceed
             * @param payment
             * @param amountRefunded
             * @return {*}
             */
            around: function (proceed, payment, amountRefunded) {
                if (
                    payment.code === StripeTerminalConstant.CODE
                    || payment.method === StripeTerminalConstant.CODE
                ) {
                    const { t, order } = this.props;

                    return (
                        <Fragment>
                            {getAdditionalInfo(payment)}
                            {getPaidInfo(payment, order, t)}
                            {getRefundedInfo(payment, order, t)}
                        </Fragment>

                    )
                }

                return proceed(payment, amountRefunded);
            }
        }
    },
}