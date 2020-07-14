import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import QuoteItemService from "../checkout/quote/ItemService";
import RulesApplierService from "./RulesApplierService";
import UtilityService from "./UtilityService";
import CatalogDataService from "../catalog/CatalogDataService";
import SimpleActionConstant from "../../view/constant/salesrule/SimpleActionConstant";
import CurrencyHelper from "../../helper/CurrencyHelper";
import NumberHelper from "../../helper/NumberHelper";
import {fire} from "../../event-bus";

export class SalesRuleValidatorService extends CoreService {
    static className = 'SalesRuleValidatorService';
    static DISCOUNT_RULE_ID = "POS_CUSTOM_DISCOUNT";
    rulesItemTotals = {};

    /**
     * Reset quote applied rule ids
     *
     * @param quote
     * @param address
     */
    reset(quote, address) {
        quote.applied_rule_ids = "";
        address.applied_rule_ids = "";
    }

    /**
     * Sort valid sales rule by priority
     *
     * @param quote
     */
    sortSalesRuleByPriority(quote) {
        if (quote.valid_salesrule && quote.valid_salesrule.length > 1) {
            quote.valid_salesrule.sort((a, b) => {
                /* The rule which has high sort order will have high priority */
                /* If the rule's sort order is the same, the rule which has low id will have high priority */
                if (a.sort_order === b.sort_order) {
                    return (a.rule_id - b.rule_id);
                } else if (a.sort_order < b.sort_order) {
                    return -1;
                } else if (a.sort_order > b.sort_order) {
                    return 1;
                }
                return -1;
            });
        }
    }

    /**
     * Calculate quote totals for each rule and save results
     *
     * @param {object} quote
     * @param {object} address
     * @param {array} quoteItems
     * @return {{}}
     */
    initTotals(quote, address, quoteItems) {
        address.cart_fixed_rules = {};
        if (!quoteItems || !quoteItems.length) {
            return {};
        }
        this.rulesItemTotals = {};
        if (quote.valid_salesrule && quote.valid_salesrule.length > 0) {
            quote.valid_salesrule.map(rule => {
                let ruleTotalItemsPrice = 0;
                let ruleTotalBaseItemsPrice = 0;
                let validItemsCount = 0;
                quoteItems.map(item => {
                    if (item.parent_item_id) {
                        return false;
                    }
                    /* Only add price if the item match with rule */
                    if (!rule.valid_item_ids.includes(parseFloat(item.item_id))) {
                        return false;
                    }
                    let qty = UtilityService.getItemQty(item, quote, rule);
                    /* Add full price of item on quote to ruleTotalItemsPrice */
                    ruleTotalItemsPrice += (this.getItemPrice(item) * qty);
                    /* Add full base price of item on quote to ruleTotalBaseItemsPrice */
                    ruleTotalBaseItemsPrice += (this.getItemBasePrice(item) * qty);
                    /* Increase number item match with rule */
                    validItemsCount++;
                    return item;
                });
                this.rulesItemTotals[rule.rule_id] = {
                    items_price: ruleTotalItemsPrice,
                    base_items_price: ruleTotalBaseItemsPrice,
                    items_count: validItemsCount
                };
                return rule;
            })
        }

        return this.rulesItemTotals;
    }

    /**
     * Quote item discount calculation process
     * Calculate rules for item then update to quote and address
     *
     * @param {object} quote
     * @param {object} address
     * @param {object} item
     */
    process(quote, address, item) {
        item.discount_amount = 0;
        item.base_discount_amount = 0;
        item.discount_percent = 0;
        let itemPrice = this.getItemPrice(item);
        if (itemPrice < 0) {
            return this;
        }
        let appliedRuleIds = RulesApplierService.applyRules(quote, address, item);
        RulesApplierService.setAppliedRuleIds(quote, address, item, appliedRuleIds);
    }

    /**
     * @todo discount for shipping free
     * calculate discount value for shipping fee
     *
     * @param quote
     * @param address
     */
    processShippingAmount(quote, address) {
        let shippingAmount = address.shipping_amount_for_discount;
        let baseShippingAmount = 0;
        /* If shipping_amount_for_discount value is not value, use shipping_amount value */
        if (typeof shippingAmount !== 'undefined' && shippingAmount !== null) {
            baseShippingAmount = address.base_shipping_amount_for_discount;
        } else {
            shippingAmount = address.shipping_amount;
            baseShippingAmount = address.base_shipping_amount;
        }
        let appliedRuleIds = [];
        if (!quote.valid_salesrule || !quote.valid_salesrule.length) {
            return appliedRuleIds;
        }
        quote.valid_salesrule.map(rule => {
            if (!rule.apply_to_shipping) {
                return rule;
            }
            let discountAmount = 0;
            let baseDiscountAmount = 0;
            let rulePercent = Math.min(100, rule.discount_amount);
            let quoteAmount = 0;
            switch (rule.simple_action) {
                /* case SimpleActionConstant.TO_PERCENT_ACTION:
                     rulePercent = Math.max(0, NumberHelper.minusNumber(100, rule.discount_amount));
                     break; */
                case SimpleActionConstant.BY_PERCENT_ACTION:
                    /* Apply discount percent for shipping amount which available for apply discount */
                    discountAmount = (NumberHelper.minusNumber(
                        shippingAmount,
                        (address.shipping_discount_amount || 0)
                    )) * rulePercent / 100;
                    baseDiscountAmount = (NumberHelper.minusNumber(
                        baseShippingAmount,
                        (address.base_shipping_discount_amount || 0)
                    )) * rulePercent / 100;
                    /* Maximum discount percent is 100% */
                    let discountPercent = Math.min(100, (address.shipping_discount_percent || 0) + rulePercent);
                    address.shipping_discount_percent = discountPercent;
                    break;
                /* case SimpleActionConstant.TO_FIXED_ACTION:
                     quoteAmount = CurrencyHelper.convert(rule.discount_amount);
                     discountAmount = NumberHelper.minusNumber(shippingAmount, quoteAmount);
                     baseDiscountAmount = NumberHelper.minusNumber(baseShippingAmount, rule.discount_amount);
                     break; */
                case SimpleActionConstant.BY_FIXED_ACTION:
                    /* Convert price if POS use multi currency */
                    quoteAmount = CurrencyHelper.convert(rule.discount_amount);
                    discountAmount = quoteAmount;
                    baseDiscountAmount = rule.discount_amount;
                    break;
                case SimpleActionConstant.CART_FIXED_ACTION:
                    /*
                    *  With cart fixed action, use discount amount which didn't use for discount item
                    *  to discount shipping fee
                    */
                    /* Get list available amount of all rules matching with quote */
                    let cartRules = address.cart_fixed_rules;
                    if (typeof cartRules[rule.rule_id] === 'undefined') {
                        /* If discount amount is undefined, we will use rule discount value */
                        cartRules[rule.rule_id] = rule.discount_amount;
                    }
                    if (cartRules[rule.rule_id] > 0) {
                        /* Convert price if POS use multi currency */
                        quoteAmount = CurrencyHelper.convert(cartRules[rule.rule_id]);
                        discountAmount = Math.min(
                            NumberHelper.minusNumber(
                                shippingAmount,
                                (address.shipping_discount_amount || 0)
                            ), quoteAmount
                        );
                        baseDiscountAmount = Math.min(
                            NumberHelper.minusNumber(
                                baseShippingAmount,
                                (address.base_shipping_discount_amount || 0)
                            ), cartRules[rule.rule_id]
                        );
                        /* Update available discount amount of rule after apply to discount */
                        cartRules[rule.rule_id] = NumberHelper.minusNumber(
                            cartRules[rule.rule_id],
                            baseDiscountAmount
                        );
                    }
                    address.cart_fixed_rules = cartRules;
                    break;
                default:
                    break;
            }
            /* Maximum discount amount for shipping is shipping amount */
            discountAmount = Math.min(NumberHelper.addNumber(
                (address.shipping_discount_amount || 0),
                discountAmount
            ), shippingAmount);
            baseDiscountAmount = Math.min(
                NumberHelper.addNumber(
                    (address.base_shipping_discount_amount || 0),
                    baseDiscountAmount
                ), baseShippingAmount
            );

            fire('service_salesrule_validator_process_shipping_amount_before_update_shipping_discount_amount', {
                address: address,
                rule: rule,
                discountAmount: discountAmount
            });

            address.shipping_discount_amount = discountAmount;
            address.base_shipping_discount_amount = baseDiscountAmount;
            appliedRuleIds.push(rule.rule_id);
            /* Add rule discount description label to address object */
            RulesApplierService.addDiscountDescription(address, rule);
            return rule;
        });
        /* Do NOT merge applied rule ids when address was applied custom discount */
        if(address.applied_rule_ids !== SalesRuleValidatorService.DISCOUNT_RULE_ID){
            address.applied_rule_ids = UtilityService.mergeIds(address.applied_rule_ids, appliedRuleIds);
        }
        /* Do NOT merge applied rule ids when address was applied custom discount */
        if(quote.applied_rule_ids !== SalesRuleValidatorService.DISCOUNT_RULE_ID){
            quote.applied_rule_ids = UtilityService.mergeIds(quote.applied_rule_ids, appliedRuleIds);
        }
        return this;
    }

    /**
     * Return item price for calculate discount value
     *
     * @param {object} item
     * @return {number}
     */
    getItemPrice(item) {
        let price = item.discount_calculation_price;
        let calcPrice = QuoteItemService.getCalculationPrice(item);
        return price === null || price === undefined ? calcPrice : price;
    }

    /**
     * Return base item price for calculate discount value
     *
     * @param {object} item
     * @return {number}
     */
    getItemBasePrice(item) {
        let price = item.base_discount_calculation_price;
        let calcPrice = QuoteItemService.getBaseCalculationPrice(item);
        return price === null || price === undefined ? calcPrice : price;
    }

    /**
     * Return item original price for calculate discount value
     *
     * @param {object} item
     * @param {object} quote
     * @return {number}
     */
    getItemOriginalPrice(item, quote) {
        return CatalogDataService.getTaxPrice(item, quote, QuoteItemService.getOriginalPrice(item), true);
    }

    /**
     * Return item base original price for calculate discount value
     *
     * @param {object} item
     * @param {object} quote
     * @return {number}
     */
    getItemBaseOriginalPrice(item, quote) {
        return CatalogDataService.getTaxPrice(item, quote, QuoteItemService.getBaseOriginalPrice(item), true);
    }

    /**
     * Return items list sorted by possibility to apply prioritized rules
     *
     * @param {object} quote
     * @param {object[]} items
     * @return {Array}
     */
    sortItemsByPriority(quote, items) {
        let sortedItemIds = [];
        let sortedItems = [];
        if (quote.valid_salesrule && quote.valid_salesrule.length > 0) {
            /* Sort sales rule by priority */
            this.sortSalesRuleByPriority(quote);
            quote.valid_salesrule.map(rule => {
                items.map(item => {
                    /* Item match prioritized rule will be apply discount first */
                    if (rule.valid_item_ids.includes(parseFloat(item.item_id)) &&
                        !sortedItemIds.includes(parseFloat(item.item_id))) {
                        sortedItemIds.push(parseFloat(item.item_id));
                        sortedItems.push(item);
                    }
                    return item;
                });
                return rule;
            })
        }
        /* Add the items which are not match any rules to items list */
        items.map(item => {
            if (!sortedItemIds.includes(parseFloat(item.item_id))) {
                sortedItems.push(item);
            }
            return item;
        });
        return sortedItems;
    }

    /**
     * get rules item totals which are initial on initTotal function for calculate cart fixed discount
     *
     * @param {number} key
     * @return {*}
     */
    getRuleItemTotalsInfo(key) {
        if(!key) {
            return {};
        }
        if (this.rulesItemTotals[key]) {
            return this.rulesItemTotals[key];
        } else {
            return {};
        }
    }

    /**
     * decrease item_count on rules item totals object after apply cart fixed rule for any item
     *
     * @param key
     */
    decrementRuleItemTotalsCount(key) {
        if(key && this.rulesItemTotals[key] && this.rulesItemTotals[key].items_count) {
            this.rulesItemTotals[key].items_count--;
        }
    }
}


/** @type SalesRuleValidatorService */
let salesRuleValidatorService = ServiceFactory.get(SalesRuleValidatorService);

export default salesRuleValidatorService;
