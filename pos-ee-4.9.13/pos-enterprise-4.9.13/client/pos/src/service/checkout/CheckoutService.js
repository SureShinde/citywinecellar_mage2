import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory"
import QuoteService from "./QuoteService";
import ActionLogService from "../../service/sync/ActionLogService";
import SyncConstant from "../../view/constant/SyncConstant";
import OrderResourceModel from "../../resource-model/order/OrderResourceModel";
import StatusConstant from "../../view/constant/order/StatusConstant";
import Config from "../../config/Config";
import DateTimeHelper from "../../helper/DateTimeHelper";
import CustomPrefixHelper from "../../helper/CustomPrefixHelper";
import ProductTypeConstant from "../../view/constant/ProductTypeConstant";
import NumberHelper from "../../helper/NumberHelper";
import UserService from "../user/UserService";
import ConfigHelper from "../../helper/ConfigHelper";
import SearchConstant from "../../view/constant/SearchConstant";
import OrderService from "../sales/OrderService";
import ShipmentService from "../sales/order/ShipmentService";
import InvoiceService from "../sales/order/InvoiceService";
import PaymentHelper from "../../helper/PaymentHelper";
import cloneDeep from "lodash/cloneDeep";
import AddressConstant from "../../view/constant/checkout/quote/AddressConstant";
import StockService from "../catalog/StockService";
import AddCustomerPopupConstant from "../../view/constant/customer/AddCustomerPopupConstant";
import CurrencyHelper from "../../helper/CurrencyHelper";
import ShippingConstant from "../../view/constant/ShippingConstant";
import PaymentService from "../payment/PaymentService";
import {fire} from "../../event-bus";
import CustomAttributesProcessorService from "../customer/CustomAttributesProcessorService";

export class CheckoutService extends CoreService {
    static className = 'CheckoutService';
    preOrder = false;

    /**
     * place order
     * @param quote
     * @param {Object | boolean} additionalData
     * @return {Promise<{entity_id: number}>}
     */
    async placeOrder(quote, additionalData = false) {
        quote = cloneDeep(quote);
        QuoteService.placeOrderBefore(quote);
        PaymentService.recalculatePaymentDataInQuote(quote);

        let orderResource = this.getResourceModel(OrderResourceModel);
        let order = this.convertQuoteToOrder(quote);

        /** merge additional data to order */
        if (additionalData) {
            Object.assign(order, additionalData);
        }

        if (this.getPreOrder()) {
            order.increment_id = this.getPreOrder().increment_id;
            this.clearPreOrder();
        }

        let create_shipment = quote.shipping_method || quote.is_virtual ? 0 : 1;
        let payload = { create_shipment };
        fire('service_checkout_init_create_shipment_variable_after', { payload });
        create_shipment = payload.create_shipment;
        /*let hasCustomSale = quote.items.find(item => item.product.id < 0);
         if (hasCustomSale) {
         create_shipment = 0;
         }*/
        let baseTotalDue = CurrencyHelper.roundToFloat(
            OrderService.getBaseTotalDue(order), CurrencyHelper.DEFAULT_DISPLAY_PRECISION
        );
        let create_invoice = baseTotalDue === 0 ? 1 : 0;

        let params = {
            order: PaymentHelper.filterOrderData(order),
            create_shipment: create_shipment,
            create_invoice: create_invoice
        };

        let url_api = orderResource.getResourceOnline().getPathPlaceOrder();
        await ActionLogService.createDataActionLog(
            SyncConstant.REQUEST_PLACE_ORDER, url_api, SyncConstant.METHOD_POST, params
        );

        let orderStatusInformation = this.getOrderStatus(order.is_virtual, create_shipment, create_invoice);

        order.state = orderStatusInformation.state;
        order.status = orderStatusInformation.status;

        order.search_string = this.generateSearchString(order);

        /** Add gift codes available refund **/
        order.gift_voucher_gift_codes_available_refund = order.gift_voucher_gift_codes_discount;

        if (create_shipment) {
            ShipmentService.createShipmentAfterPlaceOrder(order);
        }
        if (create_invoice) {
            order = InvoiceService.createInvoiceAfterPlaceOrder(order);
        }
        order.payments.map(payment => payment.is_paid = 1);
        fire('checkout-place-order-before', { order });
        order = await orderResource.placeOrder(order);

        let eventDataAfter = {
            order: order,
            createShipment: create_shipment,
            createInvoice: create_invoice
        };
        fire('service_checkout_place_order_after', eventDataAfter);

        return order;
    }

    /**
     * Get order status and state by isCreateShipment, isCreateInvoice and isVirtualQuote
     * @param isVirtualQuote
     * @param isCreateShipment
     * @param isCreateInvoice
     * @returns {*}
     */
    getOrderStatus(isVirtualQuote, isCreateShipment, isCreateInvoice) {
        if ((isCreateShipment && isCreateInvoice) || (isCreateInvoice && isVirtualQuote)) {
            return {
                state: StatusConstant.STATE_COMPLETE,
                status: StatusConstant.STATUS_COMPLETE
            };
        } else if ((isCreateShipment || isCreateInvoice) && !isVirtualQuote) {
            return {
                state: StatusConstant.STATE_PROCESSING,
                status: StatusConstant.STATUS_PROCESSING
            };
        } else {
            return {
                state: StatusConstant.STATE_NEW,
                status: StatusConstant.STATUS_PENDING,
            };
        }
    }

    /**
     * submit order and save to indexedDb
     *
     * @param quote
     * @return {{entity_id: number}}
     */
    convertQuoteToOrder(quote) {
        let order = this.convertQuoteDataToOrder(quote);
        let billingAddress = QuoteService.getBillingAddress(quote);
        let shippingAddress = QuoteService.getShippingAddress(quote);
        if (quote.is_virtual) {
            this.convertQuoteAddressToOrder(billingAddress, order);
        } else {
            this.convertQuoteAddressToOrder(shippingAddress, order);
        }
        order.addresses = this.convertQuoteAdrressesToOrderAddresses(order, quote.addresses);
        order.items = this.convertQuoteItemsToOrderItems(quote, order, quote.items);
        order.billing_address_id = billingAddress ? billingAddress.id : null;
        order.shipping_address_id = shippingAddress ? shippingAddress.id : null;
        order.pos_staff_id = UserService.getStaffId();
        order.pos_staff_name = UserService.getStaffName();
        order.pos_location_id = Config.location_id;
        if (quote.comment) {
            OrderService.addComment(order, quote.comment, false, true);
        }
        return order;
    }

    /**
     * Convert quote data to order
     *
     * @param {object} quote
     * @return {{entity_id: number,
     * increment_id: string,
     * status: string,
     * state: string,
     * quote_id, pos_id: *,
     * customer_email: string,
     * customer_firstname: string,
     * customer_lastname: string,
     * customer_middlename: string,
     * customer_prefix: string,
     * customer_suffix: string,
     * customer_taxvat: string,
     * customer_dob: string,
     * customer_gender: *,
     * payments: *|Array,
     * coupon_code: string,
     * store_id: *|number,
     * is_virtual: number,
     * base_to_global_rate: *|number,
     * base_to_order_rate: *,
     * base_total_paid: number,
     * total_paid: number,
     * base_total_qty_ordered: *,
     * total_qty_ordered: *|number,
     * store_to_base_rate: *|number,
     * store_to_order_rate: *,
     * customer_is_guest: *|number,
     * customer_note_notify: *|number,
     * customer_group_id: *|number,
     * email_sent: number,
     * send_email: number,
     * quote_address_id: null,
     * base_total_due: number,
     * total_due: number,
     * applied_rule_ids: string,
     * base_currency_code: string,
     * global_currency_code: string,
     * order_currency_code: string,
     * store_currency_code: string,
     * store_name: string,
     * customer_note: string,
     * total_item_count: number}}
     */
    convertQuoteDataToOrder(quote) {
        let currentTimestamp = new Date().getTime();
        let databaseCurrentTime = DateTimeHelper.getDatabaseDateTime(currentTimestamp);
        let increment_id = Config.pos_id + '-' + parseInt(currentTimestamp / 1000, 10);
        increment_id = CustomPrefixHelper.getUseCustomPrefix() ?
        CustomPrefixHelper.getCustomPrefix() + increment_id :
            increment_id;
        let deliveryDate = quote.pos_delivery_date;
        if (deliveryDate && Config.config.shipping && Config.config.shipping.delivery_date) {
            deliveryDate = DateTimeHelper.getDatabaseDateTime(new Date(deliveryDate).getTime());
        }
        let order = {
            entity_id: currentTimestamp,
            increment_id: increment_id,
            status: StatusConstant.STATUS_PENDING,
            state: StatusConstant.STATE_NEW,
            quote_id: quote.id,
            pos_id: Config.pos_id,
            customer_email: quote.customer_email ? quote.customer_email : "",
            customer_firstname: quote.customer_firstname ? quote.customer_firstname : "",
            customer_lastname: quote.customer_lastname ? quote.customer_lastname : "",
            customer_middlename: quote.customer_middlename ? quote.customer_middlename : "",
            customer_prefix: quote.customer_prefix ? quote.customer_prefix : "",
            customer_suffix: quote.customer_suffix ? quote.customer_suffix : "",
            customer_taxvat: quote.customer_taxvat ? quote.customer_taxvat : "",
            customer_dob: quote.customer_dob ? quote.customer_dob : "",
            customer_gender: quote.customer_gender,
            payments: quote.payments,
            coupon_code: quote.coupon_code ? quote.coupon_code : "",
            store_id: quote.store_id,
            is_virtual: quote.is_virtual ? 1 : 0,
            base_to_global_rate: quote.base_to_global_rate,
            base_to_order_rate: quote.base_to_quote_rate,
            base_total_paid: QuoteService.getBaseTotalPaid(quote),
            total_paid: QuoteService.getTotalPaid(quote),
            base_pos_change: QuoteService.getBasePosChange(quote),
            pos_change: QuoteService.getPosChange(quote),
            base_total_qty_ordered: quote.base_total_qty_ordered,
            total_qty_ordered: quote.items_qty,
            store_to_base_rate: quote.store_to_base_rate,
            store_to_order_rate: quote.store_to_quote_rate,
            customer_is_guest: quote.customer_is_guest,
            customer_note_notify: quote.customer_note_notify,
            customer_group_id: quote.customer_group_id,
            email_sent: 0,
            send_email: 1,
            quote_address_id: null,
            base_total_due: QuoteService.getBaseTotalDue(quote),
            total_due: QuoteService.getTotalDue(quote),
            applied_rule_ids: quote.applied_rule_ids ? quote.applied_rule_ids : "",
            base_currency_code: quote.base_currency_code ? quote.base_currency_code : "",
            global_currency_code: quote.global_currency_code ? quote.global_currency_code : "",
            order_currency_code: quote.quote_currency_code ? quote.quote_currency_code : "",
            store_currency_code: quote.store_currency_code ? quote.store_currency_code : "",
            store_name: quote.store_name ? quote.store_name : "",
            customer_note: quote.customer_note ? quote.customer_note : "",
            total_item_count: quote.items_count,
            pos_delivery_date: deliveryDate,
            created_at: databaseCurrentTime,
            updated_at: databaseCurrentTime,
            giftcodes_applied_discount_for_shipping: null,
            applied_taxes: quote.applied_taxes ? this.modifyOrderAppliedTaxes(quote.applied_taxes) : {},
            extension_attributes: quote.extension_attributes ? quote.extension_attributes : {}
        };
        order.pos_fulfill_online = quote.pos_fulfill_online ? 1 : 0;

        /** Gift card **/
        order.codes_base_discount = quote.codes_base_discount;
        order.codes_discount = quote.codes_discount;
        order.gift_voucher_gift_codes = quote.gift_voucher_gift_codes;
        order.gift_voucher_gift_codes_discount = quote.gift_voucher_gift_codes_discount;
        order.base_giftvoucher_discount_for_shipping = quote.base_giftvoucher_discount_for_shipping;
        order.giftvoucher_discount_for_shipping = quote.giftvoucher_discount_for_shipping;
        order.base_gift_voucher_discount = quote.base_gift_voucher_discount;
        order.gift_voucher_discount = quote.gift_voucher_discount;
        order.magestore_base_discount = quote.magestore_base_discount;
        order.magestore_discount = quote.magestore_discount;
        order.giftcodes_applied_discount_for_shipping = quote.giftcodes_applied_discount_for_shipping;

        order.os_pos_custom_discount_reason = quote.os_pos_custom_discount_reason;
        order.os_pos_custom_discount_type = quote.os_pos_custom_discount_type;
        order.os_pos_custom_discount_amount = quote.os_pos_custom_discount_amount;

        order.tmp_customer_id = quote.tmp_customer_id;

        return order;
    }

    /**
     * Change data of order applied taxes before save
     */
    modifyOrderAppliedTaxes(applied_taxes) {
        Object.keys(applied_taxes).forEach(tax => {
            Object.keys(applied_taxes[tax]).forEach(data => {
                if(data === 'title' && applied_taxes[tax][data]) {
                    if(typeof applied_taxes[tax][data] !== "string" && typeof applied_taxes[tax][data].value !== "undefined") {
                        applied_taxes[tax][data] = applied_taxes[tax][data].value;
                    }
                }
            });
        });
        return applied_taxes;
    }

    /**
     * Convert quote address to order data
     *
     * @param {object} quoteAdress
     * @param {object} order
     */
    convertQuoteAddressToOrder(quoteAdress, order) {
        order.weight = quoteAdress.weight;
        order.customer_id = quoteAdress.customer_id;
        order.base_discount_amount = quoteAdress.base_discount_amount;
        order.discount_amount = quoteAdress.discount_amount;
        order.base_grand_total = quoteAdress.base_grand_total;
        order.grand_total = quoteAdress.grand_total;
        order.base_shipping_amount = quoteAdress.base_shipping_amount;
        order.shipping_amount = quoteAdress.shipping_amount;
        order.base_shipping_tax_amount = quoteAdress.base_shipping_tax_amount;
        order.shipping_tax_amount = quoteAdress.shipping_tax_amount;
        order.base_subtotal = quoteAdress.base_subtotal;
        order.subtotal = quoteAdress.subtotal;
        order.base_tax_amount = quoteAdress.base_tax_amount;
        order.tax_amount = quoteAdress.tax_amount;
        order.base_shipping_discount_amount = quoteAdress.base_shipping_discount_amount;
        order.shipping_discount_amount = quoteAdress.shipping_discount_amount;
        order.base_subtotal_incl_tax = quoteAdress.base_subtotal_incl_tax;
        order.subtotal_incl_tax = quoteAdress.subtotal_incl_tax;
        order.weight = quoteAdress.weight;
        order.discount_description = Object.values(quoteAdress.discount_description).join(',');
        /** @todo create default shipping method */
        if (!order.is_virtual) {
            order.shipping_method = quoteAdress.shipping_method ?
                quoteAdress.shipping_method : ShippingConstant.STORE_PICKUP_SHIPPING_METHOD_CODE;
            order.shipping_description = quoteAdress.shipping_description ?
                quoteAdress.shipping_description : Config.config.shipping.default_shipping_title;
        }
        order.coupon_rule_name = quoteAdress.coupon_rule_name;
        order.base_shipping_incl_tax = quoteAdress.base_shipping_incl_tax;
        order.shipping_incl_tax = quoteAdress.shipping_incl_tax;
        order.base_discount_tax_compensation_amount = quoteAdress.base_discount_tax_compensation_amount;
        order.discount_tax_compensation_amount = quoteAdress.discount_tax_compensation_amount;
        order.base_shipping_discount_tax_compensation_amnt = quoteAdress.base_shipping_discount_tax_compensation_amnt;
        order.shipping_discount_tax_compensation_amount = quoteAdress.shipping_discount_tax_compensation_amount;
    }

    /**
     * Convert quote addresses to order addresses
     *
     * @param {object} order
     * @param {Array} quoteAddresses
     */
    convertQuoteAdrressesToOrderAddresses(order, quoteAddresses) {
        let orderAddresses = [];
        let addressFields = Config && Config.config && Config.config.customer_address_form;
        let requireFields = addressFields && Array.isArray(addressFields)
            && addressFields.filter(field => {
                return field.required === true
                    && !CustomAttributesProcessorService.isCustomAttribute(field.attribute_code, AddCustomerPopupConstant.POPUP_TYPE_ADDRESS);
            });
        quoteAddresses.forEach(address => {
            if (order.is_virtual && address.address_type === AddressConstant.SHIPPING_ADDRESS_TYPE) {
                return;
            }
            let orderAddress = {
                parent_id: order.entity_id,
                address_type: address.address_type,
                customer_address_id: address.customer_address_id,
                quote_address_id: address.address_id,
                region_id: address.region_id,
                customer_id: address.customer_id,
                fax: address.fax ? address.fax : "",
                region: address.region ? address.region : "",
                postcode: address.postcode ? address.postcode : "",
                lastname: address.lastname ? address.lastname : "",
                street: address.street ? address.street : [],
                city: address.city ? address.city : "",
                email: address.email ? address.email : "",
                telephone: address.telephone ? address.telephone : "",
                country_id: address.country_id ? address.country_id : "",
                firstname: address.firstname ? address.firstname : "",
                prefix: address.prefix ? address.prefix : "",
                middlename: address.middlename ? address.middlename : address.middlename,
                suffix: address.suffix ? address.suffix : "",
                company: address.company ? address.company : "",
                vat_id: address.vat_id,
                vat_is_valid: address.vat_id,
                vat_request_id: address.vat_request_id,
                vat_request_date: address.vat_request_date,
                vat_request_success: address.vat_request_success
            };
            this.putRequestFieldsToAddress(orderAddress, requireFields);
            orderAddresses.push(orderAddress);
        });
        return orderAddresses;
    }

    /**
     * Put request field to address
     *
     * @param address
     * @param requireFields
     * @returns {*}
     */
    putRequestFieldsToAddress(address, requireFields) {
        requireFields.forEach(field => {
            if (!field.attribute_code) {
                return true;
            }
            if (typeof address[field.attribute_code] !== 'undefined') {
                return true;
            }
            if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_TEXT) {
                address[field.attribute_code] = 'N/A';
            }
            if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_SELECT ||
                field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_HIDDEN ||
                field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_DATE) {
                if (Array.isArray(field.options) && field.options.length) {
                    let validOption = field.options.find(option => option.value !== '');
                    address[field.attribute_code] = validOption ? validOption.value : '';
                }
                address[field.attribute_code] = '';
            }
            if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_MULTILINE) {
                if (field.multiline_count) {
                    address[field.attribute_code] = ['N/A'];
                }
            }
            if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_BOOLEAN) {
                address[field.attribute_code] = false;
            }
        });
        return address;
    }

    /**
     * Get product stock service of product
     *
     * @param product
     * @return {*}
     */
    getProductStockService(product) {
        return StockService.getProductStockService(product);
    }

    /**
     * Convert quote items to order items
     *
     * @param {object} quote
     * @param {object} order
     * @param {Array} quoteItems
     */
    convertQuoteItemsToOrderItems(quote, order, quoteItems) {
        return quoteItems.map(item => this.convertQuoteItemToOrderItem(item, quote, order));
    }

    /**
     * Convert a quote item to an order item
     *
     * @param {object} item
     * @param {object} quote
     * @param {object} order
     */
    convertQuoteItemToOrderItem(item, quote, order) {
        let qty = item.qty;
        let parentItemId = item.parent_item_id;
        if (parentItemId) {
            let parentItem = QuoteService.getParentItem(quote, item);
            if (parentItem && parentItem.item_id &&
                (parentItem.product_type === ProductTypeConstant.BUNDLE ||
                parentItem.product_type === ProductTypeConstant.CONFIGURABLE)) {
                qty = NumberHelper.multipleNumber(qty, parentItem.qty);
            }
        }
        let product = item.product;
        let backorderQty = item.qty_backordered;
        if (product) {
            let productStockService = this.getProductStockService(product);
            let backOrder = productStockService.getBackorders(product);
            let manageStock = productStockService.isManageStock(product);
            if (backOrder && manageStock) {
                let productQty = productStockService.getProductQty(product);
                productQty = Math.max(productQty, 0);
                backorderQty = Math.max(NumberHelper.minusNumber(qty, productQty), 0);
                if (backorderQty > qty) {
                    backorderQty = qty;
                }
            }
        }

        // Add tax details for item
        let applied_taxes = {};
        if (item.applied_taxes) {
            Object.keys(item.applied_taxes).forEach((taxId) => {
                let percent = item.applied_taxes[taxId].percent;
                if (!percent) {
                    return;
                }
                let taxRates = item.applied_taxes[taxId].rates;
                let amount = item.applied_taxes[taxId].amount;
                let baseAmount = item.applied_taxes[taxId].base_amount;
                // Calculate tax for each rate
                let taxRateIds = Object.keys(taxRates);
                let index = taxRateIds.length, sumBaseRealAmount = 0.0, sumRealAmount = 0.0;
                taxRateIds.forEach(taxRateId => {
                    let applied_tax = applied_taxes[taxRateId] || {
                        item_id: item.applied_taxes[taxId].item_id,
                        tax_percent: taxRates[taxRateId].percent,
                        amount: 0.0,
                        base_amount: 0.0,
                        real_amount: 0.0,
                        real_base_amount: 0.0,
                        associated_item_id: item.applied_taxes[taxId].associated_item_id,
                        taxable_item_type: item.applied_taxes[taxId].item_type,
                        tax_code: taxRates[taxRateId].code
                    };
                    let baseRealAmount, realAmount;
                    if (index > 1) {
                        realAmount = applied_tax.tax_percent / percent * amount;
                        baseRealAmount = applied_tax.tax_percent / percent * baseAmount;
                    } else {
                        realAmount = amount - sumRealAmount;
                        baseRealAmount = baseAmount - sumBaseRealAmount;
                    }
                    realAmount = CurrencyHelper.roundToFloat(realAmount);
                    sumRealAmount = CurrencyHelper.roundToFloat(sumRealAmount + realAmount);
                    baseRealAmount = CurrencyHelper.roundToFloat(baseRealAmount);
                    sumBaseRealAmount = CurrencyHelper.roundToFloat(sumBaseRealAmount + baseRealAmount);

                    applied_tax.amount = CurrencyHelper.roundToFloat(
                        applied_tax.amount + realAmount
                    );
                    applied_tax.base_amount = CurrencyHelper.roundToFloat(
                        applied_tax.base_amount + baseRealAmount
                    );
                    applied_tax.real_amount = CurrencyHelper.roundToFloat(
                        applied_tax.real_amount + realAmount
                    );
                    applied_tax.real_base_amount = CurrencyHelper.roundToFloat(
                        applied_tax.real_base_amount + baseRealAmount
                    );

                    applied_taxes[taxRates[taxRateId].code] = applied_tax;
                    index--;
                });
            });
        }

        let orderItem = {
            item_id: parseFloat(item.item_id),
            order_id: order.entity_id,
            parent_item_id: parseFloat(item.parent_item_id),
            quote_item_id: parseFloat(item.item_id),
            store_id: item.store_id,
            product_id: item.product_id,
            product_type: item.product_type,
            product_options: item.product_options ? JSON.stringify(item.product_options) : "",
            weight: item.weight,
            is_virtual: item.is_virtual,
            sku: item.sku,
            name: item.name,
            description: item.description,
            applied_rule_ids: item.applied_rule_ids,
            additional_data: item.additional_data,
            is_qty_decimal: item.is_qty_decimal,
            no_discount: item.no_discount,
            qty_backordered: backorderQty,
            qty_ordered: qty,
            // qty_shipped: quote.create_shipment ? qty : 0,
            base_cost: item.base_cost,
            price: item.price,
            base_price: item.base_price,
            original_price: item.original_price,
            base_original_price: item.base_original_price,
            tax_percent: item.tax_percent,
            tax_amount: item.tax_amount,
            base_tax_amount: item.base_tax_amount,
            discount_percent: item.discount_percent,
            discount_amount: item.discount_amount,
            base_discount_amount: item.base_discount_amount,
            row_total: item.row_total,
            base_row_total: item.base_row_total,
            base_tax_before_discount: item.base_tax_before_discount,
            tax_before_discount: item.tax_before_discount,
            price_incl_tax: item.price_incl_tax,
            base_price_incl_tax: item.base_price_incl_tax,
            row_total_incl_tax: item.row_total_incl_tax,
            base_row_total_incl_tax: item.base_row_total_incl_tax,
            discount_tax_compensation_amount: item.discount_tax_compensation_amount,
            base_discount_tax_compensation_amount: item.base_discount_tax_compensation_amount,
            free_shipping: !item.free_shipping ? 0 : (!isNaN(item.free_shipping) ? 1 : +item.free_shipping),
            weee_tax_applied: item.weee_tax_applied,
            weee_tax_applied_amount: item.weee_tax_applied_amount,
            weee_tax_applied_row_amount: item.weee_tax_applied_row_amount,
            weee_tax_disposition: item.weee_tax_disposition,
            weee_tax_row_disposition: item.weee_tax_row_disposition,
            base_weee_tax_applied_amount: item.base_weee_tax_applied_amount,
            base_weee_tax_applied_row_amnt: item.base_weee_tax_applied_row_amnt,
            base_weee_tax_disposition: item.base_weee_tax_disposition,
            base_weee_tax_row_disposition: item.base_weee_tax_row_disposition,
            pos_base_original_price_excl_tax: item.pos_base_original_price_excl_tax,
            pos_original_price_excl_tax: item.pos_original_price_excl_tax,
            pos_base_original_price_incl_tax: item.pos_base_original_price_incl_tax,
            pos_original_price_incl_tax: item.pos_original_price_incl_tax,

            /** Gift card */
            base_gift_voucher_discount: item.base_gift_voucher_discount,
            gift_voucher_discount: item.gift_voucher_discount,
            giftcodes_applied: item.giftcodes_applied,
            magestore_base_discount: item.magestore_base_discount,
            magestore_discount: item.magestore_discount,

            /** Custom price reason */
            os_pos_custom_price_reason: item.os_pos_custom_price_reason,

            applied_taxes: applied_taxes ? applied_taxes : null,

            pos_custom_price: item.custom_price
        }
        /* Magento save qty_backordered = null in 2.3.x */
        orderItem.qty_backordered = null;
        return orderItem;
    }

    /**
     * generate order's search string
     * @param order
     * @return {string}
     */
    generateSearchString(order) {
        let searchString = order.increment_id;
        searchString += " " + order.customer_email + " " + order.customer_firstname + " " + order.customer_lastname;
        order.addresses.map(address => searchString += " " + address.telephone);
        order.items.map(item => {
            if (!item.parent_item_id) {
                let searchStringProduct = item.name + " " + item.sku;
                let configBarcode = ConfigHelper.getConfig(SearchConstant.BARCODE_CONFIG);
                if (configBarcode && configBarcode !== 'name' && configBarcode !== 'sku') {
                    searchStringProduct += " " + item[configBarcode];
                }
                searchString += " " + searchStringProduct;
            }
            return searchString;
        });

        order.payments && order.payments.forEach(payment => {
            if (!payment.reference_number) {
                return;
            }

            searchString += " " + payment.reference_number;
        });

        return searchString;
    }

    /**
     *  need for online payment
     * @param quote
     */
    generatePreOrder(quote) {
        this.preOrder = this.convertQuoteToOrder(quote);
    }

    /**
     *
     * @param increment
     */
    setIncrementPreOrder(increment) {
        if (!this.preOrder) {
            return;
        }

        this.preOrder.increment_id = increment;
    }

    /**
     *  need for online payment
     */
    clearPreOrder() {
        this.preOrder = false;
    }

    /**
     *  need for online payment
     * @return {*}
     */
    getPreOrder() {
        return this.preOrder;
    }
}

/**
 *
 * @type {CheckoutService}
 */
let checkoutService = ServiceFactory.get(CheckoutService);

export default checkoutService;
