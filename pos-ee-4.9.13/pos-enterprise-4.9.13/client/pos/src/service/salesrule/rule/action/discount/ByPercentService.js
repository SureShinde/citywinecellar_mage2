import {SalesRuleAbstractDiscountService} from "./AbstractDiscountService";
import ServiceFactory from "../../../../../framework/factory/ServiceFactory";
import ValidatorService from "../../../ValidatorService";
import NumberHelper from "../../../../../helper/NumberHelper";

export class SalesRuleByPercentService extends SalesRuleAbstractDiscountService {
    static className = 'SalesRuleByPercentService';

    /**
     * Get Rule Percent and Calculate Discount Data
     * @param {object} quote
     * @param {object} address
     * @param {object} rule
     * @param {object} item
     * @param {number} qty
     */
    calculate(quote, address, rule, item, qty) {
        let rulePercent = Math.min(100, rule.discount_amount);

        return this._calculate(quote, rule, item, qty, rulePercent);
    }

    /**
     * Get Qty for Discount (Discount Qty Step (Buy X))
     * @param {number} qty
     * @param {object} rule
     * @return {number}
     */
    fixQuantity(qty, rule) {
        let step = rule.discount_step;
        /* Get Qty if Step > 0 */
        if (step) {
            qty = Math.floor(qty / step) * step;
        }

        return qty;
    }

    /**
     * Get Discount Data by Item, Rule, Qty, Rule Percent and Quote
     * @param quote
     * @param rule
     * @param item
     * @param qty
     * @param rulePercent
     * @private
     */
    _calculate(quote, rule, item, qty, rulePercent) {
        let discountData = {};

        /* Get price for calculation */
        let itemPrice = ValidatorService.getItemPrice(item);
        let baseItemPrice = ValidatorService.getItemBasePrice(item);
        let itemOriginalPrice = ValidatorService.getItemOriginalPrice(item, quote);
        let baseItemOriginalPrice = ValidatorService.getItemBaseOriginalPrice(item, quote);

        /* Get Ratio */
        let rulePct = rulePercent / 100;

        /* Begin Calculate Discount Data*/
        discountData.amount = (NumberHelper.minusNumber(qty * itemPrice, item.discount_amount || 0) > 0)
            ? (NumberHelper.minusNumber(qty * itemPrice, item.discount_amount || 0) * rulePct) : 0;

        discountData.base_amount = (NumberHelper.minusNumber(qty * baseItemPrice, item.base_discount_amount || 0) > 0)
            ? (NumberHelper.minusNumber(qty * baseItemPrice, item.base_discount_amount || 0) * rulePct) : 0;

        discountData.original_amount = (NumberHelper.minusNumber(qty * itemOriginalPrice,
            item.discount_amount || 0) > 0)
            ? (NumberHelper.minusNumber(qty * itemOriginalPrice, item.discount_amount || 0) * rulePct) : 0;

        discountData.base_original_amount = (NumberHelper.minusNumber(qty * baseItemOriginalPrice,
            item.base_discount_amount || 0) > 0)
            ? (NumberHelper.minusNumber(qty * baseItemOriginalPrice, item.base_discount_amount || 0) * rulePct) : 0;

        /* End Calculate*/

        /* Get Item Discount Percent */
        if (!rule.discount_qty || rule.discount_qty > qty) {
            item.discount_percent = Math.min(100, (item.discount_percent || 0) + rulePercent);
        }
        return discountData;
    }
}


/** @type SalesRuleByPercentService */
let salesRuleByPercentService = ServiceFactory.get(SalesRuleByPercentService);

export default salesRuleByPercentService;