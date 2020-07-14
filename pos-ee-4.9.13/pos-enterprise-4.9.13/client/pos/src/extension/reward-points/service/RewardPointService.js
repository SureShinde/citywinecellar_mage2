import * as _ from "lodash";
import CoreService from "../../../service/CoreService";
import ServiceFactory from "../../../framework/factory/ServiceFactory";
import {RewardPointHelper} from "../helper/RewardPointHelper";
import CurrencyHelper from "../../../helper/CurrencyHelper";
import UserService from "../../../service/user/UserService";
import QuoteItemService from "../../../service/checkout/quote/ItemService";
import QuoteService from "../../../service/checkout/QuoteService";
import OrderItemService from "../../../service/sales/order/OrderItemService";
import ProductTypeConstant from "../../../view/constant/ProductTypeConstant";
import ResetRewardProcessor from "./quote/processor/ResetRewardProcessor";

export class RewardPointService extends CoreService {
    static className = 'RewardPointService';
    usedPoint = 0;

    /**
     *
     * @param customer
     * @return {boolean}
     */
    customerCanSpendPoint(customer) {
        if (!customer || !customer.extension_attributes) return false;

        const pointBalance           = customer.extension_attributes['point_balance'];
        const minimumRedeemablePoint = RewardPointHelper.getMinimumRedeemablePoint();
        if (!minimumRedeemablePoint) {
            return pointBalance > 0;
        }

        return pointBalance >= minimumRedeemablePoint;
    }

    /**
     *
     * @param customer
     * @return {*}
     */
    getCustomerPointBalance(customer) {
        return customer.extension_attributes ? customer.extension_attributes['point_balance'] : 0;
    }

    /**
     *
     * @param type
     * @param quote
     * @return {object}
     */
    getActiveRateByType(type, quote) {

        // prevent earn for guest
        if (!quote.customer_id) {
            return false;
        }

        const customerGroupId = quote.customer_group_id;
        const websiteId       = UserService.getWebsiteId();
        const methodName      = type === 'earn' ? 'getEarningRates' : 'getSpendingRates';
        /** @var {object} rate */
        return RewardPointHelper[methodName]().find(rate => {
            /*
             "rate_id": "1",
             "website_ids": "1",
             "customer_group_ids": "1,2,3",
             "direction": "2",
             "points": "1",
             "money": "100.0000",
             "max_price_spended_type": null,
             "max_price_spended_value": null,
             "status": "1",
             "sort_order": "0"
             * */
            const customerGroupIds = rate.customer_group_ids.split(',');
            const websiteIds       = rate.website_ids.split(',');
            if (-1 === websiteIds.indexOf(`${websiteId}`)) {
                return false;
            }
            return customerGroupIds.indexOf(`${customerGroupId}`) !== -1;

        });
    }

    /**
     *
     * @param quote
     * @return {object}
     */
    getActiveEarningRate(quote) {
        return this.getActiveRateByType('earn', quote);
    }

    /**
     *
     * @param quote
     * @return {object}
     */
    getActiveSpendingRate(quote) {
        return this.getActiveRateByType('spend', quote);
    }

    /**
     *
     * @param point
     * @param quote
     * @return {number}
     */
    getDiscountAmountByPoint(point, quote) {
        const activeSpendingRate = this.getActiveSpendingRate(quote);
        if (!activeSpendingRate) return 0;

        let quoteTotal    = this.getQuoteBaseTotal(quote);
        let maxPriceSpend = 0;
        let maxPrice      = activeSpendingRate.max_price_spended_value * 1;
        if (activeSpendingRate.max_price_spended_type === 'by_price') {
            maxPriceSpend = maxPrice;
        }
        if (activeSpendingRate.max_price_spended_type === 'by_percent') {
            maxPriceSpend = quoteTotal * maxPrice / 100;
        }

        if (maxPriceSpend) {
            return Math.min(maxPriceSpend, point * activeSpendingRate.money / activeSpendingRate.points);
        }

        return point * activeSpendingRate.money / activeSpendingRate.points;
    }

    /**
     *
     * @param quote
     * @return {string}
     */
    getActiveSpendingRateLabel(quote) {
        return `1 ${
            RewardPointHelper.getPointName()
            } = ${
            CurrencyHelper.format(this.getDiscountAmountByPoint(1, quote), false)
            }`;
    }

    /**
     *
     * @param quote
     * @return {number}
     */
    getEarnPointForQuote(quote) {
        let baseAmount = quote.base_grand_total;
        if (!RewardPointHelper.canEarnByShipping()) {
            baseAmount -= quote.base_shipping_amount || 0;
            if (RewardPointHelper.canEarnByTax()) {
                baseAmount -= quote.base_shipping_tax_amount || 0;
            }
        }

        if (!RewardPointHelper.canEarnByTax()) {
            baseAmount -= quote.base_tax_amount || 0;
        }

        baseAmount = Math.max(0, baseAmount);
        if (baseAmount < 0) return 0;

        const activeRate = this.getActiveEarningRate(quote);
        if (!activeRate) return 0;

        return RewardPointHelper.round(baseAmount / parseFloat(activeRate.money)) * activeRate.points;
    }

    /**
     * Return item base price
     *
     * @param item
     * @return {number}
     */
    getItemBasePrice(item) {
        let price = (item.discount_calculation_price || 0);
        return price ? price : (item.base_calculation_price || 0);
    }

    /**
     * pre collect total for quote/address and return quote total
     *
     * @param  quote
     * @param  address
     * @param {boolean} isApplyAfterTax
     * @return {number}
     */
    getQuoteBaseTotal(quote, address = null, isApplyAfterTax = false) {
        let baseShippingAmount = 0;
        let baseTotal          = this.getQuoteBaseTotalWithoutShippingFee(quote, isApplyAfterTax);
        if (!address) {
            address = QuoteService.getShippingAddress(quote);
            if (quote.is_virtual) {
                address = QuoteService.getBillingAddress(quote);
            }
        }

        if (RewardPointHelper.allowSpendForShippingFee()) {
            let shippingAmount = address.shipping_amount_for_discount;
            if (shippingAmount) {
                baseShippingAmount = address.base_shipping_amount_for_discount;
            } else {
                baseShippingAmount = address.base_shipping_amount;
            }
            baseTotal += _.toNumber(baseShippingAmount)
                - _.toNumber(address.base_shipping_discount_amount || 0)
                + _.toNumber(address.rewardpoints_base_discount_for_shipping || 0);
            if (isApplyAfterTax) {
                baseTotal += _.toNumber(address.base_shipping_tax_amount || 0);
            }
        }
        return baseTotal;
    }

    /**
     * pre collect total for quote/address and return quote total without shipping fee
     *
     * @param quote
     * @param {boolean} isApplyAfterTax
     * @return {number}
     */
    getQuoteBaseTotalWithoutShippingFee(quote, isApplyAfterTax = false) {
        let baseTotal = 0;

        quote.items.forEach(item => {
            if (item.parent_item_id) {
                return;
            }

            let qty = QuoteItemService.getTotalQty(item, quote);
            baseTotal += this.getItemBasePrice(item) * qty
                - _.toNumber(item.base_discount_amount || 0)
                + _.toNumber(item.extension_attributes.rewardpoints_base_discount || 0);

            if (isApplyAfterTax) {
                baseTotal += _.toNumber(item.base_tax_amount || 0);
            }

        });
        return baseTotal;
    }

    /**
     * get max points can used to spend for a quote
     *
     * @param quote
     * @return {number}
     */
    getMaximumOfRedeemableForQuote(quote) {
        const activeSpendingRate = this.getActiveSpendingRate(quote);
        if (!activeSpendingRate) return 0;
        const maxRedeemablePoint = RewardPointHelper.getSpendMaxPointPerOrder();

        let quoteTotal    = this.getQuoteBaseTotal(quote);
        let maxPriceSpend = 0;
        let maxPrice      = activeSpendingRate.max_price_spended_value * 1;
        let baseRate      = activeSpendingRate.money * 1;
        let pointsSpended = activeSpendingRate.points * 1;

        if (activeSpendingRate.max_price_spended_type === 'by_price') {
            maxPriceSpend = maxPrice;
        }

        if (activeSpendingRate.max_price_spended_type === 'by_percent') {
            maxPriceSpend = quoteTotal * maxPrice / 100;
        }


        const limitSpendingPointsOfAppliedSpendingRate = Math.ceil(maxPriceSpend / baseRate) * pointsSpended;
        const availablePointBalance                    = this.getCustomerPointBalance(quote.customer);

        let pointList = [availablePointBalance, Math.ceil(quoteTotal / baseRate) * pointsSpended];

        if (limitSpendingPointsOfAppliedSpendingRate) {
            pointList.push(limitSpendingPointsOfAppliedSpendingRate);
        }

        if (maxRedeemablePoint) {
            pointList.push(maxRedeemablePoint);
        }

        return Math.min(...pointList);
    }

    /**
     *  remove used point
     */
    removeUsedPoint() {
        return this.setUsedPoint(0);
    }

    /**
     *
     * @return {number}
     */
    getUsedPoint() {
        return this.usePoint;
    }

    /**
     *  set used point
     * @param {number} point
     * @return {RewardPointService}
     */
    setUsedPoint(point) {
        this.usePoint = point;
        return this;
    }

    /**
     * get shipping earning point from $order
     * @return {number}
     */
    static getShippingEarningPoints(order){
        if(!order){
            return 0;
        }

        let shippingEarningPoints = order.extension_attributes.rewardpoints_earn;

        order.items.forEach(item => {
            if (item.parent_item_id) {
                return;
            }
            if (item.has_children) {
                OrderItemService.getChildrenItems(item, order).forEach(child => {
                    shippingEarningPoints -= child.extension_attributes.rewardpoints_earn || 0;
                });
            } else {
                shippingEarningPoints -= item.extension_attributes.rewardpoints_earn || 0;
            }
        });

        return shippingEarningPoints;
    }

    /**
     *
     * @param creditmemo
     * @param order
     * @return {{maxReturnSpend: number, maxAdjustmentEarned: number}}
     */
    getMaxReturnSpendAndMaxAdjustmentEarned(creditmemo, order) {
        let isFullRefund        = true;
        let maxAdjustmentEarned = 0;
        let maxReturnSpend      = 0;
        let returnedSpend       = 0;
        creditmemo.items.forEach(item => {
            let parentItem = item.order_item.parent_item_id
                ? OrderItemService.getParentItem(item.order_item, order) : false;
            let parentProductOptions = false;
            let productOptions       = false;

            /** catcher parse JSON */
            try {
                parentProductOptions = parentItem ? JSON.parse(parentItem.product_options) : false;
                productOptions = item.order_item.product_options ? JSON.parse(item.order_item.product_options) : false;
            } catch (e) {
                return;
            }


            if (item.order_item.parent_item_id && !parentItem) return;
            if (item.order_item.parent_item_id && !parentProductOptions) return;

            if (
                !item.order_item.qty_ordered
                || (
                    item.order_item.product_type === ProductTypeConstant.SIMPLE
                    && parentItem.product_type   === ProductTypeConstant.CONFIGURABLE
                )
                || (
                    parentItem.product_type === ProductTypeConstant.SIMPLE
                    && !parentProductOptions.product_calculations
                )
                || (
                    item.order_item.product_type === ProductTypeConstant.BUNDLE
                    && !productOptions.product_calculations
                )
            ) return;
            /**
             * refunding rw discount items = total rw discount items / ordered qty items * refunding rw qty items
             * maxReturnSpend = refunding rw discount items / total rw discount order * spent point
             *
             * */
            maxReturnSpend +=
                Math.floor(
                    _.toNumber(item.order_item.extension_attributes.rewardpoints_discount || 0)
                    / item.order_item.qty_ordered
                    * item.qty
                    / order.extension_attributes.rewardpoints_discount
                    * order.extension_attributes.rewardpoints_spent
                );

            returnedSpend +=
                Math.floor(
                    _.toNumber(item.order_item.extension_attributes.rewardpoints_discount || 0)
                    / item.order_item.qty_ordered
                    * item.order_item.qty_refunded
                    / order.extension_attributes.rewardpoints_discount
                    * order.extension_attributes.rewardpoints_spent
                );

            maxAdjustmentEarned += Math.floor(
                _.toInteger(item.order_item.extension_attributes.rewardpoints_earn || 0) / item.order_item.qty_ordered * item.qty
            );

            isFullRefund &= item.qty === item.order_item.qty_ordered -item.order_item.qty_refunded;
        });

        if (isFullRefund) {
            maxReturnSpend = order.extension_attributes.rewardpoints_spent;
            if (returnedSpend) {
                maxReturnSpend -= returnedSpend;
            }
        }

        if (order.qty_refunded) {
            maxAdjustmentEarned += RewardPointService.getShippingEarningPoints(order)
        }

        if (order.extension_attributes.creditmemo_rewardpoints_earn) {
            let availableRewardPointsEarn = order.extension_attributes.rewardpoints_earn - order.extension_attributes.creditmemo_rewardpoints_earn;
            maxAdjustmentEarned = Math.min(maxAdjustmentEarned, availableRewardPointsEarn);
        }

        maxReturnSpend      = Math.max(0, maxReturnSpend);
        maxAdjustmentEarned = Math.max(0, maxAdjustmentEarned);

        return {
            maxReturnSpend,
            maxAdjustmentEarned
        }
    }

    /**
     *
     * @param order
     * @return {*}
     */
    static filterDataHoldOrder(order) {
        ResetRewardProcessor.execute(order);
        if (typeof order.extension_attributes !== 'undefined'){
            delete order.extension_attributes.rewardpoints_spent;
            delete order.extension_attributes.rewardpoints_base_discount;
            delete order.extension_attributes.rewardpoints_discount;
            delete order.extension_attributes.rewardpoints_earn;
            delete order.extension_attributes.rewardpoints_base_amount;
            delete order.extension_attributes.rewardpoints_amount;
            delete order.extension_attributes.rewardpoints_base_discount_for_shipping;
            delete order.extension_attributes.rewardpoints_discount_for_shipping;
            delete order.magestore_base_discount_for_shipping;
            delete order.magestore_discount_for_shipping;
            delete order.magestore_base_discount;
            delete order.magestore_discount;
            delete order.base_discount_amount;
            delete order.discount_amount;
        }
        order.addresses.map(address => {
            delete address.rewardpoints_spent;
            delete address.rewardpoints_base_discount;
            delete address.rewardpoints_discount;
            delete address.rewardpoints_base_amount;
            delete address.rewardpoints_amount;
            delete address.magestore_base_discount_for_shipping;
            delete address.magestore_discount_for_shipping;
            delete address.rewardpoints_base_discount_for_shipping;
            delete address.rewardpoints_discount_for_shipping;
            delete address.magestore_base_discount;
            delete address.magestore_discount;
            delete address.rewardpoints_earn;
            delete address.base_discount_amount;
            delete address.discount_amount;
            return address;
        });

        delete order.items.map(item => {
            if (!item.product) return item;
            if (typeof item.extension_attributes !== 'undefined'){
                delete item.extension_attributes.rewardpoints_base_discount;
                delete item.extension_attributes.rewardpoints_discount;
                delete item.magestore_base_discount;
                delete item.magestore_discount;
                delete item.extension_attributes.rewardpoints_earn;
                delete item.extension_attributes.rewardpoints_spent;
                delete item.discount_amount;
                delete item.base_discount_amount;
                delete item.discount_percent;
            }
            return item;
        });

        return order;
    }
}

/**
 * @type {RewardPointService} rewardPointService
 */
let rewardPointService = ServiceFactory.get(RewardPointService);

export default rewardPointService;
