import {RewardPointHelper} from "../../helper/RewardPointHelper";
import * as _ from "lodash";
import React from "react";
import NumberHelper from "../../../../helper/NumberHelper";

export default {
    total_discount_after: [
        function displayPointDiscount(component) {
            let OrderHelper = require("../../../../helper/OrderHelper").default;
            let isEnabledRewardPoint = RewardPointHelper.isEnabledRewardPoint();
            let pointName = RewardPointHelper.getPointName();
            let {order, t} = component.props;
            let pointDiscount = order && order.extension_attributes && order.extension_attributes.rewardpoints_discount ? _.toNumber(order.extension_attributes.rewardpoints_discount) : 0;

            return isEnabledRewardPoint && pointDiscount ?
                <li key='point-discount'>
                    <span className="title">{t('{{pointName}} Discount', {pointName})}</span>
                    <span className="value">-{OrderHelper.formatPrice(pointDiscount, order)}</span>
                </li>
                : '';
        }
    ],
    grand_total_after: [
        function displayEarnedPoint(component) {
            let isEnabledRewardPoint = RewardPointHelper.isEnabledRewardPoint();
            let pointName = RewardPointHelper.getPointName();
            let pluralOfPointName = RewardPointHelper.getPluralOfPointName();
            let {order, t} = component.props;

            return isEnabledRewardPoint && order.extension_attributes && order.extension_attributes.rewardpoints_earn ?
                <li key='earned-point'>
                    <span className="title">{t('Earned')}</span>
                    <span className="value">{
                        t('{{point}} {{pointLabel}}', {
                            point: NumberHelper.formatDisplayGroupAndDecimalSeparator(order.extension_attributes.rewardpoints_earn),
                            pointLabel: order.extension_attributes.rewardpoints_earn > 1
                                ? pluralOfPointName
                                : pointName
                        })
                    }</span>
                </li>
                : '';
        },
        function displaySpentPoint(component) {
            let isEnabledRewardPoint = RewardPointHelper.isEnabledRewardPoint();
            let pointName = RewardPointHelper.getPointName();
            let pluralOfPointName = RewardPointHelper.getPluralOfPointName();
            let {order, t} = component.props;

            return isEnabledRewardPoint && order.extension_attributes && order.extension_attributes.rewardpoints_spent ?
                <li key='spent-point'>
                    <span className="title">{t('Spent')}</span>
                    <span className="value">{
                        t('{{point}} {{pointLabel}}', {
                            point: NumberHelper.formatDisplayGroupAndDecimalSeparator(order.extension_attributes.rewardpoints_spent),
                            pointLabel: order.extension_attributes.rewardpoints_spent > 1
                                ? pluralOfPointName
                                : pointName
                        })
                    }</span>
                </li>
                : '';
        },
    ]
}
