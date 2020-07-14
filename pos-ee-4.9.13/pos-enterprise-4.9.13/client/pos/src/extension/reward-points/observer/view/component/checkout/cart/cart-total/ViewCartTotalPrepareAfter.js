import {listen} from "../../../../../../../../event-bus";
import {RewardPointHelper} from "../../../../../../helper/RewardPointHelper";
import NumberHelper from "../../../../../../../../helper/NumberHelper";

export default class ViewCartTotalPrepareAfter {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('view_cart_total_prepare_after', (eventData) => {
            let RewardPointService = require("../../../../../../service/RewardPointService").default;
            let self = eventData.cartTotal;
            const {quote, t} = self.props;
            let pointDiscount = -quote.rewardpoints_discount;

            if (RewardPointService.customerCanSpendPoint(quote.customer)) {
                const pointName = RewardPointHelper.getPointName();
                const usedPoint = RewardPointService.getUsedPoint();
                let title = t('{{pointName}} Discount', {pointName});

                if (usedPoint) {
                    title = t('{{pointName}} Discount ({{usedPoint}} {{pointName}})', {
                        pointName: usedPoint > 1
                            ? RewardPointHelper.getPluralOfPointName()
                            : RewardPointHelper.getPointName(),
                        usedPoint: NumberHelper.formatDisplayGroupAndDecimalSeparator(usedPoint)
                    });
                }

                self.addToTotals(
                    "spend_point",
                    title,
                    pointDiscount || 0,
                    ""
                );
            }
            if (
                quote.customer && (
                    RewardPointHelper.canEarnWhenSpend()
                    || (!quote.rewardpoints_spent && !RewardPointHelper.canEarnWhenSpend())
                )
            ) {
                self.addToTotals("earn_point", t('Customer will earn'),
                    quote.rewardpoints_earn || 0, ""
                );
            }

        }, 'ViewCartTotalPrepareAfter');
    }
}
