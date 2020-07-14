import React, {Fragment} from 'react';
import CoreComponent from "../../../../framework/component/CoreComponent";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";
import CoreContainer from "../../../../framework/container/CoreContainer";
import ContainerFactory from "../../../../framework/factory/ContainerFactory";
import NumberHelper from "../../../../helper/NumberHelper";
import CustomSale from '../../catalog/product/custom-sale/CustomSale';
import Config from "../../../../config/Config";
import CustomSaleConstant from "../../../constant/custom-sale/CustomSaleConstant";
import ProductList from "../../catalog/ProductList";

export class CartHeaderComponent extends CoreComponent {
    static className = 'CartHeaderComponent';

    constructor(props) {
        super(props);
        this.state = {
            isOpenCustomSalePopup: false,
            isNew: false,
            canShowCustomSaleButton: Config.config[CustomSaleConstant.PRODUCT_ID_PATH]
        }
    }

    /**
     *  Show popup custom sale
     */
    showPopupCustomSale() {
        this.setState({
            isOpenCustomSalePopup: true
        });
    }

    /**
     *  Hide popup custom sale
     */
    hidePopupCustomSale() {
        this.setState({
            isOpenCustomSalePopup: false,
            isNew: true
        });
    }

    /**
     * Set is new custom sale
     * @param {boolean} isNew
     */
    setIsNewCustomSale(isNew = true) {
        this.setState({
            isNew: isNew
        });
    }

    /**
     * render template
     *
     * @returns {*}
     */
    template() {
        const {items_qty} = this.props.quote;

        return (
            <Fragment>
                <div className="cart-header wrapper-header">
                    <div className="header-left">
                        <div className="header-customer">
                            <strong className="title">
                                {this.props.t('Cart')} { items_qty ? '(' + NumberHelper.formatDisplayGroupAndDecimalSeparator(items_qty) + ')' : '' }
                            </strong>
                            {
                                this.state.canShowCustomSaleButton && this.props.currentPage === ProductList.className ?
                                    <button
                                        className="btn-customesale"
                                        type="button"
                                        data-toggle="modal"
                                        data-target="#popup-custom-sale"
                                        onClick={() => this.showPopupCustomSale()}
                                    ><span>{this.props.t('Custom Sale')}</span></button> :
                                    ""
                            }
                        </div>
                    </div>
                </div>
                {
                    this.state.canShowCustomSaleButton ?
                        <CustomSale isOpenCustomSalePopup={this.state.isOpenCustomSalePopup}
                                    isNew={this.state.isNew}
                                    hidePopupCustomSale={() => this.hidePopupCustomSale()}
                                    setIsNewCustomSale={(isNew) => this.setIsNewCustomSale(isNew)}/> :
                        ""
                }

            </Fragment>
        );
    }
}

/**
 *
 * @type {CartHeaderComponent}
 */
const component = ComponentFactory.get(CartHeaderComponent);

export class CartHeaderContainer extends CoreContainer {
    static className = 'CartHeaderContainer';

    /**
     *
     * @param state
     * @return {{quote: *}}
     */
    static mapState(state) {
        const {quote} = state.core.checkout;
        let {currentPage} = state.core.checkout.index;
        return {
            quote,
            currentPage
        }
    }
}

/**
 *
 * @type {CartFooterContainer}
 */
const container = ContainerFactory.get(CartHeaderContainer);

export default container.getConnect(component);