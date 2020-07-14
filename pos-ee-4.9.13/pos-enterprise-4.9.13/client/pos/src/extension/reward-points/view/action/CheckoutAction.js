import ActionsConstant from '../constant/actions';

export default {
    /**
     *  whenever user click point discount
     * @param quote
     * @return {{type: string, quote: *}}
     */
    checkoutToSpendRewardPoint: (quote) => {
        return {
            type: ActionsConstant.CHECKOUT_TO_SPEND_REWARD_POINT,
            quote
        }
    },
}
