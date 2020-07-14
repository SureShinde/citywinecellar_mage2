import React from 'react';
import PropTypes from "prop-types";
import CoreComponent from "../../../../../../framework/component/CoreComponent";
import CoreContainer from "../../../../../../framework/container/CoreContainer";
import ComponentFactory from "../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../framework/factory/ContainerFactory";
import ShippingAction from "../../../action/order/ShippingAction";

class CreateShipmentButtonComponent extends CoreComponent {
    static className = 'CreateShipmentButtonComponent';

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            isLoading: false
        }
    }

    componentWillReceiveProps(nextProps, nextContext) {
        const {productQtys, afterLoadProductQtys} = nextProps;
        const {isLoading} = this.state;
        if (productQtys && isLoading) {
            this.setState({isLoading: false}, () => afterLoadProductQtys(true));
        }

    }

    startLoadProductQtys() {
        const {startLoadProductQtys, order} = this.props;
        this.setState({isLoading: true}, () => {
            startLoadProductQtys(order);
        })
    }

    /**
     * template to render
     * @returns {*}
     */
    template() {
        const {isLoading} = this.state;
        const {t, canShip} = this.props;
        let classNames = ["btn btn-default btn-order-take-shipment"];

        if (!canShip) {
            classNames.push('disabled');
        }

        return (
            <li>
                <button className={classNames.join(' ')}
                        onClick={() => canShip && this.startLoadProductQtys()}>
                    {
                        isLoading
                            ? <div className="loader-product"/>
                            : t('Take Shipment')
                    }
                </button>

            </li>
        );
    }
}

CreateShipmentButtonComponent.propTypes = {
    order: PropTypes.object.isRequired,
    canShip: PropTypes.bool.isRequired,
    afterLoadProductQtys: PropTypes.func.isRequired,
    startLoadProductQtys: PropTypes.func.isRequired,
};

class CreateShipmentButtonContainer extends CoreContainer {
    static className = 'CreateShipmentButtonContainer';

    /**
     *
     * @param state
     * @returns {{}}
     */
    static mapState(state) {
        const {productQtys} = state.extension.orderCreateShipmentReducer;
        return {
            productQtys,
        };
    }

    /**
     *
     * @param dispatch
     * @return {{startLoadProductQtys: (function(): *)}}
     */
    static mapDispatch(dispatch) {
        return {
            startLoadProductQtys: (order) => dispatch(ShippingAction.startLoadProductQtys(order))
        };
    }

}

/**
 * @type {CreateShipmentButtonContainer}
 */
export default ContainerFactory.get(CreateShipmentButtonContainer).withRouter(
    ComponentFactory.get(CreateShipmentButtonComponent)
)