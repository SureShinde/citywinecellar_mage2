import {listen} from "../../../../../../../event-bus";
import {RewardPointHelper} from "../../../../../helper/RewardPointHelper";
import * as _ from "lodash";

export default class RefundOperationServiceObserver {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('service_creditmemo_refund_operation_refund_after', (eventData) => {
            let {creditmemo, order} = eventData;
            /** reward point */
            if (RewardPointHelper.isEnabledRewardPoint()) {
                order.creditmemo_rewardpoints_earn = _.toNumber(order.extension_attributes.creditmemo_rewardpoints_earn || 0)
                    + _.toNumber(creditmemo.extension_attributes.rewardpoints_earn || 0);
                order.creditmemo_rewardpoints_discount = _.toNumber(order.extension_attributes.creditmemo_rewardpoints_discount || 0)
                    + _.toNumber(creditmemo.extension_attributes.rewardpoints_earn || 0);

                order.creditmemo_rewardpoints_base_discount = _.toNumber(order.extension_attributes.creditmemo_rewardpoints_base_discount || 0)
                    + _.toNumber(creditmemo.extension_attributes.rewardpoints_earn || 0);
            }
        }, 'rewardpoints');
    }
}
