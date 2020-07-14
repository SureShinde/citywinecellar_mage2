import React, {Fragment} from 'react';
import CoreComponent from "../../../../../framework/component/CoreComponent";
import ComponentFactory from "../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../../framework/container/CoreContainer";
import CurrencyHelper from "../../../../../helper/CurrencyHelper";
import {ButtonToolbar, Popover, Tab, Tabs} from "react-bootstrap";
import QuoteService from "../../../../../service/checkout/QuoteService";
import CouponTypeConstant from "../../../../constant/salesrule/CouponTypeConstant";
import QuoteAction from "../../../../action/checkout/QuoteAction";
import PaymentHelper from "../../../../../helper/PaymentHelper";
import {Payment} from "../../Payment";
import CheckoutAction from "../../../../action/CheckoutAction";
import GiftcardForm from "./GiftcardForm";
import NumPad from "../../../lib/react-numpad";
import PosOverlayTrigger from "../../../lib/react-bootstrap/PosOverlayTrigger";
import QuoteCustomDiscountService, {CustomDiscountService} from
        "../../../../../service/checkout/quote/CustomDiscountService";
import i18n from "../../../../../config/i18n";
import NumberHelper from "../../../../../helper/NumberHelper";
import LoadingImage from "../../../../style/images/loading.gif";
import {fire} from "../../../../../event-bus";
import DeviceHelper from "../../../../../helper/DeviceHelper";
import ApiResponseConstant from "../../../../constant/ApiResponseConstant";

export class CartTotalsDiscountComponent extends CoreComponent {
    static className = 'CartTotalsDiscountComponent';

    static STATE_COUPON = 'coupon';
    static STATE_PERCENT = 'percent';
    static STATE_FIXED = 'fixed';

    /**
     *   initial state
     *z
     */
    constructor(props) {
        super(props);
        this.showOnPages = [Payment.className, GiftcardForm.className];
        this.state = {
            couponcode: '',
            message: '',
            couponCodeApplied: '',
            is_appling_coupon: false,
            current_state: CartTotalsDiscountComponent.STATE_COUPON,
            custom_discount_type: '',
            custom_discount_amount: 0,
            custom_discount_reason: ''
        };
        this.handleSelectDiscountType = this.handleSelectDiscountType.bind(this);
        this.onDiscountAmountChange = this.onDiscountAmountChange.bind(this);
        this.onDiscountReasonChange = this.onDiscountReasonChange.bind(this);
        this.handleApplyCustomDiscount = this.handleApplyCustomDiscount.bind(this);
        this.checkPromotion = this.checkPromotion.bind(this);
        this.showPopover = this.showPopover.bind(this);
        document.addEventListener('keydown', (event) => {
            const keyName = event.key;

            if (keyName === "Enter") {
                if (this.state.couponcode) {
                    this.submitCouponCode();
                }
            }
        });
    }

    /**
     *  will receive props reset state when not apply coupon
     *
     * @param nextProps
     * @return
     */
    componentWillReceiveProps(nextProps) {
        if (!nextProps.quote.coupon_code) {
            this.setState({
                message: '',
                is_appling_coupon: false,
                couponcode: '',
                couponCodeApplied: ''
            })
        }
    }

    /**
     * Hide back drop
     */
    hideBackDrop() {
        this.props.hideBackDrop();
        if (!this.state.couponCodeApplied) {
            this.resetState();
        }
        this.setState({message: '', is_appling_coupon: false});
    }

    /**
     *  Reset State to default
     *
     * @return
     */
    resetState() {
        this.setState({
            message: '',
            is_appling_coupon: false,
            couponcode: '',
            couponCodeApplied: '',
            current_state: CartTotalsDiscountComponent.STATE_COUPON,
            custom_discount_type: '',
            custom_discount_amount: 0,
            custom_discount_reason: ''
        })
    }

    /**
     *
     * @returns {CartTotalsDiscountComponent}
     */
    checkPromotion() {
        let quote = this.props.quote;
        let configCheckPromotion = true;
        let hasCustomSale = quote.items.find(item => item.product.id < 0);
        if (hasCustomSale) {
            configCheckPromotion = false;
        }
        if (configCheckPromotion) {
            QuoteService.submitCouponCode(quote, "")
                .then(async rules => {
                    if (rules && rules.length) {
                        let promotionRules = rules.filter(
                            rule => rule.coupon_type === CouponTypeConstant.COUPON_TYPE_NO_COUPON
                        );
                        if (promotionRules) {
                            quote.valid_salesrule = promotionRules;
                            QuoteService.collectTotals(quote);
                            if (!quote.grand_total) {
                                await this.props.actions.setQuote(quote);
                                this.props.actions.checkoutToSelectPayments(quote);
                            } else {
                                this.props.actions.setQuote(quote);
                            }
                        }
                    }
                })
                .catch(error => {
                    if (
                        error.code === ApiResponseConstant.EXCEPTION_CODE_FORCE_CHANGE_POS
                        || error.code === ApiResponseConstant.EXCEPTION_CODE_FORCE_SIGN_OUT
                    ) {
                        // do something
                    }
                });
        }
        return this;
    }

    /**
     *  Submit coupon code to check promotion
     *
     * @return rules
     */
    submitCouponCode() {
        let couponCode = this.state.couponcode;
        this.setState({messsge: '', is_appling_coupon: true});
        QuoteService.submitCouponCode(this.props.quote, couponCode)
            .then(async rules => {
                let quote = this.props.quote;
                if (rules && rules.length) {
                    fire('view_cart_discount_call_api_submit_coupon_code_after', {
                        quote: quote,
                        rules: rules
                    });
                    let couponRule = rules.find(rule => rule.coupon_type === CouponTypeConstant.COUPON_TYPE_SPECIFIC);
                    if (couponRule && couponRule.rule_id) {
                        quote.coupon_code = couponCode;
                        this.setState({couponCodeApplied: this.state.couponcode});
                        quote.valid_salesrule = rules;
                        this.props.actions.addCouponCodeAfter(quote);
                        QuoteService.collectTotals(quote);
                        if (!quote.grand_total) {
                            await this.props.actions.setQuote(quote);
                            this.props.actions.checkoutToSelectPayments(quote);
                        } else {
                            this.props.actions.setQuote(quote);
                        }
                    } else {
                        return this.setState({message: this.props.t('Invalid Coupon Code'), is_appling_coupon: false})
                    }
                } else {
                    return this.setState({message: this.props.t('Invalid Coupon Code'), is_appling_coupon: false})
                }
                this.hideBackDrop();
                return rules;
            })
            .catch(error => {
                if (
                    error.code === ApiResponseConstant.EXCEPTION_CODE_FORCE_CHANGE_POS
                    || error.code === ApiResponseConstant.EXCEPTION_CODE_FORCE_SIGN_OUT
                ) {
                    this.hideBackDrop();
                }
                return this.setState({
                    message: error.message,
                    is_appling_coupon: false
                })
            });
    }


    /**
     *  Remove coupon code in quote
     *
     * @return rules
     */
    removeCouponCode() {
        let quote = this.props.quote;
        this.props.actions.removeCouponCode(quote);
        this.resetState();
        this.checkPromotion();
    }


    /**
     *  Change coupon code when type input field
     *
     * @return rules
     */
    couponCodeChange(e) {
        this.setState({couponcode: e.target.value});
        this.setState({message: ''});
    }

    setInputCoupon(element) {
        if (element) {
            this.refInputCoupon = element;
        }
    }

    /**
     *
     * @param quote
     * @param total
     * @param hasPaidOrWaitingGatewayPayment
     * @returns {*}
     */
    static getDiscountElement(quote, total, hasPaidOrWaitingGatewayPayment) {
        let discountAmount = total.value;
        let classNameAmount = (discountAmount === 0) ? "add-discount" : "amount";
        let displayValue = (discountAmount === 0) ? "" : `${CurrencyHelper.format(discountAmount)}`;
        let className = ["totals-discount", "totals-action"];

        // if has any gate way payment is error or processing payment => user cannot use discount function
        if (hasPaidOrWaitingGatewayPayment) {
            classNameAmount = '';
            className = ["totals-discount"];
        }
        let title = total.title;
        let {os_pos_custom_discount_type, os_pos_custom_discount_amount} = quote;
        if (os_pos_custom_discount_type && displayValue.length > 0) {
            switch (os_pos_custom_discount_type) {
                case CustomDiscountService.DISCOUNT_TYPE_PERCENT:
                    title = i18n.translator.translate(
                        'Custom Discount ({{max}})',
                        {
                            max: NumberHelper.formatDisplayGroupAndDecimalSeparator(os_pos_custom_discount_amount) + "%"
                        }
                    );
                    break;
                case CustomDiscountService.DISCOUNT_TYPE_FIXED:
                    title = i18n.translator.translate('Custom Discount');
                    break;
                default:
                    break
            }
        }

        return (
            <li className={className.join(" ")}>
                <span className="mark">{title}</span>
                {
                    quote.checking_promotion ? (
                        <img className="checking-promotion" src={LoadingImage} alt=""/>
                    ) : (
                        <span className={classNameAmount}>{displayValue}</span>
                    )
                }
            </li>
        );
    }

    /**
     *
     * @returns {boolean}
     */
    canUseCustomDiscount() {
        let maxDiscountPercent = QuoteCustomDiscountService.getMaxDiscountPercent();
        return (maxDiscountPercent && (maxDiscountPercent > 0)) ? true : false;
    }

    /**
     *
     * @param discountAmount
     * @returns {CartTotalsDiscountComponent}
     */
    onDiscountAmountChange(discountAmount) {
        let quote = this.props.quote;
        let discountType = this.state.custom_discount_type;
        let discountData = QuoteCustomDiscountService.getDiscountData(quote, discountType, discountAmount);
        let message = "";
        discountAmount = parseFloat(discountAmount);
        switch (discountType) {
            case CustomDiscountService.DISCOUNT_TYPE_PERCENT:
                if (discountData.percent !== discountAmount) {
                    message = this.props.t('You can only discount up to {{max}}', {max: discountData.percent + "%"});
                    discountAmount = discountData.percent;
                }
                break;
            case CustomDiscountService.DISCOUNT_TYPE_FIXED:
                if (discountData.amount !== discountAmount) {
                    let max = CurrencyHelper.format(discountData.amount, CurrencyHelper.getCurrency());
                    message = this.props.t('You can only discount up to {{max}}', {max: max});
                    discountAmount = discountData.amount;
                }
                break;
            default:
                break
        }
        this.setState({custom_discount_amount: discountAmount, message: message});
        return this;
    }

    /**
     *
     * @param event
     * @returns {CartTotalsDiscountComponent}
     */
    onDiscountReasonChange(event) {
        let reason = event.target.value;
        this.setState({custom_discount_reason: reason});
        return this;
    }

    /**
     *
     * @returns {CartTotalsDiscountComponent}
     */
    async handleApplyCustomDiscount() {
        let quote = this.props.quote;
        let discountType = this.state.custom_discount_type;
        let discountAmount = this.state.custom_discount_amount;
        let discountReason = this.state.custom_discount_reason;

        if (discountType && (discountAmount > 0)) {
            delete quote.checking_promotion;
            /*await this.props.actions.removeCouponCode(quote);*/
            this.setState({
                is_appling_coupon: false,
                couponcode: '',
                couponCodeApplied: ''
            });
            this.props.actions.setCustomDiscount(quote, discountType, discountAmount, discountReason);
        } else {
            if (!this.state.couponCodeApplied) {
                this.props.actions.removeCustomDiscount(quote);
                this.checkPromotion();
            }
        }
        this.hideBackDrop();
        return this;
    }

    /**
     *
     * @returns {CartTotalsDiscountComponent}
     */
    showPopover() {
        let {
            os_pos_custom_discount_type,
            os_pos_custom_discount_amount,
            os_pos_custom_discount_reason
        } = this.props.quote;
        let state = {
            current_state: CartTotalsDiscountComponent.STATE_COUPON
        };

        fire('view_cart_discount_show_popover_before', {
            component: this,
            state: state
        });

        if (os_pos_custom_discount_type) {
            state.custom_discount_type = os_pos_custom_discount_type;
            switch (os_pos_custom_discount_type) {
                case CustomDiscountService.DISCOUNT_TYPE_PERCENT:
                    state.current_state = CartTotalsDiscountComponent.STATE_PERCENT;
                    break;
                case CustomDiscountService.DISCOUNT_TYPE_FIXED:
                    state.current_state = CartTotalsDiscountComponent.STATE_FIXED;
                    break;
                default:
                    break
            }
        }
        if (os_pos_custom_discount_amount) {
            state.custom_discount_amount = os_pos_custom_discount_amount;
        }
        if (os_pos_custom_discount_reason) {
            state.custom_discount_reason = os_pos_custom_discount_reason;
        }
        this.setState(state);
        this.props.showBackDrop();
        return this;
    }

    /**
     * Get coupon component template
     * @returns {*}
     */
    getCouponTemplate() {
        let classNameInput = (this.state.couponCodeApplied === "") ?
            "form-control input-coupon" : "form-control input-coupon label-coupon";
        let classNameButtonApply = "";
        if (this.state.couponCodeApplied !== "") {
            classNameButtonApply = "hidden";
        } else {
            classNameButtonApply = (this.state.couponcode === "") ?
                "btn btn-default btn-coupon disabled" : "btn btn-default btn-coupon";
        }
        let classNameButtonRemove = (this.state.couponCodeApplied === "") ? "hidden" : "btn btn-default btn-coupon";
        let classBtnRemoveInput = "hidden";
        if ((this.state.couponCodeApplied === "") && (this.state.couponcode !== "")) {
            classBtnRemoveInput = "btn-remove";
        }
        let disabledButtonApply = (this.state.couponcode === "");
        let classNameMessage = (this.state.message === "") ? "invalid-coupon hidden" : "invalid-coupon";
        let autoFocus = ((this.state.couponcode === "" && !DeviceHelper.isMobile()));
        // let autoFocus = (this.state.couponcode === "");
        let disabledInputCoupon = (this.state.couponCodeApplied !== "");

        return (
            <div className="discount-content">
                <div className="img-discount"/>
                <div className="form-coupon">
                    <input type="text" className={classNameInput} placeholder="Enter code here"
                           onChange={(event) => this.couponCodeChange(event)} autoFocus={autoFocus}
                           disabled={disabledInputCoupon}
                           value={this.state.couponcode}
                           ref={this.setInputCoupon.bind(this)}
                    />
                    <button className={classBtnRemoveInput} type="button"
                            onClick={() => this.resetState()}>
                    </button>
                    <button className={classNameButtonApply} type="button"
                            onClick={() => this.submitCouponCode()}
                            disabled={disabledButtonApply}>
                        {this.props.t('Apply')}
                    </button>
                    <button className={classNameButtonRemove} type="button"
                            onClick={() => this.removeCouponCode()}>
                        {this.props.t('Remove')}
                    </button>
                    <div className={classNameMessage}>
                        {this.state.message}
                    </div>
                </div>
            </div>
        );
    }

    /**
     * Get custom discount component template
     * @param maxValue
     * @returns {*}
     */
    getCustomDiscountTemplate(maxValue) {
        let currentDiscountType = this.state.custom_discount_type;
        let currentDiscountAmount = this.state.custom_discount_amount;

        switch (currentDiscountType) {
            case CustomDiscountService.DISCOUNT_TYPE_PERCENT:
                currentDiscountAmount = NumberHelper.formatDisplayGroupAndDecimalSeparator(currentDiscountAmount) + "%";
                break;
            case CustomDiscountService.DISCOUNT_TYPE_FIXED:
                currentDiscountAmount = CurrencyHelper.format(currentDiscountAmount, CurrencyHelper.getCurrency());
                break;
            default:
                break;
        }

        let classNameMessage = (this.state.message === "") ? "invalid-coupon hidden" : "invalid-coupon";
        return (
            <div className="discount-content">
                <div className="form-coupon">
                    <textarea type="text"
                              className="form-control"
                              placeholder={this.props.t("Reason")}
                              value={this.state.custom_discount_reason}
                              style={{resize: 'none'}}
                              onChange={(event) => this.onDiscountReasonChange(event)}
                    />
                </div>
                <div className="form-coupon">

                    <NumPad.CustomNumber
                        onChange={(amount) => this.onDiscountAmountChange(amount)}
                        position="centerLeft"
                        arrow="left"
                        rightAdd={40}
                        isShowAction={true}
                        max={maxValue}>
                        <span className="form-control discount-amount">
                            {currentDiscountAmount}
                        </span>
                    </NumPad.CustomNumber>
                    <button className="btn btn-default btn-coupon"
                            onClick={this.handleApplyCustomDiscount}
                            type="button">
                        {this.props.t('Apply')}
                    </button>
                    <div className={classNameMessage}>
                        {this.state.message}
                    </div>
                </div>
            </div>
        );
    }

    /**
     * Handle select discount type
     * @param key
     */
    handleSelectDiscountType(key) {
        let type = "";
        switch (key) {
            case CartTotalsDiscountComponent.STATE_PERCENT:
                type = CustomDiscountService.DISCOUNT_TYPE_PERCENT;
                break;
            case CartTotalsDiscountComponent.STATE_FIXED:
                type = CustomDiscountService.DISCOUNT_TYPE_FIXED;
                break;
            default:
                break
        }
        this.setState({custom_discount_type: type, custom_discount_amount: 0, message: ""});
    }

    /**
     * Render tax total
     *
     * @return {*}
     */
    template() {
        let {total, hasPaidOrWaitingGatewayPayment, quote} = this.props;
        let canUseCustomDiscount = this.canUseCustomDiscount();
        let couponTemplate = this.getCouponTemplate();
        let customDiscountTemplatePercent = this.getCustomDiscountTemplate(10000);
        let customDiscountTemplatePrice = this.getCustomDiscountTemplate(NumberHelper.MAX_CURRENCY_DISPLAY);
        let currentCurrency = CurrencyHelper.getCurrentCurrency();
        let popoverCoupon = (
            <Popover id="coupon_popover"
            >
                <div className="popup-add-discount">
                    {
                        canUseCustomDiscount ?
                            <Tabs defaultActiveKey={this.state.current_state}
                                  animation={false}
                                  onSelect={(type) => this.handleSelectDiscountType(type)}
                                  bsStyle="pills"
                                  className="discount-container"
                                  id="discount-container">
                                <Tab eventKey={CartTotalsDiscountComponent.STATE_COUPON}
                                     title={this.props.t('Coupon Code')}>
                                    {couponTemplate}
                                </Tab>
                                <Tab eventKey={CartTotalsDiscountComponent.STATE_PERCENT} title="%">
                                    {customDiscountTemplatePercent}
                                </Tab>
                                <Tab eventKey={CartTotalsDiscountComponent.STATE_FIXED}
                                     title={currentCurrency.currency_symbol}>
                                    {customDiscountTemplatePrice}
                                </Tab>
                            </Tabs>
                            :
                            <Fragment>
                                <div className="discount-title">{this.props.t('Coupon Code')}</div>
                                {couponTemplate}
                            </Fragment>
                    }
                </div>
                <div className="loader-couponcode"
                     style={{display: (this.state.is_appling_coupon ? 'block' : 'none')}}>
                    <div className="loader-product"/>
                </div>
            </Popover>
        );

        let eventData = {
            component: this,
            canUseCustomDiscount: canUseCustomDiscount,
            couponTemplate: couponTemplate,
            customDiscountTemplatePercent: customDiscountTemplatePercent,
            customDiscountTemplatePrice: customDiscountTemplatePrice,
            currentCurrency: currentCurrency,
            popoverCoupon: popoverCoupon
        };
        fire('view_cart_total_discount_template_create_popover_template_after', eventData);
        popoverCoupon = eventData.popoverCoupon;

        return (
            <Fragment key={total.code}>
                <ButtonToolbar className={this.canShow() ? "" : "hidden"}>
                    {
                        hasPaidOrWaitingGatewayPayment ? CartTotalsDiscountComponent.getDiscountElement(
                            quote,
                            total,
                            hasPaidOrWaitingGatewayPayment
                        ) : (
                            <PosOverlayTrigger trigger="click"
                                               placement="right"
                                               overlay={popoverCoupon}
                                               rootClose
                                               onClick={() => this.showPopover()}
                            >
                                {
                                    CartTotalsDiscountComponent.getDiscountElement(
                                        quote,
                                        total,
                                        hasPaidOrWaitingGatewayPayment
                                    )
                                }
                            </PosOverlayTrigger>
                        )
                    }
                </ButtonToolbar>
            </Fragment>
        )
    }
}

export class CartTotalsDiscountContainer extends CoreContainer {
    static className = 'CartTotalsDiscountContainer';

    /**
     *
     * @param state
     * @return {{quote: *}}
     */
    static mapState(state) {
        const {currentPage} = state.core.checkout.index;
        const {quote} = state.core.checkout;
        const hasPaidOrWaitingGatewayPayment = PaymentHelper.hasPaidOrWaitingGatewayPayment(quote.payments);
        return {
            currentPage,
            hasPaidOrWaitingGatewayPayment
        }
    }

    /**
     *
     * @param dispatch
     * @return {{actions: {placeOrder, placeOrderResult, placeOrderError, checkoutToSelectPayments}|ActionCreator<any>|ActionCreatorsMapObject}}
     */
    static mapDispatch(dispatch) {
        return {
            actions: {
                setCustomDiscount: (quote, discountType, discountAmount, discountReason) => dispatch(
                    QuoteAction.setCustomDiscount(quote, discountType, discountAmount, discountReason)
                ),
                removeCustomDiscount: (quote) => dispatch(QuoteAction.removeCustomDiscount(quote)),
                removeCouponCode: (quote) => dispatch(QuoteAction.removeCouponCode(quote)),
                addCouponCodeAfter: (quote) => dispatch(QuoteAction.addCouponCodeAfter(quote)),
                setQuote: (quote) => dispatch(QuoteAction.setQuote(quote)),
                checkoutToSelectPayments: (quote, initPayments) => dispatch(
                    CheckoutAction.checkoutToSelectPayments(quote, initPayments)
                ),
            }
        }
    }
}

/**
 *
 * @type {CartTotalsTaxContainer}
 */
const container = ContainerFactory.get(CartTotalsDiscountContainer);
export default container.getConnect(ComponentFactory.get(CartTotalsDiscountComponent));
