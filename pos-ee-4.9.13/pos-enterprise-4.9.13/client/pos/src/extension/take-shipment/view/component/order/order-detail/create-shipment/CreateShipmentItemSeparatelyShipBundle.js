import React, {Fragment} from 'react';
import PropTypes from "prop-types";
import {CoreComponent} from "../../../../../../../framework/component/index";
import ComponentFactory from "../../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../../../../framework/container/CoreContainer";
import CurrencyHelper from "../../../../../../../helper/CurrencyHelper";
import OrderItemService from "../../../../../../../service/sales/order/OrderItemService";
import NumPad from "../../../../../../../view/component/lib/react-numpad";
import NumberHelper from "../../../../../../../helper/NumberHelper";

class CreateShipmentItemSeparatelyShipBundleComponent extends CoreComponent {
    static className = 'CreateShipmentItemSeparatelyShipBundleComponent';

    setQtyBoxElement = element => this.qtyBoxElement = element;

    constructor(props) {
        super(props);
        let order = props.order;
        let shipmentItem = props.shipmentItem;
        let orderItem = shipmentItem.order_item;


        this.state = {
            item_option: this.getItemOption(orderItem, order),
            decimalSymbol: CurrencyHelper.getDecimalSymbol()
        }
    }

    /**
     * Get item option
     *
     * @param orderItem
     * @param order
     * @return {*}
     */
    getItemOption(orderItem, order) {
        let options = OrderItemService.getOrderItemOptionLabelsAsArray(orderItem, order);
        if (!options || !options.length) {
            return null;
        }
        return <p className="option">{options.join(', ')}</p>;
    }

    /**
     *
     * @return {*}
     */
    getProductColumn() {
        let shipmentItem = this.props.shipmentItem;
        let orderItem = shipmentItem.order_item;
        return <td className={"t-product"}>
            <p className="title"
               dangerouslySetInnerHTML={{__html: orderItem.name}}/>
            <p className="sku">[{orderItem.sku}]</p>
            {this.state.item_option}
        </td>;
    }

    /**
     *
     * @param child
     * @return {*}
     */
    getQtyToShip(child) {
        let shipmentItem = child || this.props.shipmentItem;
        let qty = (shipmentItem.qty_left || 0).toString();
        return this.formatQty(qty, shipmentItem.order_item);
    }

    /**
     * get current refund qty
     *
     * @return {string}
     */
    getQty(child) {
        let shipmentItem = child || this.props.shipmentItem;
        let qty = (shipmentItem.qty || 0).toString();
        return this.formatQty(qty, shipmentItem.order_item);
    }

    /**
     *
     * @param qty
     * @param orderItem
     * @return {*}
     */
    formatQty(qty, orderItem) {
        return orderItem.is_qty_decimal ?
            CurrencyHelper.formatNumberStringToCurrencyString(qty, this.props.order.order_currency_code) :
            qty;
    }

    /**
     * @param child
     * @return {boolean}
     */
    isDisable(child) {
        if (child) {
            return child.disable;
        }
        return this.props.shipmentItem.disable;
    }

    /**
     * Check can decrease item qty
     *
     * @param child
     * @return {boolean}
     */
    canDecreaseQty(child) {
        let shipmentItem = child || this.props.shipmentItem;
        return shipmentItem.qty > 0 && !this.isDisable();
    }

    /**
     * Check can increase item qty
     *
     * @return {boolean}
     */
    canIncreaseQty(child) {
        let shipmentItem = child || this.props.shipmentItem;
        return shipmentItem.qty < shipmentItem.qty_left && !this.isDisable();
    }

    /**
     * Decrease qty
     *
     * @param child
     */
    decreaseQty(child) {
        let shipmentItem = child || this.props.shipmentItem;
        let qty = NumberHelper.minusNumber(shipmentItem.qty, 1);
        this.props.updateShipmentItemParam(shipmentItem, {qty: qty}, true);
    }

    /**
     * Increase qty
     *
     * @param child
     */
    increaseQty(child) {
        let shipmentItem = child || this.props.shipmentItem;
        let qty = NumberHelper.addNumber(shipmentItem.qty, 1);
        this.props.updateShipmentItemParam(shipmentItem, {qty: qty}, true);
    }

    /**
     * Update qty
     *
     * @param child
     * @param qty
     */
    updateQty(child, qty) {
        let shipmentItem = child || this.props.shipmentItem;
        this.props.updateShipmentItemParam(shipmentItem, {qty: qty}, true);
    }

    /**
     * Get current error message
     *
     * @return {*}
     */
    getErrorMessage(child) {
        let shipmentItem = child || this.props.shipmentItem;
        return shipmentItem.error_message ?
            <div className="t-alert">
                <p>{shipmentItem.error_message}</p>
            </div> :
            ""
    }

    /**
     *
     * @param child
     * @returns {*}
     */
    getChildContent(child) {
        const {decimalSymbol} = this.state;
        return (
            <tr className="selection-bundle-item" key={child.order_item.item_id}>
                <td className={"t-col"}>&nbsp;</td>
                <td className={"t-product t-bundle-product"}>
                    <p className="title"
                       dangerouslySetInnerHTML={{__html: child.order_item.name}}/>
                    <p className="sku">[{child.order_item.sku}]</p>
                </td>
                <td className={"t-qty t-bundle-product"}>
                    <span>{this.getQtyToShip(child)}</span>
                </td>
                <td className={"t-qtyrefund t-bundle-product"}>
                    <div className="product-field-qty">
                        <div className="box-field-qty" ref={ref => this.setQtyBoxElement(ref)}>
                            <NumPad.Popover
                                onChange={(newQty) => this.updateQty(child, newQty)}
                                position="centerLeft"
                                arrow="left"
                                value={(child.qty || 0)}
                                isDecimal={!!child.is_qty_decimal}
                                decimalSeparator={decimalSymbol}
                                min={0}
                                max={(child.qty_left || 0)}
                                useParentCoords={true}
                                isShowAction={true}
                            >
                                <span className="form-control qty">{this.getQty(child)}</span>
                            </NumPad.Popover>
                            <a className={"btn-number qtyminus" + (this.canDecreaseQty(child) ? "" : " disabled")}
                               data-field="qty"
                               onClick={() => this.decreaseQty(child)}>-</a>
                            <a className={"btn-number qtyplus" + (this.canIncreaseQty(child) ? "" : " disabled")}
                               data-field="qty"
                               onClick={() => this.increaseQty(child)}>+</a>
                        </div>
                    </div>
                    {this.getErrorMessage(child)}
                </td>
            </tr>
        )
    }

    /**
     * template to render
     * @returns {*}
     */
    template() {
        const {childrenItems} = this.props;

        return (
            <Fragment>
                <tr className="parent-bundle-item">
                    <td className={"t-col"}>&nbsp;</td>
                    {this.getProductColumn()}
                    <td className={"t-qty"}>
                        <span/>
                    </td>
                    <td className={"t-qtyrefund"}>
                        <div className="product-field-qty">
                        </div>
                    </td>
                </tr>
                {
                    childrenItems.map(children => this.getChildContent(children))
                }
            </Fragment>
        )
    }
}

CreateShipmentItemSeparatelyShipBundleComponent.propTypes = {
    childrenItems: PropTypes.array.isRequired,
    updateShipmentItemParam: PropTypes.func.isRequired,
    order: PropTypes.object.isRequired,
    shipmentItem: PropTypes.object.isRequired,
    decimalSymbol: PropTypes.string.isRequired,
};


class CreateShipmentItemSeparatelyShipBundleContainer extends CoreContainer {
    static className = 'CreateShipmentItemSeparatelyShipBundleContainer';
}

/**
 * @type {CreateShipmentItemSeparatelyShipBundleContainer}
 */
export default ContainerFactory.get(CreateShipmentItemSeparatelyShipBundleContainer).withRouter(
    ComponentFactory.get(CreateShipmentItemSeparatelyShipBundleComponent)
)