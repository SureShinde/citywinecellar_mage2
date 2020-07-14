import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import {AbstractAddProductService} from "./AbstractService";
import ItemService from "../ItemService";
import i18n from "../../../../config/i18n";
import NumberHelper from "../../../../helper/NumberHelper";
import {toast} from "react-toastify";

export class GiftCardService extends AbstractAddProductService {
    static className = 'GiftCardService';


    /**
     * Add product to quote
     *
     * @param {object} quote
     * @param {object} data
     * @return {*}
     */
    addProduct(quote, data) {
        let items = this.getItemsByProductId(quote, data.product.id);
        let updateItem = null;
        let totalItemsQtyIncart = this.getProductTotalItemsQtyInCart(items, quote);
        let addedItemId = null;
        if (items && items.length > 0) {
            updateItem = items.find(item => {

                if (!item.parent_item_id && !item.product_options) return true;
                if (!item.product_options || !data.product_options) return false;
                if (!item.product_options.info_buyRequest || !data.product_options.info_buyRequest) return false;

                return this.isSameOptions(
                    item.product_options.info_buyRequest,
                    data.product_options.info_buyRequest
                );

            });
            if (!updateItem) {
                data.qty = this.getAddQty(data.product, data.qty);
            }
        } else {
            data.qty = this.getAddQty(data.product, data.qty);
        }
        let totalQty = parseFloat(data.qty);
        totalQty = NumberHelper.addNumber(totalQty, totalItemsQtyIncart);
        let validateQty = this.validateQty(data.product, data.qty, totalQty);
        if (!validateQty.success) {
            toast.error(
                i18n.translator.translate(validateQty.message),
                {
                    className: 'wrapper-messages messages-warning'
                }
            );
            return validateQty;
        }

        if (!updateItem) {
            let item = {...ItemService.createItem(data.product, parseFloat(data.qty)), quote_id: quote.id};
            if (data.product_options) {
                item.product_options = data.product_options;
            }
            if (data.has_custom_price) {
                item = {
                    ...item,
                    custom_price: data.custom_price,
                    os_pos_custom_price_reason: data.os_pos_custom_price_reason,
                    customPriceType: data.customPriceType,
                }
                if (data.hasCustomDiscountPerItem) {
                    item = {
                        ...item,
                        customPriceDiscountValue: data.customPriceDiscountValue,
                        customPriceDiscountType: data.customPriceDiscountType,
                        unitCustomPriceDiscount: data.unitCustomPriceDiscount,
                        os_pos_custom_price_reason: data.customDiscountReason,
                        has_custom_discount_price: true,
                        original_price_from_starting: data.originalPriceFromStarting,
                    }
                }
            }
            quote.items.push(item);
            addedItemId = item.item_id;
        } else {
            quote.items.forEach(item => {
                if (item.item_id === updateItem.item_id) {
                    item.product = data.product;
                    item.qty = item.qty + data.qty;
                    addedItemId = updateItem.item_id;
                }
            })
        }
        return {
            success: true,
            quote: quote,
            added_item_id: addedItemId
        };
    }

}

/** @type {GiftCardService} */
let giftCardService = ServiceFactory.get(GiftCardService);

export default giftCardService;