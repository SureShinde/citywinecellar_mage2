import React, {Fragment} from 'react';
import {CoreComponent} from "../../../../../../framework/component/index";
import CoreContainer from "../../../../../../framework/container/CoreContainer";
import ComponentFactory from "../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../framework/factory/ContainerFactory";
import OrderHelper from "../../../../../../helper/OrderHelper";
import NumberHelper from "../../../../../../helper/NumberHelper";
import PaymentService from "../../../../../../service/payment/PaymentService";
import CreditmemoService from "../../../../../../service/sales/order/CreditmemoService";
import CurrencyHelper from "../../../../../../helper/CurrencyHelper";
import PaymentConstant from "../../../../../constant/PaymentConstant";
import ConfigHelper from "../../../../../../helper/ConfigHelper";
import PaymentHelper from "../../../../../../helper/PaymentHelper";

class CreateCreditmemoStepPaymentAddPaymentComponent extends CoreComponent {
    static className = 'CreateCreditmemoStepPaymentAddPaymentComponent';

    paymentInputElements = [];

    setPaymentInputElement = (element, index) => {
        this.paymentInputElements[index] = element;
    };

    setNumPadBackDropElement = element => this.numPadBackDropElement = element;
    setNumPadElement = element => this.numPadElement = element;
    setNumPadAmountElement = element => this.numPadAmountElement = element;

    acceptKeyboardKeys = ["00", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "delete", "backspace"];

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            payment_list: [],
            show_payment_list: false,
            show_numpad: false,
            numpad_payment: null,
            numpad_payment_index: 0,
            numpad_amount: 0
        };
        this.getPaymentList(props);
        document.body.addEventListener('keyup', event => this.onKeyupKeyboard(event.key));
    }

    /**
     * Get payment list
     * @return {Promise<void>}
     */
    async getPaymentList(props) {
        let payment_list = [];
        if (ConfigHelper.isEnableStoreCredit() && !props.order.customer_is_guest) {
            payment_list.push({
                'code': PaymentConstant.STORE_CREDIT,
                'is_default': 0,
                'is_pay_later': 0,
                'is_reference_number': 0,
                'is_suggest_money': 0,
                'title': 'Store Credit',
                'type': PaymentConstant.PAYMENT_TYPE_OFFLINE
            })
        }
        let payments = await PaymentService.getAll();
        if (payments && payments.length) {
            let order = this.props.order;
            let selectedPayments = this.props.payments;
            if (order.payments && Array.isArray(order.payments) && order.payments.length) {
                let allRefundedPayments = order.payments.filter(
                    orderPayment => orderPayment.type === PaymentConstant.TYPE_REFUND
                );

                payments.forEach((payment) => {
                    if (!CreditmemoService.isAcceptedPayment(payment.code)) {
                        return;
                    }

                    if (!CreditmemoService.isUseTransactionPayment(payment.code)) {
                        return payment_list.push(payment);
                    }

                    let allSameMethodRefundedPayments = allRefundedPayments.filter(
                        orderPayment => orderPayment.method === payment.code
                    );

                    let paidPayments = this.props.order.payments.filter(
                        orderPayment =>
                        orderPayment.method === payment.code
                        && orderPayment.type === PaymentConstant.TYPE_CHECKOUT
                    );

                    paidPayments.forEach((paidPayment) => {
                        let selected = selectedPayments.find(
                            selectedPayment => paidPayment.increment_id === selectedPayment.orderPayment.increment_id
                        );

                        if (selected) {
                            return;
                        }

                        let refundedPayments = allSameMethodRefundedPayments.filter(
                            refundedPayment => refundedPayment.parent_increment_id
                            && refundedPayment.parent_increment_id === paidPayment.increment_id
                        );

                        let amountRefunded = refundedPayments.reduce((current, next) => current + next.amount_paid, 0);

                        if (amountRefunded >= paidPayment.amount_paid) {
                            return;
                        }

                        payment_list.push({
                            ...payment,
                            title: payment.title,
                            amount_paid: paidPayment.amount_paid,
                            amount_refunded: amountRefunded,
                            orderPayment: {...paidPayment},
                        });
                    })

                });
            }
        }
        this.setState({payment_list: payment_list}, () => this.preparePayment());
    }

    /**
     * Check order has store credit payment
     * @param order
     * @return {boolean}
     */
    orderHasStoreCredit(order) {
        if (order && order.payments && order.payments.length) {
            return !!order.payments.find(payment => payment.method === PaymentConstant.STORE_CREDIT);
        }
        return false;
    }

    /**
     * Prepare payment
     *
     * @return {boolean}
     */
    preparePayment() {
        let order = this.props.order;
        let payments = this.props.payments;
        if (payments && payments.length) {
            return false;
        }

        let addedPaymentCodes = [];
        let nonIsUseTransactionPayment = [];

        if (!order.payments || !order.payments.length) {
            return false;
        }
        const { payment_list } = this.state;
        /** Data/Checkout/Order/PaymentInterface orderPayment */
        order.payments.forEach(orderPayment => {
            if(orderPayment.type === PaymentConstant.TYPE_REFUND) {
                return;
            }
            /** get payment list, (which is not added + cash, webpos credit card )*/
            let paymentList = payment_list.filter(payment =>
                !addedPaymentCodes.includes(payment.increment_id)
                || [PaymentConstant.CASH, PaymentConstant.CREDIT_CARD].includes(payment.code)
            );

            /** Data/Payment/PaymentInterface payment */
            let payment = paymentList.find(payment => payment.code === orderPayment.method);

            if (!payment) {
                return;
            }

            /** record as added */
            addedPaymentCodes.push(payment.increment_id);
            if (CreditmemoService.isUseTransactionPayment(payment.code)) {
                /** Stripe Terminal, Tyro,... */
                return this.addPayment(payment, this.getRefundableAmountForPayment(payment), paymentList);
            }

            if (payment.code !== PaymentConstant.STORE_CREDIT) {
                if (nonIsUseTransactionPayment.indexOf(payment.code) < 0) {
                    nonIsUseTransactionPayment.push(payment.code);
                    return this.addPayment(payment, 0, paymentList);
                } else {
                    return;
                }
            }

            /** Only for Store Credit  */
            let totalPaid = 0,
                totalRefund = 0;
            order.payments.forEach(payment => {
                if (payment.method !== PaymentConstant.STORE_CREDIT) {
                    return;
                }

                if (payment.type !== PaymentConstant.TYPE_REFUND) {
                    totalPaid = NumberHelper.addNumber(totalPaid, payment.amount_paid);
                    return;
                }

                totalRefund = NumberHelper.addNumber(totalRefund, payment.amount_paid);
            });

            let maxRefund = Math.max(NumberHelper.minusNumber(totalPaid, totalRefund), 0);
            let remaining = this.props.getRemaining();
            if (!ConfigHelper.isSpentCreditOnShippingFee()) {
                remaining = NumberHelper.minusNumber(remaining, this.props.creditmemo.shipping_amount);
            }
            this.addPayment(payment, Math.min(maxRefund, remaining), paymentList);
        })
    }

    /**
     * Show payment list
     */
    showPaymentList() {
        if (!this.state.show_payment_list) {
            this.setState({show_payment_list: true});
            if (this.props.scrollbar) {
                setTimeout(() => {
                    this.props.scrollbar.scrollTo(this.props.scrollbar.offset.x, this.props.scrollbar.limit.y);
                }, 50)
            }
        }
    }

    /**
     * Check can add payment
     * @param payment
     * @return {boolean}
     */
    canAddPayment(payment) {
        if (payment.code !== PaymentConstant.STORE_CREDIT) {
            return true;
        }
        return !this.props.payments.find(payment => payment.method === PaymentConstant.STORE_CREDIT);
    }

    /**
     *
     * @param quotePayment
     * @param index
     */
    async removePayment(quotePayment, index) {

        // add item into list
        if (CreditmemoService.isUseTransactionPayment(quotePayment.method)) {

            let payment = await PaymentService.getByCode(quotePayment.method);

            this.setState({
                payment_list: [...this.state.payment_list, {
                    ...quotePayment,
                    ...payment,
                }]
            })
        }


        this.props.removePayment(index);
    }

    /**
     * Add payment
     *
     * @param payment
     * @param amount_paid
     * @param paymentList
     */
    addPayment(payment, amount_paid, paymentList) {
        paymentList = paymentList || this.state.payment_list;

        let order = this.props.order,
            creditmemo = this.props.creditmemo,
            remaining = this.props.getRemaining();
        if (remaining <= 0) {
            return false;
        }
        if (typeof amount_paid === 'undefined' || amount_paid === null) {
            amount_paid = remaining;
        }
        if (payment.code === PaymentConstant.STORE_CREDIT) {
            if (!ConfigHelper.isEnableStoreCredit()) {
                return false;
            }
            if (!ConfigHelper.isSpentCreditOnShippingFee()) {
                if (NumberHelper.minusNumber(remaining, creditmemo.shipping_amount) <= 0) {
                    return false;
                }
            }
        }
        let base_amount_paid = OrderHelper.convertToBase(amount_paid, order);
        let newPayment = {
            increment_id: PaymentHelper.generateIncrement(payment.code + Math.round(Math.random() * 10000)),
            method: payment.code,
            title: payment.title,
            amount_paid: amount_paid,
            base_amount_paid: base_amount_paid,
            amount_refunded: payment.amount_refunded ? payment.amount_refunded : undefined,
            reference_number: "",
            type: PaymentConstant.TYPE_REFUND,
            orderPayment: payment.orderPayment || false,
            parent_increment_id: payment.orderPayment ? payment.orderPayment.increment_id : undefined
        };
        this.props.addPayments([newPayment]);

        // remove item in list
        if (CreditmemoService.isUseTransactionPayment(newPayment.method)) {
            let payment_list = paymentList.filter(paymentItem =>
                this.canAddPayment(paymentItem)
                && (
                    !paymentItem.orderPayment
                    || paymentItem.increment_id !== newPayment.increment_id
                )
            );

            this.setState({
                payment_list
            })
        }

        this.setState({show_payment_list: false});
    }

    /**
     * Check payment is reference number
     *
     * @param paymentCode
     * @return {boolean}
     */
    isReferenceNumber(paymentCode) {
        return !!this.state.payment_list.find(payment => payment.code === paymentCode && payment.is_reference_number);
    }

    /**
     * Show number pad
     *
     * @param event
     * @param payment
     * @param paymentIndex
     */
    showNumPad(event, payment, paymentIndex) {
        event.target.blur();
        this.calculateNumpadPosition(event);
        document.body.appendChild(this.numPadElement);
        document.body.appendChild(this.numPadBackDropElement);
        this.numPadAmountElement.value = CurrencyHelper.formatNumberStringToCurrencyString(
            0, this.props.order.order_currency_code
        );
        this.onKeyupKeyboard = this.clickNumPad;
        this.setState({
            show_numpad: true,
            numpad_payment: payment,
            numpad_payment_index: paymentIndex,
            numpad_amount: 0
        });
    }

    /**
     * Calculate numpad possition
     *
     * @param event
     */
    calculateNumpadPosition(event) {
        this.setState({numpad_left: event.target.getBoundingClientRect().left - 295});
        this.setState({numpad_top: event.target.getBoundingClientRect().top - 155});
    }

    /**
     * Hide number pad
     */
    hideNumpad( isConfirm = false ) {
        document.body.removeChild(this.numPadElement);
        document.body.removeChild(this.numPadBackDropElement);
        this.onKeyupKeyboard = this.disableKeyupKeyboard;
        if(isConfirm){
            this.setPaymentAmount(this.state.numpad_amount);
        }
        this.setState({show_numpad: false});
    }

    /**
     * click numpad
     *
     * @param key
     */
    clickNumPad(key) {
        if (!this.acceptKeyboardKeys.includes(key.toString().toLowerCase())) {
            return false;
        }
        let numpadAmount = this.state.numpad_amount.toString().replace(".", "");
        if (["delete", "backspace"].includes(key.toString().toLowerCase())) {
            numpadAmount = numpadAmount.substr(0, numpadAmount.length - 1);
        } else {
            numpadAmount = numpadAmount + key.toString();
        }
        numpadAmount = this.putDecimalSymbol(numpadAmount);
        this.numPadAmountElement.value = CurrencyHelper.formatNumberStringToCurrencyString(
            numpadAmount, this.props.order.order_currency_code
        );
        this.setState({numpad_amount: numpadAmount});
        this.setPaymentAmount(numpadAmount);
    }

    /**
     * Set payment amount
     *
     * @param amount
     */
    setPaymentAmount(amount) {
        let order = this.props.order,
            creditmemo = this.props.creditmemo,
            payment = this.state.numpad_payment,
            paymentIndex = this.state.numpad_payment_index,
            error = "";

        if (CreditmemoService.isUseTransactionPayment(payment.method)) {
            let refundableAmount = this.getRefundableAmountForPayment(payment);
            if (amount > refundableAmount) {
                error = this.props.t('The maximum value allowed to refund by this method is {{amount}}',
                    {amount: OrderHelper.formatPrice(refundableAmount, order)});

                return this.props.updatePayment(payment, paymentIndex, {amount_paid: refundableAmount, error});
            }
        }
        let remaining = this.getRemainingWithout(payment);
        if (payment.method === PaymentConstant.STORE_CREDIT) {
            if (!ConfigHelper.isSpentCreditOnShippingFee()) {
                remaining = NumberHelper.minusNumber(remaining, creditmemo.shipping_amount);
            }
        }
        if (amount > remaining) {
            error = this.props.t('The maximum value allowed to refund by this method is {{amount}}',
                {amount: OrderHelper.formatPrice(remaining, order)})
        }
        let amount_paid = Math.min(amount, remaining);
        this.props.updatePayment(payment, paymentIndex, {amount_paid, error});
    }

    /**
     * Event to press keyboard after show numpad
     *
     * @param key
     */
    onKeyupKeyboard(key) {
        return key;
    }

    /**
     * Disable press keyboard event after hide numpad
     *
     * @param key
     * @return {null}
     */
    disableKeyupKeyboard(key) {
        return key;
    }

    /**
     * Put decimal amount
     *
     * @param amount
     * @return {string}
     */
    putDecimalSymbol(amount) {
        amount = amount.toString();
        amount = "00000" + amount;
        let currencyFormat = CurrencyHelper.getCurrencyFormat(this.props.order.order_currency_code);
        let intPrice = amount,
            decimalPrice = "";
        if (currencyFormat.precision > 0) {
            intPrice = amount.substr(0, amount.length - currencyFormat.precision);
            decimalPrice = amount.substr(-currencyFormat.precision);
        }
        intPrice = intPrice.replace(/^0+/, '');
        if (!intPrice) {
            intPrice = "0";
        }
        return intPrice + "." + decimalPrice;
    }

    /**
     * Change reference number
     *
     * @param payment
     * @param paymentIndex
     * @param reference_number
     */
    changeReferenceNumber(payment, paymentIndex, reference_number) {
        this.props.updatePayment(payment, paymentIndex, {reference_number});
    }

    /**
     *
     * @param payment
     * @return {number}
     */
    getRefundableAmountForPayment(payment) {
        let refundableAmount = payment.orderPayment.amount_paid;
        if (payment.amount_refunded) {
            refundableAmount = NumberHelper.minusNumber(refundableAmount, payment.amount_refunded);
        }
        let remaining = this.getRemainingWithout(payment);
        if (refundableAmount > remaining) {
            return remaining;
        }

        return refundableAmount;
    }

    /**
     *
     * @param payment
     * @return {number}
     */
    getTotalPaymentsWithout(payment) {
        let totalOtherPaymentsAmount = 0;
        let selectedPayments = this.props.payments;
        selectedPayments.forEach((selectedPayment) => {
            if (payment.method === selectedPayment.method) {
                if (!payment.increment_id) {
                    return;
                }

                if (payment.increment_id === selectedPayment.increment_id) {
                    return;
                }

                totalOtherPaymentsAmount
                    = NumberHelper.addNumber(totalOtherPaymentsAmount, selectedPayment.amount_paid);
                return;
            }

            totalOtherPaymentsAmount = NumberHelper.addNumber(totalOtherPaymentsAmount, selectedPayment.amount_paid);
        });

        return totalOtherPaymentsAmount;
    }

    getRemainingWithout(payment) {
        let creditmemo = this.props.creditmemo;
        let totalOtherPaymentsAmount = this.getTotalPaymentsWithout(payment);
        return NumberHelper.minusNumber(creditmemo.grand_total, totalOtherPaymentsAmount);
    }

    /**
     *
     * @param payment
     * @return {*}
     */
    getPaymentImageContent(payment) {
        if (!payment.icon) {
            const paymentCode = payment.code || payment.method;
            return <div className={"img image-default image-" + paymentCode}/>;
        }

        return <span className={"img"}><img alt={"payment-icon"} className={"img payment-offline-icon"} src={payment.icon}/></span>
    }

    /**
     *
     * @param payment
     * @param amountRefunded
     * @return {*}
     */
    getPaymentDescriptionContent(payment, amountRefunded) {
        if (!payment.orderPayment) {
            return null;
        }
        return (
            <Fragment>
                <span className="additional-info">
                    {this.props.t('Ref')}:&nbsp;{payment.orderPayment.reference_number}
                </span>
                <span className="additional-info">
                    {this.props.t('Paid')}:&nbsp;
                    {OrderHelper.formatPrice(
                        payment.orderPayment.amount_paid, this.props.order
                    )}
                </span>
                <span className="additional-info">
                    {this.props.t('Refunded')}:&nbsp;
                    {amountRefunded}
                </span>
            </Fragment>
        )
    }

    /**
     *
     * @param index
     * @param payment
     * @param amountRefunded
     * @return {*}
     */
    getPaymentContent(index, payment, amountRefunded) {
        return (
            <div key={index + payment.code}
                 className="item"
                 onClick={() => this.addPayment(
                     payment, this.getRefundableAmountForPayment(payment)
                 )}>
                {this.getPaymentImageContent(payment)}
                <span className="title">{this.props.t(payment.title)}</span>
                {this.getPaymentDescriptionContent(payment, amountRefunded)}
            </div>
        );
    }

    /**
     *
     * @param methods
     * @return {*}
     */
    getRefundByTransactionPaymentMethodsContent(methods) {
        if (!methods || !methods.length) {
            return null;
        }
        return (
            <div className={"add-payment-refund" + (this.state.show_payment_list ? "" : " hidden")}>
                {
                    methods.map((payment, index) => {
                        const amountRefunded = OrderHelper.formatPrice(payment.amount_refunded, this.props.order);
                        return this.getPaymentContent(index, payment, amountRefunded)
                    })
                }
            </div>
        )
    }

    /**
     *
     * @param methods
     * @return {*}
     */
    getAddAblePaymentMethodsContent(methods) {
        if (!methods || !methods.length) {
            return null;
        }
        return (
            <div className={"add-payment-refund" + (this.state.show_payment_list ? "" : " hidden")}>
                {methods.map((payment, index) => {
                    return <div key={index + payment.code}
                                className="item"
                                onClick={ () => this.addPayment(payment, 0) }>
                        {this.getPaymentImageContent(payment)}
                        <span className="title">{this.props.t(payment.title)}</span>
                    </div>
                })}
            </div>
        )
    }

    /**
     *
     * @return {*}
     */
    getAddPaymentRefundContent() {
        const { payment_list } = this.state;
        const { t } = this.props;

        let addableMethods = payment_list.filter(payment =>
            this.canAddPayment(payment) && !CreditmemoService.isUseTransactionPayment(payment.code)
        );

        let refundByTransactionPaymentMethods = payment_list.filter(payment =>
            this.canAddPayment(payment) && CreditmemoService.isUseTransactionPayment(payment.code)
        );

        return <Fragment>
            <div className="payment-full-amount add-payment"
                 onClick={() => this.showPaymentList()}>
                <div className="info">
                    <span className="label">{t('Add Payment')}</span>
                </div>
                <a className="add-cash">&nbsp;</a>
            </div>
            {this.getRefundByTransactionPaymentMethodsContent(refundByTransactionPaymentMethods)}
            {this.getAddAblePaymentMethodsContent(addableMethods)}
        </Fragment>
    }

    /**
     *
     * @param payment
     * @return {*}
     */
    getLabelSelectedPaymentRefundContent(payment) {
        return <span className="label">
            {payment.orderPayment
                ? (
                <Fragment>
                        <span className="title">
                            {this.props.t(payment.title)}
                        </span>
                            <span className="additional-info">
                            {this.props.t('Ref')}:&nbsp;{payment.orderPayment.reference_number}
                        </span>
                            <span className="additional-info">
                            {this.props.t('Paid')}:&nbsp;
                                {OrderHelper.formatPrice(
                                    payment.orderPayment.amount_paid, this.props.order
                                )}
                        </span>
                            <span className="additional-info">
                            {this.props.t('Refunded')}:&nbsp;
                                {OrderHelper.formatPrice(
                                    payment.amount_refunded, this.props.order
                                )}
                        </span>
                </Fragment>
            )
                : this.props.t(payment.title)
            }
        </span>
    }

    /**
     *
     * @return {*}
     */
    getSelectedPaymentRefundContent() {
        return this.props.payments.map((payment, index) => {
            let isAllowUseRefercenceNo = this.isReferenceNumber(payment.method)
                && !PaymentHelper.hasUsingEWallet(payment.method);

            return <div key={new Date().getTime() + index} className="payment-full-amount">
                <div className="info">
                    {this.getPaymentImageContent(payment)}
                    {
                        this.getLabelSelectedPaymentRefundContent(payment)
                    }
                    <input type="text"
                           ref={element => this.setPaymentInputElement(element, index)}
                           className="value form-control"
                           defaultValue={OrderHelper.formatPrice(payment.amount_paid, this.props.order)}
                           onClick={(event) => this.showNumPad(event, payment, index)}/>
                    {
                        isAllowUseRefercenceNo ?
                            <input type="text"
                                   className="reference form-control"
                                   placeholder={this.props.t('Reference No')}
                                   defaultValue={payment.reference_number}
                                   onBlur={(event) => this.changeReferenceNumber(
                                       payment, index, event.target.value)}/>
                            : null
                    }
                    <div className="validation-text">
                        {payment.error}
                    </div>
                </div>
                <a className="remove-cash" onClick={() => this.removePayment(payment, index)}>&nbsp;</a>
            </div>
        })
    }

    /**
     * template to render
     * @returns {*}
     */
    template() {
        return (
            <Fragment>
                <div className="box">
                    <div className="box-title">
                        <strong className="title">{this.props.t('Remaining')}</strong>
                        <span className="price">
                        {OrderHelper.formatPrice(this.props.getRemaining(), this.props.order)}
                    </span>
                    </div>
                    {
                        this.getSelectedPaymentRefundContent()
                    }
                    {
                        this.props.getRemaining() > 0 ?
                            this.getAddPaymentRefundContent() :
                            null
                    }
                </div>
                <div ref={this.setNumPadElement}
                     className="popover fade left in"
                     style={{
                         display: this.state.show_numpad ? "block" : "none",
                         top: this.state.numpad_top + 'px',
                         left: this.state.numpad_left + 'px',
                     }}>
                    <div className="arrow" style={{top: "50%"}}/>
                    <div className="popover-content">
                        <div className="popup-calculator popup-calculator2">
                            <div className="product-field-qty">
                                <div className="box-field-qty">
                                    <input ref={this.setNumPadAmountElement}
                                           name="qty-catalog" id="qty-catalog"
                                           className="form-control qty"
                                           defaultValue={CurrencyHelper.formatCurrencyStringToNumberString(
                                               this.state.numpad_amount, this.props.order.order_currency_code
                                           )}/>
                                </div>
                            </div>
                            <ul className="list-number">
                                <li onClick={() => this.clickNumPad(7)}><a>7</a></li>
                                <li onClick={() => this.clickNumPad(8)}><a>8</a></li>
                                <li onClick={() => this.clickNumPad(9)}><a>9</a></li>
                                <li onClick={() => this.clickNumPad(4)}><a>4</a></li>
                                <li onClick={() => this.clickNumPad(5)}><a>5</a></li>
                                <li onClick={() => this.clickNumPad(6)}><a>6</a></li>
                                <li onClick={() => this.clickNumPad(1)}><a>1</a></li>
                                <li onClick={() => this.clickNumPad(2)}><a>2</a></li>
                                <li onClick={() => this.clickNumPad(3)}><a>3</a></li>
                                <li onClick={() => this.clickNumPad("00")}><a>00</a></li>
                                <li onClick={() => this.clickNumPad(0)}><a>0</a></li>
                                <li className="clear-number" onClick={() => this.clickNumPad("delete")}>
                                    <a><span>remove</span></a>
                                </li>
                                <li className="cancel" onClick={() => this.hideNumpad()}>
                                    <a><span>Cancel</span></a>
                                </li>
                                <li className="confirm" onClick={() => this.hideNumpad(true)}>
                                    <a><span>Confirm</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div ref={this.setNumPadBackDropElement}
                     className="modal-backdrop fade in popover-backdrop"
                     onClick={() => this.hideNumpad()}
                     style={{display: this.state.show_numpad ? "block" : "none"}}/>
            </Fragment>
        );
    }
}

class CreateCreditmemoStepPaymentAddPaymentContainer extends CoreContainer {
    static className = 'CreateCreditmemoStepPaymentAddPaymentContainer';

}

/**
 * @type {CreateCreditmemoStepPaymentAddPaymentContainer}
 */
export default ContainerFactory.get(CreateCreditmemoStepPaymentAddPaymentContainer).withRouter(
    ComponentFactory.get(CreateCreditmemoStepPaymentAddPaymentComponent)
)
