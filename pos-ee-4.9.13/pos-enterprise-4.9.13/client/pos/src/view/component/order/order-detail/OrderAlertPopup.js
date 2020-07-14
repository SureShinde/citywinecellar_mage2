import React from 'react';
import PropTypes from 'prop-types';
import {Modal} from "react-bootstrap";
import {CoreComponent} from "../../../../framework/component/index";
import CoreContainer from "../../../../framework/container/CoreContainer";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../framework/factory/ContainerFactory";

class OrderAlertPopup extends CoreComponent {
    static className = 'OrderAlertPopup';
    /**
     * template to render
     * @returns {*}
     */
    template() {
        return (
            <Modal
                className={"popup-messages"}
                show={this.props.showOrderAlertPopup}
                onHide={() => this.props.closeOrderAlertPopup()}
            >
                <Modal.Body>
                    <p>
                        {this.props.t(this.props.orderAlertPopupData.message)}
                    </p>
                </Modal.Body>
                <Modal.Footer className={"close-modal"}>
                    <button onClick={() => this.props.closeOrderAlertPopup()}>{this.props.t('CLOSE')}</button>
                </Modal.Footer>
            </Modal>
        );
    }
}

OrderAlertPopup.propTypes = {
    showOrderAlertPopup: PropTypes.bool,
    orderAlertPopupData: PropTypes.object,
    closeOrderAlertPopup: PropTypes.func,
};

class OrderAlertPopupContainer extends CoreContainer {
    static className = 'OrderAlertPopupContainer';
}

/**
 * @type {OrderAlertPopupContainer}
 */
export default ContainerFactory.get(OrderAlertPopupContainer).withRouter(
    ComponentFactory.get(OrderAlertPopup)
)