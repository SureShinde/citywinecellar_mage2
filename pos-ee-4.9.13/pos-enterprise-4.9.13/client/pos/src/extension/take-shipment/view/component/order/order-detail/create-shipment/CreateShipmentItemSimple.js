import React from 'react';
import PropTypes from "prop-types";
import {CoreComponent} from "../../../../../../../framework/component/index";
import ComponentFactory from "../../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../../../../framework/container/CoreContainer";
import CurrencyHelper from "../../../../../../../helper/CurrencyHelper";
import OrderItemService from "../../../../../../../service/sales/order/OrderItemService";
import NumPad from "../../../../../../../view/component/lib/react-numpad";
import NumberHelper from "../../../../../../../helper/NumberHelper";

export class CreateShipmentItemSimpleComponent extends CoreComponent {
    static className = 'CreateShipmentItemSimpleComponent';

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
     * @return {*}
     */
    getQtyToShip() {
        let shipmentItem = this.props.shipmentItem;
        let qty = (shipmentItem.qty_left || 0).toString();
        return this.formatQty(qty, shipmentItem.order_item);
    }

    /**
     * get current refund qty
     *
     * @return {string}
     */
    getQty() {
        let shipmentItem = this.props.shipmentItem;
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
     * @return {boolean}
     */
    isDisable() {
        return this.props.shipmentItem.disable;
    }

    /**
     * Check can decrease item qty
     *
     * @return {boolean}
     */
    canDecreaseQty() {
        const {shipmentItem} = this.props;
        const {qty} = shipmentItem;
        return qty > 0 && !this.isDisable();
    }

    /**
     * Check can increase item qty
     *
     * @return {boolean}
     */
    canIncreaseQty() {
        const {shipmentItem} = this.props;
        const {qty, qty_left} = shipmentItem;
        return qty < qty_left && !this.isDisable();
    }

    /**
     * Decrease qty
     */
    decreaseQty() {
        let shipmentItem = this.props.shipmentItem;
        let qty = NumberHelper.minusNumber(shipmentItem.qty, 1);
        this.props.updateShipmentItemParam(shipmentItem, {qty: qty}, true);
    }

    /**
     * Increase qty
     */
    increaseQty() {
        let shipmentItem = this.props.shipmentItem;
        let qty = NumberHelper.addNumber(shipmentItem.qty, 1);
        this.props.updateShipmentItemParam(shipmentItem, {qty: qty}, true);
    }

    /**
     * Update qty
     *
     * @param qty
     */
    updateQty(qty) {
        let shipmentItem = this.props.shipmentItem;
        this.props.updateShipmentItemParam(shipmentItem, {qty: qty}, true);
    }

    /**
     * Get current error message
     *
     * @return {*}
     */
    getErrorMessage() {
        let shipmentItem = this.props.shipmentItem;
        return shipmentItem.error_message ?
            <div className="t-alert">
                <p>{shipmentItem.error_message}</p>
            </div> :
            ""
    }

    /**
     * template to render
     * @returns {*}
     */
    template() {
        const {shipmentItem} = this.props;
        const {decimalSymbol} = this.state;
        return (
            <tr>
                <td className={"t-col"}>&nbsp;</td>
                {this.getProductColumn()}
                <td className={"t-qty"}>
                    <span>{this.getQtyToShip()}</span>
                </td>
                <td className={"t-qtyrefund"}>
                    <div className="product-field-qty">
                        <div className="box-field-qty" ref={this.setQtyBoxElement}>
                            <NumPad.Popover
                                onChange={(newQty) => this.updateQty(newQty)}
                                position="centerLeft"
                                arrow="left"
                                value={(shipmentItem.qty || 0)}
                                isDecimal={!!shipmentItem.is_qty_decimal}
                                decimalSeparator={decimalSymbol}
                                min={0}
                                max={(shipmentItem.qty_left || 0)}
                                useParentCoords={true}
                                isShowAction={true}
                            >
                                <span className="form-control qty">{this.getQty()}</span>
                            </NumPad.Popover>
                            <a className={"btn-number qtyminus" + (this.canDecreaseQty() ? "" : " disabled")}
                               data-field="qty"
                               onClick={() => this.decreaseQty()}>-</a>
                            <a className={"btn-number qtyplus" + (this.canIncreaseQty() ? "" : " disabled")}
                               data-field="qty"
                               onClick={() => this.increaseQty()}>+</a>
                        </div>
                    </div>
                    {this.getErrorMessage()}
                </td>
            </tr>
        )
    }
}

CreateShipmentItemSimpleComponent.propTypes = {
    childrenItems: PropTypes.array,
    updateShipmentItemParam: PropTypes.func.isRequired,
    order: PropTypes.object.isRequired,
    shipmentItem: PropTypes.object.isRequired,
    decimalSymbol: PropTypes.string.isRequired,
};

class CreateShipmentItemSimpleContainer extends CoreContainer {
    static className = 'CreateShipmentItemSimpleContainer';
}

/**
 * @type {CreateShipmentItemSimpleContainer}
 */
export default ContainerFactory.get(CreateShipmentItemSimpleContainer).withRouter(
    ComponentFactory.get(CreateShipmentItemSimpleComponent)
)