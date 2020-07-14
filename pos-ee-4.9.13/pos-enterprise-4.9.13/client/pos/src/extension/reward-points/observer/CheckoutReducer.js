import ActionsConstant from "../view/constant/actions"

export default function(eventData) {
  let {state, action} = eventData;
  if (action.type === ActionsConstant.CHECKOUT_TO_SPEND_REWARD_POINT) {
    let pages = state.pages;
    let SpendRewardPoint = require('../view/component/checkout/SpendRewardPoint').default;
    if (pages.indexOf(SpendRewardPoint) === -1) {
      pages.push(SpendRewardPoint);
    }
    eventData.state = {...state, pages: pages, currentPage: SpendRewardPoint.className};
  }
}
