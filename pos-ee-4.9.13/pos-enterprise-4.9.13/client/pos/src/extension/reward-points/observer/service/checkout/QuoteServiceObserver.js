import {listen} from "../../../../../event-bus";

export default class OrderServiceObserver {
  /**
   * constructor
   * @param props
   */
  constructor() {
    let removePoint = () => {
      let RewardPointService = require("../../../service/RewardPointService").default;
      RewardPointService.getUsedPoint() && RewardPointService.removeUsedPoint();
    };
    listen('service_quote_add_product_after', removePoint, 'rewardpoints');
    listen('service_quote_update_qty_cart_item_after', removePoint, 'rewardpoints');
    listen('service_quote_update_custom_price_cart_item_after', removePoint, 'rewardpoints');
    listen('service_quote_remove_cart_item_after', removePoint, 'rewardpoints');
  }
}
