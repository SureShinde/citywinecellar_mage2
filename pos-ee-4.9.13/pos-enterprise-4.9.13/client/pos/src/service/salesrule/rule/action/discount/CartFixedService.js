import {SalesRuleAbstractDiscountService} from "./AbstractDiscountService";
import ServiceFactory from "../../../../../framework/factory/ServiceFactory";
import ValidatorService from "../../../ValidatorService";
import CurrencyHelper from "../../../../../helper/CurrencyHelper";

export class SalesRuleCartFixedService extends SalesRuleAbstractDiscountService {
    static className = 'SalesRuleCartFixedService';

    cartFixedRuleUsedForAddress = {};

    /**
     * calculate discount data
     * @param {object} quote
     * @param {object} address
     * @param {object} rule
     * @param {object} item
     * @param {object} qty
     */
    calculate(quote, address, rule, item, qty) {
        /*Reset cartFixedRuleUsedForAddress*/
        this.cartFixedRuleUsedForAddress = {};
        /*Initial cartFixedRuleUsedForAddress*/
        let discountData = {};
        /*Get Total info of rule*/
        let ruleTotals = ValidatorService.getRuleItemTotalsInfo(rule.rule_id);

        /*get item's price info*/
        let itemPrice = ValidatorService.getItemPrice(item);
        let baseItemPrice = ValidatorService.getItemBasePrice(item);
        let itemOriginalPrice = ValidatorService.getItemOriginalPrice(item, quote);
        let baseItemOriginalPrice = ValidatorService.getItemBaseOriginalPrice(item, quote);

        /*set rule.discount_amount to address.cart_fixed_rules*/
        let cartRules = address.cart_fixed_rules;
        if (!cartRules[rule.rule_id]) {
            cartRules[rule.rule_id] = rule.discount_amount;
        }

        /*If rule's discount amount > 0, calculate discount data*/
        if (cartRules[rule.rule_id] > 0) {
            let baseDiscountAmount = 0;
            let quoteAmount = 0;

            if (ruleTotals.items_count <= 1) {
                /*discount amount = rule's discount amount
                * If item's total price < rule's discount amount, discount amount = item's total price*/
                quoteAmount = CurrencyHelper.convert(cartRules[rule.rule_id]);
                baseDiscountAmount = Math.min(baseItemPrice * qty, cartRules[rule.rule_id]);
            } else {
                /*calculate item's discount rate from item price then get item's maximum discount amount*/
                let discountRate = ruleTotals.base_items_price ? (baseItemPrice * qty/ruleTotals.base_items_price) : 0;
                let maximumItemDiscount = rule.discount_amount * discountRate;
                quoteAmount = CurrencyHelper.convert(maximumItemDiscount);
                /*discount amount <= item's total price*/
                baseDiscountAmount = Math.min(baseItemPrice * qty, maximumItemDiscount);
                /*decrease ruleTotal's items_count value*/
                ValidatorService.decrementRuleItemTotalsCount(rule.rule_id);
            }
            baseDiscountAmount = CurrencyHelper.roundToFloat(baseDiscountAmount);
            /*decrease cartRule's value*/
            cartRules[rule.rule_id] -= baseDiscountAmount;
            /*assign discountData's value*/
            discountData.amount = CurrencyHelper.roundToFloat(Math.min(itemPrice * qty, quoteAmount));
            discountData.base_amount = baseDiscountAmount;
            discountData.original_amount = CurrencyHelper.roundToFloat(Math.min(itemOriginalPrice * qty, quoteAmount));
            discountData.base_original_amount = CurrencyHelper.roundToFloat(baseItemOriginalPrice);
        }

        address.cart_fixed_rules = cartRules;

        return discountData;
    }

    /**
     * Set information about usage cart fixed rule by quote address
     *
     * @param {number} ruleId
     * @param {number} itemId
     */
    setCartFixedRuleUsedForAddress(ruleId, itemId) {
        if (ruleId !== null && ruleId !== undefined) {
            this.cartFixedRuleUsedForAddress[ruleId] = itemId;
        }
    }

    /**
     * Retrieve information about usage cart fixed rule by quote address
     *
     * @param {number} ruleId
     * @return {number|null}
     */
    getCartFixedRuleUsedForAddress(ruleId) {
        if (this.cartFixedRuleUsedForAddress[ruleId]) {
            return this.cartFixedRuleUsedForAddress[ruleId];
        }
        return null;
    }
}


/** @type SalesRuleCartFixedService */
let salesRuleCartFixedService = ServiceFactory.get(SalesRuleCartFixedService);

export default salesRuleCartFixedService;
