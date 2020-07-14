import * as _ from 'lodash';

export default function(eventData) {
  let {oldCustomer, quote} = eventData;
  let RewardPointService = require("../../service/RewardPointService").default;
  // check and remove used reward point
  if (!_.isEqual(oldCustomer,quote.customer)) {
    RewardPointService.removeUsedPoint();
  }
}
