import React from "react";
import * as _ from "lodash";
import CoreComponent from '../../../framework/component/CoreComponent';
import ComponentFactory from '../../../framework/factory/ComponentFactory';
import CoreContainer from '../../../framework/container/CoreContainer';
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import SmoothScrollbar from "smooth-scrollbar";
import DateTimeHelper from "../../../helper/DateTimeHelper";
import OrderService from "../../../service/sales/OrderService";
import OrderItem from "./order-detail/detail-content/OrderItem";
import {Panel} from "react-bootstrap";
import moment from "moment/moment";
import ContentPaymentItem from "./order-detail/detail-content/ContentPaymentItem";
import ShippingMethod from "./order-detail/detail-content/ShippingMethod";
import ShippingAddress from './order-detail/detail-content/ShippingAddress';
import BillingAddress from './order-detail/detail-content/BillingAddress';
import OrderAction from "../../action/OrderAction";
import StatusConstant from "../../constant/order/StatusConstant";
import CommentItem from "./order-detail/detail-content/CommentItem";
import PermissionConstant from "../../constant/PermissionConstant";
import CreditmemoAction from "../../action/order/CreditmemoAction";
import OrderHelper from "../../../helper/OrderHelper";
import TaxHelper from "../../../helper/TaxHelper";
import OrderWeeeDataService from "../../../service/weee/OrderWeeeDataService";
import CustomerService from "../../../service/customer/CustomerService";
import PaymentConstant from "../../constant/PaymentConstant";
import Permission from "../../../helper/Permission";
import LocationService from "../../../service/LocationService";
import i18n from "../../../config/i18n";
import {toast} from "react-toastify";
import OrderGiftCardPopupService from "../../../service/sales/order/order-gift-card/OrderGiftCardPopupService";
import NumberHelper from "../../../helper/NumberHelper";
import layout from "../../../framework/Layout";
import {fire} from "../../../event-bus";

export class OrderDetail extends CoreComponent {
    static className = 'OrderDetail';

    setBlockContentElement = element => {
        this.block_content = element;
        if (!this.scrollbarOrderDetail) {
            this.scrollbarOrderDetail = SmoothScrollbar.init(this.block_content);
        }
    };

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            canCancel: false,
            canTakePayment: false,
            canRefund: false,
            printBtnClassName: 'btn btn-default',
            isRefunding: false
        }
    }

    /**
     * Component will receive props
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        if (this.props.order !== nextProps.order) {
            if (nextProps.order && !nextProps.order.items) {
                return;
            }
            if (this.scrollbarOrderDetail) {
                this.scrollbarOrderDetail.scrollTo(0, 0);
            }

            let paymentStatus = OrderService.getPaymentStatus(nextProps.order);
            let abilities = {
                canCancel: (
                    OrderService.canCancel(nextProps.order)
                ),
                canTakePayment: (
                    paymentStatus.realValue === StatusConstant.PAYMENT_STATUS_PARTIAL_PAID
                    || paymentStatus.realValue === StatusConstant.PAYMENT_STATUS_UNPAID
                ),
                canRefund: (
                    OrderService.canCreditmemo(nextProps.order)
                    && this.isAllowed(PermissionConstant.PERMISSION_REFUND)
                )
            };

            fire('component_order_detail_component_will_receive_props_calculate_order_abilities_after', { abilities, nextProps });
            this.setState(abilities);

            if (this.state.isRefunding) {
                this.setState({isRefunding: false});
                this.props.openCreditmemoOrder();
            }
        }
    }

    /**
     * component will unmount
     */
    componentWillUnmount() {
        if (this.scrollbarOrderDetail) {
            SmoothScrollbar.destroy(this.scrollbarOrderDetail);
        }
    }

    /**
     * refund
     */
    async refund() {
        if (!this.state.canRefund) {
            return;
        }

        let orderHasGiftCard = OrderHelper.hasGiftCardItems(this.props.order);

        if (!orderHasGiftCard) {
            return this.props.openCreditmemoOrder();
        }

        if (!window.navigator.onLine) {
            return toast.error(
                i18n.translator.translate(
                    'Connection failed. ' +
                    'You must connect to a Wi-Fi ' +
                    'or cellular data network to refund order contains Gift card product'
                ),
                {
                    className: 'wrapper-messages messages-warning',
                    autoClose: 3000
                }
            );
        }

        let {order} = this.props;

        this.setState({isRefunding: true});

        OrderGiftCardPopupService.check(
            order,
            {},
            (orderData) => {
                this.props.actions.updateOrder(orderData);
            }
        );
    }

    /**
     * handle click take payment
     */
    handleClickTakePayment() {
        if (this.state.canTakePayment) {
            this.props.openTakePayment();
        }
    }

    /**
     * handle click cancel order
     */
    handleClickCancelOrder() {
        let deepCanCancel = OrderService.deepCanCancel(this.props.order);
        if (deepCanCancel.canCancel) {
            return this.props.openCancelOrder();
        }

        if (!deepCanCancel.message) {
            return;
        }

        return this.props.openOrderAlertPopup(deepCanCancel)
    }


    /**
     * handle click add note
     */
    handleClickAddComment() {
        this.props.openAddComment();
    }

    /**
     * handle click send email
     */
    handleClickSendEmail() {
        this.props.openSendEmail();
    }

    /**
     * handle click reorder
     */
    async handleClickReorder() {
        this.props.startReorder();
    }

    /**
     * Print order by order data
     * @param order
     * @returns {Promise.<void>}
     */
    async handleClickPrint(order) {
        const needLoadCustomer = !order.customer_is_guest;
        if (!needLoadCustomer) {
            this.props.actions.reprintOrder(order, null);
        } else {
            try {
                this.setState({
                    printBtnClassName: 'btn btn-default loader'
                });
                let customer = await CustomerService.getById(order.customer_id);
                this.setState({
                    printBtnClassName: 'btn btn-default'
                });
                this.props.actions.reprintOrder(
                    order,
                    customer
                )
            }
            catch (e) {
                this.props.actions.reprintOrder(order, null);
            }
        }
    }

    /**
     * get display location
     * @param order
     * @return {*}
     */
    getDisplayLocation(order) {
        if (
            Permission.isAllowed(PermissionConstant.PERMISSION_MANAGE_ORDER_CREATED_AT_ALL_LOCATION)
            || Permission.isAllowed(PermissionConstant.PERMISSION_MANAGE_ALL_ORDERS_IN_SYSTEM)
        ) {
            let locationId = order.pos_location_id ? order.pos_location_id : order.warehouse_id;
            if (locationId) {
                let location = LocationService.getLocationById(locationId);
                if (location) {
                    return this.props.t("Location: {{name}}", {name: location.name})
                }
            }
        }
        return "";
    }

    /**
     * get discount amount
     * @param order
     * @returns {number}
     */
    getDiscountAmount(order) {
        return order && order.discount_amount ? Math.abs(order.discount_amount) : 0;
    }

    /**
     * Return loading flag value
     *
     * @return boolean
     */
    isLoading() {
        return this.props.isDetailLoading;
    }

    /**
     * template
     * @returns {*}
     */
    template() {
        let {order, isLoading, t} = this.props;
        if (order && !order.items) {
            order = null;
        }
        let billingAddress = OrderService.getBillingAddress(order);
        let created_at = order ?
            moment(DateTimeHelper.convertDatabaseDateTimeToLocalDate(order.created_at)).format('L LT') : "";
        let due = (
            order
            && order.total_due > 0
            && order.state !== StatusConstant.STATE_CANCELED
            && order.state !== StatusConstant.STATE_CLOSED
        )
            ? this.props.t("Due: {{total_due}}", {total_due: OrderHelper.formatPrice(order.total_due, order)})
            : "";

        let giftcardDiscount = order && order.gift_voucher_gift_codes ? order.gift_voucher_discount : 0;
        let discount = this.getDiscountAmount(order) - giftcardDiscount;

        let weeeTotal = order && order.items ? OrderWeeeDataService.getTotalAmounts(order.items, order) : "";

        let paymentStatus = OrderService.getPaymentStatus(order);

        let statusHistory = (order && order.status_histories) ? order.status_histories : [];
        statusHistory = _.orderBy(statusHistory, 'created_at', 'desc');

        let customer_name = (order && order.customer_firstname && order.customer_lastname && !order.customer_is_guest) ?
            (order.customer_firstname + ' ' + order.customer_lastname) :
            this.props.t('Guest');
        let telephone = order && !order.customer_is_guest && billingAddress ?
            (billingAddress.telephone || t('N/A')) :
            null;
        let other_payment;
        if (order && order.payments) {
            let actutal_total_paid = 0;
            order.payments.forEach((payment) => {
                if (payment.type !== PaymentConstant.TYPE_REFUND) {
                    actutal_total_paid = NumberHelper.addNumber(payment.amount_paid, actutal_total_paid);
                }
            });
            if (actutal_total_paid < order.total_paid) {
                let amount = order.total_paid - actutal_total_paid;
                other_payment = {
                    amount_paid: amount,
                    base_amount_paid: amount,
                    card_type: null,
                    is_paid: 1,
                    method: "otherpayment",
                    payment_date: null,
                    receipt: null,
                    reference_number: null,
                    title: "Other",
                    type: 0
                };
            }
        }
        return (
            <div className="wrapper-order-right">
                <div className="block-title">{order ? order.increment_id : ''}</div>

                <div className="loader-product"
                     style={{display: (this.isLoading() ? 'block' : 'none'), marginTop: '300px', marginBottom: '500px'}}>
                </div>

                <div data-scrollbar ref={this.setBlockContentElement} className="block-content">

                    {
                        !order ?
                            (
                                isLoading || this.isLoading() ?
                                    <div/>
                                    :
                                    <div className="page-notfound">
                                        <div className="icon"></div>
                                        {
                                            this.props.showNoInternet ?
                                                this.props.t('Please connect to Internet to find orders')
                                                :
                                                this.props.t('No order is found.')
                                        }
                                    </div>
                            )
                            :
                            <div>
                                <div className="order-info">
                                    <div className="order-price">
                                        <div className="price">{OrderHelper.formatPrice(order.grand_total, order)}</div>
                                        <div>
                                            <span className="price-label due">{due}</span>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div className="col-sm-6">
                                            <div className="order-detail">
                                                <div>
                                                    {
                                                        this.props.t(
                                                            "Order Date: {{created_at}}",
                                                            {created_at: created_at}
                                                        )
                                                    }
                                                </div>
                                                <div>{this.getDisplayLocation(order)}</div>
                                                <div>
                                                    {
                                                        this.props.t(
                                                            "Customer: {{name}}",
                                                            {name: customer_name}
                                                        )
                                                    }
                                                </div>
                                                {
                                                    telephone ?
                                                        <div>
                                                            {
                                                                this.props.t(
                                                                    "Customer Phone: {{telephone}}",
                                                                    {telephone: telephone}
                                                                )
                                                            }
                                                        </div> :
                                                        telephone
                                                }
                                                <div className="old">
                                                    {
                                                        order.pos_staff_name ?
                                                            this.props.t("Staff: {{name}}", {name: order.pos_staff_name})
                                                            :
                                                            ''
                                                    }
                                                </div>
                                                {layout('order')('orderHistory')('staff_name_layout')('order_detail_staff_name_after')()(this)}
                                                <div className="order-status">
                                                    <span className={
                                                        "status " + order.status.toLowerCase().replace(' ', '-')
                                                    }>
                                                        {
                                                            this.props.t(
                                                                OrderService.getDisplayStatus(order.state, order.status)
                                                            )
                                                        }
                                                    </span>
                                                    <span className={"status " + paymentStatus.className}>
                                                        {paymentStatus.value}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-sm-6">
                                            <div className="order-total">
                                                <ul>
                                                    <li>
                                                        <span className="title">{this.props.t('Subtotal')}</span>
                                                        <span className="value">
                                                            {OrderService.getDisplaySubtotal(order)}
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <span className="title">
                                                            {OrderHelper.getDiscountDisplay(order)}
                                                        </span>
                                                        <span className="value">-{OrderHelper.formatPrice(discount, order)}</span>
                                                    </li>

                                                    {layout('order')('order_detail_layout')('total_discount_after')()(this)}

                                                    {
                                                        !order.is_virtual ?
                                                            <li>
                                                                <span className="title">
                                                                    {this.props.t('Shipping')}
                                                                </span>
                                                                <span className="value">
                                                                    {OrderService.getDisplayShippingAmount(order)}
                                                                </span>
                                                            </li>
                                                            : null
                                                    }
                                                    {
                                                        giftcardDiscount ?
                                                            <li>
                                                                <span className="title">{t('Gift Card')}</span>
                                                                <span className="value">
                                                                    {OrderHelper.formatPrice(-giftcardDiscount, order)}
                                                                </span>
                                                            </li> : ''
                                                    }
                                                    <li>
                                                        <span className="title">{this.props.t('FPT')}</span>
                                                        <span className="value">
                                                            {OrderHelper.formatPrice(weeeTotal, order)}
                                                        </span>
                                                    </li>
                                                    {
                                                        !TaxHelper.orderDisplayZeroTaxSubTotal() && order.tax_amount === 0
                                                            ?
                                                            null
                                                            :
                                                            <li>
                                                                <span className="title">{this.props.t('Tax')}</span>
                                                                <span className="value">
                                                                    {OrderHelper.formatPrice(order.tax_amount, order)}
                                                                </span>
                                                            </li>
                                                    }

                                                    {layout('order')('order_detail_layout')('total_tax_after')()(this)}

                                                    <li>
                                                        <span className="title">{this.props.t('Grand Total')}</span>
                                                        <span className="value">
                                                            {OrderHelper.formatPrice(order.grand_total, order)}
                                                        </span>
                                                    </li>

                                                    {layout('order')('order_detail_layout')('grand_total_after')()(this)}

                                                    <li>
                                                        <span className="title">{this.props.t('Total Paid')}</span>
                                                        <span className="value">
                                                            {OrderHelper.formatPrice(order.total_paid, order)}
                                                        </span>
                                                    </li>
                                                    {
                                                        order.pos_change ?
                                                            <li>
                                                                <span className="title">
                                                                    {this.props.t('Total Change')}
                                                                </span>
                                                                <span className="value">
                                                                    {OrderHelper.formatPrice(order.pos_change, order)}
                                                                </span>
                                                            </li>
                                                            :
                                                            null
                                                    }
                                                    {
                                                        order.total_refunded ?
                                                            <li>
                                                                <span className="title">
                                                                    {this.props.t('Total Refunded')}
                                                                </span>
                                                                <span className="value">
                                                                    {OrderHelper.formatPrice(order.total_refunded, order)}
                                                                </span>
                                                            </li>
                                                            :
                                                            null
                                                    }
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion" className="panel-group" role="tablist" aria-multiselectable="true">
                                    <Panel eventKey="1" defaultExpanded key={Math.random()}>
                                        <Panel.Heading>
                                            <Panel.Title toggle>{this.props.t('Items Ordered')}</Panel.Title>
                                        </Panel.Heading>
                                        <Panel.Body collapsible>
                                            {
                                                order.items.map(item => {
                                                    return <OrderItem key={'' + item.item_id + Math.random()}
                                                                      item={item} order={order}/>
                                                })
                                            }
                                        </Panel.Body>
                                    </Panel>
                                    <div className="panel-flex">
                                        <Panel eventKey="2" key={Math.random()}>
                                            <Panel.Heading>
                                                <Panel.Title toggle>{this.props.t('Payment Method')}</Panel.Title>
                                            </Panel.Heading>
                                            <Panel.Body collapsible>
                                                <div className="text-right">
                                                    <span className={"status " + paymentStatus.className}>
                                                        {paymentStatus.value}
                                                    </span>
                                                </div>
                                                <ul className="payment-method">
                                                    {
                                                        order.payments ?
                                                            order.payments.map((payment, index) =>
                                                                payment.type !== PaymentConstant.TYPE_REFUND ?
                                                                    <ContentPaymentItem key={index} payment={payment}
                                                                                 order={order}/> :
                                                                    null
                                                            )
                                                            :
                                                            null
                                                    }
                                                    {
                                                        other_payment ?
                                                            <ContentPaymentItem payment={other_payment} order={order}/> : null
                                                    }
                                                </ul>
                                            </Panel.Body>
                                        </Panel>
                                        {
                                            !order.is_virtual ?
                                                <Panel eventKey="3" key={Math.random()}>
                                                    <Panel.Heading>
                                                        <Panel.Title toggle>
                                                            {this.props.t('Shipping Method')}
                                                        </Panel.Title>
                                                    </Panel.Heading>
                                                    <Panel.Body collapsible>
                                                        <ShippingMethod order={order}/>
                                                    </Panel.Body>
                                                </Panel>
                                                : null
                                        }
                                        {
                                            !order.is_virtual ?
                                                <Panel eventKey="4" key={Math.random()}>
                                                    <Panel.Heading>
                                                        <Panel.Title toggle>
                                                            {this.props.t('Shipping Address')}
                                                        </Panel.Title>
                                                    </Panel.Heading>
                                                    <Panel.Body collapsible>
                                                        <ShippingAddress order={order}/>
                                                    </Panel.Body>
                                                </Panel>
                                                : null
                                        }
                                        <Panel eventKey="5" key={Math.random()}>
                                            <Panel.Heading>
                                                <Panel.Title toggle>{this.props.t('Billing Address')}</Panel.Title>
                                            </Panel.Heading>
                                            <Panel.Body collapsible>
                                                <BillingAddress order={order}/>
                                            </Panel.Body>
                                        </Panel>
                                    </div>
                                    {
                                        statusHistory && statusHistory.length ?
                                            <Panel eventKey="6" key={Math.random()}>
                                                <Panel.Heading>
                                                    <Panel.Title toggle>{this.props.t('Comment History')}</Panel.Title>
                                                </Panel.Heading>
                                                <Panel.Body collapsible>
                                                    <ul className="comment-history">
                                                        {
                                                            statusHistory.map((comment, index) =>
                                                                <CommentItem key={comment.entity_id + '_' + index}
                                                                             comment={comment}/>
                                                            )
                                                        }
                                                    </ul>
                                                </Panel.Body>
                                            </Panel>
                                            :
                                            null
                                    }
                                </div>
                            </div>
                    }
                </div>
                {
                    order ?
                        <div className="block-actions">
                            <ul className={"actions"
                            + (!this.isAllowed(PermissionConstant.PERMISSION_REFUND) ? ' hidden-refund' : '')}>
                                {layout('order')('order_detail_layout')('actions')('take_payment_before')()(this)}
                                <li>
                                    <button
                                        className={"btn btn-default " + (!this.state.canTakePayment ? 'disabled' : '')}
                                        onClick={() => this.handleClickTakePayment()}>
                                        {
                                            this.props.t('Take Payment')
                                        }
                                    </button>
                                </li>
                                {layout('order')('order_detail_layout')('actions')('refund_before')()(this)}
                                <li className={(!this.isAllowed(PermissionConstant.PERMISSION_REFUND) ? 'hidden' : '')}>
                                    <button
                                        className={"btn btn-default " + (!this.state.canRefund ? 'disabled' : '')}
                                        onClick={() => this.refund()}
                                    >{this.props.t('Refund')}</button>
                                </li>
                                {layout('order')('order_detail_layout')('actions')('cancel_before')()(this)}
                                <li>
                                    <button className={"btn btn-default " + (!this.state.canCancel ? 'disabled' : '')}
                                            onClick={() => this.handleClickCancelOrder()}>
                                        {
                                            this.props.t('Cancel')
                                        }
                                    </button>
                                </li>
                                {layout('order')('order_detail_layout')('actions')('print_before')()(this)}
                                <li>
                                    <button
                                        className={this.state.printBtnClassName}
                                        onClick={() => this.handleClickPrint(order)}>
                                        {
                                            this.props.t('Print')
                                        }
                                    </button>
                                </li>
                                {layout('order')('order_detail_layout')('actions')('email_before')()(this)}
                                <li>
                                    <button className="btn btn-default"
                                            onClick={() => this.handleClickSendEmail()}>
                                        {this.props.t('Email')}</button>
                                </li>
                                {layout('order')('order_detail_layout')('actions')('add_note_before')()(this)}
                                <li>
                                    <button className="btn btn-default"
                                            onClick={() => this.handleClickAddComment()}>
                                        {this.props.t('Add Note')}
                                    </button>
                                </li>
                                {layout('order')('order_detail_layout')('actions')('reorder_before')()(this)}
                                <li>
                                    <button className="btn btn-default"
                                            onClick={() => this.handleClickReorder()}>
                                        {this.props.t('Reorder')}
                                    </button>
                                </li>
                            </ul>
                        </div>
                        :
                        null
                }

            </div>
        )
    }
}

class OrderDetailContainer extends CoreContainer {
    static className = 'OrderDetailContainer';

    static mapDispatch(dispatch) {
        return {
            actions: {
                reprintOrder: (order, customer) =>
                    dispatch(OrderAction.reprintOrder(order, customer)),
                createCreditmemo: creditmemo => dispatch(CreditmemoAction.createCreditmemo(creditmemo)),
                updateOrder: orderData => dispatch(OrderAction.syncActionUpdateDataFinish([orderData]))
            }
        }
    }
}

export default ContainerFactory.get(OrderDetailContainer).withRouter(
    ComponentFactory.get(OrderDetail)
);
