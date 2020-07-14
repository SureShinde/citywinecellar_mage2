import React, {Fragment} from "react";
import {StripeTerminalSettingAbstract} from "./StripeTerminalSettingAbstract";
import CoreContainer from '../../../../../../../../framework/container/CoreContainer';
import ComponentFactory from '../../../../../../../../framework/factory/ComponentFactory';
import ContainerFactory from "../../../../../../../../framework/factory/ContainerFactory";
import StripeTerminalService from "../../../../../../service/payment/type/StripeTerminalService";
import StripeTerminalConstant from "../../../../../constant/payment/StripeTerminalConstant";

export class RegisterNewTerminal extends StripeTerminalSettingAbstract {
    static className = 'RegisterNewTerminal';
    static title = 'Register New Terminal';

    /**
     * @inheritDoc
     */
    setupData() {
        this.state = {
            isRequesting: false,
            newRegistrationCode: '',
            newReaderLabel: '',
        }
    }

    /**
     * @param event
     */
    setNewRegistration(event) {
        this.setState({
            newRegistrationCode: event.target.value
        })
    }

    /**
     * @param event
     */
    setNewReaderLabel(event) {
        this.setState({
            newReaderLabel: event.target.value
        })
    }

    /**
     *
     * @returns {Promise<void>}
     */
    async registerReader() {
        const {isRequesting, newRegistrationCode, newReaderLabel} = this.state;
        if (isRequesting || !newRegistrationCode || !newReaderLabel) {
            return;
        }

        this.setState({isRequesting: true});
        return StripeTerminalService.registerDevice(
            newReaderLabel,
            newRegistrationCode,
            () => {
                this.setNewReaderLabel({ target: { value: '' }});
                this.setNewRegistration({ target: { value: '' }});
                this.connectionSuccess(StripeTerminalConstant.NEW_READER_HAS_BEEN_REGISTERED_SUCCESSFULLY_MESSAGE)
            },
            reason => this.connectionFail(reason)
        );
    }

    /**
     * template
     * @returns {*}
     */
    template() {
        const {t} = this.props;
        const {isRequesting, newRegistrationCode, newReaderLabel} = this.state;
        let buttonRegisterClassName = `btn btn-default`;
        const canRegisterReader = Boolean(newRegistrationCode.length && newReaderLabel.length);

        if (!canRegisterReader) {
            buttonRegisterClassName = `${buttonRegisterClassName} disabled`;
        }

        return (
            <Fragment>
                <ul className="list-lv1">
                    <li>
                        <label className="title">
                            <strong className="asterisk">{t('Terminal Registration Code')}</strong>
                        </label>
                        <input type="text"
                               id="register_registration_code"
                               className="form-control"
                               value={newRegistrationCode}
                               onChange={(event) => this.setNewRegistration(event)}
                        />
                    </li>
                    <li>
                        <label className="title description-line" htmlFor="register_registration_code">
                            <span className="title-description">
                                {t('Enter the key sequence 0-7-1-3-9 on the terminal to display it\'s registration code.')}
                            </span>
                        </label>
                    </li>
                    <li>
                        <label className="title required">
                            <strong className="asterisk">{this.props.t('Terminal Label')}</strong>
                        </label>
                        <input type="text"
                               className="form-control"
                               value={newReaderLabel}
                               onChange={(event) => this.setNewReaderLabel(event)}
                        />
                    </li>
                </ul>
                <div className={'bottom'}>
                    <button
                        disabled={isRequesting}
                        className={buttonRegisterClassName}
                        type="button"
                        onClick={() => this.registerReader()}>
                        {
                            isRequesting ? (<div
                                className="loader-setting"/>) : t('Register')
                        }
                    </button>
                </div>
            </Fragment>
        )
    }
}

class RegisterNewTerminalContainer extends CoreContainer {
    static className = 'RegisterNewTerminalContainer';
}

/**
 * @type {RegisterNewTerminalContainer}
 */
export default ContainerFactory.get(RegisterNewTerminalContainer).withRouter(
    ComponentFactory.get(RegisterNewTerminal)
);