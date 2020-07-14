import {listen} from "../../../../../../event-bus";
import {RewardPointHelper} from "../../../../helper/RewardPointHelper";
import StatusConstant from "../../../../../../view/constant/order/StatusConstant";

export default class TakePaymentServiceObserver {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('service_take_payment_take_payment_after', (eventData) => {
            /** in case use rwp */
            let {order, createInvoice} = eventData;
            if (
                typeof order.extension_attributes !== 'undefined' && order.extension_attributes
                && RewardPointHelper.isEnabledRewardPoint()
                && order.customer_id
                && !RewardPointHelper.holdPointDay()
            ) {
                let CustomerService = require("../../../../service/customer/RewardPointCustomerService").default;
                let point =  0;
                if (
                    (RewardPointHelper.allowReceivingPointsWhenInvoiceIsCreated() && createInvoice)
                    || (!RewardPointHelper.allowReceivingPointsWhenInvoiceIsCreated() && order.state === StatusConstant.STATE_COMPLETE)
                ) {
                    point += order.extension_attributes.rewardpoints_earn;
                }
                CustomerService.rewardCustomerWithPoint(
                    order.customer_id,
                    point
                );
            }
        }, 'rewardpoints');
    }
}
