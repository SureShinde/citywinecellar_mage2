import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import {AbstractQuoteService} from "../AbstractService";
import CurrencyHelper from "../../../../helper/CurrencyHelper";
import {AbstractProductTypeService} from "../../../catalog/product/type/AbstractTypeService";
import ItemConstant from "../../../../view/constant/checkout/cart/ItemConstant";

export class AbstractQuoteItemService extends AbstractQuoteService {
    static className = 'AbstractQuoteItemService';

    /**
     * Calculate row total
     *
     * @param item
     * @param quote
     * @returns {*}
     */
    calcRowTotal(item, quote) {
        let qty = this.getTotalQty(item, quote);
        let total = CurrencyHelper.roundToFloat(this.getCalculationPriceOriginal(item)) * qty;
        let baseTotal = CurrencyHelper.roundToFloat(this.getBaseCalculationPriceOriginal(item)) * qty;
        item.row_total = CurrencyHelper.roundToFloat(total);
        item.base_row_total = CurrencyHelper.roundToFloat(baseTotal);
        return item;
    }

    /**
     * Get total item quantity (include parent item relation)
     *
     * @return  int|float
     */
    getTotalQty(item, quote) {
        if (item.parent_item_id) {
            return item.qty * this.getParentItem(quote, item).qty;
        }
        return item.qty;
    }

    /**
     * Get item price used for quote calculation process.
     * This method get original custom price applied before tax calculation
     *
     * @param item
     * @return {*}
     */
    getCalculationPriceOriginal(item) {
        if (!item.calculation_price) {
            if (item.original_custom_price) {
                item.calculation_price = item.original_custom_price
            } else {
                item.calculation_price = this.getConvertedPrice(item);
            }
        }
        return item.calculation_price;
    }

    /**
     * Get original calculation price used for quote calculation in base currency.
     *
     * @param {object} item
     * @return {number}
     */
    getBaseCalculationPriceOriginal(item) {
        if (!item.base_calculation_price) {
            if (item.original_custom_price) {
                let price = parseFloat(item.original_custom_price);
                if (price) {
                    let rate = CurrencyHelper.convert(price) / price;
                    price = price / rate;
                }
                item.base_calculation_price = price;
            } else {
                item.base_calculation_price = item.price;
            }
        }
        return item.base_calculation_price;
    }

    /**
     * Get item price used for quote calculation process.
     * This method get custom price (if it is defined) or original product final price
     *
     * @return {number}
     */
    getCalculationPrice(item) {
        if (!item.calculation_price) {
            if (item.custom_price) {
                item.calculation_price = item.custom_price;
            } else {
                item.calculation_price = this.getConvertedPrice(item);
            }
        }
        return item.calculation_price;
    }

    /**
     * Get calculation price used for quote calculation in base currency.
     *
     * @param item
     * @return {*}
     */
    getBaseCalculationPrice(item) {
        if (!item.base_calculation_price) {
            if (item.custom_price) {
                let price = parseFloat(item.custom_price);
                if (price) {
                    let rate = CurrencyHelper.convert(price) / price;
                    price = price / rate;
                }
                item.base_calculation_price = price;
            } else {
                item.base_calculation_price = item.price;
            }
        }
        return item.base_calculation_price;
    }

    /**
     * Get item price converted to quote currency
     *
     * @param {object} item
     * @return {number}
     */
    getConvertedPrice(item) {
        if (!item.converted_price) {
            item.converted_price = CurrencyHelper.convert(item.price);
        }
        return item.converted_price;
    }

    /**
     * Get original price (retrieved from product) for item.
     * Original price value is in quote selected currency
     *
     * @param {object} item
     * @return {number}
     */
    getOriginalPrice(item) {
        if (!item.original_price) {
            item.original_price = CurrencyHelper.convert(this.getBaseOriginalPrice(item));
        }
        return item.original_price;
    }

    /**
     * Get Original item price (got from product) in base website currency
     *
     * @param {object} item
     * @return {number}
     */
    getBaseOriginalPrice(item) {
        return item.base_original_price;
    }

    /**
     * Checking if there children calculated or parent item
     * when we have parent quote item and its children
     *
     * @param {object} item
     * @param {object} quote
     * @return {boolean}
     */
    isChildrenCalculated(item, quote) {
        let calculate = null;
        if (item.parent_item_id) {
            calculate = this.getParentItem(quote, item).product.price_type;
        } else {
            calculate = item.product.price_type;
        }
        return ((typeof calculate !== 'undefined' || calculate !== null) &&
        parseFloat(calculate) === AbstractProductTypeService.CALCULATE_CHILD);
    }

    /**
     * Set custom price for quote item
     *
     * @param item
     * @param data
     * @returns {AbstractQuoteItemService}
     */
    setCustomPrice(item, data){
        item.calculation_price = data.customPrice;
        item.base_calculation_price = null;
        item.custom_price = data.customPrice;
        item.os_pos_custom_price_reason = data.reason;
        item.customPriceType = data.customPriceType;
        item.customPriceDiscountValue = data.customPriceDiscountValue;
        item.customPriceDiscountType = data.customPriceDiscountType;
        item.unitCustomPriceDiscount = data.unitCustomPriceDiscount;
        item.has_custom_discount_price = true;
        item.original_price_from_starting = data.originalPrice;

        let additional_data = typeof(item.additional_data) === 'object' ? item.additional_data : {};
        if (data.unitCustomPriceDiscount || data.customPriceType === ItemConstant.STATE_CUSTOM_DISCOUNT_PRICE) {
            additional_data.customPriceDiscountValue = data.customPriceDiscountValue;
            additional_data.customPriceDiscountType = data.customPriceDiscountType;
            additional_data.unitCustomPriceDiscount = data.unitCustomPriceDiscount;
            additional_data.customDiscountReason = data.reason;
            additional_data.hasCustomDiscountPerItem = true;
            additional_data.originalPriceFromStarting = data.originalPrice;
        } else {
            if (additional_data.unitCustomPriceDiscount) {
                delete additional_data.customPriceDiscountValue;
                delete additional_data.customPriceDiscountType;
                delete additional_data.unitCustomPriceDiscount;
                delete additional_data.customDiscountReason;
                delete additional_data.hasCustomDiscountPerItem;
                delete additional_data.originalPriceFromStarting;
            }
        }
        additional_data.customPriceType = data.customPriceType;
        item.additional_data = JSON.stringify(additional_data);

        return this;
    }
}

/** @type AbstractQuoteItemService */
let abstractQuoteItemService = ServiceFactory.get(AbstractQuoteItemService);

export default abstractQuoteItemService;