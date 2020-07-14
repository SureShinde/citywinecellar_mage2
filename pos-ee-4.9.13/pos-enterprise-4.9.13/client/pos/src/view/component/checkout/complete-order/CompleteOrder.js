import React from 'react';
import PropTypes from 'prop-types';
import CoreComponent from "../../../../framework/component/CoreComponent";
import CompleteOrderPaymentItem from "./CompleteOrderPaymentItem";
import CoreContainer from "../../../../framework/container/CoreContainer";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";
import QuoteAction from "../../../action/checkout/QuoteAction";
import ContainerFactory from "../../../../framework/factory/ContainerFactory";
import CheckoutAction from "../../../action/CheckoutAction";
import CurrencyHelper from "../../../../helper/CurrencyHelper";
import {Modal} from 'react-bootstrap';
import SmoothScrollbar from "smooth-scrollbar";
import PaymentHelper from "../../../../helper/PaymentHelper";
import {toast} from "react-toastify";
import i18n from "../../../../config/i18n";
import PaymentAction from "../../../action/PaymentAction";
import StoreCreditService from "../../../../service/store-credit/StoreCreditService";
import PaymentConstant from "../../../constant/PaymentConstant";
import CheckoutService from "../../../../service/checkout/CheckoutService";
import NumberHelper from "../../../../helper/NumberHelper";
import layout from "../../../../framework/Layout";
import {fire} from "../../../../event-bus";
import QuoteService from "../../../../service/checkout/QuoteService";

class CompleteOrder extends CoreComponent {
    static className = 'CompleteOrder';
    setCompleteOrderElement = element => {
        this.complete_order = element;
        if (this.scrollbar) {
            SmoothScrollbar.destroy(this.scrollbar);
        }
        if (!this.scrollbar && this.complete_order) {
            this.scrollbar = SmoothScrollbar.init(this.complete_order);
        }
    };

    disableButtonComplete = false;

    /**
     * Constructor
     * @param props
     */
    constructor(props) {
        super(props);
        let roundGrandTotal = CurrencyHelper.round(props.quote.grand_total,
            CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
        let roundTotalPaid = CurrencyHelper.round(this.calculateTotalPaid(props),
            CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
        let current_remain = roundGrandTotal - roundTotalPaid;
        this.state = {
            current_remain: current_remain,
            remain: CurrencyHelper.convertToBase(current_remain),
            total: props.quote.base_grand_total,
            isOpenCompleteOrderPopup: false,
            isOpenWarningPopup: false,
            isPlacing: false,
            modalContent: "",
            warningModalContent: "",
        };
        this.disableButtonComplete = false;

        // show alert confirm if refresh
        window.onbeforeunload = this.onbeforeunload;
    }

    componentWillUnmount() {
        window.onbeforeunload = null;
    }

    /**
     * Component will receive props - set remain state
     *
     * @param nextProps
     */
    async componentWillReceiveProps(nextProps) {
        let current_remain = nextProps.quote.grand_total - this.calculateTotalPaid(nextProps);
        await this.setState(
            {
                remain: CurrencyHelper.convertToBase(current_remain),
                current_remain: current_remain,
                total: nextProps.quote.base_grand_total,
            }
        );

        const {error, quote} = nextProps;
        if (error) {
            this.disableButtonComplete = false;
            await this.setState({isPlacing: false});
        }

        let isSuccessAll = PaymentHelper.isSuccessAll(quote.payments);
        if (isSuccessAll && this.state.isPlacing) {
            return this._placeAndPrint(
                nextProps.additionalData ? {...nextProps.additionalData} : false
            );
        }

    }

    onbeforeunload = () => {
        const {quote} = this.props;
        return PaymentHelper.hasPaidOrWaitingGatewayPayment(quote.payments) ? true : null;
    };

    /**
     * Calculate total paid
     * @returns {number}
     */
    calculateTotalPaid(props) {
        let totalPaid = 0;
        props.quote.payments.map((item) => totalPaid += item.amount_change ?
            NumberHelper.addNumber(item.amount_paid, item.amount_change) : item.amount_paid);
        return totalPaid;
    }

    /**
     * get payment info
     *
     * @param method
     * @param index
     * @return {{index: *}}
     */
    getPaymentData(method, index) {
        let paymentData;
        // check store credit and return object store credit default
        if (method === PaymentConstant.STORE_CREDIT) {
            paymentData = StoreCreditService.storeCreditDefault();
        } else {
            paymentData = this.props.payments.find((item) => item.code === method);
        }
        return {...paymentData, index: index};
    }

    /**
     * Handle click delete payment
     * @param indexPayment
     */
    deletePayment(indexPayment) {
        let quote = this.props.quote;
        let payments = quote.payments.filter((item, index) => index !== indexPayment);
        this.props.actions.setPayments(payments);
        if (payments.length === 0 && quote.grand_total) {
            this.props.resetState();
        }
    }

    /**
     * Add more payment methods
     */
    addPayment() {
        this.props.addPayment(this.state.remain);
    }

    /**
     * Handle click edit payment
     * @param paymentData
     */
    editPayment(paymentData) {
        this.props.selectPayment(paymentData, this.state.remain);
    }

    /**
     * handle click complete order
     */
    clickCompleteOrder() {
        if (this.disableButtonComplete) return;
        if (this.state.remain > 0) {
            this.markAsPartial();
        } else if (this.state.remain < 0 && Math.abs(this.state.remain) > QuoteService.getTotalCash(this.props.quote)) {
            this.showWarningPopup();
        } else {
            this.placeOrder();
        }
    }

    /**
     * Place Order
     * @return {Promise<any>|*|{type: string, quote: *}|Promise<{order_increment_id: *}>}
     */
    async placeOrder() {
        if (this.disableButtonComplete) return;

        const {quote} = this.props;
        this.setState({isOpenCompleteOrderPopup: false});
        this.disableButtonComplete = true;

        if ( !window.navigator.onLine && PaymentHelper.isRequireInternet(quote)) {
            return toast.error(
                i18n.translator.translate(PaymentConstant.LOST_INTERNET_CONNECTION_MESSAGE),
                {
                    className: 'wrapper-messages messages-warning',
                    autoClose: 3000
                }
            );
        }

        this.setState({isPlacing: true});

        let isSuccessAll = PaymentHelper.isSuccessAll(quote.payments);
        // start process payment
        if (!isSuccessAll) {
            if (!CheckoutService.getPreOrder()) {
                CheckoutService.generatePreOrder(quote);
            }

            await this.props.actions.prepareProcessPayment(this.props.quote);
            return this.props.actions.processPayment(this.props.quote);
        }

        this._placeAndPrint();
    }

    /**
     *
     * @param additionalData
     * @private
     */
    _placeAndPrint(additionalData = false) {
        this.props.actions.placeOrder(this.props.quote, additionalData);
        this.setState({
            isPlacing: false
        });
        /**
         *   trigger print
         *
         *   todo: config auto show print or not
         *
         */
    }

    /**
     * Handle click mark as partial
     */
    markAsPartial() {
        let totalPaid = CurrencyHelper.format(this.calculateTotalPaid(this.props), null, null);
        let total = this.getDisplayValue(this.state.total);
        let modalContent = this.props.t(
                "{{totalPaid}} has been paid towards the {{total}} total of this order. " +
                "Press ‘Complete order’ to continue." ,
                {
                    totalPaid: totalPaid,
                    total: total
                }
            );
        this.setState({
            isOpenCompleteOrderPopup: true,
            modalContent: modalContent
        });
    }

    /**
     * get display value
     * @param value
     * @returns {*}
     */
    getDisplayValue(value) {
        return CurrencyHelper.convertAndFormat(value);
    }

    /**
     * Cancel popup
     */
    cancelPopup() {
        this.setState({
            isOpenCompleteOrderPopup: false,
        });
    }

    /**
     * Close warning popup
     */
    closeWarningPopup() {
        this.setState({
            isOpenWarningPopup: false,
        });
    }

    /**
     * Show warning popup
     */
    showWarningPopup() {
        let modalContent = this.props.t("Change amount can't be greater than total amount of cash payment.");
        this.setState({
            isOpenWarningPopup: true,
            warningModalContent: modalContent
        });
    }

    /**
     * Render template
     * @returns {*}
     */
    template() {
        const {quote, t} = this.props;
        const {remain} = this.state;
        let isWaitingProcessPaymentComplete = PaymentHelper.isWaitingProcessPaymentComplete(quote.payments);
        let title = remain >= 0 ? 'Remaining' : 'Change';
        let blockContentClassName = ['block-content'];
        let isNoPaymentScreen = !quote.payments.length && !remain;

        if (isNoPaymentScreen) {
            title = 'Total';
            blockContentClassName.push('block-no-payment');
        }

        fire(
            'component_complete_order_template_calculate_block_content_class_name_after',
            { component: this, blockContentClassName }
        );

        return (
            <div className="wrapper-payment active" id="wrapper-payment3">
                <div className="block-title">
                    <strong className="title">
                        {this.props.t(this.state.remain > 0 ? 'Split Payment' : 'Payment')}
                    </strong>
                </div>
                <div className={blockContentClassName.join(' ')}
                     data-scrollbar
                     ref={this.setCompleteOrderElement}
                >
                    <ul className="payment-total">
                        {
                            <li className="total">
                                <span className="label">{this.props.t(title)}</span>
                                <span className="value">{CurrencyHelper.format(Math.abs(this.state.current_remain), null, null)}</span>
                            </li>
                        }
                    </ul>
                    <div>
                        {
                            !quote.payments.length && !quote.grand_total ? (
                                <div className="no-payment-required">
                                    <span className="img"/>
                                    <p>{t('No Payment method required.')}</p>
                                </div>
                            ) : ''
                        }
                    </div>
                    <div>
                        {
                            quote.payments.length > 0 ?
                                this.props.quote.payments.map((item, index) => {
                                    let paymentData = this.getPaymentData(item.method, index);
                                    return <CompleteOrderPaymentItem
                                        key={index}
                                        payment={item}
                                        paymentData={paymentData}
                                        deletePayment={(indexPayment) => this.deletePayment(indexPayment)}
                                        editPayment={(paymentData) => this.editPayment(paymentData)}
                                    />
                                })
                                :
                                ''
                        }
                        {
                            this.state.remain > 0 ? (
                                <div className="payment-full-amount add-payment"
                                     onClick={() => this.addPayment()}>
                                    <div className="info">
                                        <span className="label">{this.props.t('Add Payment')}</span>
                                    </div>
                                    <a className="add-cash"> </a>
                                </div>
                            ) : ''
                        }
                    </div>
                </div>
                {layout('order')('complete_order_layout')('complete_order_button_before')()(this,isWaitingProcessPaymentComplete)}
                <div className="block-bottom old">
                    <div className="actions-accept">
                        <button
                            className={"btn btn-default btn-complete " +
                            ((isWaitingProcessPaymentComplete || this.disableButtonComplete) ? 'disabled' : '')}
                            type="button"
                            data-toggle="modal"
                            data-target="#popup-completeOrder"
                            onClick={() => this.clickCompleteOrder()}>
                            {this.props.t(this.state.remain > 0 ? 'Mark as Partial' : 'Complete Order')}
                        </button>
                    </div>
                </div>
                {layout('order')('complete_order_layout')('complete_order_button_after')()(this,isWaitingProcessPaymentComplete)}
                <div>
                    <Modal
                        bsSize={"small"}
                        className={"popup-messages old"}
                        id={"popup-completeOrder"}
                        show={this.state.isOpenCompleteOrderPopup} onHide={() => this.cancelPopup()}>
                        <Modal.Body>
                            <h3 className="title">{this.props.t('Payment Incomplete')}</h3>
                            <p>{this.state.modalContent}</p>
                        </Modal.Body>
                        <Modal.Footer className={"modal-footer actions-2column"}>
                            <a onClick={() => this.cancelPopup()}>{this.props.t('Cancel')}</a>
                            <a onClick={() => this.placeOrder()}>{this.props.t('Complete Order')}</a>
                        </Modal.Footer>
                    </Modal>
                    <Modal
                        bsSize={"small"}
                        className={"popup-messages"}
                        id={"popup-warning-complete-order"}
                        show={this.state.isOpenWarningPopup} onHide={() => this.closeWarningPopup()}>
                        <Modal.Body>
                            <h3 className="title">{this.props.t('Warning')}</h3>
                            <p>{this.state.warningModalContent}</p>
                        </Modal.Body>
                        <Modal.Footer className={"modal-footer"}>
                            <a onClick={() => this.closeWarningPopup()}>{this.props.t('Ok')}</a>
                        </Modal.Footer>
                    </Modal>
                </div>
            </div>
        )
    }
}

class CompleteOrderContainer extends CoreContainer {
    static className = "CompleteOrderContainer";

    /**
     * map state to props
     * @param state
     * @returns {{quote: *, payments: Payment.state.payment.payments|String.payments|paymentReducer.payments}}
     */
    static mapState(state) {
        let {quote} = state.core.checkout;
        let {payments} = state.core.checkout.payment;
        let {
            error,
            response,
            additionalData
        } = state.core.checkout.completeOrder;
        return {
            quote,
            payments,
            error,
            response,
            additionalData
        };
    }

    /**
     * map dispatch to props
     *
     * @param dispatch
     * @return {{actions: {setPayments: function(*=, *=): *, placeOrder: function(*=, *=): *, prepareProcessPayment: function(*=): *, processPayment: function(*=): *}}}
     */
    static mapDispatch(dispatch) {
        return {
            actions: {
                setPayments: (payments, remain) => dispatch(QuoteAction.setPayments(payments, remain)),
                placeOrder: (quote, additionalData) => dispatch(CheckoutAction.placeOrder(quote, additionalData)),
                prepareProcessPayment: (quote) => dispatch(PaymentAction.prepareProcessPayment(quote)),
                processPayment: (quote) => dispatch(CheckoutAction.processPayment(quote)),
            }
        }
    }
}

CompleteOrder.propTypes = {
    selectPayment: PropTypes.func.isRequired,
    resetState: PropTypes.func.isRequired,
};

export default ContainerFactory.get(CompleteOrderContainer).getConnect(
    ComponentFactory.get(CompleteOrder)
);
