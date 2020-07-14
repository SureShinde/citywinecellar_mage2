import {RewardPointHelper} from "../helper/RewardPointHelper";
import NumberHelper from "../../../helper/NumberHelper";

export default {
    customer_button_after: [
        function (component) {
            const {customer} = component.props.quote;
            const pointName = RewardPointHelper.getPointName();
            const pluralOfPointName = RewardPointHelper.getPluralOfPointName();

            if (customer && customer.id && customer.extension_attributes && customer.extension_attributes.point_balance) {
                return component.props.t('({{point}} {{pointLabel}})', {
                    point: NumberHelper.formatDisplayGroupAndDecimalSeparator(customer.extension_attributes.point_balance),
                    pointLabel: customer.extension_attributes.point_balance > 1
                        ? pluralOfPointName
                        : pointName
                });
            }
            return '';
        }
    ]
}
