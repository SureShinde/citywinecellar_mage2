import React, {Fragment} from 'react';
import CreateCreditmemoStepItemItemAbstractComponent from "../CreateCreditmemoStepItemItemAbstract";
import ComponentFactory from "../../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../../../../framework/container/CoreContainer";
import CurrencyHelper from "../../../../../../../helper/CurrencyHelper";
import OrderItemService from "../../../../../../../service/sales/order/OrderItemService";
import TaxHelper from "../../../../../../../helper/TaxHelper";
import {toast} from "react-toastify";
import i18n from "../../../../../../../config/i18n";
import CreditmemoConstant from "../../../../../../constant/order/CreditmemoConstant";

class CreateCreditmemoStepItemItemStorecredit extends CreateCreditmemoStepItemItemAbstractComponent {
    static className = 'CreateCreditmemoStepItemItemStorecredit';

    setQtyBoxElement = element => this.qtyBoxElement = element;
    setReturnStockCheckboxElement = element => this.returnStockCheckboxElement = element;
    setReturnStockMessageElement = element => this.returnStockMessageElement = element;

    lastQty = 0;
    lastBackToStock;
    highlightTimeout;

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        let order_item = props.order_item;
        let decimalSymbol = CurrencyHelper.getCurrencyFormat(this.props.order.order_currency_code).decimal_symbol;
        let creditmemoItemParam = order_item && props.creditmemo_items_param[order_item.item_id] ?
            props.creditmemo_items_param[order_item.item_id] :
            {};
        let orderItem = creditmemoItemParam.order_item;
        this.state = {
            creditmemo_item_param: creditmemoItemParam,
            item_option: this.getItemOption(order_item),
            decimal_symbol: (decimalSymbol ? decimalSymbol : "."),
            can_show_fpt: creditmemoItemParam.qty_to_refund > 0 && orderItem.weee_tax_applied_row_amount > 0
        };
    }

    /**
     * Component will receive props
     *
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        let newCreditmemoItemParam = nextProps.creditmemo_items_param[nextProps.order_item.item_id];
        if (newCreditmemoItemParam && newCreditmemoItemParam.qty !== this.state.creditmemo_item_param.qty) {
            let orderItem = nextProps.order_item;
            this.setState({
                creditmemo_item_param: newCreditmemoItemParam,
                can_show_fpt: newCreditmemoItemParam.qty_to_refund > 0 && orderItem.weee_tax_applied_row_amount > 0
            });
        }
    }

    /**
     * Get item option
     *
     * @param orderItem
     * @return {*}
     */
    getItemOption(orderItem) {
        let options = OrderItemService.getOrderItemOptionLabelsAsArray(orderItem, this.props.order);
        if (!options || !options.length) {
            return null;
        }
        return <p className="option">{options.join(', ')}</p>;
    }

    /**
     * Get qty to refund
     *
     * @return {string}
     */
    getQtyToRefund() {
        let qty = (this.state.creditmemo_item_param.qty_to_refund || 0).toString();
        return this.state.creditmemo_item_param.is_qty_decimal ?
            CurrencyHelper.formatNumberStringToCurrencyString(qty, this.props.order.order_currency_code) :
            qty;
    }

    /**
     * get current refund qty
     *
     * @return {number}
     */
    getQty() {
        return 0;
    }

    /**
     * Get can return stock
     *
     * @return {boolean}
     */
    canReturnStock() {
        return false;
    }

    tryUpdateQty = () => {
        return toast.error(
            i18n.translator.translate(CreditmemoConstant.PREVENT_REFUND_STORE_CREDIT_PRODUCT_ERROR_MESSAGE),
            {
                className: 'wrapper-messages messages-warning',
                autoClose: 3000
            }
        );
    };

    /**
     * template to render
     * @returns {*}
     */
    template() {
        let priceInclTax = TaxHelper.orderDisplayPriceIncludeTax();
        let additionalClassName = this.props.hide_border_top ? " t-bundle-product" : "";
        return (
            <Fragment>
                {this.state.creditmemo_item_param.qty_to_refund > 0 ? (
                    <tr className={"item-" + this.state.creditmemo_item_param.order_item.item_id}>
                        <td className={"t-col" + additionalClassName}>&nbsp;</td>
                        <td className={"t-product" + additionalClassName}>
                            <p className="title"
                               dangerouslySetInnerHTML={{__html: this.props.order_item.name}}/>
                            <p className="sku">[{this.props.order_item.sku}]</p>
                            {this.state.item_option}
                        </td>
                        <td className={"t-qty" + additionalClassName}>
                            <span>{this.getQtyToRefund()}</span>
                        </td>
                        <td className={"t-qtyrefund" + additionalClassName}>
                            <div className="product-field-qty">
                                <div className="box-field-qty" ref={this.setQtyBoxElement}>
                                <span className="form-control qty"
                                      onClick={this.tryUpdateQty}>
                                    {this.getQty()}
                                </span>
                                    <a className={"btn-number qtyminus disabled"}
                                       data-field="qty"
                                       onClick={this.tryUpdateQty}
                                    >-</a>
                                    <a className={"btn-number qtyplus disabled"}
                                       data-field="qty"
                                       onClick={this.tryUpdateQty}
                                    >+</a>
                                </div>
                            </div>
                        </td>
                        {
                            this.props.can_show_return_stock_column ?
                                <td className={"t-return" + additionalClassName}>
                                    <label className="label-checkbox">
                                        <input ref={this.setReturnStockCheckboxElement}
                                               type="checkbox"
                                               disabled={true}
                                               defaultChecked={false}/>
                                        <span>&nbsp;</span>
                                    </label>
                                    <div className="t-alert">
                                        <p ref={this.setReturnStockMessageElement}/>
                                    </div>
                                </td> :
                                null
                        }
                        <td className={"t-price" + additionalClassName}>{this.state.creditmemo_item_param.price}</td>
                        {
                            this.props.can_show_fpt_column ?
                                <td className={"t-fpt" + additionalClassName}>
                                    {this.state.creditmemo_item_param.fpt}
                                </td> :
                                null
                        }
                        <td className={"t-tax" + additionalClassName}>{this.state.creditmemo_item_param.tax}</td>
                        <td className={"t-discount" + additionalClassName}>
                            {this.state.creditmemo_item_param.discount}
                        </td>
                        <td className={"t-rowtotal" + additionalClassName}>
                            <p><b>{this.state.creditmemo_item_param.total_amount}</b></p>
                            <div className="price hidden-desktop">
                                {
                                    this.props.t(
                                        priceInclTax ? 'Price incl. Tax: {{price}}' : 'Price: {{price}}',
                                        {price: this.state.creditmemo_item_param.price}
                                    )
                                }
                            </div>
                            {
                                this.state.can_show_fpt ?
                                    <div className="fpt hidden-desktop">
                                        {this.props.t('FPT: {{price}}', {price: this.state.creditmemo_item_param.fpt})}
                                    </div> :
                                    null
                            }
                            <div className="tax hidden-desktop">
                                {this.props.t('Tax: {{price}}', {price: this.state.creditmemo_item_param.tax})}
                            </div>
                            <div className="discount hidden-desktop">
                                {
                                    this.props.t(
                                        'Discount: {{price}}',
                                        {price: this.state.creditmemo_item_param.discount}
                                    )
                                }
                            </div>
                        </td>
                        <td className="t-col">&nbsp;</td>
                    </tr>
                ) : null
                }
            </Fragment>
        )
    }
}

class CreateCreditmemoStepItemItemStorecreditContainer extends CoreContainer {
    static className = 'CreateCreditmemoStepItemItemStorecreditContainer';
}

/**
 * @type {CreateCreditmemoStepItemItemStorecreditContainer}
 */
export default ContainerFactory.get(CreateCreditmemoStepItemItemStorecreditContainer).withRouter(
    ComponentFactory.get(CreateCreditmemoStepItemItemStorecredit)
)