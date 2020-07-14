import i18n from "../../../../../../config/i18n";
import CurrencyHelper from "../../../../../../helper/CurrencyHelper";
import ServiceFactory from "../../../../../../framework/factory/ServiceFactory";
import {AbstractPopupService} from "../../../../../../service/payment/type/AbstractPopupService";

export class StripeTerminalPopupAbstractService extends AbstractPopupService {
    static className = 'StripeTerminalPopupAbstractService';
    cssClassName;
    dialogTitle;
    dialogLoader;
    dialogFooter;
    dialogCancelButton;
    closeTimeout;

    /**
     * Create modal
     *
     * @return {*}
     */
    createModal() {
        let modal = document.createElement('div');
        modal.className = `modal fade in popup-messages ${this.cssClassName}-popup popup-confirm`;
        modal.setAttribute('role', 'dialog');
        modal.style.display = 'none';

        let modalDialog = document.createElement('div');
        modalDialog.className = 'modal-dialog modal-md2';
        modal.setAttribute('role', 'document');

        modal.appendChild(modalDialog);
        return modal;
    }

    /**
     * Create modal header
     *
     * @param service
     * @returns {HTMLElement}
     */
    getModalHeader(service) {
        let modalHeader = document.createElement('div');
        modalHeader.className = 'modal-header';

        let headerTitle = document.createElement('h5');
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
     * Create modal body
     *
     * @return {HTMLElement}
     */
    getModalBody() {
        let modalBody = document.createElement('div');
        modalBody.className = 'modal-body';

        this.dialogTitle = document.createElement('h3');
        this.dialogTitle.className = 'title';
        this.dialogTitle.innerHTML = i18n.translator.translate('...');
        modalBody.appendChild(this.dialogTitle);

        this.dialogLoader = document.createElement('div');
        this.dialogLoader.className = 'loader-product loader hidden';
        modalBody.appendChild(this.dialogLoader);

        return modalBody;
    }

    /**
     *
     * @param confirmCallback
     * @param cancelCallback
     * @return {HTMLDivElement}
     */
    getModalFooter(confirmCallback, cancelCallback) {
        this.dialogFooter = document.createElement('div');
        this.dialogFooter.className = 'modal-footer actions-1column';

        /**
         *  cancel button
         * @type {HTMLAnchorElement}
         */
        this.dialogCancelButton = document.createElement('a');
        this.dialogCancelButton.className = 'close-modal';
        this.dialogCancelButton.innerHTML = i18n.translator.translate("CANCEL");

        this.dialogCancelButton.onclick = () => {
            if (this.modal) {
                this.modal.remove();
            }

            return cancelCallback();
        };

        this.dialogFooter.appendChild(this.dialogCancelButton);
        return this.dialogFooter;
    }

    /**
     *  Show loading
     */
    startProcessTransaction() {
        if (!this.dialogTitle) {
            return this;
        }
        this.dialogTitle.innerHTML = i18n.translator.translate('Processing Transaction');
        this.dialogTitle.innerHTML += '<br/>';
        this.dialogLoader.className = 'loader-product loader';
        this.dialogFooter.className = 'modal-footer actions-1column';
        return this;
    }

    /**
     * Show popup
     *
     * @param service
     * @param confirmCallback
     * @param cancelCallback
     */
    showPopup(service, confirmCallback, cancelCallback) {
        if (this.closeTimeout) {
            window.clearTimeout(this.closeTimeout);
        }

        let modalContent = document.createElement('div');
        modalContent.className = 'modal-content';

        let modalHeader = this.getModalHeader(service);
        let modalBody = this.getModalBody();

        modalContent.appendChild(modalHeader);
        modalContent.appendChild(modalBody);

        let modalFooter = this.getModalFooter(confirmCallback, cancelCallback);
        modalContent.appendChild(modalFooter);

        if (this.modal) {
            this.modal.remove();
        }

        this.modal = this.createModal();
        let modalDialog = this.modal.getElementsByClassName('modal-dialog')[0];
        modalDialog.appendChild(modalContent);
        this.modal.style.display = 'block';
        document.body.appendChild(this.modal);

        let autoRunTimeout = setTimeout(() => {
            this.startProcessTransaction();
            clearTimeout(autoRunTimeout);
            return confirmCallback();
        }, 100);

        return this;
    }

    /**
     * Show message on popup
     *
     * @param message
     * @return {*}
     */
    showMessage(message) {

        if (!this.modal) {
            return false;
        }

        if (!this.dialogTitle) {
            return false;
        }

        message = message.replace(/\n/gi, '</br>');
        this.dialogTitle.innerHTML = i18n.translator.translate(message);
        return this;
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
     *  Close modal after 0.5 seconds
     *
     * @param callback
     */
    closePopup(callback = () => {
    }) {
        if (!this.modal) {
            return;
        }

        this.closeTimeout = setTimeout(() => {
            this.dialogTitle.remove();
            this.dialogLoader.remove();
            this.dialogCancelButton.remove();
            this.dialogFooter.remove();
            this.modal.remove();
            callback();
        }, 500)
    }
}

/** @type StripeTerminalPopupAbstractService */
export default ServiceFactory.get(StripeTerminalPopupAbstractService);
