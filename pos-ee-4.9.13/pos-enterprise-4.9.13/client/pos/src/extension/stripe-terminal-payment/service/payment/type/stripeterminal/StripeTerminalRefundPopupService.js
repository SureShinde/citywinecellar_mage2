import {StripeTerminalPopupAbstractService} from "./StripeTerminalPopupAbstractService";
import i18n from "../../../../../../config/i18n";
import StripeTerminalConstant from "../../../../view/constant/payment/StripeTerminalConstant";
import ServiceFactory from "../../../../../../framework/factory/ServiceFactory";

export class StripeTerminalRefundPopupService extends StripeTerminalPopupAbstractService {
    static className = 'StripeTerminalRefundPopupService';
    cssClassName = StripeTerminalConstant.POPUP_CLASS_NAME;
    dialogRetryButton;

    /**
     * @inheritDoc
     */
    getModalFooter(confirmCallback, cancelCallback) {
        this.dialogFooter = super.getModalFooter(confirmCallback, cancelCallback);

        /**
         *  retry button
         * @type {HTMLAnchorElement}
         */
        this.dialogRetryButton = document.createElement('a');
        this.dialogRetryButton.className = 'close-modal hidden';
        this.dialogRetryButton.innerHTML = i18n.translator.translate("RETRY");

        this.dialogRetryButton.onclick = () => {
            this.startProcessTransaction();
            return confirmCallback();
        };

        this.dialogFooter.appendChild(this.dialogRetryButton);
        return this.dialogFooter;
    }


    /**
     * @inheritDoc
     */
    startProcessTransaction() {
        this.dialogRetryButton.className = 'close-modal hidden';
        return super.startProcessTransaction();
    }

    /**
     * @inheritDoc
     */
    showError(message) {
        if (!this.dialogTitle) {
            return false;
        }

        this.dialogLoader.className = 'loader-product loader hidden';
        this.dialogFooter.className = 'modal-footer actions-2column';
        this.dialogRetryButton.className = 'close-modal';
        return super.showMessage(message);
    }

    /**
     * @inheritDoc
     */
    closePopup() {
        return super.closePopup(() => {
            this.dialogRetryButton.remove();
        });
    }
}

/** @type StripeTerminalRefundPopupService */
export default ServiceFactory.get(StripeTerminalRefundPopupService);
