import React from 'react';
import ServiceFactory from "../framework/factory/ServiceFactory";
import StarWebPrintService from "./printer/StarWebPrintService";
import html2canvas from 'html2canvas';
import StylePrintComponent2x, {
    BARCODE_FONT_SIZE as BARCODE_FONT_SIZE_2X, BARCODE_HEIGHT as BARCODE_HEIGHT_2X, BARCODE_WIDTH as BARCODE_WIDTH_2X
} from "../view/component/print/StylePrintComponent2x";
import StylePrintComponent, {
    BARCODE_FONT_SIZE, BARCODE_HEIGHT, BARCODE_WIDTH
} from "../view/component/print/StylePrintComponent";
import {toast} from "react-toastify";
import i18n from "../config/i18n";

export class PrinterService {
    static className = 'PrinterService';

    /**
     *
     * @param target
     */
    removeWindow = (target) => {
        setTimeout(() => {
            target.parentNode.removeChild(target);
        }, 500);
    };

    /**
     *
     * @returns {{component: *, BARCODE_WIDTH: number, BARCODE_FONT_SIZE: number, BARCODE_HEIGHT: number}}
     */
    getStyleForPrint() {
        if (this.canUseApiPrint()) {
            return {
                BARCODE_FONT_SIZE: BARCODE_FONT_SIZE_2X,
                BARCODE_HEIGHT: BARCODE_HEIGHT_2X,
                BARCODE_WIDTH: BARCODE_WIDTH_2X,
                StylePrintComponent: <StylePrintComponent2x/>
            }
        }

        return {
            BARCODE_FONT_SIZE,
            BARCODE_HEIGHT,
            BARCODE_WIDTH,
            StylePrintComponent: <StylePrintComponent/>
        }
    }

    /**
     *
     * @returns {boolean}
     */
    canUseApiPrint() {
        return StarWebPrintService.isEnable();
    }

    /**
     *
     */
    updateUIBeforeCallApiPrint() {
        toast.success(
            i18n.translator.translate("Printer request has been sent successfully!"),
            {
                position: toast.POSITION.BOTTOM_CENTER,
                className: 'wrapper-messages messages-success'
            }
        );
    }

    /**
     *
     * @param reason
     */
    showError(reason) {
        toast.error(
            i18n.translator.translate(reason),
            {
                className: 'wrapper-messages messages-warning',
                autoClose: 5000
            }
        );
    }
    /**
     *
     * @param printWindow
     * @param callbacks
     * @returns {*}
     */
    printCanvas(printWindow, callbacks) {
        const {onBeforePrint, onAfterPrint} = callbacks;

        if (onBeforePrint) {
            onBeforePrint();
        }

        let printReceipt = printWindow.contentDocument.body.firstChild;
        this.updateUIBeforeCallApiPrint();
        return html2canvas(printReceipt, { scale: 1, foreignObjectRendering: true}).then((canvas) => {
            StarWebPrintService.print(canvas).catch(reason => this.showError(reason));
            if (onAfterPrint) {
                onAfterPrint();
            }

            //DEBUG:
            // document.body.insertBefore(canvas, document.body.firstChild);
        });
    }

    /**
     *
     * @param content
     * @param title
     * @param option
     */
    print(content, title, option) {
        if (StarWebPrintService.isEnable()) {
            // clear prev print window
            if (this.currentPrintWindow) {
                this.removeWindow(this.currentPrintWindow);
                this.currentPrintWindow = null;
            }

            const printWindow = document.createElement('iframe');
            printWindow.style.position = 'absolute';
            printWindow.style.top = '-1000px';
            printWindow.style.left = '-1000px';
            this.currentPrintWindow = printWindow;
            document.body.appendChild(printWindow);
            setTimeout(() => {
                let iFrameDoc = printWindow.contentDocument || printWindow.contentWindow.document;
                iFrameDoc.body.innerHTML = content;
                this.updateUIBeforeCallApiPrint();
                html2canvas(iFrameDoc.body, { scale: 1, foreignObjectRendering: true}).then((canvas) => {
                    StarWebPrintService.print(canvas).catch(reason => this.showError(reason));
                });
            }, 10);
            return;
        }

        let print_window = window.open('', title, option);
        if (print_window) {
            print_window.document.open();
            print_window.document.write(content);
            print_window.print();
            print_window.close();
        } else {
            window.alert("Your browser has blocked the automatic popup, " +
                "please change your browser setting or print the receipt manually");
        }
    }

    /**
     * Print window target
     *
     * @param target
     */
    printTarget(target) {
        target.contentWindow.focus();
        target.contentWindow.print();
    }
}

/**
 * @type {PrinterService}
 */
let printerService = ServiceFactory.get(PrinterService);

export default printerService;
