import {StripeTerminalPopupAbstractService} from "./StripeTerminalPopupAbstractService";
import StripeTerminalConstant from "../../../../view/constant/payment/StripeTerminalConstant";
import ServiceFactory from "../../../../../../framework/factory/ServiceFactory";

export class StripeTerminalPurchasePopupService extends StripeTerminalPopupAbstractService {
    static className = 'StripeTerminalPurchasePopupService';
    cssClassName = StripeTerminalConstant.POPUP_CLASS_NAME;
}

/** @type StripeTerminalPurchasePopupService */
export default ServiceFactory.get(StripeTerminalPurchasePopupService);