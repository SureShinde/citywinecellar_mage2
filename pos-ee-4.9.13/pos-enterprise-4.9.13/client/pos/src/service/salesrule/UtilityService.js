import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import QuoteItemService from "../checkout/quote/ItemService";
import NumberHelper from '../../helper/NumberHelper';

export class SalesRuleUtilityService extends CoreService {
    static className = 'SalesRuleUtilityService';

    /**
     * Return discount item qty
     *
     * @param {object} item
     * @param {object} quote
     * @param {object} rule
     * @return {number}
     */
    getItemQty(item, quote, rule) {
        let qty = QuoteItemService.getTotalQty(item, quote);
        let discountQty = rule.discount_qty;
        return discountQty ? Math.min(qty, discountQty) : qty;
    }

    /**
     * Update discount data with item's discount amount
     * @param {object} discountData
     * @param {object} item
     * @param {number} qty
     */
    minFix(discountData, item, qty) {
        /* Get item's price and base price */
        let itemPrice = this.getItemPrice(item);
        let baseItemPrice = this.getItemBasePrice(item);
        /* if item doesn't have discount amount, itemDiscountAmount = 0
         * else  itemDiscountAmount = item.discount_amount*/
        let itemDiscountAmount = item.discount_amount || 0;
        let itemBaseDiscountAmount = item.base_discount_amount || 0;

        /* Discount amount = min of sum(item's discount amount, discountData's amount) and item total price */
        let discountAmount = Math.min(
            NumberHelper.addNumber(itemDiscountAmount, discountData.amount),
            itemPrice * qty
        );
        let baseDiscountAmount = Math.min(
            NumberHelper.addNumber(itemBaseDiscountAmount, discountData.base_amount),
            baseItemPrice * qty
        );
        /* Update discountData */
        discountData.amount = discountAmount;
        discountData.base_amount = baseDiscountAmount;
    }

    /**
     * Return item price
     *
     * @param {object} item
     * @return {number}
     */
    getItemPrice(item) {
        let price = item.discount_calculation_price;
        let calcPrice = QuoteItemService.getCalculationPrice(item);
        /* If item doesn't have discount_calculation_price, return base calculation price
        * else return discount_calculation_price */
        return price === null || price === undefined ? calcPrice : price;
    }

    /**
     * Return base item price
     *
     * @param {object} item
     * @return {number}
     */
    getItemBasePrice(item) {
        let basePrice = item.base_discount_calculation_price;
        let baseCalcPrice = QuoteItemService.getBaseCalculationPrice(item);
        /* If item doesn't have base_discount_calculation_price, return base calculation price
        * else return base_discount_calculation_price */
        return basePrice === null || basePrice === undefined ? baseCalcPrice : basePrice;
    }

    /**
     * Merge two sets of ids
     *
     * @param {array|string} a1
     * @param {array|string} a2
     * @param {boolean} asString
     * @return {array|string}
     */
    mergeIds(a1, a2, asString = true) {
        /* If a1 or a2 is not array, convert to array of number */
        if (!Array.isArray(a1)) {
            a1 = !a1 ? [] : a1.split(',').map(Number);
        }
        if (!Array.isArray(a2)) {
            a2 = !a2 ? [] : a2.split(',').map(Number);
        }
        /* Merge 2 array */
        let a = [...new Set([...a1, ...a2])];
        /* If asString is true, convert array to string */
        if (asString) {
            a = a.join(',');
        }
        return a;
    }
}


/** @type SalesRuleUtilityService */
let salesRuleUtilityService = ServiceFactory.get(SalesRuleUtilityService);

export default salesRuleUtilityService;
