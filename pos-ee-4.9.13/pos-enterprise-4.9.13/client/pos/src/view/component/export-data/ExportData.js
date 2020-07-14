import React from 'react';
import {Modal} from 'react-bootstrap'
import {bindActionCreators} from "redux";
import CoreComponent from '../../../framework/component/CoreComponent'
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../framework/container/CoreContainer";
import ExportDataPopupAction from "../../action/ExportDataPopupAction";
import ProductTypeConstant from "../../constant/ProductTypeConstant";
import WeeeDataService from "../../../service/weee/WeeeDataService";
import OrderService from "../../../service/sales/OrderService";
import OrderHelper from "../../../helper/OrderHelper"
import ConfigData from "../../../config/Config";
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
import moment from "moment/moment";
import NumberHelper from "../../../helper/NumberHelper";
import $ from 'jquery';
import {CustomDiscountService} from "../../../service/checkout/quote/CustomDiscountService";

class ExportData extends CoreComponent {
    static className = 'ExportData';

    /**
     * Constructor
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            popup: '',
            unsynced_order: null
        };
    }

    /**
     * This function after mapStateToProps then push more items to component
     *
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        if (nextProps.success &&
            nextProps.success.error_log &&
            nextProps.success.action_log &&
            nextProps.success.error_log.length === 0 &&
            nextProps.success.action_log.length === 0) {
            this.setState({
                popup: 'message_no_data'
            });
        } else if (nextProps.success !== this.props.success) {
            this.setState({
                popup: 'confirm_message',
                unsynced_order: nextProps.success
            });
        }
    }

    /**
     * Format time to "180412_083756"
     *
     * @param date
     * @returns {string}
     */
    formatDate(date) {
        let year = date.getFullYear().toString();
        let month = (date.getMonth() + 1).toString();
        let day = date.getDate().toString();
        let hour = date.getHours().toString();
        let minute = date.getMinutes().toString();
        let second = date.getSeconds().toString();
        return year.substring(year.length - 2) + ((month.length < 2) ? ('0' + month) : month) +
            ((day.length < 2) ? ('0' + day) : day) + '_' +
            ((hour.length < 2) ? ('0' + hour) : hour) + ((minute.length < 2) ? ('0' + minute) : minute) +
            ((second.length < 2) ? ('0' + second) : second);
    }

    /**
     * Convert string from special character to English format
     * Example : Vietnamese : Xin lỗi => Xin loi
     *
     * @param str
     * @returns {*}
     */
    convertToEnglish(str) {
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
        str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, 'A');
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
        str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, 'E');
        str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
        str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, 'I');
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
        str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, 'O');
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
        str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, 'U');
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
        str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, 'Y');
        str = str.replace(/đ/g, 'd');
        str = str.replace(/Đ/g, 'D');
        return str;
    }

    /**
     * Convert string to array so that fix overflow row (>43 character)
     *
     * @param string
     * @returns array[string]
     */
    formatString(string) {
        let arr = [];
        if (string.length < 43) {
            arr.push(string);
            return arr;
        } else {
            let arrayString = string.split(' ');
            let length = arrayString.length;
            let result = [''];
            for (let index = 0; index <= length; index++) {
                if (result[result.length - 1].length > 40) {
                    if (result[result.length - 1].split(' ').length === 1) {
                        result.push(arrayString[index]);
                        continue;
                    }
                    result[result.length - 1] = result[result.length - 1].split(' ');
                    result.push(result[result.length - 1].pop());
                    result[result.length - 2] = result[result.length - 2].join(' ');
                }
                if (arrayString[index]) {
                    if (index === 0) {
                        result[result.length - 1] += arrayString[index];
                    } else {
                        result[result.length - 1] += (' ' + arrayString[index]);
                    }
                }
            }
            return result;
        }
    }

    /**
     * Export pdf file
     */
    async printData() {
        this.props.actions.clickBackDrop();
        let {error_log, action_log} = this.state.unsynced_order;

        let error_increment_ids = [];
        let log_increment_ids = [];
        error_log.forEach(action => {
            error_increment_ids.push(action.params.order.increment_id);
        });
        action_log.forEach(action => {
            log_increment_ids.push(action.params.order.increment_id);
        });

        let orders_error = await OrderService.getOrderByIncrementIds(error_increment_ids);
        let orders_log = await OrderService.getOrderByIncrementIds(log_increment_ids);
        let content = [];
        let styles = {
            bold: {
                fontSize: 10,
                bold: true,
                lineHeight: 1.5
            },
            normal: {
                fontSize: 10,
                bold: false,
                lineHeight: 1.5
            },
            header: {
                fontSize: 12,
                bold: true,
                lineHeight: 1.5
            }
        };

        let columns = [
            {title: this.props.t('Items'), dataKey: "items"},
            {title: this.props.t('SKU'), dataKey: "sku"},
            {title: this.props.t('Price'), dataKey: "price"},
            {title: this.props.t('Qty'), dataKey: "qty"},
            {title: this.props.t('Tax'), dataKey: "tax"},
            {title: this.props.t('Subtotal'), dataKey: "subtotal"},
        ];
        // print content action log
        this.printContent(orders_error, content, columns, false);
        // print content error log
        this.printContent(orders_log, content, columns, true);

        let currentTime = new Date();
        let fileName = "Orders_" + this.formatDate(currentTime);

        pdfMake.vfs = pdfFonts.pdfMake.vfs;
        let docDefinition = {
            content: content,
            styles: styles
        };
        pdfMake.createPdf(docDefinition).download(fileName + '.pdf');
    }

    /**
     * print content
     * @param orders
     * @param doc
     * @param columns
     * @param isError
     */
    printContent(orders, content, columns, isError,) {
        for (let k = 0; k < orders.length; k++) {
            let locationName = ConfigData.location_name;
            locationName = this.convertToEnglish(locationName);
            let posName = ConfigData.pos_name;
            posName = this.convertToEnglish(posName);
            let orderData = orders[k];
            let posAccount = orderData.pos_staff_name;
            posAccount = this.convertToEnglish(posAccount);
            let rows = [];
            let items = orderData.items ? orderData.items : [];
            items.forEach(
                (item) => {
                    let dataItem = {};
                    if (item.product_type === ProductTypeConstant.BUNDLE) {
                        dataItem = {
                            "items": item.name,
                            "sku": "",
                            "price": "",
                            "qty": "",
                            "tax": "",
                            "subtotal": ""
                        };
                        rows.push(dataItem);
                        let bundleItemChilds = items.filter(
                            (bundleItemChild) =>
                                Number(bundleItemChild.parent_item_id) === Number(item.item_id)
                        );
                        if (bundleItemChilds) {
                            bundleItemChilds.forEach(
                                (bundleItemChild) => {
                                    dataItem = {
                                        "items": "   " + bundleItemChild.name,
                                        "sku": bundleItemChild.sku,
                                        "price": OrderHelper.formatPrice(bundleItemChild.price, orderData),
                                        "qty": NumberHelper.formatDisplayGroupAndDecimalSeparator(bundleItemChild.qty_ordered),
                                        "tax": OrderHelper.formatPrice(bundleItemChild.tax_amount, orderData),
                                        "subtotal": OrderHelper.formatPrice(bundleItemChild.row_total_incl_tax, orderData),
                                    };
                                    rows.push(dataItem);
                                }
                            );
                        }
                    } else if (item.product_type === ProductTypeConstant.CONFIGURABLE) {
                        let orderLable = "";
                        if (item['product_options']) {
                            let strOptions = item['product_options'];
                            if (strOptions && !Array.isArray(strOptions)) {
                                let options = JSON.parse(strOptions);
                                let attributes_info = options.attributes_info;
                                if (attributes_info) {
                                    const options = attributes_info.map(attribute_info => {
                                        return `${attribute_info.value}`;
                                    });
                                    orderLable = options.join('/');
                                }
                            }
                        }
                        dataItem = {
                            "items": item.name + "\n   " + orderLable,
                            "sku": item.sku,
                            "price": OrderHelper.formatPrice(item.price, orderData),
                            "qty": NumberHelper.formatDisplayGroupAndDecimalSeparator(item.qty_ordered),
                            "tax": OrderHelper.formatPrice(item.tax_amount, orderData),
                            "subtotal": OrderHelper.formatPrice(item.row_total_incl_tax, orderData)
                        };
                        rows.push(dataItem);
                    } else if (item.parent_item_id) {
                    } else {
                        dataItem = {
                            "items": item.name,
                            "sku": item.sku,
                            "price": OrderHelper.formatPrice(item.price, orderData),
                            "qty": NumberHelper.formatDisplayGroupAndDecimalSeparator(item.qty_ordered),
                            "tax": OrderHelper.formatPrice(item.tax_amount, orderData),
                            "subtotal": OrderHelper.formatPrice(item.row_total_incl_tax, orderData)
                        };
                        rows.push(dataItem);
                    }
                }
            );

            // Convert date to format: "Apr 11, 2018"
            let orderDate = moment(orderData.created_at).format('ll');
            let order = "Order #" + orderData.increment_id;

            // Customer info
            let customerName = this.props.t('Customer Name') + ": " + orderData.customer_firstname +
                (orderData.customer_middlename ? " " + orderData.customer_middlename : "") + " " + orderData.customer_lastname;
            customerName = this.convertToEnglish(customerName);
            let customerEmail = this.props.t('Email') + ": " + orderData.customer_email;
            let customerGroup = this.props.t('Customer Group') + ": " + ConfigData.config.customer_groups.find(
                child => Number(child.id) === Number(orderData.customer_group_id)
            ).code;

            // Billing Address
            let billingAddressData = orderData.addresses.find(child => child.address_type === 'billing');
            let customerPhone = (billingAddressData.telephone) ? (this.props.t('Phone') + ": " + billingAddressData.telephone) : "";
            let billingName = billingAddressData.firstname + " " + billingAddressData.lastname;
            billingName = this.convertToEnglish(billingName);
            let countryBilling = ConfigData.countries.find(
                child => child.id === billingAddressData.country_id
            ).name;
            let billingAddress = billingAddressData.street.join(" ") +
                (billingAddressData.city ? ", " + billingAddressData.city : "") +
                (billingAddressData.region ? ", " + billingAddressData.region : "") +
                (billingAddressData.postcode ? ", " + billingAddressData.postcode : "") +
                ", " + countryBilling;
            billingAddress = this.convertToEnglish(billingAddress);
            let billingPhone = billingAddressData.telephone ? billingAddressData.telephone : "";

            // Shipping Address
            let shippingAddressData = orderData.addresses.find(child => child.address_type === 'shipping');
            let shippingName = shippingAddressData.firstname + " " + shippingAddressData.lastname;
            shippingName = this.convertToEnglish(shippingName);
            let countryShipping = ConfigData.countries.find(
                child => child.id === shippingAddressData.country_id
            ).name;
            let shippingAddress = shippingAddressData.street.join(" ") +
                (shippingAddressData.city ? ", " + shippingAddressData.city : "") +
                (shippingAddressData.region ? ", " + shippingAddressData.region : "") +
                (shippingAddressData.postcode ? ", " + shippingAddressData.postcode : "") +
                ", " + countryShipping;
            shippingAddress = this.convertToEnglish(shippingAddress);
            let shippingPhone = shippingAddressData.telephone ? shippingAddressData.telephone : "";

            // Payment method and shipping method
            let paymentMethodData = orderData.payments ? orderData.payments : [];
            let paymentMethodNames_Values = [];
            for (let i = 0; i < paymentMethodData.length; i++) {
                paymentMethodNames_Values.push({
                    name: this.convertToEnglish(paymentMethodData[i].title),
                    value: OrderHelper.formatPrice(paymentMethodData[i].amount_paid, orderData),
                    refNumber: (paymentMethodData[i].reference_number) ?
                        this.props.t('( Ref number') + " : " + paymentMethodData[i].reference_number + ")" : ""
                });
            }
            let shippingMethodName = orderData.shipping_description;
            shippingMethodName = this.convertToEnglish(shippingMethodName);
            let shippingMethodValue = OrderHelper.formatPrice(orderData.shipping_amount, orderData);
            let deliveryDate = this.props.t('Delivery Date');
            let deliveryDateValue = orderData.pos_delivery_date;

            // Subtotal, discount, fpt, tax...
            let subtotalValue = OrderHelper.formatPrice(orderData.subtotal, orderData);
            let discountCode = orderData.coupon_code;
            let discountValue = "";
            if (parseFloat(orderData.discount_amount) < 0) {
                discountValue = "-" + OrderHelper.formatPrice(orderData.discount_amount * (-1), orderData);
            } else {
                discountValue = OrderHelper.formatPrice(orderData.discount_amount, orderData);
            }
            let shippingAmountValue = OrderHelper.formatPrice(orderData.shipping_amount, orderData);
            let fptValue = OrderHelper
                .formatPrice(WeeeDataService.getTotalAmounts(orderData.items, orderData), orderData);
            let taxValue = OrderHelper.formatPrice(orderData.tax_amount, orderData);

            // Total, paid, remain
            let totalValue = OrderHelper.formatPrice(orderData.grand_total, orderData);
            let paidValue = OrderHelper.formatPrice(orderData.total_paid, orderData);
            let remainValue = OrderHelper.formatPrice(orderData.total_due, orderData);

            // Add header
            let headerContent = [
                {
                    columns: [
                        {
                            columns: [
                                {
                                    stack: [
                                        {
                                            text: this.props.t("Location Name"),
                                            style: 'normal',
                                            bold: true
                                        },
                                        {
                                            text: this.props.t("POS"),
                                            style: 'normal',
                                            bold: true
                                        },
                                    ],
                                    width: 100
                                },
                                {
                                    stack: [
                                        {
                                            text: ": " + posName,
                                            style: 'normal'
                                        },
                                        {
                                            text: ": " + locationName,
                                            style: 'normal'
                                        }
                                    ],
                                    width: "*"
                                }
                            ],
                            width: "*",
                        },
                        {
                            columns: [
                                [
                                    {
                                        text: this.props.t("Order Date"),
                                        style: 'normal',
                                        bold: true
                                    },
                                    {
                                        text: this.props.t("Cashier"),
                                        style: 'normal',
                                        bold: true
                                    }
                                ], [
                                    {
                                        text: ": " + orderDate,
                                        style: 'normal',
                                        alignment: "right"
                                    },
                                    {
                                        text: ": " + posAccount,
                                        style: 'normal',
                                        alignment: "right"
                                    }
                                ]
                            ],
                            alignment: 'left',
                            width: 160

                        },
                    ],
                },
                {
                    text: order,
                    bold: true,
                    fontSize: 12,
                    alignment: "center",
                    margin: [0, 20]
                }
            ];
            if (content.length > 0) {
                headerContent[0].pageBreak = 'before';
            }
            content.push(headerContent);

            /* Customer information*/
            let customInfo = [
                [], []
            ];
            customInfo[0].push({
                text: this.props.t('Customer Information'),
                bold: true,
                style: 'normal'
            });
            customInfo[0].push({
                text: customerName,
                style: 'normal'
            });
            customInfo[0].push({
                text: customerGroup,
                style: 'normal'
            });
            let arrayCustomerEmail = [];
            while (customerEmail.length > 25) {
                arrayCustomerEmail.push(customerEmail.substring(0, 24));
                customerEmail = customerEmail.substring(25);
            }
            if (customerEmail !== "") {
                arrayCustomerEmail.push(customerEmail);
            }
            arrayCustomerEmail.forEach(
                (el) => {
                    customInfo[1].push({
                        text: el,
                        style: 'normal',
                        fontSize: 9
                    });
                }
            );
            if (customerPhone) {
                customInfo[1].push({
                    text: customerPhone,
                    style: 'normal'
                });
            }

            content.push({
                columns: [
                    {
                        stack: customInfo[0],
                        width: "*"
                    }, {
                        stack: customInfo[1],
                        width: 160
                    }
                ],
                style: 'normal'
            });

            content.push({
                layout: 'noBorders',
                style: 'normal',
                margin: [0, 20],
                table: {
                    headerRows: 1,
                    widths: ['*', 'auto', 'auto', 'auto', 'auto', 'auto'],
                    body: this.getDataTable(columns, rows),
                }
            });

            // Add Footer

            /* Address Infomation*/
            let billingData = [];
            billingData.push({text: this.props.t('Billing Address'), style: 'bold'});
            billingData.push({text: billingName});
            this.formatString(billingAddress).forEach(
                (el) => {
                    billingData.push({text: el})
                }
            );
            billingData.push({text: billingPhone});

            let shippingData = [];
            shippingData.push({text: this.props.t('Shipping Address'), style: 'bold'});
            shippingData.push({text: shippingName});
            this.formatString(shippingAddress).forEach(
                (el) => {
                    shippingData.push({text: el});
                }
            );
            shippingData.push({text: shippingPhone});
            let addressInfomation = {
                columns: [
                    billingData,
                    shippingData
                ],
                style: 'normal'
            };
            content.push(addressInfomation);
            /*Payment method*/
            let paymentData = [];
            paymentData.push({text: this.props.t('Payment Method'), style: 'bold'});
            paymentMethodNames_Values.forEach(
                (el) => {
                    paymentData.push({
                        columns: [
                            {text: el.name},
                            {text: el.value, width: 100}
                        ]
                    });
                    if (el.refNumber !== "") {
                        paymentData.push({text: el.refNumber});
                    }

                }
            );
            /*Shipping method*/
            let shippingMethodData = [];
            shippingMethodData.push({text: this.props.t('Shipping Method'), style: 'bold'});
            let shippingMethodLeft = {
                stack: []
            };
            let shippingMethodRight = {
                stack: []
            };
            shippingMethodLeft.stack.push({
                text: shippingMethodName
            });
            shippingMethodRight.stack.push({
                text: shippingMethodValue
            });
            if((orderData.pos_delivery_date !== undefined) && (orderData.pos_delivery_date !== null)){
                shippingMethodLeft.stack.push({
                    text: deliveryDate
                });
                shippingMethodRight.stack.push({
                    text: deliveryDateValue
                });
            }
            shippingMethodData.push({
                columns: [
                    shippingMethodLeft,
                    shippingMethodRight
                ]
            });
            content.push({
                columns: [
                    paymentData,
                    shippingMethodData
                ],
                style: 'normal',
                margin: [0, 20, 0, 0]
            });

            /*Add line*/
            content.push(
                {
                    table: {
                        widths: ['*'],
                        body: [[" "], [" "]]
                    },
                    layout: {
                        hLineWidth: function (i, node) {
                            return (i === 0 || i === node.table.body.length) ? 0 : 0.5;
                        },
                        vLineWidth: function (i, node) {
                            return 0;
                        },
                    }
                },
            );
            /*Total*/
            let totalTitles = [];
            let totalValues = [];
            totalTitles.push({text: this.props.t('Subtotal')});
            let discountTitle = this.props.t('Discount');
            if (orderData.os_pos_custom_discount_amount) {
                discountTitle = this.props.t('Custom discount');
                if (orderData.os_pos_custom_discount_type === CustomDiscountService.DISCOUNT_TYPE_PERCENT ) {
                    discountTitle = discountTitle + ' (' + orderData.os_pos_custom_discount_amount + '%)';
                }
            } else {
                if (discountCode) {
                    discountTitle = discountTitle + " (" + discountCode + ")";
                }
            }
            totalTitles.push(discountTitle);
            totalTitles.push({text: this.props.t('Shipping Amount')});
            totalTitles.push({text: this.props.t('FPT')});
            totalTitles.push({text: this.props.t('Tax')});

            totalValues.push({text: subtotalValue});
            totalValues.push({text: discountValue});
            totalValues.push({text: shippingAmountValue});
            totalValues.push({text: fptValue});
            totalValues.push({text: taxValue});
            content.push({
                columns: [
                    {text: ''},
                    [
                        {
                            columns: [
                                totalTitles,
                                totalValues
                            ]
                        },
                    ]],
                style: 'normal'
            });
            /*Add line*/
            content.push(
                {
                    table: {
                        widths: ['*'],
                        body: [[" "], [" "]]
                    },
                    layout: {
                        hLineWidth: function (i, node) {
                            return (i === 0 || i === node.table.body.length) ? 0 : 0.5;
                        },
                        vLineWidth: function (i, node) {
                            return 0;
                        },
                    }
                },
            );
            /*Grand Totals*/
            let grandTotalTitles = [];
            let grandTotalValues = [];
            grandTotalTitles.push({text: this.props.t('Total')});
            grandTotalTitles.push({text: this.props.t('Paid'), italic: true});
            grandTotalTitles.push({text: this.props.t('Remain'), italic: true});
            grandTotalValues.push({text: totalValue});
            grandTotalValues.push({text: paidValue});
            grandTotalValues.push({text: remainValue});
            content.push({
                columns: [
                    {text: ''},
                    [
                        {
                            columns: [
                                {
                                    stack: grandTotalTitles,
                                },
                                {
                                    stack: grandTotalValues
                                }
                            ]
                        },
                    ]],
                style: 'normal'
            });
        }
    }

    getDataTable(titles, rows) {
        let tableRows = [];

        /*Add title table*/
        if (titles.length > 0) {
            let dataRow = [];
            for (let item of titles) {
                let value = {
                    text: item.title ? item.title : "",
                    bold: true,
                    fontSize: 12
                };
                dataRow.push(value);
            }
            tableRows.push(dataRow);
        }

        /*Add Body table*/
        if (rows.length > 0) {
            for (let item of rows) {
                var dataRow = $.map(item, function (value, index) {
                    if (value.charAt(0) === ' ') {
                        value = {
                            text: value,
                            margin: [15, 0, 0, 0]
                        }
                    }
                    return [value];
                });
                tableRows.push(dataRow);
            }
        }
        return tableRows;
    }

    template() {
        return (
            <div>
                <Modal
                    bsSize={"small"}
                    className={"popup-messages"}
                    show={(this.props.isOpen && this.state.popup === 'confirm_message')}
                    onHide={() => this.props.actions.clickBackDrop()}
                >
                    <Modal.Body>
                        <h3 className="title">{this.props.t('Export')}</h3>
                        <p> {this.props.t('Do you want to export unsynced orders to PDF files')}?</p>
                    </Modal.Body>
                    <Modal.Footer className={"logout-actions"}>
                        <a onClick={() => this.props.actions.clickBackDrop()}> {this.props.t('No')} </a>
                        <a onClick={() => this.printData()}> {this.props.t('Yes')} </a>
                    </Modal.Footer>
                </Modal>
                <Modal
                    bsSize={"small"}
                    className={"popup-messages"}
                    show={(this.props.isOpen && this.state.popup === 'message_no_data')}
                    onHide={() => this.props.actions.clickBackDrop()}
                >
                    <Modal.Body>
                        <h3 className="title">{this.props.t('Export')}</h3>
                        <p> {this.props.t('POS has no unsynced order to export')}</p>
                    </Modal.Body>
                    <Modal.Footer className={"close-modal"}>
                        <button onClick={() => this.props.actions.clickBackDrop()}>OK</button>
                    </Modal.Footer>
                </Modal>
            </div>
        );
    }
}

class ExportDataContainer extends CoreContainer {
    static className = 'ExportDataContainer';

    /**
     * This maps the state to the property of the component
     *
     * @param state
     * @returns {{isOpen: *, success: *, error: *}}
     */
    static mapState(state) {
        const {isOpen, success, error} = state.core.exportData;
        return {
            isOpen,
            success,
            error
        }
    }

    /**
     * This maps the dispatch to the property of the component
     *
     * @param dispatch
     * @returns {{actions: ({clickBackDrop, finishExportDataRequesting}|ActionCreator<any>|ActionCreatorsMapObject)}}
     */
    static mapDispatch(dispatch) {
        return {
            actions: bindActionCreators({...ExportDataPopupAction}, dispatch)
        }
    }
}

export default ContainerFactory.get(ExportDataContainer).withRouter(
    ComponentFactory.get(ExportData)
)
