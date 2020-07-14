import * as _ from "lodash";

export default {
    getDiscountAmount: {
        reward_points: {
            sortOrder: 10,
            disabled: false,
            after: function(result, order) {
                let pointDiscount = order && order.extension_attributes && order.extension_attributes.rewardpoints_discount ?
                    _.toNumber(order.extension_attributes.rewardpoints_discount) : 0;
                return result - pointDiscount;
            }
        }
    }
}
