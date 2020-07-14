import SettingListConstant from "../../../../../view/constant/settings/SettingListConstant";

export default {
    componentWillMount: {
        changeComponentWillMount: {
            sortOrder: 1,
            disabled: false,
            before: function () {
                let paymentSettingItem = this.items.find(item => item.title === SettingListConstant.GET_SETTING_PAYMENT);
                if (paymentSettingItem) {
                    return;
                }
                let StripeTerminalService = require("../../../service/payment/type/StripeTerminalService").default;
                if (!StripeTerminalService.isEnable()) {
                    return;
                }

                let PaymentDetail = require("../../../../../view/component/settings/settings/PaymentDetail").default;
                this.items.push({
                    "id": "Payment",
                    "title": SettingListConstant.GET_SETTING_PAYMENT,
                    "name" : "Payment",
                    "component": PaymentDetail,
                    "className": "li-payment",
                },)
            }
        }
    },
}