import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import i18n from "../../../../config/i18n";
import CurrencyHelper from "../../../../helper/CurrencyHelper";
import {AbstractPopupService} from "../AbstractPopupService";

export class ZippayVerifyPopupService extends AbstractPopupService {
    static className = 'ZippayVerifyPopupService';
    cssClassName = 'zippay';
    dialogTitle;
    dialogLoader;
    dialogFooter;
    dialogCancelButton;
    dialogRetryButton;
    dialogConfirmButton;
    closeTimeout;

    /**
     * Create modal
     *
     * @return {*}
     */
    createModal() {
        this.modal           = document.createElement('div');
        this.modal.className = `modal fade in popup-messages ${this.cssClassName}-popup popup-confirm`;
        this.modal.setAttribute('role', 'dialog');
        this.modal.style.display = 'none';

        let modalDialog       = document.createElement('div');
        modalDialog.className = 'modal-dialog modal-md2';
        this.modal.setAttribute('role', 'document');

        this.modal.appendChild(modalDialog);
        return this.modal;
    }

    /**
     *
     * @param service
     * @return {HTMLDivElement}
     */
    getModalHeader(service) {
        let modalHeader       = document.createElement('div');
        modalHeader.className = 'modal-header';


        let headerTitle       = document.createElement('h4');
        headerTitle.className = 'modal-title';
        headerTitle.innerHTML = i18n.translator.translate(
            `${service.payment ? service.payment.title : ''} {{amount}}`,
            {
                amount: service.payment ? CurrencyHelper.format(service.payment.amount_paid, null, null) : ''
            }
        );

        modalHeader.appendChild(headerTitle);
        return modalHeader;
    }

    /**
     *
     * @param transactionId
     * @return {HTMLDivElement}
     */
    getModalBody(transactionId) {
        let modalBody       = document.createElement('div');
        modalBody.className = 'modal-body';

        this.dialogTitle       = document.createElement('h3');
        this.dialogTitle.className = 'title';
        this.dialogTitle.innerHTML = i18n.translator.translate('Verifying Transaction '+ transactionId +'<br/>');
        modalBody.appendChild(this.dialogTitle);

        this.dialogLoader       = document.createElement('div');
        this.dialogLoader.className = 'loader-product loader';
        modalBody.appendChild(this.dialogLoader);

        return modalBody;
    }

    /**
     *
     * @param service
     * @param transactionId
     * @param confirmCallback
     * @param cancelCallback
     * @return {HTMLDivElement}
     */
    getModalFooter(service, transactionId, confirmCallback, cancelCallback) {

        this.dialogFooter       = document.createElement('div');
        this.dialogFooter.className = 'modal-footer actions-1column';

        /**
         *  cancel button
         * @type {HTMLAnchorElement}
         */
        this.dialogCancelButton       = document.createElement('a');
        this.dialogCancelButton.className = 'close-modal';
        this.dialogCancelButton.innerHTML = i18n.translator.translate("CANCEL");

        this.dialogCancelButton.onclick = () => {
            if (this.modal) {
                this.modal.remove();
            }

            return cancelCallback && cancelCallback();
        };

        this.dialogFooter.appendChild(this.dialogCancelButton);

        /**
         *  retry button
         * @type {HTMLAnchorElement}
         */
        this.dialogRetryButton       = document.createElement('a');
        this.dialogRetryButton.className = 'close-modal hidden';
        this.dialogRetryButton.innerHTML = i18n.translator.translate("RETRY");

        this.dialogRetryButton.onclick = () => {
            if (!this.canStartProcessTransaction()) {
                return;
            }
            this.startProcessTransaction(transactionId);
            return confirmCallback && confirmCallback();
        };

        this.dialogFooter.appendChild(this.dialogRetryButton);

        /**
         * confirm button
         *
         * @type {HTMLAnchorElement}
         */
        this.dialogConfirmButton       = document.createElement('a');
        this.dialogConfirmButton.className = 'close-modal hidden';
        this.dialogConfirmButton.innerHTML = i18n.translator.translate("CONFIRM");

        this.dialogConfirmButton.onclick = () => {
            if (!this.canStartProcessTransaction()) {
                return;
            }
            this.startProcessTransaction();
            return confirmCallback && confirmCallback(this.dialogInput.value);
        };

        this.dialogFooter.appendChild(this.dialogConfirmButton);

        return this.dialogFooter;
    }

    /**
     *
     * @return {boolean}
     */
    canStartProcessTransaction() {
        return true;
    }

    /**
     *
     */
    startProcessTransaction(transactionId) {
        this.dialogTitle.innerHTML = i18n.translator.translate('Verifying Transaction '+transactionId+'<br/>');
        this.dialogLoader.className = 'loader-product loader';
        this.dialogRetryButton.className = 'close-modal hidden';
        this.dialogConfirmButton.className = 'close-modal hidden';
        this.dialogFooter.className = 'modal-footer actions-1column';

    }

    /**
     * Show Zippay popup
     * @param service
     * @param transactionId
     * @param confirmCallback
     * @param loadCallback
     * @param cancelCallback
     */
    showPopup(service, transactionId, loadCallback, confirmCallback, cancelCallback) {
        if (this.closeTimeout) {
            window.clearTimeout(this.closeTimeout);
        }

        let modalContent       = document.createElement('div');
        modalContent.className = 'modal-content';

        let modalHeader = this.getModalHeader(service);
        let modalBody   = this.getModalBody(transactionId);

        modalContent.appendChild(modalHeader);
        modalContent.appendChild(modalBody);

        let modalFooter = this.getModalFooter(service, transactionId, confirmCallback, cancelCallback);
        modalContent.appendChild(modalFooter);

        if (this.modal) {
            this.modal.remove();
        }
        this.createModal();
        let modalDialog = this.modal.getElementsByClassName('modal-dialog')[0];
        modalDialog.appendChild(modalContent);
        this.modal.style.display = 'block';
        document.body.appendChild(this.modal);

        let autoFocusTimeout = setTimeout(() => {
            clearTimeout(autoFocusTimeout);
            return loadCallback();
        }, 300);

        return this;
    }

    showError(service, message) {
        this.showMessage(service, message);
        this.dialogLoader.className = 'loader-product loader hidden';
        this.dialogFooter.className = 'modal-footer actions-2column';
        this.dialogRetryButton.className = 'close-modal';
    }

    /**
     *
     * @param service
     */
    showCancelling(service) {
        this.showMessage(service, 'Cancelling');
        this.dialogConfirmButton.className = 'close-modal hidden';
        this.dialogCancelButton.className = 'close-modal hidden';
        this.dialogRetryButton.className = 'close-modal hidden';
    }

    /**
     *
     * @param service
     * @param confirmCallback
     * @param cancelCallback
     */
    showApproveConfirmation(service, confirmCallback, cancelCallback) {
        this.showMessage(service, 'The amount of the total order matches the Zip Pay/Zip Money value of the customer’s purchase');

        this.dialogConfirmButton.onclick = confirmCallback;
        this.dialogCancelButton.onclick = cancelCallback;

        this.dialogConfirmButton.className = 'close-modal';
        this.dialogCancelButton.className = 'close-modal';
        this.dialogRetryButton.className = 'close-modal hidden';

        this.dialogLoader.className = 'loader-product loader hidden';
        this.dialogFooter.className = 'modal-footer actions-2column';
    }

    /**
     * Show Zippay popup
     *
     * @param service
     * @param message
     */
    showMessage(service, message) {

        if (!this.modal) {
            return;
        }

        if (!this.dialogTitle) {
            return;
        }

        this.dialogTitle.innerHTML =  i18n.translator.translate(message.replace(/\n/gi, '</br>'));
    }

    /**
     *
     * @return {string}
     */
    getMessage() {

        if (!this.modal) {
            return '';
        }

        if (!this.dialogTitle) {
            return '';
        }

        return this.dialogTitle.innerHTML;
    }

    /**
     *
     */
    clearOptions() {
        if (!this.modal) {
            return;
        }


        if (!this.dialogFooter) {
            return;
        }

        this.dialogFooter.innerHTML = '';
    }

    /**
     * Close modal after 2 seconds
     */
    closePopup() {
        if (this.modal) {
            this.closeTimeout = setTimeout(() => {
                this.dialogTitle.remove();
                this.dialogLoader.remove();
                this.dialogRetryButton.remove();
                this.dialogCancelButton.remove();
                this.dialogConfirmButton.remove();
                this.dialogFooter.remove();
                this.modal.remove();
            }, 500)
        }
    }
}

/** @type ZippayVerifyPopupService */
let zippayVerifyPopupService = ServiceFactory.get(ZippayVerifyPopupService);

export default zippayVerifyPopupService;