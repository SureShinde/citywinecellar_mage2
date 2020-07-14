import RewardPointCustomerService from "../../../service/customer/RewardPointCustomerService";
import CreditmemoConstant from "../../../../../view/constant/order/CreditmemoConstant";
import toInteger from "lodash/toInteger";
import CreateCreditmemoConstant from "../../../view/constant/order/creditmemo/CreateCreditmemoConstant";
import {RewardPointHelper} from "../../../helper/RewardPointHelper";
import Action from "../../../../../view/action";

/**
 * Receive action CREATE_CREDITMEMO_AFTER and request
 * @param action$
 */
export default function rewardPointUpdateLoyaltyEpic(action$) {
    return action$.ofType(CreditmemoConstant.CREATE_CREDITMEMO_AFTER)
        .mergeMap(async action => {
            if (!RewardPointHelper.isEnabledRewardPoint()) {
                return Action.empty();
            }

            /** reward point  */
            const creditmemo = action.creditmemo;
            const {order}      = creditmemo;

            if (order.customer_is_guest || !order.customer_id) {
                return Action.empty();
            }
            let adjustBalance = -(creditmemo.extension_attributes[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY] || 0)
                + toInteger(creditmemo.extension_attributes[CreateCreditmemoConstant.RETURN_SPENT_KEY] || 0);
            RewardPointCustomerService.rewardCustomerWithPoint(
                order.customer_id,
                adjustBalance
            );

            return Action.empty();
        });
}
