import {CreditmemoAbstractTotalService} from "../../../../sales/order/creditmemo/total/AbstractTotalService";
import ServiceFactory from "../../../../../framework/factory/ServiceFactory";
import NumberHelper from "../../../../../helper/NumberHelper";
import OrderItemService from "../../../../sales/order/OrderItemService";
import CreditmemoPriceService from "../../../../sales/order/creditmemo/CreditmemoPriceService";

export class CreditmemoGiftcardTotalService extends CreditmemoAbstractTotalService {
    static className = 'CreditmemoGiftcardTotalService';

    /**
     * Collect creditmemo grand total
     *
     * @param creditmemo
     * @return {CreditmemoGrandTotalService}
     */
    collect(creditmemo) {
        let order = creditmemo.order;
        if (!order.gift_voucher_discount) {
            return this;
        }
        let totalDiscountAmountGiftvoucher = 0,
            baseTotalDiscountAmountGiftvoucher = 0;

        let shippingAmount = creditmemo.shipping_amount;
        if (shippingAmount) {
            totalDiscountAmountGiftvoucher = NumberHelper.multipleNumber(
                shippingAmount, order.giftvoucher_discount_for_shipping
            ) / order.shipping_amount;
            baseTotalDiscountAmountGiftvoucher = NumberHelper.multipleNumber(
                creditmemo.base_shipping_amount, order.base_giftvoucher_discount_for_shipping
            ) / order.base_shipping_amount;

            /* set base total gift_code discount amount for shipping */
            creditmemo.base_total_giftcode_discount_amount_for_shipping = baseTotalDiscountAmountGiftvoucher;
            creditmemo.total_giftcode_discount_amount_for_shipping = totalDiscountAmountGiftvoucher;

        }
        if (creditmemo.items && creditmemo.items.length) {
            creditmemo.items.forEach(item => {
                let orderItem = item.order_item;
                if (OrderItemService.isDummy(orderItem, order)) {
                    return false;
                }
                let orderItemDiscountGiftvoucher = orderItem.gift_voucher_discount,
                    baseOrderItemDiscountGiftvoucher = orderItem.base_gift_voucher_discount;
                let orderItemQty = orderItem.qty_ordered;
                let creditmemoItemQty = item.qty;

                if (orderItemDiscountGiftvoucher && orderItemQty) {
                    let discount = CreditmemoPriceService.roundPrice(
                        orderItemDiscountGiftvoucher / orderItemQty * creditmemoItemQty,
                        'regular'
                    );
                    let baseDiscount = CreditmemoPriceService.roundPrice(
                        baseOrderItemDiscountGiftvoucher / orderItemQty * creditmemoItemQty,
                        'regular'
                    );
                    totalDiscountAmountGiftvoucher = NumberHelper.addNumber(totalDiscountAmountGiftvoucher, discount);
                    baseTotalDiscountAmountGiftvoucher = NumberHelper.addNumber(
                        baseTotalDiscountAmountGiftvoucher, baseDiscount
                    );
                }
            });
        }
        creditmemo.gift_voucher_discount = totalDiscountAmountGiftvoucher;
        creditmemo.base_gift_voucher_discount = baseTotalDiscountAmountGiftvoucher;
        if (totalDiscountAmountGiftvoucher) {
            this.calculateRefundAmountForGiftCode(creditmemo)
        }
        return this;
    }

    /**
     * Calculate refund amount for giftcode
     *
     * @param creditmemo
     */
    calculateRefundAmountForGiftCode(creditmemo) {

        let listCodesAmountRefunded = this.processRefundAmountToGiftcode(creditmemo);
        let listCodesDiscountForShipping = this.processRefundAmountForShipping(creditmemo);
        delete creditmemo.base_total_giftcode_discount_amount_for_shipping;
        delete creditmemo.total_giftcode_discount_amount_for_shipping;

        let order = creditmemo.order;
        let baseGiftVoucherDiscountTotal = creditmemo.base_gift_voucher_discount;
        let giftVoucherGiftCodesAvailableRefund = order.gift_voucher_gift_codes_available_refund;

        let codes = order.gift_voucher_gift_codes;
        let refundAmounts = [];
        if (codes && giftVoucherGiftCodesAvailableRefund &&
            baseGiftVoucherDiscountTotal && baseGiftVoucherDiscountTotal > 0)
        {
            codes.split(',').forEach(code => {

                if (baseGiftVoucherDiscountTotal === 0) {
                    return false;
                }

                let refundAmount = 0;
                let availableRefund = listCodesAmountRefunded.filter((refund) => {
                    return refund.code === code;
                });
                if (!availableRefund) {
                    return false;
                }
                availableRefund = availableRefund[0].amound_refund;
                if (+availableRefund < baseGiftVoucherDiscountTotal) {
                    baseGiftVoucherDiscountTotal = NumberHelper.minusNumber(
                        baseGiftVoucherDiscountTotal, availableRefund
                    );
                    refundAmount = availableRefund;
                } else {
                    refundAmount = baseGiftVoucherDiscountTotal;
                    baseGiftVoucherDiscountTotal = 0;
                }

                /* if have gift code discount for shipping */
                if (listCodesDiscountForShipping.length > 0){
                    let refundShip = listCodesDiscountForShipping.filter((refund) => {
                        return refund.code === code;
                    });
                    if(refundShip.length > 0) {
                        refundAmount += refundShip[0].discount;
                    }
                }
                refundAmounts.push(refundAmount);
            });
        }
        refundAmounts = refundAmounts.join(',');
        creditmemo.gift_voucher_gift_codes = order.gift_voucher_gift_codes;
        creditmemo.gift_voucher_gift_codes_refund_amount = refundAmounts;
    }

    /**
     *
     * @param creditmemo
     * @returns {Array}
     */
    processRefundAmountToGiftcode(creditmemo){
        let listCodeAmountRefunded = [];
        let creditmemoItems = creditmemo.items;
        let listCodes = creditmemo.order.gift_voucher_gift_codes;
        listCodes.split(',').forEach((listcode) =>{
            let amountRefund = 0;
            creditmemoItems.forEach((item) => {
                if(item.qty > 0){
                    if(item.order_item.giftcodes_applied){
                        let giftcodesAppliedItem = JSON.parse(item.order_item.giftcodes_applied);
                        giftcodesAppliedItem.forEach(code =>{
                            if((listcode === code.code) && (code.qty_ordered > 0)){
                                let gift_code_discount = code.gift_card_discount_amount;
                                let ordered_qty = code.qty_ordered;
                                amountRefund += (gift_code_discount/ordered_qty) * item.qty;
                            }
                        })
                    }
                }
            });
            let object = {
                code : listcode,
                amound_refund : amountRefund,
            };
            listCodeAmountRefunded.push(object);
        });
        return listCodeAmountRefunded;
    }

    /**
     *
     * @param creditmemo
     * @returns {Array}
     */
    processRefundAmountForShipping(creditmemo){
        let giftcodesDiscountForShipping = [];
        let totalDiscount = creditmemo.total_giftcode_discount_amount_for_shipping;
        if(totalDiscount){
            let giftcodesApplied = creditmemo.order.giftcodes_applied_discount_for_shipping;
            giftcodesDiscountForShipping = this.getGiftcodeDiscountForShipping(totalDiscount,giftcodesApplied);
        }
        return giftcodesDiscountForShipping;
    }

    /**
     *
     * @param totalDiscount
     * @param giftcodesApplied
     * @returns {Array}
     */
    getGiftcodeDiscountForShipping(totalDiscount, giftcodesApplied){
        let giftcodesAppliedDiscountForShipping = [];
        if(giftcodesApplied){
             giftcodesAppliedDiscountForShipping = JSON.parse(giftcodesApplied);
        }
        let total = 0;
        if(giftcodesAppliedDiscountForShipping.length > 0){
            giftcodesAppliedDiscountForShipping.forEach(giftcode => {
                total = NumberHelper.addNumber(total, giftcode.base_discount);
            });
        }
        let remain_discount = total;
        let result = [];
        if(totalDiscount > 0 && total > 0) {
            giftcodesAppliedDiscountForShipping.forEach(( codeDiscount) => {
                if(remain_discount >0 ){
                    let discount_amount = totalDiscount * codeDiscount.discount / total;
                    if(discount_amount > remain_discount){
                        discount_amount = remain_discount;
                    }
                    let discount = CreditmemoPriceService.roundPrice(
                        discount_amount,
                        'regular'
                    );
                    let object = {
                        code : codeDiscount.code,
                        discount : discount
                    };
                    result.push(object);
                    remain_discount -= discount_amount;
                }
            });
        }
        return result;
    }
}

/** @type CreditmemoGiftcardTotalService */
let creditmemoGiftcardTotalService = ServiceFactory.get(CreditmemoGiftcardTotalService);

export default creditmemoGiftcardTotalService;