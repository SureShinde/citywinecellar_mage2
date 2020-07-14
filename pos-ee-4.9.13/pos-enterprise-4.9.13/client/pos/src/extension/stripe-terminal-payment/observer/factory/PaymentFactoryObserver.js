import { listen } from "../../../../event-bus";
import StripeTerminalConstant from "../../view/constant/payment/StripeTerminalConstant";

export default class PaymentFactoryObserver {
    /**
     *
     */
    constructor() {
        listen('factory_payment_factory_init_after', ({ factory }) => {
            if (!factory) {
                return;
            }
            const {StripeTerminalService} = require("../../service/payment/type/StripeTerminalService");
            factory.map[StripeTerminalConstant.CODE] = StripeTerminalService;
        })
    }
}