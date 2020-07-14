import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import {AbstractAddProductService} from "./AbstractService";
/*import {toast} from "react-toastify";*/
import ItemService from "../ItemService";

/*import i18n from "../../../../config/i18n";*/

export class AddConfigurableProductService extends AbstractAddProductService {
    static className = 'AddConfigurableProductService';

    /**
     * Add configurable product
     *
     * @param quote
     * @param data
     * @return {*}
     */
    addProduct(quote, data) {
        let addedItemId = null;
        let configProduct = data.product;
        let childrenProduct = data.children_product;
        let parentItem = null;
        let childrenItem = null;
        let parentItems = this.getItemsByProductId(quote, configProduct.id);
        if (parentItems && parentItems.length > 0) {
            parentItem = parentItems.find(parentItem => {
                if (typeof parentItem.custom_price !== 'undefined' && parentItem.custom_price !== null) return false;
                let childrenItems = this.getChildrenItems(quote, parentItem);
                if (childrenItems && childrenItems.length > 0) {
                    childrenItem = childrenItems[0];
                    if (childrenItem.product_id === childrenProduct.id) {
                        return !(parentItem.product_options.info_buyRequest.options &&
                        !this.isSameOptions(
                            parentItem.product_options.info_buyRequest.options,
                            data.product_options.info_buyRequest.options
                        ));
                    }
                }
                return false;
            });
        }
        /*qtyValidate += this.getProductTotalItemsQtyInCart(childItems, quote);
         if (parentItem && parentItem.item_id) {
         /!** todo: This will be change when custom option function is developed *!/
         /!* Add more configurable product with same children product id and difference custom option*!/
         } else {
         let productStockService = this.getProductStockService(childrenProduct);
         let minSaleQty = productStockService.getMinSaleQty(childrenProduct);
         while (minSaleQty > qtyValidate) {
         qtyValidate = 0;
         let qtyIncrement = productStockService.getQtyIncrement(data.product);
         while (minSaleQty > qtyValidate) {
         qtyValidate += qtyIncrement;
         }
         }
         }
         let validateQty = this.validateQty(childrenProduct, data.qty, qtyValidate);
         if (!validateQty.success) {
         toast.error(
         i18n.translator.translate(validateQty.message),
         {
         className: 'wrapper-messages messages-warning'
         }
         );
         return validateQty;
         }*/

        if (!parentItem || !parentItem.item_id) {
            parentItem = {...ItemService.createItem(configProduct, parseFloat(data.qty)), quote_id: quote.id};
            parentItem.product_options = data.product_options;
            parentItem.has_children = true;
            parentItem.weight = childrenProduct.weight;

            /* If child product is virtual product => parent product is virtual too */
            if(childrenProduct.is_virtual) {
                parentItem.is_virtual = 1;
            }

            childrenItem = {...ItemService.createItem(childrenProduct, 1), quote_id: quote.id};
            childrenItem.item_id = parentItem.item_id + "1";
            childrenItem.parent_item_id = parentItem.item_id;
            childrenItem.product_options = {
                info_buyRequest: data.product_options.info_buyRequest
            };
            parentItem.sku = childrenItem.sku;
            if (data.has_custom_price) {
                parentItem = {
                    ...parentItem,
                    custom_price: data.custom_price,
                    os_pos_custom_price_reason: data.os_pos_custom_price_reason,
                    customPriceType: data.customPriceType,
                }
                if (data.hasCustomDiscountPerItem) {
                    parentItem = {
                        ...parentItem,
                        customPriceDiscountValue: data.customPriceDiscountValue,
                        customPriceDiscountType: data.customPriceDiscountType,
                        unitCustomPriceDiscount: data.unitCustomPriceDiscount,
                        os_pos_custom_price_reason: data.customDiscountReason,
                        has_custom_discount_price: true,
                        original_price_from_starting: data.originalPriceFromStarting,
                    }
                }
            }
            quote.items.push(parentItem);
            quote.items.push(childrenItem);
            addedItemId = parentItem.item_id;
        } else {
            parentItem.qty = parentItem.qty + parseFloat(data.qty);
            addedItemId = parentItem.item_id;
        }
        return {
            success: true,
            quote: quote,
            added_item_id: addedItemId
        };
    }
}

/** @type AddConfigurableProductService */
let addConfigurableProductService = ServiceFactory.get(AddConfigurableProductService);

export default addConfigurableProductService;
