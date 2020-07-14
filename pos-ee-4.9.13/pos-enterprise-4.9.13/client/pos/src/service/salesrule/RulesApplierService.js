import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import QuoteItemService from "../checkout/quote/ItemService";
import UtilityService from "./UtilityService";
import SalesRuleCalculatorFactoryService from "./rule/action/discount/CalculatorFactoryService";
import CouponTypeConstant from "../../view/constant/salesrule/CouponTypeConstant";
import CurrencyHelper from "../../helper/CurrencyHelper";
import {CustomDiscountService} from "../checkout/quote/CustomDiscountService"
import QuoteCustomDiscountService from "../checkout/quote/CustomDiscountService"
import {fire} from "../../event-bus";

export class SalesRuleRulesApplierService extends CoreService {
    static className = 'SalesRuleRulesApplierService';

    /**
     * Apply rules
     *
     * @param quote
     * @param address
     * @param item
     * @return {Array} list of applied rule ids
     */
    applyRules(quote, address, item) {
        /* Initial applied rule ids */
        let appliedRuleIds = [];
        /*If quote.valid_salesrule is not empty or null, check rules*/
        if (quote.valid_salesrule && quote.valid_salesrule.length) {
            /*Check each quote's sale rule*/
            quote.valid_salesrule.map(rule => {
                /*If item_id is not in rule.valid_item_ids, check with it's children*/
                if (!rule.valid_item_ids.includes(parseFloat(item.item_id))) {
                    /*Get list children of item*/
                    let childrens = QuoteItemService.getChildrenItems(quote, item);
                    let isContinue = true;
                    /*If item doesn't have child or all of it's children's item_id are not in rule.valid_item_ids,
                    * do no thing and continue to check next rule*/
                    if (childrens && childrens.length > 0) {
                        let children = childrens.find(childItem =>
                            rule.valid_item_ids.includes(parseFloat(childItem.item_id))
                        );
                        if (children && children.item_id) {
                            isContinue = false;
                        }
                    }
                    if (isContinue) {
                        return false;
                    }
                }
                /*Apply rule to item*/
                this.applyRule(quote, item, rule, address);
                /*Add rule's id to appliedRuleIds*/
                appliedRuleIds.push(rule.rule_id);
                return rule;
            });
        }
        return appliedRuleIds;
    }

    /**
     * Add rule discount description label to address object
     *
     * @param {object} address
     * @param {object} rule
     */
    addDiscountDescription(address, rule) {
        let description = address.discount_description;
        let ruleLabel = rule.store_labels && rule.store_labels.length ? rule.store_labels[0] : null;
        let label = '';
        if (ruleLabel) {
            /* If ruleLabel is not empty, label = ruleLabel*/
            label = ruleLabel;
        } else {
            /* If ruleLabel is empty and address has coupon code, label = address's coupon code*/
            if (address.coupon_code && address.coupon_code.length) {
                label = address.coupon_code;
            }
        }
        /* If label is not empty, add rule label to description*/
        if (label.length) {
            description[rule.rule_id] = label;
        }
        /* Update address's discount description */
        address.discount_description = description;
    }


    /**
     * Apply rule to item
     * @param {object} quote
     * @param {object} item
     * @param {object} rule
     * @param {object} address
     */
    applyRule(quote, item, rule, address) {
        let discountData = this.getDiscountData(quote, item, rule, address);

        fire('service_rules_applier_apply_rule_before', {
            quote: quote,
            item: item,
            rule: rule,
            address: address,
            discountData: discountData
        });

        this.setDiscountData(discountData, item);
        this.maintainAddressCouponCode(address, rule, quote.coupon_code);
        this.addDiscountDescription(address, rule);
        /* reset quote's custom discount data if applied rule isn't custom discount*/
        if(rule.rule_id !== CustomDiscountService.DISCOUNT_RULE_ID){
            QuoteCustomDiscountService.reset(quote);
        }
    }

    /**
     * Get discount data
     * @param {object} quote
     * @param {object} item
     * @param {object} rule
     * @param {object} address
     * @return {object} discount data
     */
    getDiscountData(quote, item, rule, address) {
        /* Get item's qty */
        let qty = UtilityService.getItemQty(item, quote, rule);
        /* Get discount calculator service by rule action*/
        let discountCalculator = SalesRuleCalculatorFactoryService.create(rule.simple_action);
        /* If discountCalculator is undefined, return discount data with value is 0 */
        if(typeof discountCalculator === 'undefined'){
            return {amount : 0, base_amount : 0};
        }

        /* get qty for discount */
        qty = discountCalculator.fixQuantity(qty, rule);
        /* Calculate discount data */
        let discountData = discountCalculator.calculate(quote, address, rule, item, qty);
        /* update discount data with item's discount amount */
        UtilityService.minFix(discountData, item, qty);
        return discountData;
    }

    /**
     * Set discount data to item
     * @param {object} discountData
     * @param {object} item
     */
    setDiscountData(discountData, item) {
        item.discount_amount = CurrencyHelper.roundToFloat(discountData.amount);
        item.base_discount_amount = CurrencyHelper.roundToFloat(discountData.base_amount);
        item.original_discount_amount = CurrencyHelper.roundToFloat(discountData.original_amount);
        item.base_original_discount_amount = CurrencyHelper.roundToFloat(discountData.base_original_amount);
    }

    /**
     * Set coupon code to address
     *
     * @param {object} address
     * @param {object} rule
     * @param {string} couponCode
     * @return {SalesRuleRulesApplierService}
     */
    maintainAddressCouponCode(address, rule, couponCode) {
        if (rule.coupon_type !== CouponTypeConstant.COUPON_TYPE_NO_COUPON) {
            address.coupon_code = couponCode;
        }
        return this;
    }

    /**
     * Set applied rule ids to item, quote and address
     * @param {object} quote
     * @param {object} address
     * @param {object} item
     * @param {Array} appliedRuleIds
     */
    setAppliedRuleIds(quote, address, item, appliedRuleIds) {
        item.applied_rule_ids = appliedRuleIds.join(',');
        /* If address is not applied custom discount, update data of applied_rule_ids */
        if(address.applied_rule_ids !== CustomDiscountService.DISCOUNT_RULE_ID){
            address.applied_rule_ids = UtilityService.mergeIds(address.applied_rule_ids, appliedRuleIds);
        }
        /* If quote is not applied custom discount, update data of applied_rule_ids */
        if(quote.applied_rule_ids !== CustomDiscountService.DISCOUNT_RULE_ID){
            quote.applied_rule_ids = UtilityService.mergeIds(quote.applied_rule_ids, appliedRuleIds);
        }
        return this;
    }
}


/** @type SalesRuleRulesApplierService */
let salesRuleRulesApplierService = ServiceFactory.get(SalesRuleRulesApplierService);

export default salesRuleRulesApplierService;
