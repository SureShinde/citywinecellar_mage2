import React from 'react';
import PropTypes from 'prop-types';
import {CoreComponent} from "../../../../../../../../framework/component/index";
import ComponentFactory from "../../../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../../../../../framework/container/CoreContainer";

class ShipmentCancellationComponent extends CoreComponent {
    static className = 'ShipmentCancellationComponent';

    /**
     *  trigger whenever click YES button
     */
    ok() {
        const {onClickOk} = this.props;
        onClickOk();
        this.close();
    }

    /**
     *  trigger whenever click NO button
     */
    close() {
        const {onClose} = this.props;
        onClose();
    }
    /**
     * template to render
     * @returns {*}
     */
    template() {
        let display = this.props.isOpen ? 'block' : 'none';
        return (
            <div className="modal fade in popup-messages" role="dialog" style={{display}}>
                <div className="modal-dialog modal-sm" role="document">
                    <div className="modal-content">
                        <div className="modal-body">
                            <h3 className="title">{this.props.t('Cancel Create Shipment')}</h3>
                            <p>{this.props.t('Are you sure you want to cancel this step?')}</p>
                        </div>
                        <div className="modal-footer actions-2column">
                            <a className="close-modal"
                               onClick={() => this.close()}>
                                {this.props.t('No')}
                            </a>
                            <a className="close-modal"
                               onClick={() => this.ok()}>
                                {this.props.t('Yes')}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

ShipmentCancellationComponent.propTypes = {
    onClickOk: PropTypes.func.isRequired,
    onClose: PropTypes.func.isRequired,
    isOpen: PropTypes.bool.isRequired,
};


class ShipmentCancellationContainer extends CoreContainer {
    static className = 'ShipmentCancellationContainer';
}

/**
 * @type {ShipmentCancellationContainer}
 */
export default ContainerFactory.get(ShipmentCancellationContainer).withRouter(
    ComponentFactory.get(ShipmentCancellationComponent)
)