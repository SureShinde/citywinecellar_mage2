import React, {Fragment} from 'react';
import PropTypes from "prop-types";
import {CreateShipmentItemSimpleComponent} from "./CreateShipmentItemSimple";
import ComponentFactory from "../../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../../../../framework/container/CoreContainer";
import NumPad from "../../../../../../../view/component/lib/react-numpad";
import CurrencyHelper from "../../../../../../../helper/CurrencyHelper";

class CreateShipmentItemTogetherShipBundleComponent extends CreateShipmentItemSimpleComponent {
    static className = 'CreateShipmentItemTogetherShipBundleComponent';

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
     *
     * @param child
     * @return {*}
     */
    getQtyToShip(child) {
        if (child) {
            let qty = (child.qty_left || 0).toString();
            return this.formatQty(qty, child.order_item);
        }

        let shipmentItem = this.props.shipmentItem;
        let qty = (shipmentItem.qty_left || 0).toString();
        return this.formatQty(qty, shipmentItem.order_item);
    }

    /**
     * get current refund qty
     *
     * @return {string}
     */
    getQty(child) {
        let shipmentItem = this.props.shipmentItem;
        let qty = shipmentItem.qty || 0;
        if (child) {
            return this.formatQty(child.qty_left * qty / shipmentItem.qty_left, child.order_item);
        }

        return this.formatQty((qty).toString(), shipmentItem.order_item);
    }

    /**
     *
     * @param child
     * @returns {*}
     */
    getChildContent(child) {
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
                        <div className="box-field-qty">
                                <span className="form-control qty"
                                >
                                    {this.getQty(child)}
                                </span>
                            <a className={"btn-number qtyminus disabled"}
                               data-field="qty"
                            >-</a>
                            <a className={"btn-number qtyplus disabled"}
                               data-field="qty"
                            >+</a>
                        </div>
                    </div>
                </td>
            </tr>
        )
    }

    /**
     * template to render
     * @returns {*}
     */
    template() {
        const {childrenItems, shipmentItem} = this.props;
        const {decimalSymbol} = this.state;
        return (
            <Fragment>
                <tr className="parent-bundle-item">
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
                                <a className={"btn-number qtyplus" + (this.canIncreaseQty() ? " " : " disabled")}
                                   data-field="qty"
                                   onClick={() => this.increaseQty()}>+</a>
                            </div>
                        </div>
                        {this.getErrorMessage()}
                    </td>
                </tr>
                {
                    childrenItems.map(children => this.getChildContent(children))
                }
            </Fragment>
        )
    }
}

CreateShipmentItemTogetherShipBundleComponent.propTypes = {
    childrenItems: PropTypes.array.isRequired,
    updateShipmentItemParam: PropTypes.func.isRequired,
    order: PropTypes.object.isRequired,
    shipmentItem: PropTypes.object.isRequired,
    decimalSymbol: PropTypes.string.isRequired,
};

class CreateShipmentItemTogetherShipBundleContainer extends CoreContainer {
    static className = 'CreateShipmentItemTogetherShipBundleContainer';
}

/**
 * @type {CreateShipmentItemTogetherShipBundleContainer}
 */
export default ContainerFactory.get(CreateShipmentItemTogetherShipBundleContainer).withRouter(
    ComponentFactory.get(CreateShipmentItemTogetherShipBundleComponent)
)