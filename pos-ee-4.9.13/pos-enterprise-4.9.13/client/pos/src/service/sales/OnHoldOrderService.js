import {AbstractOrderService} from "./AbstractService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import StatusConstant from "../../view/constant/order/StatusConstant";
import QuoteService from "../checkout/QuoteService";
import SyncConstant from "../../view/constant/SyncConstant";
import ActionLogService from "../sync/ActionLogService";
import cloneDeep from 'lodash/cloneDeep';
import CheckoutService from "../checkout/CheckoutService";
import QueryService from "../QueryService";
import ProductService from "../catalog/ProductService";
import ProductTypeConstant from "../../view/constant/ProductTypeConstant";
import OrderService from "./OrderService";
import MultiCartService from "../MultiCartService";
import MultiCheckoutAction from "../../view/action/MultiCheckoutAction";
import QuoteAction from "../../view/action/checkout/QuoteAction";
import CustomerService from "../customer/CustomerService";
import AddProductService from "../checkout/quote/AddProductService";
import CustomSaleConstant from "../../view/constant/custom-sale/CustomSaleConstant";
import Config from "../../config/Config";
import DateTimeHelper from "../../helper/DateTimeHelper";
import i18n from "../../config/i18n";
import UserService from "../user/UserService";
import {fire} from "../../event-bus";
import {GiftCardProductHelper} from "../../helper/GiftCardProductHelper";
import GiftCardProductConstant from "../../view/constant/catalog/GiftCardProductConstant";

export class OnHoldOrderService extends AbstractOrderService {
    static className = 'OnHoldOrderService';

    /**
     * hold order
     * @param quote
     * @return {Promise<{entity_id: number}>}
     */
    async holdOrder(quote) {
        // remove payment info before hold order
        quote.payments = [];

        QuoteService.placeOrderBefore(quote);
        let order = CheckoutService.convertQuoteToOrder(quote);

        let eventDataBefore = {order: order};
        // Event hold order before
        fire('hold_order_process_before', eventDataBefore);
        order = eventDataBefore.order;

        order.state = StatusConstant.STATE_HOLDED;
        order.status = StatusConstant.STATUS_HOLDED;
        let params = {
            order: cloneDeep(order)
        };

        let url_api = OrderService.getResourceModel().getResourceOnline().getPathHoldOrder();
        await ActionLogService.createDataActionLog(
            SyncConstant.REQUEST_HOLD_ORDER, url_api, SyncConstant.METHOD_POST, params
        );
        order.search_string = CheckoutService.generateSearchString(order);
        await OrderService.saveToDb([order], true);

        return order;
    }

    /**
     * un-hold order
     * @param order
     * @return {Promise<*>}
     */
    async cancelOrder(order) {
        if (!order.status_histories) {
            order.status_histories = [];
        }
        let createAt = DateTimeHelper.getDatabaseDateTime();
        let entity_id = OrderService.getNextCommentEntityId(order);
        let comment = {
            comment: order.reorder
                ? i18n.translator.translate('Order had been checked out by staff {{name}}', {name: UserService.getStaffName()})
                : i18n.translator.translate('Order had been canceled by staff {{name}}', {name: UserService.getStaffName()}),
            created_at: createAt,
            entity_id: entity_id,
            is_visible_on_front: 1
        };
        order.status_histories.unshift(comment);

        order.state = StatusConstant.STATE_CANCELED;
        if (Config.orderStatus) {
            let statusData = Config.orderStatus.find(item => (item.state === order.state) && (item.is_default === '1'));
            if (statusData) {
                order.status = statusData.status
            }
        }
        let params = {
            increment_id: order.increment_id,
            comment: comment
        };
        let url_api = OrderService.getResourceModel().getResourceOnline().getPathUnHoldOrder();
        await ActionLogService.createDataActionLog(
            SyncConstant.REQUEST_CANCEL_ORDER, url_api, SyncConstant.METHOD_POST, params
        );
        await OrderService.saveToDb([order], true);
        return order;
    }


    /**
     * delete order
     * @param order
     * @return {Promise<*>}
     */
    async deleteOrder(order) {
        let params = {
            increment_id: order.increment_id
        };

        let url_api = OrderService.getResourceModel().getResourceOnline().getPathDeleteOrder();
        url_api += '/' + order.increment_id;
        await ActionLogService.createDataActionLog(
            SyncConstant.REQUEST_DELETE_ORDER, url_api, SyncConstant.METHOD_DELETE, params
        );
        await OrderService.deleteItems([order.increment_id]);
        return order;
    }

    /**
     * get data of custom product from item data
     * @param item
     * @return {{category_ids: string, config_option: Array, custom_options: Array, description: string, enable_qty_increments: null, is_options: null, is_qty_decimal: number, options: number, tier_prices: Array, json_config: null, sku: string, type_id: string, id: null|number, name: *, is_salable: number, is_virtual: boolean, maximum_qty: number, minimum_qty: number, qty_increment: number, qty_increments: null, status: number, stocks: *[]}}
     */
    getCustomProductData(item) {
        let product = {
            category_ids: "",
            config_option: [],
            custom_options: [],
            description: "",
            enable_qty_increments: null,
            is_options: null,
            is_qty_decimal: 0,
            options: 0,
            tier_prices: [],
            json_config: null,
            sku: CustomSaleConstant.SKU,
            type_id: ProductTypeConstant.SIMPLE,
            id: item.product_id,
            name: item.name,
            price: item.base_price,
            is_salable: 1,
            is_virtual: false,
            maximum_qty: 100000000000000000,
            minimum_qty: 1,
            qty_increment: 0,
            qty_increments: null,
            status: 1,
            stocks: [
                {
                    backorders: 0,
                    enable_qty_increments: 0,
                    is_in_stock: true,
                    is_qty_decimal: false,
                    manage_stock: true,
                    max_sale_qty: 100000000000000000,
                    min_qty: 0,
                    min_sale_qty: 1,
                    qty_increments: "0.0000",
                    qty: 100000000000000000,
                    product_id: item.product_id,
                    sku: CustomSaleConstant.SKU,
                    updated_time: null,
                    use_config_backorders: true,
                    use_config_enable_qty_inc: 1,
                    use_config_manage_stock: true,
                    use_config_max_sale_qty: true,
                    use_config_min_qty: true,
                    use_config_min_sale_qty: true,
                    use_config_qty_increments: 1,
                }
            ]
        };

        let productOptions = item.product_options;
        productOptions = productOptions ? JSON.parse(productOptions) : null;
        if (productOptions && productOptions.tax_class_id) {
            product.tax_class_id = productOptions.tax_class_id;
        }

        return product;
    }

    /**
     * get product list from order
     * @param order
     * @return {Promise<void>}
     */
    async getProductList(order) {
        let idFieldName = Config.dataTypeMode[SyncConstant.TYPE_PRODUCT] === SyncConstant.ONLINE_MODE ? 'entity_id' : 'id';
        let fields = [];

        for (let item of order.items) {
            if (item.parent_item_id || item.sku === CustomSaleConstant.SKU) {
                continue;
            }

            let productId = item.product_id;
            let productOptions = item.product_options;
            productOptions = productOptions ? JSON.parse(productOptions) : null;
            let isGrouped = productOptions
                && productOptions.super_product_config
                && productOptions.super_product_config.product_type === ProductTypeConstant.GROUPED;
            if (isGrouped) {
                productId = productOptions.super_product_config.product_id;
            }

            fields.push([idFieldName, productId, 'eq']);
        }

        let queryService = QueryService.reset();
        queryService.setOrder('name');
        queryService.addFieldToFilter(fields);
        if (Config.dataTypeMode[SyncConstant.TYPE_PRODUCT] === SyncConstant.OFFLINE_MODE) {
            queryService.addQueryString('');
        }

        let response = await ProductService.getProductList(queryService);
        if (response && response.items && response.items[0] && !response.items[0].stocks) {
            let productIds = ProductService.getProductIdsFromResponse(response);
            response = await ProductService.getStocksDataForResponse(response, productIds);
            response = await ProductService.getCatalogRulePricesDataForResponse(response, productIds);
        }
        return response.items;
    }

    /**
     * get products data from order
     * @param order
     */
    async getProductsData(order) {
        let products = [];
        let productList = await this.getProductList(order);

        for (let item of order.items) {
            if (item.parent_item_id) {
                continue;
            }

            if (item.sku === CustomSaleConstant.SKU) {
                products.push({
                    itemData: item,
                    product: this.getCustomProductData(item)
                });
                continue;
            }

            let productId = item.product_id;
            let productOptions = item.product_options;
            productOptions = productOptions ? JSON.parse(productOptions) : null;
            let isGrouped = productOptions
                && productOptions.super_product_config
                && productOptions.super_product_config.product_type === ProductTypeConstant.GROUPED;
            if (isGrouped) {
                productId = productOptions.super_product_config.product_id;
            }

            let product = productList.find(x => x.id === productId);

            let data = {
                itemData: item,
                product: product
            };

            if (isGrouped) {
                data.product = product.children_products.find(child => child.id === item.product_id);
            }

            products.push(data);
        }
        return products;
    }

    /**
     * check product
     * @param product
     * @param itemData
     * @param order
     * @return {boolean}
     */
    checkProduct(product, itemData, order) {
        if (!product) {
            return false;
        }
        let totalQty = parseFloat(itemData.qty_ordered);

        if (product.type_id === ProductTypeConstant.CONFIGURABLE) {
            let childItem = order.items.find(item => item.parent_item_id === itemData.item_id);
            product = product.children_products.find(child => child.id === childItem.product_id);
        }

        let result = AddProductService.getAddProductService(product).validateQty(product, 0, totalQty);
        return result.success;
    }

    /**
     * check products before reorder
     * @param order
     * @return {boolean}
     */
    async checkProducts(order) {
        let products = await this.getProductsData(order);
        for (let productData of products) {
            productData.canAdd = true;
            let product = productData.product;
            let itemData = productData.itemData;
            if (!product) {
                productData.canAdd = false;
                continue;
            }
            if (product.type_id === ProductTypeConstant.BUNDLE) {
                let childItems = order.items.filter(item => item.parent_item_id === itemData.item_id);
                childItems.map(item => {
                    let childProduct = product.children_products.find(child => child.id === item.product_id);
                    if (!this.checkProduct(childProduct, item, order)) {
                        productData.canAdd = false;
                    }
                    return null;
                });
            } else {
                if (!this.checkProduct(product, itemData, order)) {
                    productData.canAdd = false;
                }
            }
        }
        return products;
    }

    /**
     * check that all items of order can be added to cart
     * @param products
     * @return {boolean}
     */
    isCheckoutAble(products) {
        for (let productData of products) {
            if (!productData.canAdd) {
                return false;
            }
        }
        return true;
    }

    /**
     * prepare config product data
     * @param data
     * @param productData
     * @param order
     */
    prepareConfigProduct(data, productData, order) {
        let childItem = order.items.find(item => item.parent_item_id === productData.itemData.item_id);
        let childProduct = data.product.children_products.find(child => child.id === childItem.product_id);
        data.children_product = childProduct;
        data.product.custom_option = {...data.product.custom_option, simple_product: childProduct};
    }

    /**
     * prepare giftcard product data
     * @param data
     * @param productData
     * @param order
     */
    prepareGiftcardProduct(data, productData, order) {
        let product = cloneDeep(data.product);
        let priceConfig = GiftCardProductHelper.getPriceConfig(product);
        let amount = data.product_options.info_buyRequest.amount;
        let giftType = priceConfig[GiftCardProductConstant.GIFT_TYPE];
        let priceType = priceConfig[GiftCardProductConstant.GIFT_PRICE_TYPE];
        let customPrice = amount;

        if (priceType === GiftCardProductConstant.GIFT_PRICE_TYPE_PERCENT) {
            customPrice *= priceConfig[GiftCardProductConstant.GIFT_PRICE] / 100;
        }

        if (
            priceType === GiftCardProductConstant.GIFT_PRICE_TYPE_FIXED
            && giftType === GiftCardProductConstant.GIFT_TYPE_DROPDOWN_VALUES
        ) {
            let prices = priceConfig[GiftCardProductConstant.GIFT_PRICE] + '';
            let values = priceConfig[GiftCardProductConstant.GIFT_DROPDOWN_VALUE] + '';
            customPrice = prices.split(',')[
                values.split(',').indexOf('' + amount)
                ];
            if (undefined === customPrice) {
                customPrice = amount;
            }
        }

        product.price = customPrice;
        data.product = product;
        productData.product = product;
    }

    /**
     * prepare bundle product data
     * @param data
     * @param productData
     * @param order
     */
    prepareBundleProduct(data, productData, order) {
        let childItems = order.items.filter(item => item.parent_item_id === productData.itemData.item_id);
        let childrenData = childItems.map(item => {
            return {
                product: productData.product.children_products.find(child => child.id === item.product_id),
                itemData: item
            };
        });
        let childrens = [];
        let customOptions = {
            bundle_option_ids: [],
            bundle_selection_ids: [],
            bundle_identity: [data.product.id]
        };

        let bundleOptions = data.product_options.bundle_options;
        let infoBuyRequest = data.product_options.info_buyRequest;
        Object.keys(bundleOptions).map(key => {
            bundleOptions[key].value.map((value, index) => {
                customOptions.bundle_option_ids.push(bundleOptions[key].option_id);
                let selectionValue = infoBuyRequest.bundle_option[key];
                if (Array.isArray(selectionValue)) {
                    selectionValue = selectionValue[index];
                }
                customOptions['selection_qty_' + selectionValue] = value.qty;
                customOptions.bundle_selection_ids.push(selectionValue);
                customOptions.bundle_identity.push(selectionValue);
                customOptions.bundle_identity.push(value.qty);
                return null;
            });
            return null;
        });

        childrenData.map(child => {
            let childProductOptions = JSON.parse(child.itemData.product_options);
            let qty = JSON.parse(childProductOptions.bundle_selection_attributes).qty;
            childrens.push({
                product: child.product,
                qty: qty,
                product_options: childProductOptions
            });

            customOptions['product_qty_' + child.product.id] = qty;
            return child;
        });

        customOptions.bundle_identity = customOptions.bundle_identity.join('_');
        data.product.custom_options = customOptions;
        data.childrens = childrens;

        if (data.product.extension_attributes && data.product.extension_attributes.bundle_product_options) {
            data.product.extension_attributes.bundle_product_options.map(option => {
                option.product_links.map(product_link => {
                    product_link.product = data.product.children_products.find(child => child.sku === product_link.sku);
                    let childData = data.childrens.find(child => child.product.sku === product_link.sku);
                    if (childData) {
                        product_link.qty = childData.qty;
                    }
                    return null;
                });
                return null;
            })
        }
    }

    /**
     * add products to cart
     * @param products
     * @param order
     * @param quote
     * @param applyCustomPrice
     * @return {*}
     */
    addProductsToCart(products, order, quote, applyCustomPrice) {
        for (let productData of products) {
            if (!productData.canAdd) {
                continue;
            }

            let product = productData.product;
            let itemData = productData.itemData;
            if (itemData.parent_item_id) {
                continue;
            }
            let productOptions = productData.itemData.product_options;
            productOptions = productOptions ? JSON.parse(productOptions) : null;
            let data = {
                product: product,
                qty: itemData.qty_ordered,
                has_custom_price: false
            };
            if (productOptions) {
                if (productOptions.info_buyRequest && productOptions.info_buyRequest.options) {
                    let infoBuyOptions = productOptions.info_buyRequest.options;
                    product.custom_options.option_ids = Object.keys(infoBuyOptions).join(',');
                    Object.keys(infoBuyOptions).forEach(optionId => {
                        product.custom_options['option_' + optionId] = infoBuyOptions[optionId];
                    });
                }
                data.product_options = productOptions;
            }
            if (productData.product.type_id === ProductTypeConstant.GIFT_CARD) {
                this.prepareGiftcardProduct(data, productData, order);
            } else if (productData.product.type_id === ProductTypeConstant.CONFIGURABLE) {
                this.prepareConfigProduct(data, productData, order);
            } else if (productData.product.type_id === ProductTypeConstant.BUNDLE) {
                this.prepareBundleProduct(data, productData, order);
            }
            // if (applyCustomPrice && QuoteItemService.showOriginalPrice(itemData, quote, data.product)) {
            if (applyCustomPrice && typeof itemData.pos_custom_price !== 'undefined' && itemData.pos_custom_price !== null) {
                let additional_data = JSON.parse(itemData.additional_data || '{}');
                if (additional_data.hasCustomDiscountPerItem) {
                    data = {
                        ...data,
                        customPriceDiscountValue: additional_data.customPriceDiscountValue,
                        customPriceDiscountType: additional_data.customPriceDiscountType,
                        unitCustomPriceDiscount: additional_data.unitCustomPriceDiscount,
                        customDiscountReason: additional_data.customDiscountReason,
                        hasCustomDiscountPerItem: additional_data.hasCustomDiscountPerItem,
                        originalPriceFromStarting: additional_data.originalPriceFromStarting,
                    }
                }
                data = {
                    ...data,
                    has_custom_price: true,
                    custom_price: itemData.price,
                    os_pos_custom_price_reason: itemData.os_pos_custom_price_reason ? itemData.os_pos_custom_price_reason : '',
                    customPriceType: additional_data.customPriceType,
                }
            }

            let response = QuoteService.addProduct(quote, data);
            if (response.success) {
                quote = response.quote;
            }
        }
        return quote;
    }

    /**
     *  change cart's customer
     * @param order
     * @param store
     */
    async changeCustomer(order, store) {
        let quote = store.getState().core.checkout.quote;
        if (order.customer_id) {
            let customer = await CustomerService.getById(order.customer_id);
            if (customer) {
                return QuoteService.changeCustomer(quote, customer);
            }
        }
        return quote;
    }

    /**
     * reorder
     * @param order
     * @param products
     * @param history
     * @param store
     * @param applyCustomPrice
     * @return {Promise<void>}
     */
    async reorder(order, products, history, store, applyCustomPrice = false) {
        store.getState().core.multiCheckout.activeCart && await MultiCartService.updateActiveCartFromStore(store);
        await store.dispatch(MultiCheckoutAction.selectCartResult(false));
        await store.dispatch(QuoteAction.removeCart());
        await MultiCartService.searchByCurrentPos();
        await MultiCartService.addCartFromStore(store);
        history.replace('/checkout');
        setTimeout(async() => {
            let quote = await this.changeCustomer(order, store);
            quote = this.addProductsToCart(products, order, quote, applyCustomPrice);
            store.dispatch(QuoteAction.setQuote(quote));
            return null;
        }, 500);
    }
}

/** @type OnHoldOrderService */
let onHoldOrderService = ServiceFactory.get(OnHoldOrderService);

export default onHoldOrderService;
