import CreateCreditmemoConstant from "../../../../view/constant/order/creditmemo/CreateCreditmemoConstant";

export default {
    convertOrderDataToCreditmemo: {
        reward_points: {
            sortOrder: 10,
            disabled: false,
            after: function(result, creditmemo, order) {
                if(typeof result.extension_attributes === 'undefined') {
                    result.extension_attributes = {};
                }
                result.extension_attributes[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY]
                    = order[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY];
                result.extension_attributes[CreateCreditmemoConstant.RETURN_SPENT_KEY]
                    = order[CreateCreditmemoConstant.RETURN_SPENT_KEY];
                result.extension_attributes[CreateCreditmemoConstant.REWARDPOINTS_EARN]
                    = result.extension_attributes[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY];
                return result;
            }
        }
    },
    initData: {
        reward_points: {
            sortOrder: 10,
            disabled: false,
            after: function(result, creditmemo, data) {
                if(typeof creditmemo.extension_attributes === 'undefined') {
                    creditmemo.extension_attributes = {};
                }
                if(typeof data.extension_attributes !== 'undefined') {
                    if (typeof data.extension_attributes[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY] !== 'undefined') {
                        creditmemo.extension_attributes[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY] =
                            data.extension_attributes[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY];
                        creditmemo.extension_attributes[CreateCreditmemoConstant.REWARDPOINTS_EARN] =
                            creditmemo.extension_attributes[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY];
                    }
                    if (typeof data.extension_attributes[CreateCreditmemoConstant.RETURN_SPENT_KEY] !== 'undefined') {
                        creditmemo.extension_attributes[CreateCreditmemoConstant.RETURN_SPENT_KEY] =
                            data.extension_attributes[CreateCreditmemoConstant.RETURN_SPENT_KEY];
                    }
                }
            }
        }
    },
}
