import {AbstractOrderService} from "./AbstractService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import OrderResourceModel from "../../resource-model/order/OrderResourceModel";
import StatusConstant from "../../view/constant/order/StatusConstant";
import i18n from "../../config/i18n";
import CurrencyHelper from "../../helper/CurrencyHelper";
import OrderWeeeDataService from "../weee/OrderWeeeDataService";
import NumberHelper from "../../helper/NumberHelper";
import TaxHelper from "../../helper/TaxHelper";
import OrderHelper from "../../helper/OrderHelper";
import DateTimeHelper from "../../helper/DateTimeHelper";
import OrderItemService from "./order/OrderItemService";
import SyncConstant from "../../view/constant/SyncConstant";
import ActionLogService from "../../service/sync/ActionLogService";
import LocalStorageHelper from "../../helper/LocalStorageHelper";
import Config from '../../config/Config';
import PaymentFactory from "../../factory/PaymentFactory";
import PaymentHelper from "../../helper/PaymentHelper";
import {toast} from "react-toastify";
import GiftcardHelper from "../../helper/GiftcardHelper";
import PaymentConstant from "../../view/constant/PaymentConstant";
import AddressConstant from "../../view/constant/checkout/quote/AddressConstant";
import {fire} from "../../event-bus";

export class OrderService extends AbstractOrderService {
    static className = 'OrderService';
    resourceModel = OrderResourceModel;

    /**
     * Get base total paid
     *
     * @param order
     * @returns {number}
     */
    getBaseTotalPaid(order) {
        let baseTotalPaid = 0;
        let baseGrandTotal = order.base_grand_total;
        let posBasePreTotalPaid = order.pos_base_pre_total_paid;
        let takeBaseAmount = 0;
        order.payments.forEach(payment => {
            let paidAmount = payment.is_pay_later ? 0 : payment.base_amount_paid;
            if (!payment.is_paid) {
                takeBaseAmount = NumberHelper.addNumber(takeBaseAmount, paidAmount);
            }
            baseTotalPaid = NumberHelper.addNumber(baseTotalPaid, paidAmount);
        });

        if (posBasePreTotalPaid > 0) {
            baseTotalPaid = takeBaseAmount + order.base_total_paid;
        }
        // Due baseTotal depends on Total (fixed for multi currency)
        if (baseTotalPaid > baseGrandTotal || 0 === this.getTotalDue(order)) {
            baseTotalPaid = baseGrandTotal;
        }
        return baseTotalPaid;
    }

    /**
     * Get total paid
     * @param order
     * @returns {number}
     */
    getTotalPaid(order) {
        let totalPaid = 0;
        let grandTotal = order.grand_total;
        let posPreTotalPaid = order.pos_pre_total_paid;
        let takeAmount = 0;
        order.payments.forEach(payment => {
            let paidAmount = payment.is_pay_later ? 0 : payment.amount_paid;
            if (payment.is_paid) {
                takeAmount = NumberHelper.addNumber(takeAmount, paidAmount);
            }
            totalPaid = NumberHelper.addNumber(totalPaid, paidAmount);
        });
        if (posPreTotalPaid > 0) {
            totalPaid = takeAmount + order.total_paid;
        }
        if (totalPaid > grandTotal) {
            totalPaid = grandTotal;
        }
        return totalPaid;
    }

    /**
     * get total due
     *
     * @param order
     * @returns {number}
     */
    getTotalDue(order) {
        let totalPaid = this.getTotalPaid(order);
        let grandTotal = order.grand_total;
        if (!totalPaid) {
            return grandTotal;
        } else if (grandTotal >= totalPaid) {
            return grandTotal - totalPaid;
        }
        return 0;
    }

    /**
     * Get base total due
     *
     * @param order
     * @returns {number}
     */
    getBaseTotalDue(order) {
        let baseTotalPaid = this.getBaseTotalPaid(order);
        let baseGrandTotal = order.base_grand_total;
        if (!baseTotalPaid) {
            return baseGrandTotal;
        } else if (baseGrandTotal > baseTotalPaid) {
            return baseGrandTotal - baseTotalPaid;
        }
        return 0;
    }

    /**
     * get base total change
     * @param order
     * @return {number}
     */
    getBasePosChange(order) {
        let basePosChange = 0;
        order.payments.forEach(item => {
            basePosChange = NumberHelper.addNumber(basePosChange, item.base_amount_change);
        });
        return basePosChange;
    }

    /**
     * get total change
     * @param order
     * @return {number}
     */
    getPosChange(order) {
        let posChange = 0;
        order.payments.forEach(item => {
            posChange = NumberHelper.addNumber(posChange, item.amount_change);
        });
        return posChange;
    }

    /**
     * Get base total change due
     *
     * @param {object} order
     * @return {number}
     */
    getBaseTotalChangeDue(order) {
        let baseTotalPaid = this.getBaseTotalPaid(order);
        let baseGrandTotal = order.base_grand_total;
        if (baseTotalPaid > baseGrandTotal) {
            return baseGrandTotal - baseGrandTotal;
        }
        return 0;
    }

    /**
     * Get total change due
     *
     * @param {object} order
     * @return {number}
     */
    getTotalChangeDue(order) {
        let totalPaid = this.getTotalPaid(order);
        let grandTotal = order.grand_total;
        if (totalPaid > grandTotal) {
            return totalPaid - grandTotal;
        }
        return 0;
    }

    /**
     * Get list orders
     * @param queryService
     * @param searchAllTime
     * @return {*}
     */
    getListOrder(queryService, searchAllTime = false) {
        if (searchAllTime) {
            return this.getResourceModel().getDataOnline(queryService);
        }
        return this.getResourceModel().getList(queryService);
    }

    /**
     * get price label
     * @param order
     * @returns {{className: string, value: string}}
     */
    getPriceLabel(order) {
        let priceLabel = {
            className: '',
            value: ''
        };
        if (order.state === StatusConstant.STATE_CANCELED || order.state === StatusConstant.STATE_CLOSED) {
            priceLabel = {
                className: "void",
                value: i18n.translator.translate(StatusConstant.PAYMENT_STATUS_VOID)
            };
        } else {
            let grand_total =
                CurrencyHelper.roundToFloat(order.grand_total, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
            let total_paid =
                CurrencyHelper.roundToFloat(order.total_paid, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
            let total_due =
                CurrencyHelper.roundToFloat(order.total_due, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
            if (grand_total <= total_paid) {
                priceLabel = {
                    className: "paid",
                    value: i18n.translator.translate(StatusConstant.PAYMENT_STATUS_PAID)
                };
            } else if (total_due > 0 && total_due < grand_total) {
                priceLabel = {
                    className: "due",
                    value: i18n.translator.translate(
                        "Due {{value}}",
                        {value: OrderHelper.formatPrice(order.total_due, order)}
                    )
                };
            } else if (total_due === grand_total) {
                priceLabel = {
                    className: "due",
                    value: i18n.translator.translate(
                        "Due {{value}}",
                        {value: OrderHelper.formatPrice(order.total_due, order)}
                    )
                };
            }
        }
        return priceLabel;
    }

    /**
     * get display status
     * @param state
     * @param status
     * @return {string}
     */
    getDisplayStatus(state, status) {
        let statusData = {};
        if (Config.orderStatus) {
            statusData = Config.orderStatus.find(item => (item.status === status && item.state === state));
        }
        return (statusData ? statusData.label : '');
    }

    /**
     * get payment status
     * @param order
     * @return {{className: string, value: string}}
     */
    getPaymentStatus(order) {
        let paymentStatus = {
            className: '',
            value: '',
            realValue: ''
        };

        if (!order) {
            return paymentStatus;
        }


        if (order.state === StatusConstant.STATE_CANCELED || order.state === StatusConstant.STATE_CLOSED) {
            paymentStatus = {
                className: "void",
                value: i18n.translator.translate(StatusConstant.PAYMENT_STATUS_VOID),
                realValue: StatusConstant.PAYMENT_STATUS_VOID
            };
        } else {
            let grand_total =
                CurrencyHelper.roundToFloat(order.grand_total, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
            let total_paid =
                CurrencyHelper.roundToFloat(order.total_paid, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
            let total_due =
                CurrencyHelper.roundToFloat(order.total_due, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
            if (grand_total <= total_paid) {
                paymentStatus = {
                    className: "paid",
                    value: i18n.translator.translate(StatusConstant.PAYMENT_STATUS_PAID),
                    realValue: StatusConstant.PAYMENT_STATUS_PAID
                };
            } else if (total_due > 0 && total_due < grand_total) {
                paymentStatus = {
                    className: "partially_paid",
                    value: i18n.translator.translate(StatusConstant.PAYMENT_STATUS_PARTIAL_PAID),
                    realValue: StatusConstant.PAYMENT_STATUS_PARTIAL_PAID
                };
            } else if (total_due === grand_total) {
                paymentStatus = {
                    className: "unpaid",
                    value: i18n.translator.translate(StatusConstant.PAYMENT_STATUS_UNPAID),
                    realValue: StatusConstant.PAYMENT_STATUS_UNPAID
                };
            }
        }
        return paymentStatus;
    }

    /**
     * Get price display in order item
     * @param item
     * @param order
     * @return {*}
     */
    getItemDisplayPrice(item, order) {
        let price = 0;
        if (TaxHelper.orderDisplayPriceIncludeTax()) {
            price = item.price_incl_tax;
        } else {
            price = item.price;
        }
        return OrderHelper.formatPrice(price, order);
    }

    /**
     * Get original price display in order item
     * @param item
     * @param order
     * @return {*}
     */
    getItemDisplayOriginalPrice(item, order) {
        let originalPrice = 0;
        if (TaxHelper.orderDisplayPriceIncludeTax()) {
            originalPrice = item.pos_original_price_incl_tax;
        } else {
            originalPrice = item.pos_original_price_excl_tax;
        }
        return OrderHelper.formatPrice(originalPrice, order);
    }


    /**
     * Get price display in order item
     * @param item
     * @param order
     * @return {*}
     */
    getRowTotal(item, order) {
        let price = item.row_total;
        price = NumberHelper.minusNumber(price, item.discount_amount);
        price = NumberHelper.addNumber(price, item.tax_amount);
        price = NumberHelper.addNumber(price, item.discount_tax_compensation_amount);
        price = NumberHelper.addNumber(price, OrderWeeeDataService.getRowWeeeTaxInclTax(item, order));
        return OrderHelper.formatPrice(price, order);
    }


    /**
     * Get display subtotal in order detail
     * @param order
     * @return {*}
     */
    getDisplaySubtotal(order) {
        let subtotal = order.subtotal;
        if (TaxHelper.orderDisplaySubtotalIncludeTax()) {
            subtotal = order.subtotal_incl_tax;
        }
        return OrderHelper.formatPrice(subtotal, order);
    }

    /**
     * Get display shipping amount in order detail
     * @param order
     * @return {*}
     */
    getDisplayShippingAmount(order) {
        let shippingAmount = order.shipping_amount;
        if (TaxHelper.orderDisplayShippingAmountIncludeTax()) {
            shippingAmount = order.shipping_incl_tax;
        }
        return OrderHelper.formatPrice(shippingAmount, order);
    }

    /**
     * get display shipping amount in shipping method
     * @param order
     * @return {*}
     */
    getShippingMethodAmount(order) {
        let shippingAmount = order.shipping_amount;
        if (TaxHelper.shippingPriceDisplayIncludeTax()) {
            shippingAmount = order.shipping_incl_tax;
        }
        return OrderHelper.formatPrice(shippingAmount, order);
    }

    /**
     * get full address from address
     * @param address
     * @return {string}
     */
    getFullAddress(address) {
        let addressArr = [];
        let street = "";
        if (address.street) {
            street = address.street;
            addressArr.push(street);
        }
        let city = "";
        if (address.city) {
            city = address.city;
            addressArr.push(city);
        }
        let region = "";
        if (address.region) {
            region = address.region;
            addressArr.push(region);
        }
        let postCode = "";
        if (address.postcode) {
            postCode = address.postcode;
            addressArr.push(postCode);
        }
        let country = "";
        if (address.country_id) {
            country = address.country_id;
            addressArr.push(country);
        }
        return addressArr.join(", ");
    }

    /**
     * get out date orders
     * @return {*|Promise<{ids: Array}>}
     */
    getOutDateOrders() {
        return this.getResourceModel().getOutDateOrders();
    }

    /**
     * Retrieve order invoice availability
     *
     * @param order
     * @return {boolean}
     */
    canInvoice(order) {
        if (this.canUnhold(order) || this.isPaymentReview(order)) {
            return false;
        }

        let state = order.state;
        if (this.isCanceled(order) || state === StatusConstant.STATE_CLOSED) {
            return false;
        }
        let result = false;
        order.items.forEach(item => {
            if (OrderItemService.getQtyToInvoice(item, order) > 0) {
                result = true;
            }
        });
        return result;
    }

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @param order
     * @return {boolean}
     */
    canCreditmemo(order) {
        if (order) {
            if (this.canUnhold(order) || this.isPaymentReview(order)) {
                return false;
            }

            if (this.isCanceled(order) || order.state === StatusConstant.STATE_CLOSED) {
                return false;
            }

            let forceCanCreditmemo = null;

            let eventData = {
                order: order,
                forceCanCreditmemo: forceCanCreditmemo
            };
            fire('service_order_can_creditmemo_middle', eventData);
            forceCanCreditmemo = eventData.forceCanCreditmemo;

            if (forceCanCreditmemo !== null) return forceCanCreditmemo;

            // Sau này chuyển code gift card ra plugin thì làm như reward point
            if (order.gift_voucher_discount && order.grand_total === 0 && GiftcardHelper.isGiftcardEnable()) {
                let canRefundGiftCard = order.items.some(item => {
                    return (item.qty_invoiced - item.qty_refunded - item.qty_canceled) > 0;
                });
                if (canRefundGiftCard) return canRefundGiftCard;
            }

            let totalRefunded = CurrencyHelper.round(NumberHelper.minusNumber(order.total_paid, order.total_refunded));
            if (Math.abs(totalRefunded) < 0.0001) {
                return false;
            }

            if (Math.abs(NumberHelper.minusNumber(totalRefunded, order.adjustment_negative)) < 0.0001) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Retrieve order unhold availability
     *
     * @param order
     * @return {boolean}
     */
    canUnhold(order) {
        if (this.isPaymentReview(order)) {
            return false;
        }
        return order.state === StatusConstant.STATE_HOLDED;
    }

    /**
     * Retrieve order cancel availability
     *
     * @param order
     * @return {boolean}
     */
    canCancel(order) {
        if (order) {
            if (!this.canVoidOrder(order)) return false;
            if (this.canUnhold(order)) return false;
            // chi co payment online moi check dc dieu kien nay
            // if (!this.canReviewPayment(order) && this.canFetchPaymentReviewUpdate(order)) {
            //     return false;
            // }
            let allInvoiced = true;
            let allItems = this.getAllItems(order);
            for (let i = 0; i < allItems.length; i++) {
                let item = allItems[i];
                if (OrderItemService.getQtyToInvoice(item, order)) {
                    allInvoiced = false;
                    break;
                }
            }
            if (allInvoiced) return false;
            let state = order.state;
            if (this.isCanceled(order) ||
                state === StatusConstant.STATE_COMPLETE ||
                state === StatusConstant.STATE_CLOSED) {
                return false;
            }
            // Action flag dung cho server check online
            // if ($this->getActionFlag(self::ACTION_FLAG_CANCEL) === false) {
            //     return false;
            // }

            return true;
        }
        return false;
    }

    /**
     * deep Retrieve order cancel availability
     *
     * @param order
     * @return {*}
     */
    deepCanCancel(order) {
        let canCancel = this.canCancel(order);

        if (!canCancel) {
            return {canCancel};
        }

        if (!Array.isArray(order.payments)) {
            return true;
        }

        /** for some payments, they don't agree cancelling order action if pay partially */
        let preventCancelOrderRulePayments = order.payments.filter(orderPayment => {
            return PaymentHelper.hasPreventCancelOrderRule(orderPayment.method)
        });

        if (!preventCancelOrderRulePayments.length) {
            return {canCancel};
        }
        let paymentTitles = preventCancelOrderRulePayments.map(payment => payment.title);
        let paid = 0;
        let refunded = 0;
        preventCancelOrderRulePayments.forEach(orderPayment => {
            if (orderPayment.type === PaymentConstant.TYPE_CHECKOUT) {
                paid += orderPayment.amount_paid * 1;
                return;
            }

            refunded += orderPayment.amount_paid * 1;
        });

        if (refunded >= paid) {
            return {
                canCancel: true
            }
        }

        return {
            canCancel: false,
            title: 'Cancel Order',
            message: `You need to refund exactly the partially paid amount with the previously used 
            ${paymentTitles.join(', ')} payment(s) before being able to cancel this order`
        };
    }

    /**
     * Retrieve order void availability
     *
     * @param order
     * @return {boolean}
     */
    canVoidOrder(order) {
        return !(this.isCanceled(order) || this.canUnhold(order) || this.isPaymentReview(order));
    }

    /**
     * Get list item in order
     *
     * @param order
     * @return {items}
     */
    getAllItems(order) {
        let items = [];
        order.items.map(item => {
            if (!item.isDeleted) {
                items.push(item);
            }
            return null;
        });
        return items;
    }


    /**
     * Check whether the payment is in payment review state
     * In this state order cannot be normally processed. Possible actions can be:
     * - accept or deny payment
     * - fetch transaction information
     *
     * @param order
     * @return {boolean}
     */
    isPaymentReview(order) {
        return order.state === StatusConstant.STATE_PAYMENT_REVIEW;
    }

    /**
     * Check whether order is canceled
     *
     * @param order
     * @return {boolean}
     */
    isCanceled(order) {
        return order.state === StatusConstant.STATE_CANCELED;
    }

    /**
     * add comment to order
     *
     * @param order
     * @param commentText
     * @param notify
     * @param visibleOnFront
     * @return {*}
     */
    addComment(order, commentText, notify = false, visibleOnFront = false) { // eslint-disable-line
        if (!order.status_histories) {
            order.status_histories = [];
        }
        let createAt = DateTimeHelper.getDatabaseDateTime();
        let comment = {
            comment: commentText,
            created_at: createAt,
            entity_id: this.getNextCommentEntityId(order),
            is_visible_on_front: +visibleOnFront
        };
        order.status_histories.unshift(comment);
        return order;
    }


    /**
     * send email to customer
     *
     * @param increment_id
     * @param email
     * @return {*}
     */
    async sendEmail(increment_id, email) {
        let orderResource = this.getResourceModel(OrderResourceModel);
        let url_api = orderResource.getResourceOnline().getPathSendEmailOrder();
        let params = {
            increment_id: increment_id,
            email: email
        };
        await ActionLogService.createDataActionLog(
            SyncConstant.REQUEST_SEND_EMAIL_ORDER, url_api, SyncConstant.METHOD_POST, params
        );
    }

    /**
     * cancel order
     * @param order
     * @param commentText
     * @param notify
     * @param visibleOnFront
     * @returns {Promise.<*>}
     */
    async cancel(order, commentText, notify, visibleOnFront) {
        // cacultor offline
        let comment = '';
        if (commentText) {
            if (!order.status_histories) {
                order.status_histories = [];
            }
            let createAt = DateTimeHelper.getDatabaseDateTime();
            comment = {
                comment: commentText,
                created_at: createAt,
                entity_id: this.getNextCommentEntityId(order),
                is_visible_on_front: +visibleOnFront
            };
            order.status_histories.unshift(comment);
        }

        try {
            if (this.canCancel(order) || this.isPaymentReview(order) || this.isFraudDetected(order)) {
                let state = StatusConstant.STATE_CANCELED;
                let items = [];
                this.getAllItems(order).forEach(item => {
                    if ((state !== StatusConstant.STATE_PROCESSING) && OrderItemService.getQtyToRefund(item, order)) {
                        if (OrderItemService.isProcessingAvailable(item, order)) {
                            state = StatusConstant.STATE_PROCESSING;
                        } else {
                            state = StatusConstant.STATE_COMPLETE;
                        }
                    }
                    item = OrderItemService.cancel(item, order);
                    items.push(item);
                });

                order.items = items;

                order.subtotal_canceled = order.subtotal - order.subtotal_invoiced;
                order.base_subtotal_canceled = order.base_subtotal - order.base_subtotal_invoiced;

                order.tax_canceled = order.tax_amount - order.tax_invoiced;
                order.base_tax_canceled = order.base_tax_amount - order.base_tax_invoiced;

                order.shipping_cancel = order.shipping - order.shipping_invoiced;
                order.base_shipping_cancel = order.base_shipping - order.base_shipping_invoiced;

                order.disccount_canceled = Math.abs(order.discount_amount - order.discount_invoiced);
                order.base_disccount_canceled = Math.abs(order.base_discount_amount - order.base_discount_invoiced);


                order.total_canceled = order.grand_total - order.total_paid;
                order.base_total_canceled = order.base_grand_total - order.base_total_paid;

                order.state = state;

                if (Config.orderStatus) {
                    let statusData = Config.orderStatus.find(
                        item => (item.state === state) && (item.is_default === '1')
                    );
                    if (statusData) {
                        order.status = statusData.status
                    }
                }

                // can set statu (sau khi co config tra ve)
                //     $this->setState($state)
                // ->setStatus($this->getConfig()->getStateDefaultStatus($state));


                // action log
                let orderResource = this.getResourceModel(OrderResourceModel);
                orderResource.saveToDb([order], true);

                let url_api = orderResource.getResourceOnline().getPathCancelOrder();
                let params = {
                    increment_id: order.increment_id,
                    comment: comment,
                    request_increment_id: PaymentHelper.generateIncrement()
                };

                await  ActionLogService.createDataActionLog(
                    SyncConstant.REQUEST_CANCEL_ORDER, url_api, SyncConstant.METHOD_POST, params
                );
            } else {
                toast.error(
                    i18n.translator.translate('Cancel order not available'),
                    {className: 'wrapper-messages messages-warning'}
                );
            }
        }
        catch (error) {
            console.log(error.message); // eslint-disable-line
        }


        return order;
    }


    /**
     * get next comment entity id
     * @param order
     * @returns {number}
     */
    getNextCommentEntityId(order) {
        if (!order.status_histories || !order.status_histories.length) {
            return 1;
        }
        return Math.max(...order.status_histories.map(item => item.entity_id)) + 1;
    }

    /**
     * save order status to local storage
     * @param orderStatus
     */
    saveOrderStatus(orderStatus) {
        let data = JSON.stringify(orderStatus);
        LocalStorageHelper.set(LocalStorageHelper.ORDER_STATUS, data);
        Config.orderStatus = orderStatus;
    }

    /**
     * get order status from local storage
     * @return {*|string}
     */
    getOrderStatus() {
        return LocalStorageHelper.get(LocalStorageHelper.ORDER_STATUS);
    }

    /**
     * process Payment
     * @param order
     * @return {Promise<{order_increment_id: string|*}>}
     */
    async processPayment(order) {
        let promises = [];
        order.payments.forEach(payment => {
            let process = Promise.resolve({});

            if (
                (!payment.reference_number && PaymentHelper.hasUsingCreditCardForm(payment.method))
                && !payment.is_paid
            ) {
                process = PaymentFactory.createByCode(payment.method).setOrder(order).setPayment(payment).execute();
            }
            promises.push(process)
        });

        let responses = await Promise.all(promises);
        let errors = [];
        let processPayments = {};

        responses.forEach((response, index) => {
            processPayments[order.payments[index].method + index] = response;
            response.errorMessage && errors.push(response.errorMessage);
        });

        if (errors.length) {
            return {
                error: true,
                message: errors.join(', '),
                processPayments
            };
        }

        return {
            error: false,
            processPayments
        };

    }

    /**
     * isFraudDetected
     * @param order
     * @returns {boolean}
     */
    isFraudDetected(order) {
        return order.state === StatusConstant.STATE_PAYMENT_REVIEW && order.status === StatusConstant.STATUS_FRAUD
    }

    /**
     * get list order statuses
     * @return {Promise<any>}
     */
    getListOrderStatuses() {
        return this.getResourceModel().getListOrderStatuses();
    }

    /**
     * get order by increment ids
     * @param ids
     * @returns {*|Promise}
     */
    getOrderByIncrementIds(ids) {
        return this.getResourceModel().getOrderByIncrementIds(ids);
    }

    /**
     * get out of permission orders
     * @param queryService
     * @param isSync
     * @returns {*|Promise<any>}
     */
    getOutOfPermissionOrders(queryService = {}, isSync = false) {
        return this.getResourceModel().getOutOfPermissionOrders(queryService, isSync);
    }

    /**
     * Get Order billing address
     *
     * @param order
     * @returns {*}
     */
    getBillingAddress(order) {
        if (order && order.addresses && Array.isArray(order.addresses) && order.addresses.length) {
            return order.addresses.find(address => address.address_type === AddressConstant.BILLING_ADDRESS_TYPE);
        }
        return null;
    }

    /**
     * get data by id
     * @param incrementId
     * @return {*|Promise<any>}
     */
    getByIncrement(incrementId) {
        return this.getResourceModel().loadOrderByIncrement(incrementId);
    }
}

/** @type OrderService */
let orderService = ServiceFactory.get(OrderService);

export default orderService;
