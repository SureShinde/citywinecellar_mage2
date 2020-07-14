import StripeTerminalConstant from "../../../../view/constant/payment/StripeTerminalConstant";

export default {
    template: {
        changeOnTemplate: {
            sortOrder: 1,
            disabled: false,
            before: function () {
                let paymentSettingItem = this.items.find(item => item.id === StripeTerminalConstant.TITLE);
                if (paymentSettingItem) {
                    return;
                }
                let StripeTerminalService = require("../../../../service/payment/type/StripeTerminalService").default;
                let StripeTerminalSetting = require("../../../../view/component/settings/settings/payments/StripeTerminalSetting").default;

                this.items.push( {
                    "id"       : StripeTerminalConstant.TITLE,
                    "title"    : StripeTerminalConstant.SETTING_PAYMENT_STRIPE_TERMINAL,
                    "name"     : StripeTerminalConstant.TITLE,
                    "component": StripeTerminalSetting,
                    "visible"  : StripeTerminalService.isEnable()
                },)
            }
        }
    },
}