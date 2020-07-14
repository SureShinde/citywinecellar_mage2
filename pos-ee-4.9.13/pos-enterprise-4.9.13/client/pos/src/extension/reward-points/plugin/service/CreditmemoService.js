import {RewardPointHelper} from "../../helper/RewardPointHelper";
/**
 * Plugin to process when creating creditmemo
 */
export default {
  createCreditmemo: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      after: function(creditmemo) {
        if (
          creditmemo.order
            && creditmemo.order.extension_attributes
            && creditmemo.order.extension_attributes.rewardpoints_spent
            && RewardPointHelper.isEnabledRewardPoint()
        ) {
          creditmemo.allow_zero_grand_total = true;
        }
        return creditmemo;
      }
    }
  },
};
