import { listen } from "../../../../../../../event-bus";
import StripeTerminalConstant from "../../../../../view/constant/payment/StripeTerminalConstant";
import i18n from "../../../../../../../config/i18n";

export default class RefundOperationServiceObserver {
    /**
     *
     */
    constructor() {
        listen('service_refund_operation_refund_before_add_comment', (payload) => {
            if (!payload.creditmemo) {
                return;
            }

            if (!payload.creditmemo.payments || !payload.creditmemo.payments.length) {
                return;
            }

            let {creditmemo} = payload;
            let grandTotal = creditmemo.grand_total;

            creditmemo.payments.forEach(payment => {
                if (payment.method !== StripeTerminalConstant.CODE) {
                    return;
                }
                grandTotal -= payment.amount_paid;
            });

            if (!grandTotal) {
                payload.comment = false;
                return;
            }

            let CurrencyHelper = require("../../../../../../../helper/CurrencyHelper").default;

            payload.comment =  i18n.translator.translate(
                "We refunded {{amount}} offline.",
                {amount: CurrencyHelper.format(creditmemo.grand_total, creditmemo.base_currency_code, null)}
            );
        })
    }
}