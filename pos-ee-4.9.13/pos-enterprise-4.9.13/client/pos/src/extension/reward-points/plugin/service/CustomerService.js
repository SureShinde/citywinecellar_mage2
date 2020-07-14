import {RewardPointHelper} from "../../helper/RewardPointHelper";

/**
 * Plugin for customer service
 */
export default {
  needUpdateData: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      after: function(result) {
        return result || RewardPointHelper.isEnabledRewardPoint();
      }
    }
  },
};
