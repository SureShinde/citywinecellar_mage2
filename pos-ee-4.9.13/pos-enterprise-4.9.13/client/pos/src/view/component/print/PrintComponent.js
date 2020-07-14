import React, {Fragment} from 'react';
import ReactToPrint from "../lib/react-to-print/ReactToPrint";
import JsBarcode from 'jsbarcode';
import PropTypes from 'prop-types';
import CoreComponent from "../../../framework/component/CoreComponent";
import CurrencyHelper from "../../../helper/CurrencyHelper";
import LocationService from "../../../service/LocationService";
import {toast} from "react-toastify";
import DateTimeHelper from "../../../helper/DateTimeHelper";
import PaymentHelper from "../../../helper/PaymentHelper";
import OrderWeeeDataService from "../../../service/weee/OrderWeeeDataService";
import PaymentConstant from "../../constant/PaymentConstant";
import UserService from "../../../service/user/UserService";
import ConfigHelper from "../../../helper/ConfigHelper";
import ReceiptService from "../../../service/receipt/ReceiptService";
import PosService from "../../../service/PosService";
import SessionService from "../../../service/session/SessionService";
import moment from "moment/moment";
import OrderItemService from "../../../service/sales/order/OrderItemService";
import ProductTypeConstant from "../../constant/ProductTypeConstant";
import AddressConstant from "../../constant/checkout/quote/AddressConstant";
import GiftcardHelper from "../../../helper/GiftcardHelper";
import NumberHelper from "../../../helper/NumberHelper";
import OrderHelper from "../../../helper/OrderHelper";
import TaxHelper from "../../../helper/TaxHelper";
import ShippingConstant from "../../constant/ShippingConstant";
import PrinterService from "../../../service/PrinterService";
import {fire, listen} from "../../../event-bus";
import layout from "../../../framework/Layout";
import ComponentFactory from "../../../framework/factory/ComponentFactory";

export default class PrintComponent extends CoreComponent {
    static className = 'PrintComponent';

    /**
     *   initial state
     *
     */
    constructor(props) {
        super(props);
        this.state = {
            barcode: null
        };

        listen('order-history-set-current-order-after', ({ order }) => {
            if (order) {
                this.updateBarcode(order);
            }
        });

        listen('checkout-place-order-before', ({ order }) => {
            if (order) {
                this.updateBarcode(order);
            }
        });
    }

    /**
     *  component did update data
     *
     * @return void print content
     */
    componentDidUpdate() {
        const {orderData, reportData, creditmemo} = this.props;
        const newObject = orderData || reportData || creditmemo;

        if (!newObject ) {
            return;
        }
        try {
            this.printContent();
        } catch (e) {
            this.props.hasOwnProperty('finishPrint') && this.props.finishPrint();
            toast.error(
                this.props.t("Your browser has blocked the automatic popup, " +
                    "please change your browser setting or print the receipt manually"),
                {className: 'wrapper-messages messages-warning'}
            );
        }
    }

    /**
     *
     * @param order
     */
    updateBarcode(order) {
        const {
            BARCODE_HEIGHT,
            BARCODE_WIDTH,
            BARCODE_FONT_SIZE,
        } = PrinterService.getStyleForPrint();

        JsBarcode("#barcode", order.increment_id, {
            font: 'Helvetica',
            fontSize: BARCODE_FONT_SIZE,
            width: BARCODE_WIDTH,
            height: BARCODE_HEIGHT,
            displayValue: true
        });

        const s = new XMLSerializer().serializeToString(document.getElementById('barcode'));
        const encodedData = window.btoa(s);
        const barcode = `data:image/svg+xml;base64,${encodedData}`;
        return this.setState({
            barcode
        });
    }

    /**
     *  Call function print
     *
     * @return void
     */
    printContent() {
        this.refElement && this.refElement.triggerRef.click();
    }

    /**
     *  set refElement
     *
     * @return void
     */
    setReactToPrint(element) {
        if (element) {
            this.refElement = element;
        }
    }

    /**
     *  component render DOM expression
     *  @return string
     *
     * */
    template() {
        return (
            <div className="hidden">
                <svg id="barcode"/>
                <ReactToPrint
                    ref={this.setReactToPrint.bind(this)}
                    trigger={() => <button id="triggerPrintButton" type="button" className="hidden">Print</button>}
                    copyStyles={false}
                    onAfterPrint={() => { this.props.finishPrint(); window.focus(); } }
                    content={() => {
                        return this.componentRef
                    }}
                />
                <WrapToPrintComponent ref={(node) => (this.componentRef = node)}
                                  barcode={this.state.barcode}
                                  orderData={this.props.orderData}
                                  reportData={this.props.reportData}
                                  isReprint={this.props.isReprint}
                                  t={this.props.t}
                                  customer={this.props.customer}
                                  quote={this.props.quote}
                                  creditmemo={this.props.creditmemo}
                />
            </div>
        )
    }
}

PrintComponent.propsTypes = {
    barcode: PropTypes.string,
    orderData: PropTypes.object,
    reportData: PropTypes.object,
    isReprint: PropTypes.bool,
    creditmemo: PropTypes.object,
    customer: PropTypes.object,
    quote: PropTypes.object,
    finishPrint: PropTypes.func
};

export class ToPrintComponent extends CoreComponent {
    static className = 'ToPrintComponent';

    /**
     *   initial state
     *
     */
    constructor(props) {
        super(props);
        this.state = {}
    }


    /**
     *  get template order or report
     *
     * @return template
     */
    getTemplate() {
        let {orderData, creditmemo, reportData} = this.props;
        if (creditmemo) {
            return (this.getTemplateCreditmemoReceipt(creditmemo));
        } else if (orderData) {
            return (this.getTemplateReceipt(orderData));
        } else if (reportData) {
            return (this.getTemplateReport(reportData));
        }
    }


    /**
     *  get template receipt
     *
     * @param {object} orderData
     * @return template receipt
     */
    getTemplateReceipt(orderData) {
        const {isReprint, t, barcode} = this.props;
        let companyName = LocationService.getCurrentStoreName();
        let locationName = LocationService.getCurrentLocationName();
        let locationAddress = ReceiptService.getDisplayLocationAddress();
        let locationPlacedOrder = LocationService.getDisplayLocation(orderData);
        let locationTelephone = LocationService.getCurrentLocationTelephone();
        let receiptHeader = LocationService.getCurrentLocationHeader();
        let receiptFooter = LocationService.getCurrentLocationFooter();
        let incrementId = (orderData.increment_id) ? ("" + orderData.increment_id) : "";
        let createAt = DateTimeHelper.getReceiptDateTime(orderData.created_at);
        let printedAt = DateTimeHelper.getCurrentDateTime();
        let staffName = orderData.pos_staff_name;
        let customerName = ReceiptService.getFullName(orderData);
        let phone = "";
        let addresses = orderData.addresses;
        let receiptLogo = ReceiptService.getReceiptLogo();
        addresses.map(addess => {
            if (addess.address_type === AddressConstant.SHIPPING_ADDRESS_TYPE) {
                phone = addess.telephone;
            }
            return addess
        });

        let items = orderData.items;
        let classNameReprint = isReprint ? "reprint" : "hidden";

        let isRefunded = false;
        items.forEach((item) => {
            if (item.qty_refunded) {
                isRefunded = true;
            }
        });

        let PaymentReceipts = [];
        let payments = orderData.payments;
        if (payments) {
            payments.forEach(payment => {
                if (payment.type) return;
                if (!payment.receipt) return;
                PaymentReceipts.push(payment.receipt);
            });
        }
        let classNameRefunded = isRefunded ? "reprint" : "hidden";

        const {
            StylePrintComponent,
        } = PrinterService.getStyleForPrint();
        return (
            <Fragment>
                {StylePrintComponent}
                <div className="block-printreceipt">
                    <img className={(receiptLogo) ? "" : "hidden"} src={receiptLogo} alt={"logo"}
                         width={"250"} height={"50"}/>
                    <div className={(receiptHeader) ? "" : "hidden"}
                         dangerouslySetInnerHTML={{__html: receiptHeader}} />
                    <div className={(companyName) ? "title" : "hidden"}>{companyName}</div>
                    <p className={(locationName) ? "" : "hidden"}>{locationName}</p>
                    <br/>
                    <p className={(locationAddress) ? "" : "hidden"}>{locationAddress}</p>
                    <p className={(locationTelephone) ? "" : "hidden"}>{locationTelephone}</p>
                    <table>
                        <tbody>
                        <tr>
                            <td colSpan="4">
                                <hr/>
                            </td>
                        </tr>
                        <Fragment key="incrementId">
                            <tr>
                                <td>#{incrementId}</td>
                                <td className="text-right">{createAt}</td>
                            </tr>
                        </Fragment>
                        <Fragment key="locationPlacedOrder">
                            <tr className={"created-at-location-field"}>
                                <td>{t('Created At Location')}</td>
                                <td className="text-right">{locationPlacedOrder}</td>
                            </tr>
                        </Fragment>
                        <Fragment key="Cashier">
                            <tr>
                                <td>{t('Cashier')}</td>
                                <td className="text-right">{staffName}</td>
                            </tr>
                        </Fragment>
                        {layout('print')('cashier_name_layout')('receipt_cashier_name_after')()(this,orderData)}
                        <Fragment key="Customer">
                            <tr className={!orderData.customer_is_guest ? "" : "hidden"}>
                                <td>{t('Customer')}</td>
                                <td className="text-right">{customerName}</td>
                            </tr>
                        </Fragment>
                        <Fragment key="Phone">
                            <tr className={!orderData.customer_is_guest ? "" : "hidden"}>
                                <td>{t('Phone')}</td>
                                <td className="text-right">{phone}</td>
                            </tr>
                        </Fragment>
                        <tr>
                            <td colSpan="4">
                                <hr/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table>
                        <thead>
                        <tr>
                            <th className="t-name">{t('Item')}</th>
                            <th className="t-qty text-right">{t('Qty')}</th>
                            <th className="t-price text-right">{t('Price')}</th>
                            <th className="t-total text-right">{t('Subtotal')}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {this.getTemplateItems(items, orderData, null)}
                        </tbody>
                    </table>
                    <table>
                        <tbody>
                        <tr>
                            <td colSpan="4">
                                <hr/>
                            </td>
                        </tr>
                        {this.getTemplateSubtotal(orderData)}
                        {this.getTemplateDiscount(orderData)}
                        {layout('print')('template_receipt')('discount_after')()(this, orderData)}
                        {
                            orderData.gift_voucher_discount ? this.getTemplateGiftcardDiscount() : null
                        }
                        {
                            orderData.gift_voucher_gift_codes && orderData.gift_voucher_gift_codes_discount ?
                                this.getTemplateGiftcodes(
                                    orderData,
                                    'gift_voucher_gift_codes',
                                    'gift_voucher_gift_codes_discount',
                                    orderData.order_currency_code
                                ) : null
                        }

                        {layout('print')('template_receipt_layout')('discount_after')()(this, orderData)}

                        {this.getTemplateShipping(orderData)}
                        {this.getTemplateFPT(orderData)}
                        {this.getTemplateTax(orderData)}
                        {layout('print')('template_receipt')('tax_after')()(this, orderData)}
                        {this.getTemplateGrandTotal(orderData)}

                        {layout('print')('template_receipt_layout')('grand_total_after')()(this, orderData)}

                        {this.getTemplatePaymentArea(orderData)}
                        {layout('print')('template_receipt')('payment_after')()(this)}
                        {this.getTemplatePluginArea(orderData)}
                        </tbody>
                    </table>
                    <br/>
                    <div className="text-center">
                        <p>{t('Thank you for your purchase!')}</p>
                        <img alt="" src={barcode}/>
                        <div className={classNameReprint}>
                            <span>*****</span><strong>{t('REPRINT')}</strong><span>*****</span>
                        </div>
                        <div className={classNameRefunded}>
                            <span>*****</span><strong>{t('REFUNDED')}</strong><span>*****</span>
                        </div>

                        <div className="reprint">{t('Printed At')}: {printedAt}</div>
                    </div>

                    <div className={(receiptFooter) ? "" : "hidden"}
                         dangerouslySetInnerHTML={{__html: receiptFooter}} />
                </div>
                {
                    PaymentReceipts.length ? (<hr/>) : null
                }
                {
                    PaymentReceipts.map((PaymentReceipt, key) => {
                        return (
                            <Fragment key={key}>
                                <pre key={key}>
                                    {PaymentReceipt}
                                </pre>
                                <hr/>
                            </Fragment>
                        )
                    })
                }
            </Fragment>
        )
    }


    /**
     *  get template creditmemo receipt
     *
     * @param {object} creditmemo
     * @return template receipt
     */
    getTemplateCreditmemoReceipt(creditmemo) {
        let orderData = creditmemo.order;
        let companyName = LocationService.getCurrentStoreName();
        let locationName = LocationService.getCurrentLocationName();
        let locationAddress = ReceiptService.getDisplayLocationAddress();
        let locationTelephone = LocationService.getCurrentLocationTelephone();
        let receiptHeader = LocationService.getCurrentLocationHeader();
        let receiptFooter = LocationService.getCurrentLocationFooter();
        let receiptLogo = ReceiptService.getReceiptLogo();
        let incrementId = (creditmemo.increment_id) ? ("" + creditmemo.increment_id) : "";
        let originOrderId = (orderData.increment_id) ? ("" + orderData.increment_id) : "";
        let createAt = DateTimeHelper.getCurrentDateTime();
        let printedAt = DateTimeHelper.getCurrentDateTime();
        let staffName = UserService.getStaffName();
        let customerName = ReceiptService.getFullName(orderData);
        let phone = "";
        let addresses = orderData.addresses;
        addresses.map(addess => {
            if (addess.address_type === AddressConstant.SHIPPING_ADDRESS_TYPE) {
                phone = addess.telephone;
            }
            return addess
        });

        let items = [];
        let existBundleItem = [];
        let creditmemoItems = creditmemo.items;
        let orderItems = orderData.items;
        let itemShowInReceipt;
        orderItems.forEach((orderItem) => {
            creditmemoItems.forEach((creditmemoItem) => {
                if (creditmemoItem.order_item_id === orderItem.item_id && creditmemoItem.qty) {
                    itemShowInReceipt = orderItem;
                    if (orderItem.parent_item_id) {
                        let parentIsGroup = false;
                        let parentItem = null;
                        orderItems.forEach((termItem) => {
                            if (
                                termItem.item_id === orderItem.parent_item_id
                                && termItem.product_type === ProductTypeConstant.BUNDLE
                            ) {
                                parentItem = termItem;
                                parentIsGroup = true;
                                return false;
                            }
                        });

                        if (parentIsGroup && existBundleItem.indexOf(parentItem.item_id) === -1) {
                            // itemShowInReceipt.parent_item_id = "";
                            existBundleItem.push(parentItem.item_id);
                            items.push(parentItem);
                        }

                    } else {
                        items.push(itemShowInReceipt);
                    }
                }
                return creditmemoItem;
            });
            return orderItem;
        });

        let PaymentReceipts = [];
        Array.isArray(orderData.payments) && orderData.payments.forEach(payment => {
            if (!payment.type) return;
            if (!payment.receipt) return;
            PaymentReceipts.push(payment.receipt);
        });
        const {t, barcode} = this.props;
        let currencyCode = orderData.order_currency_code;
        let {total_qty, subtotal, shipping_amount, adjustment_positive} = creditmemo;
        let {
            adjustment_negative,
            discount_amount,
            tax_amount,
            grand_total,
            gift_voucher_discount
        } = creditmemo;
        discount_amount = NumberHelper.addNumber(discount_amount, gift_voucher_discount);

        let eventDataGetDiscountAfter = {
            creditmemo: creditmemo,
            discount_amount: discount_amount
        };
        fire('view_print_get_template_creditmemo_receipt_get_discount_amount_after', eventDataGetDiscountAfter);
        discount_amount = eventDataGetDiscountAfter.discount_amount;

        const {
            StylePrintComponent,
        } = PrinterService.getStyleForPrint();
        return (
            <Fragment>
                {StylePrintComponent}
                {layout('print')('template_receipt')('style_print_component_after')()(this)}
                <div className="block-printreceipt">
                    <img className={(receiptLogo) ? "" : "hidden"} src={receiptLogo} alt={"logo"}
                         width={"250"} height={"50"}/>
                    <div className={(receiptHeader) ? "" : "hidden"}
                         dangerouslySetInnerHTML={{__html: receiptHeader}} />
                    <div className={(companyName) ? "title" : "hidden"}>{companyName}</div>
                    <p className={(locationName) ? "" : "hidden"}>{locationName}</p>
                    <br/>
                    <p className={(locationAddress) ? "" : "hidden"}>{locationAddress}</p>
                    <p className={(locationTelephone) ? "" : "hidden"}>{locationTelephone}</p>
                    <br/>
                    <div className="title">{t('Refund Receipt')}</div>
                    <br/>
                    <table>
                        <tbody>
                        <tr>
                            <td colSpan="4">
                                <hr/>
                            </td>
                        </tr>
                        <Fragment key="incrementId">
                            <tr>
                                <td>#{incrementId}</td>
                                <td className="text-right">{createAt}</td>
                            </tr>
                        </Fragment>
                        <Fragment key="originOrderId">
                            <tr>
                                <td>{t('Original Order')}</td>
                                <td className="text-right">#{originOrderId}</td>
                            </tr>
                        </Fragment>
                        <Fragment key="Cashier">
                            <tr>
                                <td>{t('Cashier')}</td>
                                <td className="text-right">{staffName}</td>
                            </tr>
                        </Fragment>
                        <Fragment key="Customer">
                            <tr className={!orderData.customer_is_guest ? "" : "hidden"}>
                                <td>{t('Customer')}</td>
                                <td className="text-right">{customerName}</td>
                            </tr>
                        </Fragment>
                        <Fragment key="Phone">
                            <tr className={!orderData.customer_is_guest ? "" : "hidden"}>
                                <td>{t('Phone')}</td>
                                <td className="text-right">{phone}</td>
                            </tr>
                        </Fragment>
                        <tr>
                            <td colSpan="4">
                                <hr/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table>
                        <thead>
                        <tr>
                            <th className="t-name">{t('Item')}</th>
                            <th className="t-qty text-right">{t('Qty')}</th>
                            <th className="t-price text-right">{t('Price')}</th>
                            <th className="t-total text-right">{t('Subtotal')}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {this.getTemplateItems(items, orderData, creditmemoItems)}
                        </tbody>
                    </table>
                    <table>
                        <tbody>
                        <tr>
                            <td colSpan="4">
                                <hr/>
                            </td>
                        </tr>
                        {this.getTemplateTotal(
                            t('Total items refunded'),
                            NumberHelper.formatDisplayGroupAndDecimalSeparator(total_qty), "Total items refunded"
                        )}
                        {
                            subtotal ?
                                this.getTemplateTotal(
                                    t('Subtotal'),
                                    CurrencyHelper.format(subtotal, currencyCode, null),
                                    "Subtotal"
                                ) : null
                        }
                        {
                            shipping_amount ?
                                this.getTemplateTotal(
                                    t('Refund Shipping'),
                                    CurrencyHelper.format(shipping_amount, currencyCode, null),
                                    "Refund Shipping"
                                ) : null
                        }
                        {
                            adjustment_positive ?
                                this.getTemplateTotal(
                                    t('Adjustment Refund'),
                                    CurrencyHelper.format(adjustment_positive, currencyCode, null),
                                    "Adjustment Refund"
                                ) : null
                        }
                        {
                            adjustment_negative ?
                                this.getTemplateTotal(
                                    t('Adjustment Fee'),
                                    CurrencyHelper.format(0 - Math.abs(adjustment_negative), currencyCode,
                                        null),
                                    "Adjustment Fee"
                                ) : null
                        }
                        {
                            discount_amount ?
                                this.getTemplateTotal(
                                    OrderHelper.getDiscountDisplay(orderData, false),
                                    CurrencyHelper.format(
                                        0 - Math.abs(discount_amount),
                                        currencyCode,
                                        null
                                    ),
                                    "Discount"
                                ) : null
                        }

                        {layout('print')('template_creditmemo')('discount_after')()(this, orderData)}

                        {
                            gift_voucher_discount ? this.getTemplateGiftcardDiscount() : null
                        }
                        {
                            creditmemo.gift_voucher_gift_codes && creditmemo.gift_voucher_gift_codes_refund_amount ?
                                this.getTemplateGiftcodes(
                                    creditmemo,
                                    'gift_voucher_gift_codes',
                                    'gift_voucher_gift_codes_refund_amount',
                                    creditmemo.order_currency_code,
                                ) : null
                        }
                        {
                            tax_amount ?
                                this.getTemplateTotal(
                                    t('Tax'), CurrencyHelper.format(tax_amount, currencyCode, null),
                                    "Tax"
                                ) : null
                        }
                        {
                            this.getTemplateTotal(
                                t('Grand Total Refunded'),
                                CurrencyHelper.format(grand_total, currencyCode, null),
                                "Grand Total Refunded",
                                true
                            )
                        }
                        {this.getTemplateListRefundTo(creditmemo, orderData)}
                        </tbody>
                    </table>
                    {this.getTemplatePluginArea(orderData, creditmemo, true)}
                    <br/><br/>
                    <div className="text-center">
                        <p>{t('Thank you for your purchase!')}</p>
                        <img alt="" src={barcode}/>
                        <div className="reprint">
                            <span>*****</span><strong>{t('REFUNDED')}</strong><span>*****</span>
                        </div>
                        <div className="reprint">{t('Printed At') + ': ' + printedAt}</div>
                    </div>

                    <div className={(receiptFooter) ? "" : "hidden"}
                         dangerouslySetInnerHTML={{__html: receiptFooter}} />
                </div>
                {
                    PaymentReceipts.length ? (<hr/>) : null
                }
                {
                    PaymentReceipts.map((PaymentReceipt, key) => {
                        return (
                            <pre key={key}>
                                {PaymentReceipt}
                            </pre>
                        )
                    })
                }
            </Fragment>
        )
    }

    getTemplateReport(reportData) {
        const {t} = this.props;
        let sessionIsOpen = !reportData.status;
        let title = sessionIsOpen ? t('X - REPORT') : t('Z - REPORT');
        let titleTheoretical = sessionIsOpen ? t('Theoretical Cash Amount') : t('Theoretical Closing Balance');
        const {
            StylePrintComponent,
        } = PrinterService.getStyleForPrint();
        return (
            <Fragment>
                {StylePrintComponent}
                <div className="block-printreceipt">
                    <div className="title">{title}</div>
                    <p className="">Session ID: {reportData.shift_increment_id}</p>
                    <br/>
                    <table>
                        <tbody>
                            {
                                this.getTemplateRowInTable(
                                    t('Location'),
                                    ReceiptService.getDisplayLocationAddress(reportData.location_id),
                                    'Location',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('POS'),
                                    PosService.getCurrentPosName(),
                                    'POS',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Staff'),
                                    reportData.staff_name,
                                    'Staff',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Opened'),
                                    moment(DateTimeHelper.convertDatabaseDateTimeToLocalDate(reportData.opened_at))
                                        .format('L LT'),
                                    'Opended',
                                    false,
                                    false
                                )
                            }
                            {
                                sessionIsOpen ? null : this.getTemplateRowInTable(
                                    t('Closed'),
                                    moment(DateTimeHelper.convertDatabaseDateTimeToLocalDate(reportData.closed_at))
                                        .format('L LT'), 'Closed', false, false
                                )
                            }
                        </tbody>
                    </table>
                    <br/>
                    <table>
                        <tbody>
                            {
                                sessionIsOpen ? this.getTemplateRowOneColBold(t('NET AMOUNT'), 'title')
                                : this.getTemplateRowTwoColBold(t('NET AMOUNT'), t('CASH DIFFERENCE'), 'title')
                            }
                            {
                                sessionIsOpen ? this.getTemplateRowOneColBold(
                                    SessionService.getDisplayNetAmount(reportData),
                                    'amount')
                                : this.getTemplateRowTwoColBold(SessionService.getDisplayNetAmount(reportData),
                                    SessionService.getDisplayCashDifference(reportData),
                                    'amount')
                            }
                        </tbody>
                    </table>
                    <br/>
                    <table>
                        <tbody>
                            {
                                this.getTemplateRowInTable(
                                    '#' + t('Cash'),
                                    '',
                                    'Cash',
                                    true,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Opening Balance'),
                                    CurrencyHelper.format(reportData.opening_amount, reportData.shift_currency_code),
                                    'Opening Balance',
                                    false,
                                    false
                                )
                            }
                            {
                                sessionIsOpen ? null : this.getTemplateRowInTable(
                                    t('Closing Balance'),
                                    CurrencyHelper.format(reportData.closed_amount, reportData.shift_currency_code),
                                    'Closing Balance',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    titleTheoretical,
                                    SessionService.getDisplayTheoretical(reportData),
                                    titleTheoretical,
                                    false,
                                    false
                                )
                            }
                            {
                                sessionIsOpen ? null
                                : this.getTemplateRowInTable(
                                    t('Cash Difference'),
                                    SessionService.getDisplayCashDifference(reportData),
                                    'Cash Difference',
                                    false,
                                    false
                                    )
                            }
                        </tbody>
                    </table>
                    <br/>
                    <table>
                        <tbody>
                            {
                                this.getTemplateRowInTable(
                                    t('Cash Sales'),
                                    SessionService.getDisplayCashSales(reportData),
                                    'Cash Sales',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Cash Refund'),
                                    SessionService.getDisplayCashRefund(reportData),
                                    'Cash Refund',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Pay Ins'),
                                    SessionService.getDisplayPayIns(reportData),
                                    'Pay Ins',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Payouts'),
                                    SessionService.getDisplayPayOuts(reportData),
                                    'Payouts',
                                    false,
                                    false
                                )
                            }
                        </tbody>
                    </table>
                    <br/>
                    <table>
                        <tbody>
                            {
                                this.getTemplateRowInTable(
                                    '#' + t('Payment Method'),
                                    '',
                                    'Payment Method',
                                    true,
                                    false
                                )
                            }
                            {this.getTemplatePaymentsReport(reportData)}
                        </tbody>
                    </table>
                    <br/>
                    <table>
                        <tbody>
                            {
                                this.getTemplateRowInTable(
                                    '#' + t('Sales'),
                                    '', 'Sales',
                                    true,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Total Amount'),
                                    SessionService.getDisplayTotalAmount(reportData),
                                    'Total Amount',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Refund Amount'),
                                    SessionService.getDisplayTotalRefund(reportData),
                                    'Refund Amount',
                                    false,
                                    false
                                )
                            }
                            {
                                this.getTemplateRowInTable(
                                    t('Net Amount'),
                                    SessionService.getDisplayNetAmount(reportData),
                                    'Net Amount',
                                    false,
                                    false
                                )
                            }
                        </tbody>
                    </table>
                    <br/>

                    {layout('print')('template_report')('sale_data_after')()(this, reportData)}

                    <div className="text-center">
                        <div className="reprint">{t('Printed At') + ' :' + DateTimeHelper.getDayOfWeekDateTime()}</div>
                    </div>
                </div>
            </Fragment>
        )
    }

    /**
     * Get template for list refund to
     * @param creditmemo
     * @param order
     * @return {*}
     */
    getTemplateListRefundTo(creditmemo, order) {
        let templates = [];
        let payments = creditmemo.params.creditmemo.payments;
        if (payments) {
            payments.forEach((payment, index) => {
                if (!payment.amount_paid) {
                    return null;
                }

                templates.push(this.getTemplateRefundTo(payment, index, order));
            });
        }
        if (creditmemo.gift_voucher_discount) {
            templates.push(this.getTemplateTotal(
                this.props.t('Refund to Gift Card'),
                "",
                "GiftcardDiscount",
                false,
                true
            ));
        }
        if (creditmemo.gift_voucher_gift_codes && creditmemo.gift_voucher_gift_codes_refund_amount) {
            templates.push(...this.getTemplateGiftcodes(
                creditmemo,
                'gift_voucher_gift_codes',
                'gift_voucher_gift_codes_refund_amount',
                creditmemo.order_currency_code,
                false,
                true
            ));
        }
        if (templates.length) {
            templates.unshift(
                <tr key={templates.length + 2}>
                    <td colSpan="4">
                        <hr/>
                    </td>
                </tr>
            )
        }
        return templates;
    }

    /**
     *
     * @param items
     * @param order
     * @param creditmemoItems
     * @returns {Array}
     */
    getTemplateItems(items, order, creditmemoItems) {
        let itemTemps = [];
        items.map(item => {
            if (!item.parent_item_id) {
                itemTemps.push(item);
            }
            return itemTemps;
        });
        return (
            itemTemps.map(item => {
                return this.getTemplateItem(item, items, order, creditmemoItems)
                    ;
            })
        )
    }

    /**
     *
     * @param item
     * @param items
     * @param order
     * @param creditmemoItems
     * @returns {*}
     */
    getTemplateItem(item, items, order, creditmemoItems) {
        let creditmemoItem = ReceiptService.getCreditmemoItem(item, creditmemoItems);
        let isCreditMemoItem = false;
        let childrens = OrderItemService.getChildrenItems(item, order);
        let itemId = item.item_id;
        if (creditmemoItem) {
            creditmemoItem.product_options = item.product_options;
            creditmemoItem.product_type = item.product_type;
            item = creditmemoItem;
            isCreditMemoItem = true;
        }

        if (item.product_type === ProductTypeConstant.BUNDLE) {
            // let childrens = order.all_items?order.all_items:order.items;
            if (OrderItemService.isChildrenCalculated(item, order)) {
                childrens = childrens.map(children => {
                    if (children.parent_item_id === itemId) {
                        let childCreditmemoItem = ReceiptService.getCreditmemoItem(children, creditmemoItems);
                        if (childCreditmemoItem && childCreditmemoItem.qty) {
                            isCreditMemoItem = true;
                            return childCreditmemoItem;
                        }
                        if (!isCreditMemoItem) {
                            return children;
                        }
                    }
                    return null;
                });
            }
            childrens = childrens.filter(children => (children != null));
            return this.getTemplateItemBundle(item, order, childrens, isCreditMemoItem);
        } else {
            return this.getTemplateDetailItem(item, false, order, isCreditMemoItem);
        }
    }

    /**
     * get template detail item
     * @param item
     * @param isChildren
     * @param order
     * @param isCreditMemoItem
     * @return {*}
     */
    getTemplateDetailItem(item, isChildren, order, isCreditMemoItem = false) {
        let itemId = item.item_id;
        if (isCreditMemoItem) {
            itemId = item.order_item_id;
        }
        let name = item.name;
        let classForName = (isChildren) ? "t-name t-bundle" : "t-name";
        let optionLabel = ReceiptService.getOptionLabelByItem(item);
        let customOptions = ReceiptService.getCustomOptionsByItem(item);
        let bundleOptions = ReceiptService.getBundleOptionsByItem(item, order, isCreditMemoItem);

        // get gift card value
        let giftcardOptions = ReceiptService.getGiftcardOptionsByItem(item, order);

        let reason = ReceiptService.getCustomPriceReason(item, order, isCreditMemoItem);
        // print creditmemo dont show original price
        let classOriginalPrice = (ReceiptService.showOriginPrice(item)) ? "" : "hidden";
        let displayOriginalPrice = ReceiptService.displayOriginalPrice(item, order);
        let price = ReceiptService.displayPrice(item, order, isCreditMemoItem);
        let rowTotal = ReceiptService.getRowTotal(item, order, isCreditMemoItem);
        let qty = ReceiptService.getQty(item, isCreditMemoItem);
        return (
            <Fragment key={itemId + "itemWrap"}>
                <Fragment key={itemId}>
                    <tr>
                        <td className={classForName}>{name}
                            <div className={(optionLabel) ? "t-bundle" : "hidden"}><i>{optionLabel}</i></div>
                            {this.getTemplateOptions(customOptions)}
                            {this.getTemplateOptions(bundleOptions)}
                            {this.getTemplateOptions(giftcardOptions)}
                            {this.getTemplateReason(reason)}
                        </td>
                        <td className="t-qty text-right">{NumberHelper.formatDisplayGroupAndDecimalSeparator(qty)}</td>
                        <td className="t-price text-right">{price}
                            <div className={classOriginalPrice}>({displayOriginalPrice})</div>
                        </td>
                        <td className="t-total text-right">{rowTotal}</td>
                    </tr>
                </Fragment>
                {this.getTemplateRefundedItem(item, order)}
            </Fragment>
        )
    }

    /**
     * Template item bundle
     * @param item
     * @param order
     * @param childrens
     * @param isCreditMemoItem
     * @returns {*}
     */
    getTemplateItemBundle(item, order, childrens, isCreditMemoItem = false) {
        let itemId = item.item_id;
        let name = item.name;
        let isChildrenCalculated = OrderItemService.isChildrenCalculated(item, order);
        if (isChildrenCalculated) {
            return (
                <Fragment key={itemId + "itemWrap"}>
                    <Fragment key={itemId}>
                        <tr>
                            <td className="t-name">{name}</td>
                            <td className="t-qty text-right"/>
                            <td className="t-price text-right"/>
                            <td className="t-total text-right"/>
                        </tr>
                    </Fragment>
                    {childrens.map(children => {
                        return this.getTemplateDetailItem(children, true, order, isCreditMemoItem)
                    })}
                </Fragment>
            );
        } else {
            return this.getTemplateDetailItem(item, false, order, isCreditMemoItem);
        }

    }

    /**
     *
     * @param item
     * @param order
     * @return {*}
     */
    getTemplateRefundedItem(item, order) {
        let itemId = item.item_id;
        let qtyRefunded = item.qty_refunded;
        let price = ReceiptService.displayPrice(item, order);
        let amountRefunded = ReceiptService.displayRefunded(item, order);
        // print creditmemo dont show refund item
        if (qtyRefunded && !this.props.creditmemo) {
            return (
                <Fragment key={itemId + "-refund"}>
                    <tr>
                        <td className="t-name t-refund-label">{this.props.t('REFUNDED')}</td>
                        <td className="t-qty text-right">
                            {NumberHelper.formatDisplayGroupAndDecimalSeparator(qtyRefunded)}
                        </td>
                        <td className="t-price text-right">{price}</td>
                        <td className="t-total text-right">{amountRefunded}</td>
                    </tr>
                </Fragment>
            )
        } else {
            return null;
        }
    }

    /**
     * get template custom options
     * @param customOptions
     */
    getTemplateOptions(customOptions) {
        return customOptions.map(customOption => {
            return <div className="t-bundle" key={customOption.toString()}>
                <i>{customOption}</i>
            </div>;
        });
    }

    getTemplateReason(reason) {
        return <div className="t-bundle" key={reason.toString()}>
            <i>{reason}</i>
        </div>;
    }

    /**
     * Get Template Payment area in report
     * @param reportData
     */
    getTemplatePaymentsReport(reportData) {
        let infoPayments = SessionService.getInfoPaymentMethod(reportData);
        return (
            infoPayments.map((infoPayment) => {
                return this.getTemplateRowInTable(
                    infoPayment.title,
                    CurrencyHelper.format(infoPayment.amount_paid, reportData.shift_currency_code),
                    infoPayment.method,
                    false,
                    false
                );
            })
        )
    }

    /**
     *  get template payments from payments data order
     *
     * @return template payments
     */
    getTemplatePayments(order) {
        let payments = order.payments;
        if (payments) {
            return (
                payments.map((payment, index) => {
                    if (!payment.amount_paid) {
                        return null;
                    }

                    if (payment.type !== PaymentConstant.TYPE_REFUND && payment.amount_paid > 0) {
                        return this.getTemplatePayment(payment, index, order);
                    }

                    return null;
                })
            )
        }
    }

    /**
     * get template payment from payment data
     * @param payment
     * @param index
     * @param order
     * @param titlePrefix
     * @returns {*}
     */
    getTemplatePayment(payment, index, order, titlePrefix = '') {
        let paymentAmount = CurrencyHelper.format(payment.amount_paid, order.order_currency_code);
        let title = `${titlePrefix}${payment.title}`;
        let referenceNumber = payment.reference_number;
        if (PaymentHelper.isPaypalDirect(payment.method) && !referenceNumber) {
            title = PaymentHelper.paypalPayViaEmailTitle();
        }

        if (referenceNumber) {
            title = `${title} (${referenceNumber}`;
            if (payment.card_type) {
                title += ` - ${payment.card_type.toUpperCase()}`;
            }
            title += ')';
        } else if (payment.card_type) {
            title = `${title} (${payment.card_type})`;
        }

        return this.getTemplateTotal(
            title,
            paymentAmount,
            index.toString(), false, true
        );
    }

    /**
     * get template refund to from payment data
     * @param payment
     * @param index
     * @param order
     * @return {*}
     */
    getTemplateRefundTo(payment, index, order) {
        return this.getTemplatePayment(payment, index, order, 'Refund to ');
    }

    /**
     *  get template payment area  from data order
     *
     * @return template total due
     */
    getTemplatePaymentArea(order) {
        let grandTotal = order.grand_total;
        if (grandTotal) {
            return (
                <Fragment>
                    <tr>
                        <td colSpan="4">
                            <hr/>
                        </td>
                    </tr>
                    {this.getTemplateTotalPaid(order)}
                    {this.getTemplatePayments(order)}
                    {this.getTemplateTotalDue(order)}
                    {this.getTemplateTotalChage(order)}
                    {this.getTemplateTotalRefunded(order)}
                </Fragment>
            )
        }
    }

    /**
     *  get template total due from data order
     *
     * @return template total due
     */
    getTemplateTotalPaid(orderData) {
        let totalPaid =
            CurrencyHelper.roundToFloat(orderData.total_paid, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
        if (totalPaid) {
            let totalPaidDisplay = CurrencyHelper.format(totalPaid, orderData.order_currency_code);
            return this.getTemplateTotal(this.props.t('Paid'), totalPaidDisplay, 'Paid', true);
        }
    }

    /**
     * get template total due from data order
     *
     * @param orderData
     * @return {template}
     */
    getTemplateTotalDue(orderData) {
        let totalDue = CurrencyHelper.roundToFloat(orderData.total_due, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
        if (totalDue) {
            let totalDueDisplay = CurrencyHelper.format(totalDue, orderData.order_currency_code);
            return this.getTemplateTotal(this.props.t('Due'), totalDueDisplay, 'Due', true);
        }
    }

    /**
     *  get template total change from data order
     *
     * @param orderData
     * @return {template}
     */
    getTemplateTotalChage(orderData) {
        let totalChange =
            CurrencyHelper.roundToFloat(orderData.pos_change, CurrencyHelper.DEFAULT_DISPLAY_PRECISION);
        if (totalChange > 0) {
            let totalChangeDisplay = CurrencyHelper.format(totalChange, orderData.order_currency_code);
            return this.getTemplateTotal(this.props.t('Change'), totalChangeDisplay, "Change", true);
        }
    }

    /**
     * get template total refunded
     * @param orderData
     */
    getTemplateTotalRefunded(orderData) {
        let totalRefunded = CurrencyHelper.roundToFloat(
            orderData.total_refunded,
            CurrencyHelper.DEFAULT_DISPLAY_PRECISION
        );
        if (totalRefunded) {
            let totalRefundedDisplay = CurrencyHelper.format(totalRefunded, orderData.order_currency_code);
            return this.getTemplateTotal(
                this.props.t('Total Refunded'),
                totalRefundedDisplay,
                "Total Refunded",
                true
            );
        }
    }

    /**
     * Get template for plugin
     * @param orderData
     * @param creditmemo
     * @param addTableTag
     * @returns {*}
     */
    getTemplatePluginArea(orderData, creditmemo, addTableTag = false) {
        if (orderData.customer_is_guest) {
            return null;
        }
        let listTemplate = [];
        let eventData = {
            component: this,
            orderData: orderData,
            creditmemo: creditmemo,
            listTemplate: listTemplate
        };
        fire('view_print_get_template_plugin_area_before', eventData);
        listTemplate = eventData.listTemplate;

        const isEnableStoreCredit = ConfigHelper.isEnableStoreCredit();
        let templateCreditBalance = isEnableStoreCredit ? this.getTemplateCreditBalance(orderData) : null;
        listTemplate.push(templateCreditBalance);

        listTemplate = listTemplate.filter(template => template && template.template);
        listTemplate.sort((a, b) => a.sortOrder - b.sortOrder);

        if (listTemplate && listTemplate.length) {
            let Hr = addTableTag ? () => (
                <table>
                    <tbody>
                    <tr>
                        <td colSpan="4">
                            <hr/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            ) : () => (
                <tr>
                    <td colSpan="4">
                        <hr/>
                    </td>
                </tr>
            );
            return (
                <Fragment>
                    {
                        addTableTag ?
                            <Fragment>
                                <Hr/>
                                <table>
                                    <tbody>
                                    {listTemplate.map(template => template.template)}
                                    </tbody>
                                </table>
                                <Hr/>
                            </Fragment> :
                            <Fragment>
                                <Hr/>
                                {listTemplate.map(template => template.template)}
                            </Fragment>
                    }
                </Fragment>
            );
        }
        return null;
    }

    /**
     * Get template credit balance
     * @returns {*}
     */
    getTemplateCreditBalance(order) {
        const {quote, orderData, t, customer} = this.props;
        let template = {
            template: null,
            sortOrder: 20
        };
        let currentCreditBalance = null;
        if (customer && customer.credit_balance) {
            currentCreditBalance = customer.credit_balance;
        } else {
            if (quote) {
                let customerCreditBalance = (quote.customer && quote.customer.credit_balance)
                    ? quote.customer.credit_balance
                    : 0;
                let payment = orderData.payments.find(payment =>
                    payment.method === PaymentConstant.STORE_CREDIT
                );
                if (payment) {
                    let useCredit = payment.amount_paid;
                    currentCreditBalance = customerCreditBalance - useCredit;
                } else {
                    currentCreditBalance = customerCreditBalance;
                }
            }
        }
        if (currentCreditBalance !== null) {
            let creditBalanceDisplay = CurrencyHelper.format(currentCreditBalance, order.order_currency_code);
            template.template = this.getTemplateTotal(
                t('Credit Balance'),
                creditBalanceDisplay,
                "Credit Balance",
                true
            );
        }
        return template;
    }

    /**
     * get template subtotal from data order
     *
     * @param orderData
     * @return {template}
     */
    getTemplateSubtotal(orderData) {
        let subtotal = orderData.subtotal;
        if (TaxHelper.orderDisplaySubtotalIncludeTax()) {
            subtotal = orderData.subtotal_incl_tax;
        }
        let subtotalDisplay = CurrencyHelper.format(subtotal, orderData.order_currency_code);
        return this.getTemplateTotal(this.props.t('Subtotal'), subtotalDisplay, "Subtotal");
    }

    /**
     *  get template discount from data order
     *
     * @param orderData
     * @return {template}
     */
    getTemplateDiscount(orderData) {
        let pluginDiscount = 0;
        let eventData = {
            orderData: orderData,
            pluginDiscount: pluginDiscount
        };
        fire('view_print_get_template_discount_before', eventData);
        pluginDiscount = eventData.pluginDiscount;

        let discount = Math.abs(orderData.discount_amount) - Math.abs(pluginDiscount) -
            Math.abs(orderData.gift_voucher_discount ? orderData.gift_voucher_discount : 0);
        if (discount) {
            let discountDisplay = CurrencyHelper.format(discount, orderData.order_currency_code);
            return this.getTemplateTotal(
                OrderHelper.getDiscountDisplay(
                    orderData,
                    false
                ),
                `-${discountDisplay}`, "Discount"
            );
        }
    }

    /**
     * get template gift card discount from data order
     *
     * @return {*}
     */
    getTemplateGiftcardDiscount() {
        return this.getTemplateTotal(
            this.props.t('Gift Card'),
            "",
            "GiftcardDiscount",
            false
        );
    }

    /**
     *  get template gift codes from data object
     *
     * @param objectData
     * @param codeKey
     * @param amountKey
     * @param currency
     * @param isNegative
     * @param paddingParrent
     * @return template giftcodes
     */
    getTemplateGiftcodes(objectData, codeKey, amountKey, currency = null, isNegative = true, paddingParrent = false) {
        let giftCodes = objectData[codeKey].split(',');
        let giftValues = objectData[amountKey].split(',');

        let templateTotals = [];

        giftCodes.forEach((code, index) => {
            if (Math.abs(giftValues[index])) {
                let amount = CurrencyHelper.format(isNegative ? -giftValues[index]
                    : giftValues[index], currency);
                templateTotals.push(
                    this.getTemplateTotal(
                        GiftcardHelper.getHiddenCode(code),
                        `${amount}`,
                        'giftcode-' + code,
                        false,
                        true,
                        paddingParrent
                    )
                )
            }
        });

        return templateTotals;
    }

    /**
     * get template shipping from data order
     *
     * @param orderData
     * @return {template}
     */
    getTemplateShipping(orderData) {
        let shipping = orderData.shipping_amount;
        if (orderData.shipping_method !== ShippingConstant.STORE_PICKUP_SHIPPING_METHOD_CODE) {
            let shippingDisplay = CurrencyHelper.format(shipping, orderData.order_currency_code);
            return this.getTemplateTotal(this.props.t('Shipping'), shippingDisplay, "Shipping");
        }

    }

    /**
     * get FPT from data order
     *
     * @param orderData
     * @return {*}
     */
    getTemplateFPT(orderData) {
        let weeeTotal = OrderWeeeDataService.getTotalAmounts(orderData.items, orderData);
        if (weeeTotal) {
            let weeeTotalDisplay = CurrencyHelper.format(weeeTotal, orderData.order_currency_code);
            return this.getTemplateTotal(this.props.t('FPT'), weeeTotalDisplay, "FPT");
        }
    }

    /**
     * get template tax from data order
     *
     * @param orderData
     * @return {*}
     */
    getTemplateTax(orderData) {
        let tax = orderData.tax_amount;
        if (tax) {
            let taxDisplay = CurrencyHelper.format(tax, orderData.order_currency_code);
            return this.getTemplateTotal(this.props.t('Tax'), taxDisplay, "Tax");
        }
    }

    /**
     * get template grand total from data order
     *
     * @param orderData
     * @return {*}
     */
    getTemplateGrandTotal(orderData) {
        let grandTotalDisplay = CurrencyHelper.format(orderData.grand_total, orderData.order_currency_code);
        return this.getTemplateTotal(this.props.t('Grand Total'), grandTotalDisplay, "GrandTotal", true);
    }

    /**
     * get template total
     *
     * @param title
     * @param display
     * @param key
     * @param isBold
     * @param hasPadding
     * @param paddingParent
     * @return {*}
     */
    getTemplateTotal(title, display, key, isBold, hasPadding = false, paddingParent = false) {
        let classParent = paddingParent ? "t-name t-bundle" : "";
        let className = hasPadding ? "t-name t-bundle" : "t-name";
        title = isBold ? <b>{title}</b> : title;
        display = isBold ? <b>{display}</b> : display;
        return (
            <Fragment key={key}>
                <tr>
                    <td className={classParent} colSpan="3">
                        <div className={className}>{title}</div>
                    </td>
                    <td className="text-right">{display}</td>
                </tr>
            </Fragment>
        );
    }

    /**
     *
     * @param title
     * @param display
     * @param key
     * @param isBold
     * @param hasPadding
     */
    getTemplateRowInTable(title, display, key, isBold, hasPadding) {
        let className = hasPadding ? "t-name t-bundle" : "t-name";
        title = isBold ? <b>{title}</b> : title;
        display = isBold ? <b>{display}</b> : display;
        return display ? (
            <Fragment key={key}>
                <tr>
                    <td>
                        <div className={className}>{title}</div>
                    </td>
                    <td className="text-right">{display}</td>
                </tr>
            </Fragment>
        ) : null;
    }

    getTemplateRowTwoColBold(title, display, key) {
        return (
            <Fragment key={key}>
                <tr>
                    <td>
                        <div className="text-center"><b>{title}</b></div>
                    </td>
                    <td className="text-center">
                        <div className="text-center"><b>{display}</b></div>
                    </td>
                </tr>
            </Fragment>
        );
    }

    getTemplateRowOneColBold(display, key) {
        return (
            <Fragment key={key}>
                <tr>
                    <td className="text-center">
                        <div className="text-center"><b>{display}</b></div>
                    </td>
                </tr>
            </Fragment>
        );
    }

    /**
     *  component render DOM expression
     *  @return string
     *
     * */
    template() {
        return (
            <div className="">
                {this.getTemplate()}
            </div>
        );
    }
}

ToPrintComponent.propTypes = {
    orderData: PropTypes.object,
    reportData: PropTypes.object,
    isReprint: PropTypes.bool,
    t: PropTypes.func,
    creditmemo: PropTypes.object,
    customer: PropTypes.object,
    quote: PropTypes.object
};

const WrapToPrintComponent = ComponentFactory.get(ToPrintComponent);
