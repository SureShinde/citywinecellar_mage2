import React from 'react';
import PropTypes from "prop-types";
import {CoreComponent} from "../../../../../../../../framework/component/index";
import ComponentFactory from "../../../../../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../../../../../framework/container/CoreContainer";
import OrderHelper from "../../../../../../../../helper/OrderHelper";

class ShipmentConfirmationComponent extends CoreComponent {
    static className = 'ShipmentConfirmationComponent';

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            note: ''
        }
    }

    /**
     * Set comment text
     * @param event
     */
    setNoteText(event) {
        let value = event.target.value;
        let realValue = OrderHelper.stripHtmlTags(value);
        if (value !== realValue) {
            event.target.value = realValue;
        }
        this.setState({note: realValue})
    }

    /**
     *  trigger whenever click YES button
     */
    ok() {
        const {onClickOk} = this.props;
        onClickOk(this.state.note);
        this.close();
    }

    /**
     *  trigger whenever click NO button
     */
    close() {
        const {onClose} = this.props;
        this.setState({ note: ''});
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
                <div className="modal-dialog modal-md" role="document">
                    <div className="modal-content">
                        <div className="modal-body">
                            <h3 className="title">{this.props.t('Shipment Confirmation')}</h3>
                            <p>{this.props.t('Are you sure you want to ship the selected items?')}</p>
                            <textarea className="form-control refund-confirmation-text"
                                      placeholder={this.props.t('Note to shipment (Optional)')}
                                      defaultValue={this.state.note}
                                      onChange={event => this.setNoteText(event)}>
                            </textarea>
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

ShipmentConfirmationComponent.propTypes = {
    onClickOk: PropTypes.func.isRequired,
    onClose: PropTypes.func.isRequired,
    isOpen: PropTypes.bool.isRequired,
};

class ShipmentConfirmationContainer extends CoreContainer {
    static className = 'ShipmentConfirmationContainer';
}

/**
 * @type {ShipmentConfirmationContainer}
 */
export default ContainerFactory.get(ShipmentConfirmationContainer).withRouter(
    ComponentFactory.get(ShipmentConfirmationComponent)
)