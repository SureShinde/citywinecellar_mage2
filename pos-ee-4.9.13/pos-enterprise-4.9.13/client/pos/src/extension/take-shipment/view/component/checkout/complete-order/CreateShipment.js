import React from 'react';
import PropTypes from 'prop-types';
import {listen} from "../../../../../../event-bus";
import CoreComponent from "../../../../../../framework/component/CoreComponent";
import CoreContainer from "../../../../../../framework/container/CoreContainer";
import ComponentFactory from "../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../framework/factory/ContainerFactory";
import ShippingAction from "../../../action/order/ShippingAction";
import StockService from "../../../../../../service/catalog/StockService";
import ProductService from "../../../../../../service/catalog/ProductService";

class CreateShipment extends CoreComponent {
    static className = 'CreateShipment';

    /**
     *
     * @param value
     */
    setIsCreateShipment(value) {
        if (!this.quoteHasItemsToCreateShipment()) {
            this.props.setIsCreateShipment(false);
            return;
        }

        this.props.setIsCreateShipment(value);
    }

    componentDidMount() {
        this._unmounted = false;
        this.setIsCreateShipment(this.calculateIsCreateShipmentByQuote(this.props.quote));

        listen('epic_save_shipping_execute_after', ({ quote }) => {
            if (this._unmounted) {
                return;
            }
            this.setIsCreateShipment(this.calculateIsCreateShipmentByQuote(quote));
        });
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     *
     * @param quote
     * @return {boolean}
     */
    quoteHasItemsToCreateShipment(quote) {
        quote = quote || this.props.quote;
        return !!quote.items.find(item => {
            const {product} = item;
            let productStockService = StockService.getProductStockService(product);
            if (!productStockService.isManageStock(product)) {
                return true;
            }

            const qty = ProductService.getQty(product);
            return qty > 0;
        });
    }

    /**
     *
     * @param quote
     * @return {boolean}
     */
    calculateIsCreateShipmentByQuote(quote) {
        /**
         * Mark as enable if no select shipping method and has item to ship
         *
         * @type {boolean}
         */
        let noSelect = !quote.shipping_method || !quote.shipping_method.length;
        return noSelect && this.quoteHasItemsToCreateShipment(quote);
    }
    /**
     * Render template
     * @returns {*}
     */
    template() {
        const {t, isCreateShipment} = this.props;
        return (
            <div className="payment-full-amount shipping-toggle-button">
                <div className="info">
                    <div className="price">
                        <div className="box">
                            <span className="label">{t('Ship all items?')}</span>
                            <span className="value">
                                 <label className="checkbox">
                                    <input type="checkbox"
                                           checked={isCreateShipment}
                                           onChange={(event) =>
                                               this.setIsCreateShipment(event.target.checked)}
                                    />
                                     <span/>
                                </label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

class CreateShipmentContainer extends CoreContainer {
    static className = "CreateShipmentContainer";

    /**
     *
     * @param state
     * @returns {{}}
     */
    static mapState(state) {
        const {isCreateShipment} = state.extension.checkoutCreateShipmentReducer;
        const {quote} = state.core.checkout;
        return {
            isCreateShipment,
            quote
        };
    }

    /**
     *
     * @param dispatch
     * @returns {{}}
     */
    static mapDispatch(dispatch) {
        return {
            setIsCreateShipment: isCreateShipment => dispatch(ShippingAction.setCheckoutIsCreateShipment(isCreateShipment))
        }
    }
}

CreateShipment.propTypes = {
    quote: PropTypes.object.isRequired,
    isCreateShipment: PropTypes.bool.isRequired,
    setIsCreateShipment: PropTypes.func.isRequired,
};

export default ContainerFactory.get(CreateShipmentContainer).getConnect(
    ComponentFactory.get(CreateShipment)
);