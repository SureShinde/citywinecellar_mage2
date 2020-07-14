import {listen} from "../../../../../event-bus";
import {RewardPointHelper} from "../../../helper/RewardPointHelper";
import StatusConstant from "../../../../../view/constant/order/StatusConstant";

export default class CheckoutServiceObserver {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('service_checkout_place_order_after', (eventData) => {
            /** in case use rwp */
            let {order, createInvoice} = eventData;
            if (
                typeof order.extension_attributes !== 'undefined' && order.extension_attributes
                && RewardPointHelper.isEnabledRewardPoint()
                && order.customer_id
            ) {
                let RewardPointCustomerService = require("../../../service/customer/RewardPointCustomerService").default;
                let point =  0 - order.extension_attributes.rewardpoints_spent;
                if (!RewardPointHelper.holdPointDay()) {
                    if (
                        (RewardPointHelper.allowReceivingPointsWhenInvoiceIsCreated() && createInvoice)
                        || (!RewardPointHelper.allowReceivingPointsWhenInvoiceIsCreated() && order.state === StatusConstant.STATE_COMPLETE)
                    ) {
                        point += order.extension_attributes.rewardpoints_earn;
                    }
                }
                RewardPointCustomerService.rewardCustomerWithPoint(
                    order.customer_id,
                    point
                );
            }
        }, 'rewardpoints');
    }
}
