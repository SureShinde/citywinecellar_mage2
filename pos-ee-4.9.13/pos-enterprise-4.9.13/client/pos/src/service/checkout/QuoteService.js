import {AbstractQuoteService} from "./quote/AbstractService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import AddProductService from "./quote/AddProductService";
import {Observable} from 'rxjs';
import TotalService from "./quote/TotalService";
import AddressService from "./quote/AddressService";
import AddressConstant from "../../view/constant/checkout/quote/AddressConstant";
import UpdateProductService from "./quote/UpdateProductService";
import QuoteItemService from "./quote/ItemService";
import ChangeCustomerService from "./quote/ChangeCustomerService";
import SubmitCouponCodeService from "./quote/SubmitCouponCodeService";
import CurrencyHelper from "../../helper/CurrencyHelper";
import CustomerGroupHelper from "../../helper/CustomerGroupHelper";
import NumberHelper from "../../helper/NumberHelper";
import cloneDeep from 'lodash/cloneDeep';
import {fire} from "../../event-bus";
import PriceService from "../catalog/product/PriceService";
import {GiftCardProductHelper} from "../../helper/GiftCardProductHelper";
import GiftCardProductConstant from "../../view/constant/catalog/GiftCardProductConstant";
import StockService from "../catalog/StockService";
import ProductTypeConstant from "../../view/constant/ProductTypeConstant";
import QuoteAction from "../../view/action/checkout/QuoteAction";
import ProductAction from "../../view/action/ProductAction";
import ProductService from "../catalog/ProductService";
import $ from "jquery";
import PaymentConstant from "../../view/constant/PaymentConstant";
import ItemConstant from "../../view/constant/checkout/cart/ItemConstant";

export class QuoteService extends AbstractQuoteService {
    static className = 'QuoteService';

    productListQuote = null;

    beforeCollectTotalProcessors = [];

    afterCollectTotalProcessors = [];

    initialQuoteReducerState = {
        id: new Date().getTime(),
        customer_id: null,
        customer_group_id: 0,
        customer_is_guest: 1,
        grand_total: 0,
        base_grand_total: 0,
        items: [],
        payments: [],
        addresses: [],
        customer: null
    };

    /**
     * Reset quote
     *
     * @return {{}}
     */
    resetQuote() {
        return {
            ...this.changeCustomer({
                ...cloneDeep(this.initialQuoteReducerState), id: new Date().getTime()
            })
        }
    }

    collectTotals(quote) {
        if (!quote.addresses || !quote.addresses.length) {
            quote = this.changeCustomer(quote, quote.customer);
        }
        this.beforeCollectTotalProcessors
            .sort((a, b) => a.sort_order - b.sort_order)
            .forEach(processor => processor.class.execute(quote));

        let total = TotalService.collectTotals(quote);
        quote = Object.assign(quote, total);

        this.afterCollectTotalProcessors
            .sort((a, b) => a.sort_order - b.sort_order)
            .forEach(processor => processor.class.execute(quote));

        return quote;
    }

    /**
     * Create default quote data
     *
     * @param quote
     */
    createDefaultQuoteData(quote) {
        AddressService.createTempAddress(quote);
        if (typeof quote.customer_tax_class_id === 'undefined') {
            quote.customer_tax_class_id = CustomerGroupHelper.getTaxClassId(
                CustomerGroupHelper.getQuoteCustomerGroupId(quote)
            );
        }
    }

    /**
     * Get quote to prepare product list price
     *
     * @return {*}
     */
    getProductListQuote() {
        if (!this.productListQuote) {
            this.productListQuote = {addresses: []};
            this.createDefaultQuoteData(this.productListQuote);
        }
        return this.productListQuote;
    }

    /**
     * add product to quote
     * @param quote
     * @param data
     * @returns {*}
     */
    addProduct(quote, data) {
        this.createDefaultQuoteData(quote);

        let addProductResult = AddProductService.addProduct(quote, data);

        if (addProductResult.success === false) {
            return Observable.of(addProductResult);
        }

        fire('service_quote_add_product_after', {quote: quote});

        return Observable.of({
            success: true,
            quote: this.collectTotals(quote),
            added_item_id: addProductResult.added_item_id
        });
    }

    /**
     * update qty after change on number pad
     *
     * @param quote
     * @param item
     * @param qty
     * @returns {*}
     */
    updateQtyCartItem(quote, item, qty) {
        AddressService.createTempAddress(quote);
        let updateProductServiceResult = UpdateProductService.updateQty(quote, item, qty);

        if (typeof item.unitCustomPriceDiscount !== 'undefined' && item.unitCustomPriceDiscount !== null) {
            let data = {
                customPriceDiscountValue: item.customPriceDiscountValue,
                customPriceDiscountType: item.customPriceDiscountType,
                reason: item.os_pos_custom_price_reason,
                unitCustomPriceDiscount: item.unitCustomPriceDiscount,
                customPriceType: item.customPriceType,
                customPrice: item.custom_price,
            };
            let customPriceDiscount = this.calculateCustomPriceDiscount(quote, item, item.qty, data, true);
            QuoteItemService.setCustomPrice(item, customPriceDiscount);
        }

        if (updateProductServiceResult.success === false) {
            return Observable.of(updateProductServiceResult);
        }

        fire('service_quote_update_qty_cart_item_after', {quote: quote});

        return Observable.of({
            success: true,
            quote: this.collectTotals(quote)
        });
    }

    /**
     * update custom price after change on number pad
     *
     * @param quote
     * @param item
     * @param data
     * @returns {*}
     */
    updateCustomPriceCartItem(quote, item, data) {
        AddressService.createTempAddress(quote);
        let customPriceDiscount = this.calculateCustomPriceDiscount(quote, item, item.qty, data, false);
        QuoteItemService.setCustomPrice(item, customPriceDiscount);

        fire('service_quote_update_custom_price_cart_item_after', {quote: quote});

        return Observable.of({
            success: true,
            quote: this.collectTotals(quote)
        });
    }

    /**
     * Calculate custom price discount
     *
     * @param quote
     * @param item
     * @param qty
     * @param data
     * @param changeQty
     * @returns {{customPrice: (*|number), originalPrice: number, customPriceDiscountValue, customPriceDiscountType, reason, unitCustomPriceDiscount: *, customPriceType: *}}
     */
    calculateCustomPriceDiscount(quote, item, qty, data, changeQty) {
        let finalPrice = PriceService.getPriceService(item.product).getOriginalFinalPrice(item.qty, item.product, quote, item);
        let basePrice = PriceService.getPriceService(item.product).getOriginalFinalPrice(qty, item.product, quote, item);
        let unitCustomPriceDiscount;
        let customPrice = data.customPrice;
        let reason = data.reason;
        let customPriceDiscountValue = data.customPriceDiscountValue;
        let customPriceDiscountType = data.customPriceDiscountType;
        let unitPrice = data.unitCustomPriceDiscount;
        let customPriceType = data.customPriceType;

        if (customPriceType === ItemConstant.STATE_CUSTOM_PRICE) {
            if(customPrice === null || customPrice === "" || customPrice === finalPrice){
                customPrice = null;
                reason = "";
            }
            basePrice = null;
            customPriceDiscountValue = null;
            customPriceDiscountType = null;
            unitCustomPriceDiscount = null;
        } else {
            if (customPriceDiscountValue === null || customPriceDiscountValue === "" || customPriceDiscountValue === 0 || typeof(customPriceDiscountValue) === 'undefined') {
                customPrice = null;
                reason = "";
            } else {
                if (customPriceDiscountType === ItemConstant.CUSTOM_PRICE_DISCOUNT_PERCENT) {
                    if (customPriceDiscountValue >= 100) {
                        customPriceDiscountValue = 100;
                        unitCustomPriceDiscount = basePrice;
                        customPrice = 0;
                    } else {
                        customPrice = CurrencyHelper.roundToFloat(basePrice * (100 - customPriceDiscountValue) / 100, 4);
                        unitCustomPriceDiscount = CurrencyHelper.roundToFloat(basePrice * customPriceDiscountValue / 100, 4);
                    }
                } else {
                    if (customPriceDiscountValue >= basePrice) {
                        customPrice = 0;
                        if (changeQty) {
                            unitCustomPriceDiscount = unitPrice;
                            customPriceDiscountValue = unitPrice;
                        } else {
                            customPriceDiscountValue = basePrice;
                            unitCustomPriceDiscount = basePrice;
                        }
                    } else {
                        if (changeQty) {
                            unitCustomPriceDiscount = unitPrice;
                            customPriceDiscountValue = unitPrice;
                            customPrice = CurrencyHelper.roundToFloat((basePrice - unitPrice), 4);
                        } else {
                            customPrice = CurrencyHelper.roundToFloat((basePrice - customPriceDiscountValue), 4);
                            unitCustomPriceDiscount = CurrencyHelper.roundToFloat(customPriceDiscountValue, 4);
                        }
                    }
                }
            }
        }

        return {
            customPrice: customPrice,
            originalPrice: basePrice,
            customPriceDiscountValue: customPriceDiscountValue,
            customPriceDiscountType: customPriceDiscountType,
            reason: reason,
            unitCustomPriceDiscount: unitCustomPriceDiscount,
            customPriceType: customPriceType
        };

    }

    /**
     *  remove cart item
     * @param quote
     * @param item
     * @return {*}
     */
    removeItem(quote, item) {
        if (item.product_type === 'configurable' || item.product_type === 'bundle') {
            let children = QuoteItemService.getChildrenItems(quote, item);
            quote.items = quote.items.filter(quoteItem => {
                return children.indexOf(quoteItem) === -1;
            })
        }
        const index = quote.items.indexOf(item);
        if (index !== -1) {
            quote.items.splice(index, 1);
        }
        AddressService.createTempAddress(quote);

        fire('service_quote_remove_cart_item_after', {quote: quote});

        return Observable.of({
            success: true,
            quote: this.collectTotals(quote)
        });
    }

    /**
     * Get quote billing address
     *
     * @param {object} quote
     * @return {object}
     */
    getBillingAddress(quote) {
        if (!quote.addresses || quote.addresses.length < 1) {
            return false;
        }
        return quote.addresses.find(address => address.address_type === AddressConstant.BILLING_ADDRESS_TYPE);
    }

    /**
     * Get quote shipping address
     *
     * @param {object} quote
     * @return {object}
     */
    getShippingAddress(quote) {
        if (!quote.addresses || quote.addresses.length < 1) {
            return false;
        }
        return quote.addresses.find(address => address.address_type === AddressConstant.SHIPPING_ADDRESS_TYPE);
    }

    /**
     * Get base total paid
     *
     * @param quote
     * @returns {number}
     */
    getBaseTotalPaid(quote) {
        let baseTotalPaid = 0;
        let baseGrandTotal = quote.base_grand_total;
        quote.payments.forEach(payment => {
            let paidAmount = payment.is_pay_later ? 0 : payment.base_amount_paid;
            baseTotalPaid = NumberHelper.addNumber(baseTotalPaid, paidAmount);
        });
        // Due baseTotal depends on Total (fixed for multi currency
        if (baseTotalPaid > baseGrandTotal || 0 === this.getTotalDue(quote)) {
            baseTotalPaid = baseGrandTotal;
        }
        return baseTotalPaid;
    }

    /**
     * Get total paid
     * @param quote
     * @returns {number}
     */
    getTotalPaid(quote) {
        let totalPaid = 0;
        let grandTotal = quote.grand_total;
        quote.payments.forEach(payment => {
            let paidAmount = payment.is_pay_later ? 0 : payment.amount_paid;
            totalPaid = NumberHelper.addNumber(totalPaid, paidAmount);
        });
        if (totalPaid > grandTotal) {
            totalPaid = grandTotal;
        }
        return totalPaid;
    }

    /**
     * get total due of quote
     *
     * @param quote
     * @returns {number}
     */
    getTotalDue(quote) {
        let totalPaid = this.getTotalPaid(quote);
        let grandTotal = quote.grand_total;
        if (!totalPaid) {
            return grandTotal;
        } else if (grandTotal > totalPaid) {
            return NumberHelper.minusNumber(grandTotal, totalPaid);
        }
        return 0;
    }

    /**
     * Get base total due of quote
     *
     * @param quote
     * @returns {number}
     */
    getBaseTotalDue(quote) {
        let baseTotalPaid = this.getBaseTotalPaid(quote);
        let baseGrandTotal = quote.base_grand_total;
        if (!baseTotalPaid) {
            return baseGrandTotal;
        } else if (baseGrandTotal > baseTotalPaid) {
            return NumberHelper.minusNumber(baseGrandTotal, baseTotalPaid);
        }
        return 0;
    }

    /**
     * get base total change
     * @param quote
     * @return {number}
     */
    getBasePosChange(quote) {
        let baseTotalChange = 0;
        quote.payments.forEach(item =>
            baseTotalChange = NumberHelper.addNumber(baseTotalChange, item.base_amount_change)
        );
        return baseTotalChange;
    }

    /**
     * get total change
     * @param quote
     * @return {number}
     */
    getPosChange(quote) {
        let totalChange = 0;
        quote.payments.forEach(item => totalChange = NumberHelper.addNumber(totalChange, item.amount_change));
        return totalChange;
    }

    /**
     * Set additional data for quote before place order
     *
     * @param quote
     * @return {*}
     */
    placeOrderBefore(quote) {
        quote.global_currency_code = CurrencyHelper.getGlobalCurrencyCode();
        quote.base_currency_code = CurrencyHelper.getBaseCurrencyCode();
        quote.store_currency_code = CurrencyHelper.getBaseCurrencyCode();
        quote.quote_currency_code = CurrencyHelper.getCurrentCurrencyCode();
        quote.base_to_global_rate = CurrencyHelper.getBaseCurrency().currency_rate;
        quote.base_to_quote_rate = CurrencyHelper.getCurrentCurrency().currency_rate;
        quote.store_to_base_rate = 1 / CurrencyHelper.getCurrentCurrency().currency_rate;
        quote.store_to_quote_rate = 1 / CurrencyHelper.getCurrentCurrency().currency_rate;

        fire('service_quote_place_order_before', {quote: quote});

        return quote;
    }

    /**
     * Customer for quote
     *
     * @param {object} quote
     * @param {object} customer
     */
    changeCustomer(quote, customer = null) {
        return ChangeCustomerService.changeCustomer(quote, customer);
    }

    /**
     * submit coupon code
     * @param quote
     * @param couponCode
     * @returns {*}
     */
    submitCouponCode(quote, couponCode) {
        return SubmitCouponCodeService.submit(quote, couponCode);
    }

    /**
     *
     * @param store
     * @param product
     * @returns {Promise<void>}
     */
    async addProductWithOutOptions(store, product) {
        let loadingCart = $('.loader-cart');
        if (!this.isLoadFullData(product)) {
            if (!window.pendingAddNoOptionRequest) {
                window.pendingAddNoOptionRequest = 1;
            } else {
                window.pendingAddNoOptionRequest++;
            }
            loadingCart.show();
            try {
                Object.assign(product, await ProductService.getById(product.id));
                window.pendingAddNoOptionRequest--;
            } catch (e) {
                window.pendingAddNoOptionRequest--;
            }
            if (!window.pendingAddNoOptionRequest) {
                loadingCart.hide();
            }
        }
        /* add product_options */
        let info_buyRequest = {
            options: {}
        };
        /* Gift card */
        if (GiftCardProductHelper.productIsGiftCard(product)) {
            info_buyRequest = {
                [GiftCardProductConstant.GIFT_CARD_AMOUNT]: GiftCardProductHelper.getFixedValue(product),
                product: product.id,
                ...GiftCardProductHelper.getDefaultTemplateOption(product),
            };
        }
        store.dispatch(QuoteAction.addProduct({
            product: product,
            product_options: {info_buyRequest},
            qty: StockService.getProductStockService(product).getAddQtyIncrement(product)
        }));
    }

    /**
     *
     * @param product
     * @returns {boolean}
     */
    isLoadFullData(product) {
        return (typeof product.search_string !== 'undefined');
    }

    /**
     * Add product to current quote
     * @param store
     * @param product
     * @returns {Promise<void>}
     */
    async addProductToCurrentQuote(store, product) {
        if (
            [ProductTypeConstant.SIMPLE, ProductTypeConstant.VIRTUAL, ProductTypeConstant.GIFT_CARD].includes(product.type_id)
            &&
            !product.options
        ) {
            /* add product_options */
            await this.addProductWithOutOptions(store, product);
            return;
        }

        store.dispatch(ProductAction.viewProduct(product));
    }

    /**
     * Get total cash
     * @param quote
     * @returns {number}
     */
    getTotalCash(quote) {
        let totalCash = 0;
        quote.payments.forEach(item => {
            if (item.method === PaymentConstant.CASH) {
                totalCash += item.amount_change ?
                    NumberHelper.addNumber(item.amount_paid, item.amount_change) : item.amount_paid;
            }
        });
        return totalCash;
    }
}

/** @type QuoteService */
let quoteService = ServiceFactory.get(QuoteService);

export default quoteService;
