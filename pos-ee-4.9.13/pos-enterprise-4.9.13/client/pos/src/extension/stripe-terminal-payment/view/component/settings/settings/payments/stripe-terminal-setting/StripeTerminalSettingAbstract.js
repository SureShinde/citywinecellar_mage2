import {toast} from "react-toastify";
import ComponentFactory from '../../../../../../../../framework/factory/ComponentFactory';
import AbstractGrid from "../../../../../../../../framework/component/grid/AbstractGrid";
import StripeTerminalService from "../../../../../../service/payment/type/StripeTerminalService";
import StripeTerminalConstant from "../../../../../constant/payment/StripeTerminalConstant";

export class StripeTerminalSettingAbstract extends AbstractGrid {
    static className = 'StripeTerminalSettingAbstract';
    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.setupData();
    }

    /**
     *  prepare dât, allow plugin
     */
    setupData() {
        this.state = {
            isRequesting: false,
        }
    }

    /**
     *
     * @param deviceType
     * @param discoveryType
     * @return {boolean}
     */
    infoIsValid(deviceType = false, discoveryType = false) {
        deviceType = deviceType || StripeTerminalService.getDeviceType();
        if (!deviceType) {
            this.showError('Please fill Device Type.');
            return false;
        }

        return true
    }

    showError(message) {
        toast.error(
            this.props.t(message),
            {
                className: 'wrapper-messages messages-warning',
                autoClose: 1000
            }
        );
    }

    /**
     * Connect  success
     */
    connectionSuccess(message) {
        message && toast.success(
            this.props.t(message),
            {
                position: toast.POSITION.TOP_CENTER,
                className: 'wrapper-messages messages-success',
                autoClose: 1000
            }
        );
        this.setState({isRequesting: false})
    }


    /**
     * Connect  fail
     * @param reason
     */
    connectionFail(reason) {
        reason = reason || StripeTerminalConstant.CONNECTION_ERROR_MESSAGE;
        this.showError(reason);
        this.setState({isRequesting: false})
    }
}

/**
 *  @type {StripeTerminalSettingAbstract}
 */
export default ComponentFactory.get(StripeTerminalSettingAbstract)