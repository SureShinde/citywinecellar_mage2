import React, {Fragment} from 'react';
import {listen} from "../../../../../../event-bus";
import NumberHelper from "../../../../../../helper/NumberHelper";
import {RewardPointHelper} from "../../../../helper/RewardPointHelper";
import CreateCreditmemoConstant from "../../../../view/constant/order/creditmemo/CreateCreditmemoConstant";
import StatusConstant from "../../../../../../view/constant/order/StatusConstant";

export default class PrintComponentObserver {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('view_print_get_template_discount_before', (eventData) => {
            let {orderData} = eventData;
            let pointDiscount = Math.abs(orderData.extension_attributes.rewardpoints_discount ? orderData.extension_attributes.rewardpoints_discount : 0);
            eventData.pluginDiscount = NumberHelper.addNumber(eventData.pluginDiscount, pointDiscount);
        }, 'rewardpoints');

        listen('view_print_get_template_plugin_area_before', (eventData) => {
            let {component, orderData, creditmemo} = eventData;
            if (RewardPointHelper.isEnabledRewardPoint()) {
                eventData.listTemplate.push(this.getTemplateAdjustPointDetail(component, creditmemo));
                eventData.listTemplate.push(this.getTemplateRewardPointBalance(component, orderData));
            }
        }, 'rewardpoints');

        listen('view_print_get_template_creditmemo_receipt_get_discount_amount_after', (eventData) => {
            if (RewardPointHelper.isEnabledRewardPoint()) {
                eventData.discount_amount = NumberHelper.addNumber(eventData.discount_amount, eventData.creditmemo.extension_attributes.rewardpoints_discount);
            }
        }, 'rewardpoints');
    }

    /**
     * Get template adjust point balance
     *
     * @param component
     * @param creditmemo
     * @returns {{template: null, sortOrder: number}}
     */
    getTemplateAdjustPointDetail(component, creditmemo) {
        let template = {
            template: null,
            sortOrder: 10
        };
        const pointName = RewardPointHelper.getPointName();
        const pluralOfPointName = RewardPointHelper.getPluralOfPointName();

        let adjustmentEarned = (creditmemo && (typeof creditmemo.extension_attributes !== 'undefined'))
            ? creditmemo.extension_attributes[CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY] : 0;
        let returnSpent = (creditmemo && (typeof creditmemo.extension_attributes !== 'undefined'))
            ? creditmemo.extension_attributes[CreateCreditmemoConstant.RETURN_SPENT_KEY] : 0;
        const {t} = component.props;
        if (creditmemo && (adjustmentEarned || returnSpent)) {
            template.template = (
                <Fragment key='AdjustPointDetail'>
                    {
                        adjustmentEarned ? component.getTemplateTotal(
                            t('Adjust Earned {{pointLabel}}', {
                                pointLabel: adjustmentEarned > 1 ? pluralOfPointName : pointName
                            }),
                            t('{{point}} {{pointLabel}}', {
                                point: NumberHelper.formatDisplayGroupAndDecimalSeparator(adjustmentEarned),
                                pointLabel: adjustmentEarned > 1 ? pluralOfPointName : pointName
                            }),
                            "Adjust Earned Point",
                            true
                        ) : null
                    }
                    {
                        returnSpent ? component.getTemplateTotal(
                            t('Return Spent {{pointLabel}}', {
                                pointLabel: returnSpent > 1 ? pluralOfPointName : pointName
                            }),
                            t('{{point}} {{pointLabel}}', {
                                point: NumberHelper.formatDisplayGroupAndDecimalSeparator(returnSpent),
                                pointLabel: returnSpent > 1 ? pluralOfPointName : pointName
                            }),
                            "Return Spent Point",
                            true
                        ) : null
                    }
                </Fragment>
            );
        }

        return template;
    }

    /**
     * Get template reward point balance
     *
     * @param component
     * @param orderData
     * @returns {{template: null, sortOrder: number}}
     */
    getTemplateRewardPointBalance(component, orderData) {
        const {quote, t, customer} = component.props;
        let template = {
            template: null,
            sortOrder: 30
        };
        let currentPointBalance = null;
        if (customer && customer.id && customer.extension_attributes && customer.extension_attributes.point_balance) {
            currentPointBalance = customer.extension_attributes.point_balance;
        } else {
            if (quote && quote.customer_id === orderData.customer_id) {
                let createdInvoice = orderData.total_due === 0 ? 1 : 0;
                currentPointBalance = (quote.customer && quote.customer.extension_attributes && quote.customer.extension_attributes.point_balance)
                    ? quote.customer.extension_attributes.point_balance
                    : 0;
                currentPointBalance = NumberHelper.minusNumber(currentPointBalance, orderData.extension_attributes.rewardpoints_spent);

                if (!RewardPointHelper.holdPointDay()) {
                    if (
                        (RewardPointHelper.allowReceivingPointsWhenInvoiceIsCreated() && createdInvoice)
                        || (!RewardPointHelper.allowReceivingPointsWhenInvoiceIsCreated() && orderData.state === StatusConstant.STATE_COMPLETE)
                    ) {
                        currentPointBalance = NumberHelper.addNumber(currentPointBalance, (orderData.extension_attributes.rewardpoints_earn || 0));
                    }
                }
            }
        }

        const pointName = RewardPointHelper.getPointName();
        const pluralOfPointName = RewardPointHelper.getPluralOfPointName();

        if (currentPointBalance !== null) {
            template.template = component.getTemplateTotal(
                t("{{pointName}} Balance", {pointName}),
                t('{{point}} {{pointLabel}}', {
                    point: NumberHelper.formatDisplayGroupAndDecimalSeparator(currentPointBalance),
                    pointLabel: currentPointBalance > 1
                        ? pluralOfPointName
                        : pointName
                }),
                "Point Balance",
                true
            )
        }

        return template;
    }
}
