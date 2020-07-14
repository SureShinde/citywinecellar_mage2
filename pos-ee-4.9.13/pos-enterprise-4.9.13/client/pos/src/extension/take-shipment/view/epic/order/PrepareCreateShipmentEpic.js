import ShippingConstant from "../../constant/order/ShippingConstant";
import ShippingAction from "../../action/order/ShippingAction";
import ProductService from "../../../../../service/catalog/ProductService";
import StockService from "../../../../../service/catalog/StockService";
import OrderItemService from "../../../../../service/sales/order/OrderItemService";
import LocationService from "../../../../../service/LocationService";
import ProductTypeConstant from "../../../../../view/constant/ProductTypeConstant";
import CustomSaleConstant from "../../../../../view/constant/custom-sale/CustomSaleConstant";

/**
 * load product qty to order shipping
 *
 * @param action$
 * @returns {Observable<any>}
 */
export default function PrepareCreateShipmentEpic(action$) {
    return action$.ofType(ShippingConstant.ORDER_CREATE_SHIPMENT_LOAD_PRODUCT_QTYS_BEGIN)
        .mergeMap(async (action) => {
            const {order} = action;

            if (!order || !Array.isArray(order.items)) {
                return [ShippingAction.finishLoadProductQtys({})]
            }

            let productQtys = {};

            let productIds = [];
            let prepareToShipProducts = {};
            let productMap = {};
            let bundleShipTogetherProducts = [];
            order.items.map(async item => {
                let productType = item.product_type;
                if (OrderItemService.getHasChildren(item, order)) {

                    /**
                    *   specific for bundle together ship
                    * */
                    if (
                        ProductTypeConstant.BUNDLE === productType
                        && !OrderItemService.isShipSeparately(item, order)
                    ) {
                        bundleShipTogetherProducts.push({
                            item,
                            children: OrderItemService.getChildrenItems(item, order)
                        });
                    }
                    return;
                }
                productMap[item.product_id] = {
                    type: productType,
                    sku: item.sku,
                };
                productIds.push(item.product_id);

                if (!prepareToShipProducts.hasOwnProperty(item.product_id)) {
                    prepareToShipProducts[item.product_id] = 0;
                }

                prepareToShipProducts[item.product_id] += OrderItemService.getSimpleQtyToShip(item);
            });

            let stockList = await StockService.getStockProducts(productIds);
            productIds.forEach(productId => {
                if (!productMap[productId]) {
                    return;
                }

                if (productMap[productId].sku === CustomSaleConstant.SKU) {
                    return productQtys[productId] = {
                        isManageStock: false,
                    };
                }

                if (!stockList.hasOwnProperty(productId)) {
                    return productQtys[productId] = {
                        isNotExisted: true,
                    };
                }

                let fakeProduct = {
                    type_id: productMap[productId].type,
                    stocks: stockList[productId]
                };
                let productStockService = StockService.getProductStockService(fakeProduct);

                let qty = ProductService.getQty(fakeProduct) || 0;

                /**
                 *  because order is placed and product is no backorder , so qty to ship for item is holded
                 */
                if (
                    order.pos_location_id === LocationService.getCurrentLocationId()
                    && !productStockService.getBackorders(fakeProduct)
                ) {
                    qty += prepareToShipProducts[productId];
                }

                productQtys[productId] = {
                    isManageStock: productStockService.isManageStock(fakeProduct),
                    qty,
                    stockInLocation: productStockService.getQtyInLocation(fakeProduct)
                };

            });

            if (!bundleShipTogetherProducts.length) {
                return ShippingAction.finishLoadProductQtys(productQtys)
            }

            bundleShipTogetherProducts.forEach(bundleProduct => {
                let qtyToShip = OrderItemService.getQtyToShip(bundleProduct.item);

                let childIsNotExisted =  bundleProduct.children.find(child => {
                    return productQtys[child.product_id].isNotExisted;
                });

                /**
                 *  if has any child product, which is not on current source, then you cannot ship it
                 */
                if (childIsNotExisted) {
                    return productQtys[bundleProduct.item.product_id] = {
                        qty: 0
                    }
                }

                let childrenManageStock =  bundleProduct.children.find(child => {
                    return productQtys[child.product_id].isManageStock;
                });

                /**
                 *  if has all child product, which is not manage stock, then you can ship it
                 */
                if (!childrenManageStock) {
                    return productQtys[bundleProduct.item.product_id] = {
                        qty: qtyToShip
                    }
                }

                let childrenNotEnoughToFullShip =  bundleProduct.children.filter(child => {
                    if (!productQtys[child.product_id].isManageStock) {
                        return false;
                    }

                    if (productQtys[child.product_id].qty === false) {
                        return true;
                    }

                    let childQtyToShip = OrderItemService.getSimpleQtyToShip(child);

                    return productQtys[child.product_id].qty < childQtyToShip;
                });

                /**
                 *  if has product, which is not enough to perform full shipment
                 */
                if (childrenNotEnoughToFullShip.length) {
                    let qtys = [];
                    childrenNotEnoughToFullShip.forEach(child => {
                        qtys.push(parseInt(productQtys[child.product_id].qty / qtyToShip, 10));
                    });

                    return productQtys[bundleProduct.item.product_id] = {
                        qty: Math.min(...qtys, 0)
                    }
                }

                return productQtys[bundleProduct.item.product_id] = {
                    qty: qtyToShip
                }
            });

            return ShippingAction.finishLoadProductQtys(productQtys)
        }).catch(error => {
            console.log(error);
        });
}