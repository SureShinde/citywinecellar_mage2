import * as _ from "lodash";
import ServiceFactory from "../../../../../../framework/factory/ServiceFactory";
import {CreditmemoAbstractTotalService} from "../../../../../../service/sales/order/creditmemo/total/AbstractTotalService";
import OrderItemService from "../../../../../../service/sales/order/OrderItemService";
import CreditmemoPriceService from "../../../../../../service/sales/order/creditmemo/CreditmemoPriceService";

export class CreditmemoPointTotalService extends CreditmemoAbstractTotalService {
    static className = 'CreditmemoPointTotalService';

    code = "rewardpoint";

    collect(creditmemo) {
        super.collect(creditmemo);

        if(typeof creditmemo.extension_attributes === 'undefined') {
            creditmemo.extension_attributes = {};
        }
        creditmemo.extension_attributes.rewardpoints_discount      = 0;
        creditmemo.extension_attributes.rewardpoints_base_discount = 0;

        const {order} = creditmemo;

        if (!order || order.extension_attributes.rewardpoints_discount < 0.0001) {
            return this;
        }

        let totalDiscountAmount       = 0;
        let baseTotalDiscountAmount   = 0;
        // let baseTotalDiscountRefunded = _.toNumber(order.creditmemo_rewardpoints_base_discount) || 0;
        // let totalDiscountRefunded     = _.toNumber(order.creditmemo_rewardpoints_discount) || 0;


        /**
         * Calculate how much shipping discount should be applied
         * basing on how much shipping should be refunded.
         */
        let baseShippingAmount = _.toNumber(creditmemo.base_shipping_amount) || 0;
        if (baseShippingAmount) {
            baseTotalDiscountAmount =
                baseShippingAmount * order.extension_attributes.rewardpoints_base_discount_for_shipping / order.base_shipping_amount;
            totalDiscountAmount     = _.toNumber(creditmemo.shipping_amount)
                * order.extension_attributes.rewardpoints_discount_for_shipping / order.shipping_amount;
        }


        /** @var {object} item */
        creditmemo.items.forEach(item => {
            let orderItem = item.order_item;

            if (OrderItemService.isDummy(orderItem, creditmemo.order)) {
                return false;
            }

            let orderItemDiscount     =
                    orderItem.extension_attributes.rewardpoints_discount * orderItem.qty_invoiced / orderItem.qty_ordered;
            let baseOrderItemDiscount =
                    orderItem.extension_attributes.rewardpoints_base_discount * orderItem.qty_invoiced / orderItem.qty_ordered;

            let orderItemQty = orderItem.qty_invoiced * 1;

            if (orderItemDiscount && orderItemQty) {
                totalDiscountAmount +=
                    _.toNumber(CreditmemoPriceService.roundPrice(
                        orderItemDiscount / orderItemQty * item.qty, 'regular', true
                    ));
                baseTotalDiscountAmount +=
                    _.toNumber(CreditmemoPriceService.roundPrice(
                        baseOrderItemDiscount / orderItemQty * item.qty, 'base', true
                    ));
            }
        });

        creditmemo.extension_attributes.rewardpoints_discount      = totalDiscountAmount;
        creditmemo.extension_attributes.rewardpoints_base_discount = baseTotalDiscountAmount;

        return this;
    }


}

/** @type {CreditmemoPointTotalService} */
let creditmemoPointTotalService = ServiceFactory.get(CreditmemoPointTotalService);

export default creditmemoPointTotalService;
