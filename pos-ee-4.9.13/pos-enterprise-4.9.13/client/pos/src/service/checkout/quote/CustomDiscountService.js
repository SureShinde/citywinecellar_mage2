import ServiceFactory from "../../../framework/factory/ServiceFactory";
import {AbstractQuoteService} from "./AbstractService";
import CustomerGroupHelper from "../../../helper/CustomerGroupHelper";
import LocalStorageHelper from "../../../helper/LocalStorageHelper";
import Config from "../../../config/Config";
import {Observable} from 'rxjs';
import QuoteService from '../QuoteService';
import CurrencyHelper from "../../../helper/CurrencyHelper";

export class CustomDiscountService extends AbstractQuoteService {
    static className = 'CustomDiscountService';

    static DISCOUNT_TYPE_FIXED = "$";
    static DISCOUNT_TYPE_PERCENT = "%";
    static DISCOUNT_RULE_ID = "POS_CUSTOM_DISCOUNT";
    static MAX_DISCOUNT_CONFIG_PATH = "max_discount_percent";

    /**
     * Apply custom discount rule
     * @param quote
     * @param discountType
     * @param discountAmount
     * @param reason
     * @returns {*}
     */
    applyCustomRule(quote, discountType, discountAmount, reason) {
        let discountData = this.getDiscountData(quote, discountType, discountAmount);
        let discountValue = discountData.amount;
        if (discountValue > 0) {
            let simple_action = (discountType === CustomDiscountService.DISCOUNT_TYPE_PERCENT) ?
                "by_percent" :
                "cart_fixed";
            let name = "POS Custom Discount";
            let valid_item_ids = this.getAllVisibleItems(quote).map(item => item.item_id);
            let customer_group_ids = [];
            let allCustomerGroup = CustomerGroupHelper.getAllCustomerGroup();
            if (allCustomerGroup) {
                allCustomerGroup.forEach(group => {
                    customer_group_ids.push(group.id);
                });
            }
            let baseDiscountAmount = discountValue;
            if (discountType === CustomDiscountService.DISCOUNT_TYPE_FIXED) {
                baseDiscountAmount = CurrencyHelper.convertToBase(discountValue, CurrencyHelper.getCurrency());
            }
            let customRules = [
                {
                    action_condition: null,
                    apply_to_shipping: false,
                    condition: 0,
                    coupon_type: "1",
                    customer_group_ids: customer_group_ids,
                    description: name,
                    discount_amount: baseDiscountAmount,
                    discount_qty: null,
                    discount_step: 0,
                    from_date: "",
                    is_active: true,
                    is_advanced: true,
                    is_rss: false,
                    name: name,
                    product_ids: [],
                    rule_id: CustomDiscountService.DISCOUNT_RULE_ID,
                    store_labels: [],
                    stop_rules_processing: false,
                    sort_order: 0,
                    simple_action: simple_action,
                    simple_free_shipping: 0,
                    to_date: "",
                    times_used: 0,
                    use_auto_generation: false,
                    uses_per_coupon: 0,
                    uses_per_customer: 0,
                    valid_item_ids: valid_item_ids,
                    website_ids: [LocalStorageHelper.get(LocalStorageHelper.WEBSITE_ID)]
                }
            ];
            quote.valid_salesrule = customRules;
            quote.coupon_code = null;
        } else {
            reason = "";
            discountType = "";
            discountValue = 0;
        }

        quote.os_pos_custom_discount_reason = reason;
        quote.os_pos_custom_discount_type = discountType;
        quote.os_pos_custom_discount_amount = discountValue;

        return Observable.of({
            quote: QuoteService.collectTotals(quote)
        });
    }

    /**
     * Remove custom discount rule
     * @param quote
     * @returns {*}
     */
    removeCustomRule(quote) {
        quote.valid_salesrule = '';
        quote.os_pos_custom_discount_reason = '';
        quote.os_pos_custom_discount_type = '';
        quote.os_pos_custom_discount_amount = 0;
        return Observable.of({
            quote: QuoteService.collectTotals(quote)
        });
    }

    /**
     * Reset custom data
     * @param quote
     * @returns {*}
     */
    reset(quote) {
        quote.os_pos_custom_discount_reason = '';
        quote.os_pos_custom_discount_type = '';
        quote.os_pos_custom_discount_amount = 0;
        return quote;
    }

    /**
     * Maximum discount percent
     * @returns {number}
     */
    getMaxDiscountPercent() {
        let maxDiscountPercent = Config.config[CustomDiscountService.MAX_DISCOUNT_CONFIG_PATH];
        return (maxDiscountPercent) ? maxDiscountPercent : 0;
    }

    /**
     * Get useable discount data
     * @param quote
     * @param discountType
     * @param discountAmount
     * @returns {{percent: number, amount: number}}
     */
    getDiscountData(quote, discountType, discountAmount) {
        let maxDiscountPercent = this.getMaxDiscountPercent();
        let discountPercent = 0;
        let discountValue = parseFloat(discountAmount);
        if (discountType === CustomDiscountService.DISCOUNT_TYPE_PERCENT) {
            discountPercent = discountValue;
        } else {
            discountPercent = (discountValue / quote.subtotal) * 100;
            discountPercent = parseFloat(discountPercent.toFixed(2));
        }

        // Make sure discount is not greater than max percent
        if (discountPercent > maxDiscountPercent) {
            discountPercent = maxDiscountPercent;
            if (discountType === CustomDiscountService.DISCOUNT_TYPE_PERCENT) {
                discountValue = discountPercent;
            } else {
                discountValue = discountPercent * quote.subtotal / 100;
                discountValue = parseFloat(discountValue.toFixed(2));
            }
        }
        return {
            percent: discountPercent,
            amount: discountValue
        };
    }
}

/** @type CustomDiscountService */
let quoteCustomDiscountService = ServiceFactory.get(CustomDiscountService);

export default quoteCustomDiscountService;
