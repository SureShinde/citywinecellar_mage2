import {AbstractTotalService} from "./AbstractService";
import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import PriceService from "../../../catalog/product/PriceService";
import QuoteItemService from "../ItemService";
import AddressService from "../AddressService";
import ProductTypeConstant from "../../../../view/constant/ProductTypeConstant";

export class QuoteTotalSubtotalService extends AbstractTotalService {
    static className = 'QuoteTotalSubtotalService';

    code = 'subtotal';

    /**
     * Collect address subtotal
     *
     * @param {object} quote
     * @param {object} address
     * @param {object} total
     * @return {QuoteTotalSubtotalService}
     */
    collect(quote, address, total) {
        super.collect(quote, address, total);
        let virtualAmount = 0,
            baseVirtualAmount = 0;
        address.total_qty = 0;
        let isVirtual = this.isVirtual(quote);
        /**
         * Only collect subtotal for billing address if quote is virtual
         * Or for shipping address if quote is not virtual
         */
        if ((isVirtual && AddressService.isBillingAddress(address)) ||
            (!isVirtual && AddressService.isShippingAddress(address))
        ) {
            /**
             * Keep only item that has qty for quote items
             */
            quote.items = quote.items.filter((item) => {
                /**
                 * _initItem also collect subtotal, init item row total
                 */
                if (this._initItem(quote, address, item) && item.qty > 0) {
                    if (item.product.is_virtual) {
                        /**
                         * Collect virtual amount, will be used for shipping package
                         */
                        virtualAmount += item.row_total;
                        baseVirtualAmount += item.base_row_total;
                    }
                    return true;
                }
                return false;
            });
        }
        total.virtual_amount = virtualAmount;
        total.base_virtual_amount = baseVirtualAmount;
        return this;
    }

    /**
     * Address item initialization & collect subtotal
     *
     * @param {object} quote
     * @param {object} address
     * @param {object} item
     * @return {boolean}
     * @private
     */
    _initItem(quote, address, item) {
        let product = item.product;
        if (!product) {
            return false;
        }
        item.converted_price = null;
        let originalPrice = item.product.price;
        if (item.product_type === ProductTypeConstant.CONFIGURABLE) {
            /**
             * Calculate originalPrice by children for configurable product
             */
            let childItem = quote.items.find(x => x.parent_item_id === item.item_id);
            if (childItem) {
                originalPrice = childItem.product.price;
            }
        }

        product.customer_group_id = address.customer_id;
        if (item.parent_item_id && QuoteItemService.isChildrenCalculated(item, quote)) {
            /**
             * Calculate row totals for Bundle children items that are children calculated
             */
            /*let pricePrice = PriceService.getPriceService(item.product).getChildFinalPrice(
                null,
                null,
                item.product,
                item.qty,
                quote
            );
            this._calculateRowTotal(quote, item, pricePrice, originalPrice);*/
            let parentItem = QuoteItemService.getParentItem(quote, item);
            let pricePrice = PriceService.getPriceService(parentItem.product).getChildFinalPrice(
                parentItem.product,
                parentItem.qty,
                item.product,
                item.qty,
                quote,
                item
            );
            this._calculateRowTotal(quote, item, pricePrice, originalPrice);
        } else if (!item.parent_item_id) {
            /**
             * Only calculate Subtotal from parent items
             */
            let pricePrice = PriceService.getPriceService(item.product).getFinalPrice(item.qty, item.product, quote, item);

            this._calculateRowTotal(quote, item, pricePrice, originalPrice);
            this._addAmount(item.row_total);
            this._addBaseAmount(item.base_row_total);
            address.total_qty = address.total_qty ? address.total_qty + item.qty : item.qty;
        }
        return true;
    }

    /**
     * Processing calculation of row price for address item
     *
     * @param {object} quote
     * @param {object} item
     * @param {number} finalPrice
     * @param {number} originalPrice
     */
    _calculateRowTotal(quote, item, finalPrice, originalPrice) {
        if (!originalPrice) {
            originalPrice = finalPrice;
        }
        item.price = finalPrice;
        item.base_original_price = originalPrice;
        QuoteItemService.getOriginalPrice(item);
        QuoteItemService.calcRowTotal(item, quote);
        return this;
    }
}

let quoteTotalSubtotalService = ServiceFactory.get(QuoteTotalSubtotalService);

export default quoteTotalSubtotalService;
