import React, {Fragment} from 'react';
import PropTypes from "prop-types";
import SmoothScrollbar from "smooth-scrollbar";
import CoreComponent from "../../../../../../framework/component/CoreComponent";
import CoreContainer from "../../../../../../framework/container/CoreContainer";
import ComponentFactory from "../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../framework/factory/ContainerFactory";
import {toast} from "react-toastify";

import ShippingAction from "../../../action/order/ShippingAction";

import CurrencyHelper from "../../../../../../helper/CurrencyHelper";
import OrderHelper from "../../../../../../helper/OrderHelper";
import OrderItemService from "../../../../../../service/sales/order/OrderItemService";
import {AbstractProductTypeService} from "../../../../../../service/catalog/product/type/AbstractTypeService";

import OrderCreateShipmentService from "../../../../service/sales/order/OrderCreateShipmentService";
import ShipmentFactoryService from "../../../../service/sales/order/ShipmentFactoryService";
import ShipmentConfirmation from "./create-shipment/popup/ShipmentConfirmation";
import ShipmentCancellation from "./create-shipment/popup/ShipmentCancellation";
import CreateShipmentItemSimpleComponent from "./create-shipment/CreateShipmentItemSimple";
import CreateShipmentItemTogetherShipBundle from "./create-shipment/CreateShipmentItemTogetherShipBundle";
import CreateShipmentItemSeparatelyShipBundle from "./create-shipment/CreateShipmentItemSeparatelyShipBundle";

class OrderCreateShipmentComponent extends CoreComponent {
    static className = 'OrderCreateShipmentComponent';

    /**
     *  UI setup
     *
     * @param element
     */
    setBlockContentElement = element => {
        if (this.scrollbar) {
            SmoothScrollbar.destroy(this.scrollbar);
        }
        if (element) {
            this.blockContentElement = element;
            this.scrollbar = SmoothScrollbar.init(this.blockContentElement);
        }
    };

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        let decimalSymbol = CurrencyHelper.getCurrencyFormat(props.order.order_currency_code).decimal_symbol;
        this.state = {
            show_confirm_popup: false,
            show_cancel_popup: false,
            shipment_items_param: this.prepareShipmentParams(props.order),
            is_max_qty: false,
            decimalSymbol: (decimalSymbol ? decimalSymbol : "."),
        };
    }

    /**
     *  update shipment data
     *
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        if (nextProps.order && nextProps.order.increment_id &&
            (!this.props.order || !this.props.order.increment_id ||
                nextProps.order.increment_id !== this.props.order.increment_id)
        ) {
            this.setState({shipment_items_param: this.prepareShipmentParams(nextProps.order)});
        }
    }

    /**
     *  trigger whenever click SHIP button
     */
    onClickShip() {
        let shipmentItems = this.getShipmentItems();
        if (!shipmentItems) {
            return toast.error(
                this.props.t('Please select an item to create shipment'),
                {
                    className: 'wrapper-messages messages-warning',
                    autoClose: 2000
                }
            );
        }
        this.showConfirmPopup();
    }

    /**
     *  always show after click SHIP button
     */
    showConfirmPopup() {
        this.setState({
            show_confirm_popup: true
        });
    }

    /**
     *
     * @param {string} note
     * @returns {boolean}
     */
    onConfirmPopupClickOk(note) {
        let shipmentItems = this.getShipmentItems();
        this.createShipment(shipmentItems, note);
    }

    /**
     *  callback after click YES/ NO button on confirm popup
     */
    onConfirmPopupClose() {
        this.setState({
            show_confirm_popup: !this.state.show_confirm_popup
        });
    }

    /**
     *  trigger whenever click CANCEL shipment button
     */
    cancel() {
        this.setState({
            show_cancel_popup: !this.state.show_cancel_popup
        });
    }

    /**
     *  trigger whenever click ship all item toggle button
     */
    useMaxQty() {
        let shipment_items_param = this.state.shipment_items_param;
        Object.keys(shipment_items_param).forEach(orderItemId => {
            let shipmentItem = shipment_items_param[orderItemId];
            let qty = this.state.is_max_qty ? 0 : shipmentItem.qty_left;

            if (shipmentItem.hasOwnProperty('max_qty') && qty > shipmentItem.max_qty) {
                qty = shipmentItem.max_qty;
                let error_message = this.props.t(`The maximum quantity to ship is {{qty}}`, {qty: shipmentItem.max_qty});
                return Object.assign(shipment_items_param[orderItemId], {qty, error_message});
            }

            if (shipmentItem.disable) {
                qty = 0;
                let error_message = this.props.t("The item is not assigned to this Source");
                return Object.assign(shipment_items_param[orderItemId], {qty, error_message});
            }

            Object.assign(shipment_items_param[orderItemId], {qty});
        });
        this.setState({shipment_items_param, is_max_qty: !this.state.is_max_qty});
    }

    /**
     *
     * @returns {boolean|Object}
     */
    getShipmentItems() {
        let shipment_items_param = this.state.shipment_items_param;
        let validateShipment = false;
        let shipmentItems = {};
        let totalQtyToShip = 0;

        Object.keys(shipment_items_param).forEach(orderItemId => {
            let shipmentItem = shipment_items_param[orderItemId];
            if (shipmentItem.qty > 0) {
                validateShipment = true;
            }
            totalQtyToShip += shipmentItem.qty;
            shipmentItems[orderItemId] = shipmentItem.qty;
        });

        if (!validateShipment || totalQtyToShip <= 0 || !Object.keys(shipmentItems).length) {
            return false;
        }

        return shipmentItems;
    }

    /**
     *
     * @param item
     * @param order
     * @returns {{order_item_id: *, order_item: *, qty: number, is_qty_decimal: (number|boolean), qty_left: (*|number)}}
     */
    prepareShipmentItemParamSkipCheckDummy(item, order) {
        let qtyLeft = OrderItemService.getSimpleQtyToShip(item);
        qtyLeft = ShipmentFactoryService.castQty(item, qtyLeft);
        let orderItemId = item.tmp_item_id || item.item_id;
        return {
            order_item_id: orderItemId,
            order_item: item,
            qty_left: qtyLeft,
            qty: 0,
            is_qty_decimal: item.is_qty_decimal
        }
    }

    /**
     *
     * @param item
     * @param order
     * @return {*}
     */
    prepareShipmentItemParam(item, order) {

        let {productQtys} = this.props;
        let {product_id} = item;
        let result = OrderCreateShipmentService.prepareShipmentItemParam(item, order);

        if (!productQtys.hasOwnProperty(product_id)) {
            return result;
        }

        if (productQtys[product_id].isNotExisted) {
            result['disable'] = true;
            return result;
        }

        if (
            productQtys[product_id].hasOwnProperty('isManageStock')
            && !productQtys[product_id].isManageStock
        ) {
            return result;
        }

        let productQty = productQtys[product_id].qty;

        if (productQty === false) {
            return result;
        }

        if (productQty >= result.qty_left) {
            return result;
        }

        result['max_qty'] = Math.max(productQtys[product_id].stockInLocation, 0);

        return result;
    }

    /**
     *
     * @param order
     * @return {Object}
     */
    prepareShipmentParams(order) {
        let shipment_items_param = {};
        if (order && Array.isArray(order.items)) {
            order.items.forEach(item => {
                if (!item.is_virtual &&
                    (!OrderItemService.getParentItem(item, order) || OrderItemService.isShipSeparately(item, order))
                ) {
                    let shipmentItemParam = this.prepareShipmentItemParam(item, order);
                    shipment_items_param[shipmentItemParam.order_item_id] = shipmentItemParam;
                }
            });
        }
        return shipment_items_param;
    }

    /**
     *
     * @param item
     * @return {*}
     */
    getBundleProductOptions(item) {
        if (item.order_item.product_type !== 'bundle') {
            return false;
        }

        let productOptions = item.order_item.product_options;

        if (!productOptions) {
            return false;
        }

        if (typeof productOptions === 'string') {
            productOptions = JSON.parse(productOptions);
        }

        return productOptions;
    }

    /**
     *
     * @param orderItemId
     * @param params
     * @return {*}
     */
    updateShipmentItemParamByOrderItemId(orderItemId, params = {}) {
        let shipment_items_param = this.state.shipment_items_param;
        if (!orderItemId || !shipment_items_param.hasOwnProperty(orderItemId)) {
            return false;
        }
        let shipmentItem = shipment_items_param[orderItemId];
        return this.updateShipmentItemParam(shipmentItem, params);
    }

    /**
     *
     * @param shipmentItem
     * @param params
     */
    updateShipmentItemParam(shipmentItem, params = {}) {
        let orderItemId = shipmentItem.order_item_id;
        let shipment_items_param = this.state.shipment_items_param;
        if (orderItemId && shipment_items_param[orderItemId]) {
            if (params.qty !== undefined) {
                let error_message = "";
                if (shipmentItem.hasOwnProperty('max_qty') && params.qty > shipmentItem.max_qty) {
                    error_message = this.props.t(`The maximum quantity to ship is {{qty}}`, {qty: shipmentItem.max_qty});
                    params.qty = shipmentItem.max_qty;
                }
                if (params.qty > shipmentItem.qty_left) {
                    error_message = this.props.t("Qty to Ship cannot be greater than Qty Left.");
                    params.qty = shipmentItem.qty_left;
                }
                if (params.qty < 0) {
                    error_message = this.props.t("Qty to Ship cannot be smaller than 0.");
                    params.qty = 0;
                }
                if (shipmentItem.disable) {
                    error_message = this.props.t("The item is not assigned to this Source");
                    params.qty = 0;
                }
                params.error_message = error_message;
            }
            Object.assign(shipment_items_param[orderItemId], params);
        }
        this.setState({shipment_items_param})
    }

    /**
     *
     * @param shipmentItems
     * @param note
     */
    createShipment(shipmentItems, note) {
        let order = this.props.order;
        this.props.actions.createShipment(order, shipmentItems, note);
        this.props.cancelCreateShipment();
    }

    /**
     *
     * @param itemId
     * @return {*}
     */
    getItemComponent(itemId) {
        let shipment_items_param = this.state.shipment_items_param;
        if (shipment_items_param[itemId]) {
            let shipmentItem = shipment_items_param[itemId];
            let props = {
                key: itemId,
                order: this.props.order,
                shipmentItem: shipmentItem,
                updateShipmentItemParam: this.updateShipmentItemParam.bind(this),
                decimalSymbol: this.state.decimalSymbol,
            };


            if (shipmentItem.order_item.parent_item_id) {
                let parentItem = Object.values(shipment_items_param)
                    .find(item => item.order_item.item_id === shipmentItem.order_item.parent_item_id);
                if (!parentItem) {
                    return this.createShipmentItemSimpleComponent(props);
                }

                if (!this.getBundleProductOptions(parentItem)) {
                    return this.createShipmentItemSimpleComponent(props);
                }

                return null;

            }
            let bundleProductOptions = this.getBundleProductOptions(shipmentItem);

            if (!bundleProductOptions) {
                return this.createShipmentItemSimpleComponent(props);
            }

            let childrenItems = Object.values(shipment_items_param)
                .filter(item => shipmentItem.order_item.item_id === item.order_item.parent_item_id);

            if (bundleProductOptions.shipment_type === AbstractProductTypeService.SHIPMENT_SEPARATELY) {
                return <CreateShipmentItemSeparatelyShipBundle
                    {...props} childrenItems={childrenItems} bundleProductOptions={bundleProductOptions}/>
            }

            if (!shipmentItem.qty_left) {
                return null;
            }

            props.order.items.forEach(item => {
                if (shipmentItem.order_item.item_id !== item.parent_item_id) {
                    return;
                }
                childrenItems.push(this.prepareShipmentItemParamSkipCheckDummy(item, props.order));
            });

            return <CreateShipmentItemTogetherShipBundle
                {...props} childrenItems={childrenItems} bundleProductOptions={bundleProductOptions}/>
        }
        return null;
    }

    /**
     *
     * @param props
     * @returns {*}
     */
    createShipmentItemSimpleComponent(props) {
        if (props.shipmentItem.qty_left <= 0) {
            return null;
        }

        return <CreateShipmentItemSimpleComponent {...props}/>;
    }

    /**
     *
     * @return {*}
     */
    headerContent() {
        const {t} = this.props;
        return (
            <Fragment>
                <div className="block-title">
                    <button className="btn-cannel" onClick={() => this.cancel()}>{t('Cancel')}</button>
                    <strong className="title">
                        {t(
                            'Ship Items - Order #{{id}}',
                            {id: this.props.order ? this.props.order.increment_id : ""}
                        )}
                    </strong>
                    <div className={"price"}>
                        {OrderHelper.formatPrice(
                            this.props.order ? this.props.order.grand_total : 0
                        )}
                    </div>
                </div>
                <div className="block-search">
                    <div className="box-check">
                        <label className="label-checkbox">
                            <span>{t('Select max Qty to Ship')}</span>
                            <input type="checkbox"
                                   defaultChecked={this.props.is_using_max_qty}
                                   onChange={(event) => this.useMaxQty(event)}/>
                            <span>&nbsp;</span>
                        </label>
                    </div>
                </div>
            </Fragment>
        );
    }

    /**
     *
     * @return {*}
     */
    footerContent() {
        const {t} = this.props;
        return (
            <div className="block-bottom">
                <div className="actions-accept">
                    <button className={"btn btn-default"}
                            type="button"
                            onClick={() => this.onClickShip()}>
                        {t('Ship')}
                    </button>
                </div>
            </div>
        );
    }


    /**
     * template to render
     * @returns {*}
     */
    template() {
        const {t} = this.props;
        return (
            <Fragment>
                <div className="wrapper-shipment-order">
                    {this.headerContent()}
                    <div className="block-content" ref={this.setBlockContentElement}>
                        <table className="table table-order">
                            <thead>
                            <tr>
                                <th className="t-col">&nbsp;</th>
                                <th className="t-product">{t('Product')}</th>
                                <th className="t-qty">{t('Qty Left')}</th>
                                <th className="t-qtyrefund">{t('Qty to Ship')}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {
                                Object.keys(this.state.shipment_items_param).map(itemId =>
                                    this.getItemComponent(itemId)
                                )
                            }
                            </tbody>
                        </table>
                    </div>
                    {this.footerContent()}
                </div>
                <ShipmentConfirmation
                    isOpen={this.state.show_confirm_popup}
                    onClose={() => this.onConfirmPopupClose()}
                    onClickOk={(note) => this.onConfirmPopupClickOk(note)}
                />
                <ShipmentCancellation
                    isOpen={this.state.show_cancel_popup}
                    onClose={() => this.cancel()}
                    onClickOk={() => this.props.cancelCreateShipment()}
                />
            </Fragment>
        );
    }
}

OrderCreateShipmentComponent.propTypes = {
    actions: PropTypes.object.isRequired,
    cancelCreateShipment: PropTypes.func.isRequired,
};

class OrderCreateShipmentContainer extends CoreContainer {
    static className = 'OrderCreateShipmentContainer';

    /**
     *
     * @param state
     * @return {{productQtys: Function.productQtys}}
     */
    static mapState(state) {
        const {productQtys} = state.extension.orderCreateShipmentReducer;
        return {
            productQtys,
        };
    }

    static mapDispatch(dispatch) {
        return {
            actions: {
                createShipment: (order, itemsToShip, note, tracks) => dispatch(
                    ShippingAction.createShipment(order, itemsToShip, note, tracks)
                )
            }
        }
    }
}

/**
 * @type {OrderCreateShipmentContainer}
 */
export default ContainerFactory.get(OrderCreateShipmentContainer).withRouter(
    ComponentFactory.get(OrderCreateShipmentComponent)
)