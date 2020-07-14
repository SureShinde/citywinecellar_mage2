import React, {Fragment} from 'react';
import CoreComponent from "../../../../framework/component/CoreComponent";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";
import CurrencyHelper from "../../../../helper/CurrencyHelper";
import CheckoutAction from "../../../action/CheckoutAction";
import CoreContainer from "../../../../framework/container/CoreContainer";
import ContainerFactory from "../../../../framework/factory/ContainerFactory";
import QuoteService from '../../../../service/checkout/QuoteService';
import CouponTypeConstant from "../../../constant/salesrule/CouponTypeConstant";
import QuoteAction from "../../../action/checkout/QuoteAction";
import Config from "../../../../config/Config";
import OnHoldOrderAction from "../../../action/OnHoldOrderAction";
import GuestCustomerHelper from "../../../../helper/GuestCustomerHelper";
import {toast} from "react-toastify";
import ProductList from "../../catalog/ProductList";
import CheckoutHelper from "../../../../helper/CheckoutHelper";
import ScanAction from "../../../action/ScanAction";
import {fire} from "../../../../event-bus";
import ApiResponseConstant from "../../../constant/ApiResponseConstant";

export class CartFooterComponent extends CoreComponent {
    static className = 'CartFooterComponent';
    isHolding = false;

    constructor(props) {
        super(props);
        this.showOnPages = [ProductList.className];
        this.isHolding = false;
    }

    /**
     * component will receive props
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        if (nextProps.quote.id !== this.props.quote.id) {
            this.isHolding = false;
        }
    }

    /**
     * Before checkout
     */
    async beforeToCheckOut() {
        let quote = this.props.quote;
        fire('view_cart_footer_before_to_checkout_before', {
            component: this,
            quote: quote,
        });
        let quoteId = quote.id;
        // check config enable guest checkout or not
        if (!GuestCustomerHelper.getStatus() && !quote.customer_id) {
            return toast.error(
                this.props.t('Please select customer before checkout.'),
                {
                    position : toast.POSITION.BOTTOM_CENTER,
                    className: 'wrapper-messages messages-warning',
                    autoClose: 2000
                });
        }

        if (this.hasGiftCardItems(quote) && !quote.customer_id) {
            return toast.error(
                this.props.t('Current cart contains Gift Card product(s). Please select customer for order.'),
                {
                    position : toast.POSITION.BOTTOM_CENTER,
                    className: 'wrapper-messages messages-warning',
                    autoClose: 2000
                });
        }
        let configCheckPromotion = true;
        let initPayments = false;
        if (Config.current_base_grand_total !== this.props.quote.base_grand_total) {
            initPayments = true;
            Config.current_base_grand_total = this.props.quote.base_grand_total;
        }
        await this.props.actions.removeCouponCode(quote);
        await  this.props.actions.removeCustomDiscount(quote);
        this.props.actions.checkoutToSelectPayments(quote, initPayments);
        this.props.actions.setScanPage();
        if (configCheckPromotion) {
            // get quote data after change QuoteReducer
            quote = this.props.quote;

            quote.checking_promotion = true;
            this.props.actions.setQuote(quote);
            QuoteService.submitCouponCode(quote, "")
                .then(rules => {
                    quote = this.props.quote;
                    if (rules && rules.length) {
                        fire('view_cart_footer_before_to_checkout_check_promotion_before', {
                            quote: quote,
                            rules: rules
                        });
                        let promotionRules = rules.filter(
                            rule => rule.coupon_type === CouponTypeConstant.COUPON_TYPE_NO_COUPON
                        );
                        if (promotionRules && quote.checking_promotion) {
                            quote.valid_salesrule = promotionRules;
                            QuoteService.collectTotals(quote);
                        }
                    }
                    delete quote.checking_promotion;
                    if(this.props.quote.id === quoteId) {
                        this.props.actions.setQuote(quote);
                        if (!quote.grand_total) {
                            this.props.actions.checkoutToSelectPayments(quote);
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
                    delete quote.checking_promotion;
                    if(this.props.quote.id === quoteId) {
                        this.props.actions.setQuote(quote);
                    }
                });
        }
    }

    /**
     * check quote has gift card items
     * @param quote
     * @return {boolean}
     */
    hasGiftCardItems(quote) {
        return CheckoutHelper.hasGiftCardItems(quote);
    }

    /**
     * handle hold order
     */
    handleHoldOrder() {
        if (this.canHold()) {
            this.props.actions.holdOrder(this.props.quote);
            this.isHolding = true;
        }
    }

    /**
     * check can hold order
     * @return {number|*|boolean}
     */
    canHold() {
        return (this.props.quote.items_qty && !this.isHolding);
    }

    /**
     *
     *  render footer cart
     *  if total qty item < 1, disable go to select payment
     *
     * @return {*}
     */
    template() {
        const canShow = this.canShow();
        const { grand_total, items_qty } = this.props.quote;
        const actionsClass = canShow ? 'actions' : 'hidden';
        const holdButtonClass = canShow ?
            this.canHold() ? 'btn btn-default btn-hold' : "btn btn-default btn-hold disabled"
            : 'hidden';
        const chargeButtonClass = canShow ?
            items_qty ? "btn btn-default btn-total" : "btn btn-default btn-total disabled"
            : 'hidden';

        return (
            <Fragment>
                <div className={actionsClass}>
                    <button className={holdButtonClass}
                            type="button"
                            onClick={() => {
                                if (!items_qty) return;
                                this.handleHoldOrder();
                            }}>
                        {this.props.t('Hold')}
                    </button>
                    <button
                        className={chargeButtonClass}
                        type="button"
                        onClick={() => {
                            if (!items_qty) return;
                            this.beforeToCheckOut();
                        }}
                    >{CurrencyHelper.format(grand_total, null, null)}</button>
                </div>
                <div className="loader-cart">
                    <div className="loader-product"/>
                </div>
            </Fragment>
        );
    }
}

/**
 *
 * @type {CartFooterComponent}
 */
const component = ComponentFactory.get(CartFooterComponent);

export class CartFooterContainer extends CoreContainer {
    static className = 'CartFooterContainer';

    /**
     *
     * @param state
     * @return {{quote: *}}
     */
    static mapState(state) {
        const {quote} = state.core.checkout;
        const {currentPage} = state.core.checkout.index;
        return {
            quote,
            currentPage
        }
    }

    /**
     *
     * @param dispatch
     * @return {{actions: {removeCouponCode: function(*=): *, setQuote: function(*=): *, checkoutToSelectPayments:
     *     function(*=, *=): *, holdOrder: function(*=): *}}}
     */
    static mapDispatch(dispatch) {
        return {
            actions: {
                removeCouponCode: (quote) => dispatch(QuoteAction.removeCouponCode(quote)),
                removeCustomDiscount: (quote) => dispatch(QuoteAction.removeCustomDiscount(quote)),
                setQuote: (quote) => dispatch(QuoteAction.setQuote(quote)),
                checkoutToSelectPayments: (quote, initPayments) => dispatch(
                    CheckoutAction.checkoutToSelectPayments(quote, initPayments)
                ),
                holdOrder: (quote) => dispatch(OnHoldOrderAction.holdOrder(quote)),
                setScanPage: scanPage => dispatch(ScanAction.setScanPage(scanPage))
            }
        }
    }
}

/**
 *
 * @type {CartFooterContainer}
 */
const container = ContainerFactory.get(CartFooterContainer);
export default container.getConnect(component);
