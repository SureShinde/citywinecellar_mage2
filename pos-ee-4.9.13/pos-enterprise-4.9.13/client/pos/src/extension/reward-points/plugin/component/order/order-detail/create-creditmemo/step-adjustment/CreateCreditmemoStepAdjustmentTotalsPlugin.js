import NumberHelper from "../../../../../../../../helper/NumberHelper";

export default {
    getDiscountAmount: {
        reward_points: {
            sortOrder: 10,
            disabled: false,
            after: function(result, creditmemo) {
                let rewardpointAmount = creditmemo.extension_attributes.rewardpoints_discount || 0;
                return NumberHelper.addNumber(result, rewardpointAmount);
            }
        }
    }
}
