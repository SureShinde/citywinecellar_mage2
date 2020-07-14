import {listen} from "../../../../../event-bus";
import {RewardPointHelper} from "../../../helper/RewardPointHelper";

export default class OrderServiceObserver {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('service_order_can_creditmemo_middle', (eventData) => {
            /** in case use rwp */
            let {order} = eventData;
            if (order.extension_attributes.rewardpoints_spent && RewardPointHelper.isEnabledRewardPoint()) {
                let canRefundPoint = order.items.some(item => {
                    if (item.parent_item_id) return false;
                    if (!item.extension_attributes.rewardpoints_spent) return false;
                    return (item.qty_invoiced - item.qty_refunded - item.qty_canceled) > 0;
                });

                if (canRefundPoint) eventData.forceCanCreditmemo = canRefundPoint;
            }
        }, 'rewardpoints');
    }
}
