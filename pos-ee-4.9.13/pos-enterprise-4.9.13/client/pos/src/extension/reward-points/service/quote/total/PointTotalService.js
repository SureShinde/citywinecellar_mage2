import * as _ from "lodash";
import {AbstractTotalService} from "../../../../../service/checkout/quote/total/AbstractService";
import ServiceFactory from "../../../../../framework/factory/ServiceFactory";
import NumberHelper from "../../../../../helper/NumberHelper";
import CurrencyHelper from "../../../../../helper/CurrencyHelper";
import {RewardPointHelper} from "../../../helper/RewardPointHelper";
import QuoteItemService from "../../../../../service/checkout/quote/ItemService";
import RewardPointService from "../../RewardPointService";
import SalesRuleUtilityService from "../../../../../service/salesrule/UtilityService";

export class PointTotalService extends AbstractTotalService {
    static className = 'PointTotalService';

    code = "rewardpoint";

    /**
     * Collect point
     *
     * @param {object} quote
     * @param {object} address
     * @param {object} total
     * @return {PointTotalService}
     */
    collect(quote, address, total) {
        super.collect(quote, address, total);
        if (!quote.customer
            || (!quote.is_virtual && address.address_type === 'billing')
            || (quote.is_virtual && address.address_type === 'shipping')) {
            return this;
        }

        let maxPoints = RewardPointService.getMaximumOfRedeemableForQuote(quote);
        let baseTotal = RewardPointService.getQuoteBaseTotal(quote);
        let maxPointsPerOrder = RewardPointHelper.getSpendMaxPointPerOrder();
        if (maxPointsPerOrder) {
            maxPoints = Math.min(maxPointsPerOrder, maxPoints);
        }

        if (!maxPoints || !RewardPointService.customerCanSpendPoint(quote.customer)) {
            return this;
        }

        let baseDiscount = 0;
        let pointUsed = 0;

        let rule = RewardPointService.getActiveSpendingRate(quote);

        if (rule) {
            let points = Math.min(RewardPointService.getUsedPoint(), maxPoints);
            RewardPointService.setUsedPoint(points);
            let ruleDiscount = RewardPointService.getDiscountAmountByPoint(RewardPointService.getUsedPoint(), quote);

            if (ruleDiscount > 0) {
                baseTotal -= ruleDiscount;
                baseDiscount += ruleDiscount;
                pointUsed += points;

                this.processDiscount(quote, address, total, ruleDiscount, points);
            }
        }

        if (baseTotal < 0.0001) {
            baseDiscount = RewardPointService.getQuoteBaseTotal(quote);
        }

        baseDiscount && this.setBaseDiscount(baseDiscount, total, quote, pointUsed);

        return this;
    }

    /**
     * Process Discount
     *
     * @param {object} quote
     * @param {object} address
     * @param {object} total
     * @param {number} ruleDiscount
     * @param {number} points
     */
    processDiscount(quote, address, total, ruleDiscount, points) {
        let baseTotalWithoutShipping = RewardPointService.getQuoteBaseTotalWithoutShippingFee(quote);
        let maxDiscountItems = Math.min(ruleDiscount, baseTotalWithoutShipping);
        quote.items.map(item => {
            if (item.parent_item_id) {
                return item;
            }
            if (item.has_children && QuoteItemService.isChildrenCalculated(item, quote)) {
                this.calculateDiscountItem(quote, total, item, baseTotalWithoutShipping, maxDiscountItems, points);
                this.distributeDiscount(quote, item);
                QuoteItemService.getChildrenItems(quote, item).map(child => {
                    this.aggregateItemDiscount(child);
                    return child;
                });
            } else {
                this.calculateDiscountItem(quote, total, item, baseTotalWithoutShipping, maxDiscountItems, points);
                this.aggregateItemDiscount(item);
            }
            return item;
        });
        if (RewardPointHelper.allowSpendForShippingFee()) {
            this.calculateDiscountShipping(address, total, ruleDiscount, maxDiscountItems);
        }
    }

    /**
     * Calculate Discount Item
     *
     * @param {object} quote
     * @param {object} total
     * @param {object} item
     * @param {number} baseTotalWithoutShipping
     * @param {number} maxDiscountItems
     * @param {number} points
     */
    calculateDiscountItem(quote, total, item, baseTotalWithoutShipping, maxDiscountItems, points) {
        let itemPrice = SalesRuleUtilityService.getItemPrice(item);
        let baseItemPrice = SalesRuleUtilityService.getItemBasePrice(item);
        let qty = QuoteItemService.getTotalQty(item, quote);
        let baseDiscountAmount = item.base_discount_amount || 0;
        let baseItemPriceAfterDiscount = baseItemPrice * qty - baseDiscountAmount;
        let discountRate = baseItemPriceAfterDiscount / baseTotalWithoutShipping;
        let maximumItemDiscount = maxDiscountItems * discountRate;
        let baseRewardDiscountAmount = Math.min(baseItemPriceAfterDiscount, maximumItemDiscount);
        baseRewardDiscountAmount = _.toNumber(CurrencyHelper.round(baseRewardDiscountAmount));

        let rewardDiscountAmount = _.toNumber(CurrencyHelper.convert(maximumItemDiscount));
        rewardDiscountAmount = Math.min(itemPrice * qty - (item.discount_amount || 0), rewardDiscountAmount);
        rewardDiscountAmount = _.toNumber(CurrencyHelper.round(rewardDiscountAmount));

        let pointSpent = NumberHelper.phpRound(
            points * baseItemPrice / baseTotalWithoutShipping,
            0,
            'PHP_ROUND_HALF_DOWN'
        );

        let extensionAttributes = {};
        if (typeof item.extension_attributes !== 'undefined' && item.extension_attributes) {
            extensionAttributes = item.extension_attributes;
        }
        extensionAttributes.rewardpoints_base_discount =
            _.toNumber(extensionAttributes.rewardpoints_base_discount || 0)
            + baseRewardDiscountAmount;
        extensionAttributes.rewardpoints_discount =
            _.toNumber(extensionAttributes.rewardpoints_discount || 0)
            + rewardDiscountAmount;
        extensionAttributes.rewardpoints_spent =
            _.toNumber(extensionAttributes.rewardpoints_spent || 0)
            + pointSpent;
        item.extension_attributes = extensionAttributes;
        item.magestore_base_discount = _.toNumber(item.magestore_base_discount || 0) + baseRewardDiscountAmount;
        item.magestore_discount = _.toNumber(item.magestore_discount || 0) + rewardDiscountAmount;
        item.discount_amount = _.toNumber(item.discount_amount || 0) + rewardDiscountAmount;
        item.base_discount_amount = _.toNumber(item.base_discount_amount || 0) + baseRewardDiscountAmount;
    }

    /**
     * Aggregate Item Discount
     *
     * @param {object} item
     */
    aggregateItemDiscount(item) {
        this._addAmount(-(item.extension_attributes.rewardpoints_discount || 0), this.code);
        this._addBaseAmount(-(item.extension_attributes.rewardpoints_base_discount || 0), this.code);
    }

    /**
     * Distribute Discount
     *
     * @param {object} quote
     * @param {object} item
     * @return {PointTotalService}
     */
    distributeDiscount(quote, item) {
        let parentBaseRowTotal = item.base_row_total || 0;
        let keys = [
            'discount_amount',
            'base_discount_amount',
            'original_discount_amount',
            'base_original_discount_amount',
            'rewardpoints_base_discount',
            'rewardpoints_discount',
            'magestore_base_discount',
            'magestore_discount'
        ];
        let roundingDelta = [];
        keys.forEach(key => {
            roundingDelta[key] = 0.0000001;
        });
        QuoteItemService.getChildrenItems(quote, item).map(child => {
            let ratio = (child.base_row_total || 0) / parentBaseRowTotal;
            keys.forEach(key => {
                let value;
                if (key === 'rewardpoints_base_discount' || key === 'rewardpoints_discount') {
                    if (!item.hasOwnProperty('extension_attributes')) {
                        return;
                    }
                    let extensionAttributes = item.extension_attributes;
                    if (!extensionAttributes.hasOwnProperty(key)) {
                        return;
                    }
                    value = extensionAttributes[key] * ratio;
                } else {
                    if (!item.hasOwnProperty(key)) {
                        return;
                    }
                    value = item[key] * ratio;
                }

                let roundedValue = _.toNumber(CurrencyHelper.round(value + roundingDelta[key]));
                roundingDelta[key] += value - _.toNumber(roundedValue);

                if (key === 'rewardpoints_base_discount' || key === 'rewardpoints_discount') {
                    let extensionAttributes = {};
                    if (child.hasOwnProperty('extension_attributes')) {
                        extensionAttributes = child.extension_attributes;
                    }
                    extensionAttributes[key] = roundedValue;
                    child.extension_attributes = extensionAttributes;
                } else {
                    child[key] = roundedValue;
                }
            });
            return child;
        });

        keys.forEach(key => {
            if (key === 'rewardpoints_base_discount' || key === 'rewardpoints_discount') {
                if (!item.hasOwnProperty('extension_attributes')) {
                    return;
                }
                let extensionAttributes = item.extension_attributes;
                extensionAttributes[key] = 0;
            } else {
                item[key] = 0;
            }
        });
        return this;
    }

    /**
     * Calculate Discount Shipping
     *
     * @param {object} address
     * @param {object} total
     * @param {number} ruleDiscount
     * @param {number} maxDiscountItems
     * @return {PointTotalService}
     */
    calculateDiscountShipping(address, total, ruleDiscount, maxDiscountItems) {
        if (ruleDiscount <= maxDiscountItems) {
            return this;
        }
        let shippingAmount = _.toNumber(address.shipping_amount_for_discount) || 0;
        let baseShippingAmount = _.toNumber(address.base_shipping_amount) || 0;
        if (shippingAmount) {
            baseShippingAmount = _.toNumber(address.base_shipping_amount_for_discount) || 0;
        }

        baseShippingAmount = baseShippingAmount - _.toNumber(address.base_shipping_discount_amount || 0);
        let baseDiscountShipping = NumberHelper.minusNumber(ruleDiscount, maxDiscountItems);
        baseDiscountShipping = Math.min(baseDiscountShipping, baseShippingAmount);
        let discountShipping = _.toNumber(CurrencyHelper.convert(baseDiscountShipping));

        if (!total.hasOwnProperty('extension_attributes')){
            total.extension_attributes = {};
        }

        total.magestore_base_discount_for_shipping = NumberHelper.addNumber(
            _.toNumber(total.magestore_base_discount_for_shipping || 0),
            baseDiscountShipping
        );
        total.magestore_discount_for_shipping = NumberHelper.addNumber(
            _.toNumber(total.magestore_discount_for_shipping || 0),
            discountShipping
        );
        total.extension_attributes.rewardpoints_base_discount_for_shipping = NumberHelper.addNumber(
            _.toNumber(total.extension_attributes.rewardpoints_base_discount_for_shipping || 0),
            baseDiscountShipping
        );
        total.extension_attributes.rewardpoints_discount_for_shipping = NumberHelper.addNumber(
            _.toNumber(total.extension_attributes.rewardpoints_discount_for_shipping || 0),
            discountShipping
        );
        total.base_shipping_discount_amount = Math.max(
            0,
            NumberHelper.addNumber(_.toNumber(total.base_shipping_discount_amount || 0), baseDiscountShipping)
        );
        total.shipping_discount_amount = Math.max(
            0,
            NumberHelper.addNumber(_.toNumber(total.shipping_discount_amount || 0), discountShipping)
        );

        this._addAmount(-discountShipping, this.code);
        this._addBaseAmount(-baseDiscountShipping, this.code);
    }

    /**
     * Set Base Discount
     *
     * @param {number} baseDiscount
     * @param {object} total
     * @param {object} quote
     * @param {number} pointUsed
     */
    setBaseDiscount(baseDiscount, total, quote, pointUsed) {
        let discount = _.toNumber(CurrencyHelper.convert(baseDiscount));
        total.discount_amount = NumberHelper.minusNumber(_.toNumber(total.discount_amount || 0), discount);
        total.base_discount_amount = NumberHelper.minusNumber(
            _.toNumber(total.base_discount_amount || 0),
            baseDiscount
        );
        if (typeof total.extension_attributes === 'undefined') {
            total.extension_attributes = {};
        }
        total.extension_attributes.rewardpoints_spent = NumberHelper.addNumber(
            _.toNumber(total.extension_attributes.rewardpoints_spent || 0),
            pointUsed
        );
        total.extension_attributes.rewardpoints_base_discount = NumberHelper.addNumber(
            _.toNumber(total.extension_attributes.rewardpoints_base_discount || 0),
            baseDiscount
        );
        total.extension_attributes.rewardpoints_discount = NumberHelper.addNumber(
            _.toNumber(total.extension_attributes.rewardpoints_discount || 0),
            discount
        );
        total.magestore_base_discount = NumberHelper.addNumber(
            _.toNumber(total.magestore_base_discount || 0),
            baseDiscount
        );
        total.magestore_discount = NumberHelper.addNumber(_.toNumber(total.magestore_discount || 0), discount);
        total.base_subtotal_with_discount = NumberHelper.minusNumber(
            _.toNumber(total.base_subtotal_with_discount || 0),
            baseDiscount
        );
        total.subtotal_with_discount = NumberHelper.minusNumber(
            _.toNumber(total.subtotal_with_discount || 0),
            discount
        );

        quote.rewardpoints_spent = total.extension_attributes.rewardpoints_spent;
        quote.rewardpoints_base_discount = total.extension_attributes.rewardpoints_base_discount;
        quote.rewardpoints_discount = total.extension_attributes.rewardpoints_discount;
        quote.magestore_base_discount = total.magestore_base_discount;
        quote.magestore_discount = total.magestore_discount;
        quote.magestore_base_discount_for_shipping = _.toNumber(total.magestore_base_discount_for_shipping) || 0;
        quote.magestore_discount_for_shipping = _.toNumber(total.magestore_discount_for_shipping) || 0;
        quote.rewardpoints_base_discount_for_shipping =
            _.toNumber(total.extension_attributes.rewardpoints_base_discount_for_shipping)
            || 0;
        quote.rewardpoints_discount_for_shipping =
            _.toNumber(total.extension_attributes.rewardpoints_discount_for_shipping)
            || 0;
    }
}

/** @type {PointTotalService} */
let pointTotalService = ServiceFactory.get(PointTotalService);

export default pointTotalService;
