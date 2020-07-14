import { listen } from "../../../../../../event-bus";
import StripeTerminalConstant from "../../../../view/constant/payment/StripeTerminalConstant";

export default class CreditmemoServiceObserver {
    /**
     *
     */
    constructor() {
        listen('service_creditmemo_create_creditmemo_before_add_comment', (payload) => {
            if (!payload.payment) {
                return;
            }

            if (payload.payment.method !== StripeTerminalConstant.CODE) {
                return;
            }

            payload.comment = `[Request] ${payload.comment}`
        })
    }
}