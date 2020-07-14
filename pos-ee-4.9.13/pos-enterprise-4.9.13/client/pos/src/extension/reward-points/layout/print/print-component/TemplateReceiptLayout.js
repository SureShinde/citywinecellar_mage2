import {RewardPointHelper} from "../../../helper/RewardPointHelper";
import CurrencyHelper from "../../../../../helper/CurrencyHelper";
import NumberHelper from "../../../../../helper/NumberHelper";

export default {
    discount_after: [
        /**
         * get template point discount from data order
         *
         * @param component
         * @param orderData
         * @returns {*}
         */
        function getTemplatePointDiscount(component, orderData) {
            if (RewardPointHelper.isEnabledRewardPoint() && orderData.extension_attributes.rewardpoints_discount) {
                let pointName = RewardPointHelper.getPointName();
                let valueDisplay = CurrencyHelper.format(orderData.extension_attributes.rewardpoints_discount, orderData.order_currency_code);
                return component.getTemplateTotal(
                    component.props.t('{{pointName}} Discount', {pointName}),
                    `-${valueDisplay}`,
                    "PointDiscount",
                    true
                );
            }
            return null;
        }
    ],
    grand_total_after: [
        /**
         * get template earned point from data order
         *
         * @param component
         * @param orderData
         * @returns {*}
         */
        function getTemplateEarnedPoint(component, orderData) {
            if (RewardPointHelper.isEnabledRewardPoint() && orderData.extension_attributes.rewardpoints_earn) {
                const pointName = RewardPointHelper.getPointName();
                const pluralOfPointName = RewardPointHelper.getPluralOfPointName();
                const {t} = component.props;
                let valueDisplay = orderData.extension_attributes.rewardpoints_earn;
                return component.getTemplateTotal(
                    t('Earned'),
                    t('{{point}} {{pointLabel}}', {
                        point: NumberHelper.formatDisplayGroupAndDecimalSeparator(valueDisplay),
                        pointLabel: valueDisplay > 1 ? pluralOfPointName : pointName
                    }),
                    "EarnedPoint",
                    true
                );
            }
            return null;
        },
        /**
         * get template spent point from data order
         *
         * @param component
         * @param orderData
         * @returns {*}
         */
        function getTemplateSpentPoint(component, orderData) {
            if (RewardPointHelper.isEnabledRewardPoint() && orderData.extension_attributes.rewardpoints_spent) {
                const pointName = RewardPointHelper.getPointName();
                const pluralOfPointName = RewardPointHelper.getPluralOfPointName();
                const {t} = component.props;
                let valueDisplay = orderData.extension_attributes.rewardpoints_spent;
                return component.getTemplateTotal(
                    t('Spent'),
                    t('{{point}} {{pointLabel}}', {
                        point: NumberHelper.formatDisplayGroupAndDecimalSeparator(valueDisplay),
                        pointLabel: valueDisplay > 1 ? pluralOfPointName : pointName
                    }),
                    "SpentPoint",
                    true
                );
            }
            return null;
        }
    ]
}
