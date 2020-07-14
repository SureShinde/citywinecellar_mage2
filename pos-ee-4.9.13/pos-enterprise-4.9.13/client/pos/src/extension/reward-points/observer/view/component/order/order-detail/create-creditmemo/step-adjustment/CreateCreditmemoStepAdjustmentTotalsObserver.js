import {listen} from "../../../../../../../../../event-bus";

export default class CreateCreditmemoStepAdjustmentTotalsObserver {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('view_create_creditmemo_step_adjustment_totals_prepare_totals_after', (eventData) => {
            let OrderHelper = require("../../../../../../../../../helper/OrderHelper").default;
            let {totals, order, creditmemo, t} = eventData;
            let rewardpointAmount = creditmemo.extension_attributes.rewardpoints_discount;
            if(rewardpointAmount){
                totals.push({
                    key: 'rewardpoints_amount',
                    label: t('Reward point'),
                    value: '-' + OrderHelper.formatPrice(rewardpointAmount, order),
                    sort_order: 55,
                });
            }
        }, 'rewardpoints');
    }
}
