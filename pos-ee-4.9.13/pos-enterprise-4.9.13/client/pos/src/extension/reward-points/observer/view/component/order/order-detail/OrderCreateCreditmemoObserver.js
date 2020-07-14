import {listen} from "../../../../../../../event-bus";
import CreateCreditmemoConstant from "../../../../../view/constant/order/creditmemo/CreateCreditmemoConstant";

export default class OrderCreateCreditmemoObserver {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('component_order_create_creditmemo_reset_adjustments_after', (eventData) => {
            let RewardPointService = require("../../../../../service/RewardPointService").default;
            let {adjustments, creditmemo, order} = eventData;
            let {maxReturnSpend, maxAdjustmentEarned} =
                RewardPointService.getMaxReturnSpendAndMaxAdjustmentEarned(
                    creditmemo,
                    order
                );

            adjustments[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY] = maxAdjustmentEarned;
            adjustments[CreateCreditmemoConstant.RETURN_SPENT_KEY] = maxReturnSpend;
        }, 'rewardpoints');
    }
}
