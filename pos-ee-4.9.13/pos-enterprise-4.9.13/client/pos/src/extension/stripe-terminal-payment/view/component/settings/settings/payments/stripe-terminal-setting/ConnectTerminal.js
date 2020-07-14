import React, {Fragment} from "react";
import ComponentFactory from '../../../../../../../../framework/factory/ComponentFactory';
import CoreContainer from '../../../../../../../../framework/container/CoreContainer';
import ContainerFactory from "../../../../../../../../framework/factory/ContainerFactory";
import StripeTerminalService from "../../../../../../service/payment/type/StripeTerminalService";
import StripeTerminalConstant from "../../../../../constant/payment/StripeTerminalConstant";
import {StripeTerminalSettingAbstract} from "./StripeTerminalSettingAbstract";

export class ConnectTerminal extends StripeTerminalSettingAbstract {
    static className = 'ConnectTerminal';
    static title = 'Connect Terminal';

    /**
     *
     * @inheritDoc
     */
    setupData() {
        let state = {
            deviceType : StripeTerminalService.getDeviceType(),
            selectedReader: false,
            isRequesting: false,
            readers: [],
        };

        const connectedReader = StripeTerminalService.getConnectedReader();

        if (connectedReader) {
            state.readers.push(connectedReader);
            state.selectedReader = connectedReader;
        }

        this.state = state;
    }

    /**
     * @param event
     */
    setDeviceType(event) {
        const value = event.target.value;
        StripeTerminalService.setDeviceType(value);
        this.setState({
            deviceType: value,
            readers: [],
        })
    }

    /**
     *
     * @param selectedReader
     */
    selectReader(selectedReader) {
        this.setState({selectedReader});
    }

    /**
     *
     * @returns {Promise<void>}
     */
    async connectReader() {
        const {isRequesting, selectedReader} = this.state;
        if (isRequesting) {
            return;
        }

        this.setState({isRequesting: true});
        return StripeTerminalService.connectToReader(
            selectedReader,
            () => this.connectionSuccess(),
            reason => this.connectionFail(reason)
        );
    }

    /**
     *
     * @returns {Promise<void>}
     */
    async disconnectReader() {
        const {isRequesting} = this.state;
        if (isRequesting) {
            return;
        }

        this.setState({isRequesting: true});
        return StripeTerminalService.disconnectReader(
            () => this.connectionSuccess(),
            reason => this.connectionFail(reason)
        );
    }

    /**
     *
     * @returns {Promise<void>}
     */
    async discoveryReaders() {
        const {isRequesting} = this.state;
        if (isRequesting) {
            return;
        }

        let deviceType = StripeTerminalService.getDeviceType();

        this.setState({isRequesting: true, readers: [], selectedReader: false });
        return StripeTerminalService.discoverReaders(
            deviceType,
            readers => {
                let newState = {
                    readers
                };
                if (readers.length) {
                    newState.selectedReader = readers[0];
                }
                this.setState(newState, () => {
                    this.connectionSuccess()
                })
            },
            reason => this.connectionFail(reason)
        );
    }

    /**
     *
     * @return {*[]}
     */
    deviceTypesTemplate() {
        const keys = Object.keys(StripeTerminalConstant.DEVICE_TYPES_MAP);
        const values = StripeTerminalConstant.DEVICE_TYPES_MAP;

        return keys.map(key => (
            <option key={key} value={key}>{values[key]}</option>
        ))
    }

    /**
     * template
     * @returns {*}
     */
    template() {
        const {t}  = this.props;
        const {isRequesting, deviceType, selectedReader, readers}  = this.state;
        const buttonDiscoveryClassName = `btn btn-default`;
        const buttonConnectClassName = `btn ${!selectedReader ? "btn-cancel" : "btn-default"}`;
        const buttonDisconnectClassName = `btn btn-default`;
        const connectedReader = StripeTerminalService.getConnectedReader();
        const canDiscoveryReader = !connectedReader;
        const canConnectReader = selectedReader && !connectedReader;

        return (
            <Fragment>
                <ul className="list-lv1">
                    <li>
                        <label className="title">
                            <strong>{t('Select Mode')}</strong>
                        </label>
                        <select className="form-control"
                                disabled={!!connectedReader}
                                value={deviceType}
                                onChange={(event) => this.setDeviceType(event)}>
                            {this.deviceTypesTemplate()}
                        </select>
                    </li>
                    <li>
                        <label className="title">
                            <strong>{t('Connect Terminal')}</strong>
                        </label>

                        <select className="form-control"
                                disabled={!!connectedReader}
                                value={selectedReader.id}
                                onChange={(event) => this.selectReader(readers[event.target.value])}>
                            {
                                readers.map((reader, key) => {
                                    return (
                                        <option key={key} value={key}>{reader.label}</option>
                                    )
                                })
                            }
                        </select>
                    </li>
                </ul>
                <div className={'bottom'}>
                    {
                        canDiscoveryReader && (
                            <button
                                disabled={isRequesting}
                                className={buttonDiscoveryClassName}
                                type="button"
                                onClick={() => this.discoveryReaders()}>{
                                isRequesting ? (<div className="loader-setting"/>) : t('Refresh List')
                            }
                            </button>
                        )
                    }

                    {
                        canConnectReader && (
                            <button
                                disabled={isRequesting}
                                className={buttonConnectClassName}
                                type="button"
                                onClick={() => this.connectReader()}>
                                {
                                    isRequesting ? (<div className="loader-setting"/>) : t('Connect')
                                }
                            </button>
                        )
                    }
                    {
                        connectedReader && (
                            <button
                                disabled={isRequesting}
                                className={buttonDisconnectClassName}
                                type="button"
                                onClick={() => this.disconnectReader()}>
                                {
                                    isRequesting ? (<div className="loader-setting"/>) : t('Disconnect')
                                }
                            </button>
                        )
                    }
                </div>
            </Fragment>
        )
    }
}

class ConnectTerminalContainer extends CoreContainer {
    static className = 'ConnectTerminalContainer ';
}

/**
 * @type {ConnectTerminalContainer}
 */
export default ContainerFactory.get(ConnectTerminalContainer).withRouter(
    ComponentFactory.get(ConnectTerminal)
);