import PaymentConstant from "../../../../../view/constant/PaymentConstant";
import StripeTerminalConstant from "../../../view/constant/payment/StripeTerminalConstant";

import CheckoutService from "../../../../../service/checkout/CheckoutService";
import ServiceFactory from "../../../../../framework/factory/ServiceFactory";

import ConfigHelper from "../../../../../helper/ConfigHelper";
import CurrencyHelper from "../../../../../helper/CurrencyHelper";
import LocalStorageHelper from "../../../../../helper/LocalStorageHelper";
import NumberHelper from "../../../../../helper/NumberHelper";
import PaymentHelper from "../../../../../helper/PaymentHelper";

import {PaymentAbstract} from "../../../../../service/payment/type/PaymentAbstract";
import PurchasePopupService from "./stripeterminal/StripeTerminalPurchasePopupService";
import RefundPopupService from "./stripeterminal/StripeTerminalRefundPopupService";
import StripeTerminal from "./stripeterminal/sdk";
import ConfigService from "../../../../../service/config/ConfigService";
import PosService from "../../../../../service/PosService";
import {listen} from "../../../../../event-bus";
import Singleton from "../../../../../resource-model/Singleton";

export class StripeTerminalService extends PaymentAbstract {
    static className = 'StripeTerminalService';
    static terminal = false;
    code = StripeTerminalConstant.CODE;
    omc;
    api_url;
    api_namespace;
    pendingPaymentIntentSecret;
    pendingPaymentIntent;
    canceled = false;

    constructor() {
        super();
        this.omc = Singleton.getOnline('Payment');
        this.api_namespace = StripeTerminalConstant.END_POINT_PATH;
        this.api_url = this.omc.getBaseUrl() + this.api_namespace;
        this.initialize();
    }

    /**
     *  init event , data
     */
    initialize() {
        this.initializeBackendClientAndTerminal();

        let localConfig = ConfigService.getConfigFromLocalStorage();
        if (typeof (localConfig) === 'string') {
            localConfig = JSON.parse(localConfig);
        }

        let connectedReaderFromConfig = this.getConnectedReaderFromConfig(localConfig);

        if (connectedReaderFromConfig) {
            this.setConnectedReader(connectedReaderFromConfig);
        }

        listen('epic_config_get_config_after', ({config}) => {
            this.setConnectedReader(this.getConnectedReaderFromConfig(config));
        })
    }

    /**
     *
     * @param popup
     * @param resolve
     * @param errorMessage
     * @return {*}
     */
    closePopupWithError(popup, resolve, errorMessage) {
        this.closePopup(popup);
        return resolve({
            error: true,
            errorMessage
        })
    }

    /**
     *
     * @param popup
     * @return {PaymentAbstract}
     */
    closePopup(popup) {
        this.pendingPaymentIntentSecret = false;
        this.pendingPaymentIntent = false;
        this.canceled = false;

        if (popup) {
            popup.closePopup();
            return this.clear();
        }

        if (RefundPopupService.isOpen()) {
            RefundPopupService.closePopup();
        }

        if (PurchasePopupService.isOpen()) {
            PurchasePopupService.closePopup();
        }

        return this.clear();
    }

    /**
     *
     * @return {Promise<*>}
     */
    async execute() {
        if (this.getCreditmemo()) {
            return new Promise((resolve, reject) => {
                return this.sendRefundRequest(resolve)
            })
        }

        return new Promise((resolve, reject) => {
            return this.sendSaleRequest(resolve, reject)
        })
    }

    /**
     *
     * @param paymentIntent
     * @param resolve
     * @param reject
     * @return {*}
     */
    pollCallback = async (paymentIntent, resolve, reject) => {
        if (this.canceled) {
            return false;
        }

        if (paymentIntent.error) {
            this.closePopup();
            return resolve({
                error: true,
                errorMessage: paymentIntent.error.message,
            });
        }

        const {status} = paymentIntent;

        if (!status) {
            this.closePopup();
            return resolve({
                error: true,
                errorMessage: StripeTerminalConstant.INVALID_PAYMENT_INTENT_EXCEPTION_MESSAGE,
            });
        }

        if (status === StripeTerminalConstant.INTENT_STATUS_SUCCEEDED) {
            const {card_present} = this.pendingPaymentIntent.charges.data[0].payment_method_details;
            const {brand, last4} = card_present;
            const response = {
                error: false,
                reference_number: paymentIntent.id,
                card_type: brand.toUpperCase(),
                cc_last4: `************${last4.toUpperCase()}`
            };

            resolve(response);
            return this.closePopup();
        }

        if (status === StripeTerminalConstant.INTENT_STATUS_REQUIRES_CAPTURE) {
            try {
                // Capture the PaymentIntent from your backend client and mark the payment as complete
                const response = await this.capturePaymentIntent({
                    paymentIntentId: paymentIntent.id
                });

                if (response.error) {
                    return this.closePopupWithError(
                        null,
                        resolve,
                        response.error.message
                    )
                }

                return this.fetchTransaction((fetchResponse) => this.pollCallback(fetchResponse, resolve, reject))
            } catch (e) {
                return this.pollCallback(
                    {
                        status: StripeTerminalConstant.ERROR_STATUS,
                        errorMessage: `${StripeTerminalConstant.CAPTURE_FAILED_EXCEPTION_MESSAGE}: ${e.message}`,
                    },
                    resolve,
                    reject
                );
            }
        }

        if (status === StripeTerminalConstant.INTENT_STATUS_REQUIRES_CONFIRMATION) {
            try {
                const response = await StripeTerminalService.terminal.processPayment(
                    paymentIntent
                );

                if (response.error) {
                    return this.closePopupWithError(
                        null,
                        resolve,
                        response.error.message
                    )
                }

                if (response.paymentIntent) {
                    this.pendingPaymentIntent = response.paymentIntent;
                }

                return this.fetchTransaction((fetchResponse) => this.pollCallback(fetchResponse, resolve, reject))
            } catch (e) {
                return this.pollCallback(
                    {
                        status: StripeTerminalConstant.ERROR_STATUS,
                        errorMessage: `${StripeTerminalConstant.CONFIRM_FAILED_EXCEPTION_MESSAGE}: ${e.message}`,
                    },
                    resolve,
                    reject
                );
            }
        }

        if (status === StripeTerminalConstant.INTENT_STATUS_REQUIRES_PAYMENT_METHOD) {

            try {
                const response = await StripeTerminalService.terminal.collectPaymentMethod(
                    this.pendingPaymentIntentSecret
                );

                if (response.error) {
                    return this.closePopupWithError(
                        null,
                        resolve,
                        response.error.message
                    )
                }

                return this.fetchTransaction((fetchResponse) => this.pollCallback(fetchResponse, resolve, reject))
            } catch (e) {
                return this.pollCallback(
                    {
                        status: StripeTerminalConstant.ERROR_STATUS,
                        errorMessage: `${StripeTerminalConstant.COLLECT_FAILED_EXCEPTION_MESSAGE}: ${e.message}`,
                    },
                    resolve,
                    reject
                );
            }
        }

        if (status === StripeTerminalConstant.INTENT_STATUS_CANCELED) {
            resolve({
                error: true,
                errorMessage: StripeTerminalConstant.MESSAGE_TRANSACTION_STATUS_CANCELLED,
            });
            return this.closePopup();
        }

        const upperCaseStatus = status.toUpperCase();

        if (upperCaseStatus === PaymentConstant.LOST_INTERNET_STATUS) {
            resolve({
                error: true,
                errorMessage: PaymentConstant.LOST_INTERNET_CONNECTION_MESSAGE,
            });
            return this.closePopup();
        }

        if (upperCaseStatus === PaymentConstant.TIMEOUT_STATUS) {
            this.closePopup();
            return resolve({
                error: true,
                errorMessage: StripeTerminalConstant.MESSAGE_TRANSACTION_STATUS_EXPIRED,
            });
        }

        if (upperCaseStatus === StripeTerminalConstant.ERROR_STATUS) {
            this.closePopup();
            return resolve({
                error: true,
                errorMessage: paymentIntent.errorMessage || StripeTerminalConstant.CONNECTION_ERROR_MESSAGE,
            });
        }

        return this.fetchTransaction((fetchResponse) => this.pollCallback(fetchResponse, resolve, reject));

    };

    /**
     *
     * @param callback
     * @return {Promise<*>}
     */
    async fetchTransaction(callback) {
        const tryDoAgain = () => {
            const timeoutHandler = setTimeout(() => {
                clearTimeout(timeoutHandler);
                return this.fetchTransaction(callback);
            }, 1000);
        };

        try {
            // We want to reuse the same PaymentIntent object in the case of declined charges, so we
            // store the pending PaymentIntent's secret until the payment is complete.
            // Read a card from the customer
            const collectResult = await StripeTerminalService.terminal.fetchPaymentIntent(
                this.pendingPaymentIntentSecret
            ).internalPromise;

            let response = {
                ...collectResult
            };

            if (collectResult.error) {
                response = {
                    status: StripeTerminalConstant.ERROR_STATUS,
                    errorMessage: `${StripeTerminalConstant.COLLECT_FAILED_EXCEPTION_MESSAGE}: ${collectResult.error.message}`,
                };
            }

            let timeoutHandler = setTimeout(() => {
                callback(response);
                clearTimeout(timeoutHandler);
            }, 1000);

        } catch (e) {
            alert(e);
            // in case sudden lost internet
            if (!window.navigator.onLine) {
                return callback({
                    status: PaymentConstant.LOST_INTERNET_STATUS
                });
            }

            return tryDoAgain();
        }
    }

    /**
     *
     * @param popup
     * @param resolve
     * @return {Promise<*>}
     */
    async tryCreateRefundRequest(popup, resolve) {
        try {
            let {amount_paid} = this.payment;
            let amount = CurrencyHelper.round(amount_paid, 2);
            amount = NumberHelper.multipleNumber(amount, 100);

            let response = await this.doPost('/refund_payment_intent', {
                request: {
                    paymentIntentId: this.payment.orderPayment.reference_number,
                    amount,
                }
            });

            // process response
            if (!response.error && !response.message) {
                this.closePopup();

                return resolve({
                    reference_number: response.id,
                    error: false,
                });

            }

            return this.showError(popup, response);
        } catch (error) {
            return this.showError(popup, error);
        }
    }

    /**
     *
     * @param resolve
     * @return {Promise<*>}
     */
    async cancelSendRefundRequest(resolve) {
        return resolve({
            error: true,
            errorMessage: StripeTerminalConstant.MESSAGE_TRANSACTION_STATUS_CANCELLED,
        });
    }

    /**
     *
     * @param resolve
     * @return {Promise<*>}
     */
    async sendRefundRequest(resolve) {
        const popup = RefundPopupService.showPopup(
            this,
            () => this.tryCreateRefundRequest(popup, resolve),
            () => this.cancelSendRefundRequest(resolve)
        );
        return popup;
    }

    /**
     *
     * @param popup
     * @param order
     * @param resolve
     * @param reject
     * @return {Promise<*>}
     */
    async tryCreatePaymentIntent(popup, order, resolve, reject) {
        if (this.canceled) {
            return false;
        }

        let {amount_paid} = this.payment;
        let amount = NumberHelper.multipleNumber(amount_paid, 100);

        try {
            const createIntentResponse = await this.createPaymentIntent({
                amount,
                currency: CurrencyHelper.getCurrentCurrencyCode(),
                description: `Order ${order.increment_id} from Magestore POS`
            });
            this.pendingPaymentIntentSecret = createIntentResponse.secret;

            const response = await StripeTerminalService.terminal.collectPaymentMethod(
                this.pendingPaymentIntentSecret
            );

            if (response.error) {
                return this.closePopupWithError(
                    popup,
                    resolve,
                    `${StripeTerminalConstant.COLLECT_FAILED_EXCEPTION_MESSAGE} ${response.error.message}`
                )
            }

            this.pendingPaymentIntent = response.paymentIntent;

            return this.pollCallback(response.paymentIntent, resolve, reject);
        } catch (e) {
            return this.closePopupWithError(
                popup,
                resolve,
                e.message ? e.message : e
            )
        }
    }

    /**
     *
     * @param popup
     * @param order
     * @param resolve
     * @param reject
     * @return {Promise<*>}
     */
    async tryCreateSaleRequest(popup, order, resolve, reject) {
        const connectedReader = this.getConnectedReader();

        if (!connectedReader) {
            return this.closePopupWithError(popup, resolve, StripeTerminalConstant.NO_CONNECTED_READER_EXCEPTION_MESSAGE);
        }

        if (StripeTerminalService.terminal.getConnectionStatus() === 'connected') {
            return this.tryCreatePaymentIntent(popup, order, resolve, reject)
        }

        const deviceType = this.getDeviceType();

        return this.discoverReaders(
            deviceType,
            () => this.tryCreatePaymentIntent(popup, order, resolve, reject),
            (message) => {
                return this.closePopupWithError(popup, resolve, message);
            })
    }

    /**
     *
     * @param popup
     * @param resolve
     * @return {Promise<*>}
     */
    async cancelSendSaleRequest(popup, resolve) {
        this.canceled = true;
        if (!this.pendingPaymentIntentSecret) {
            return this.closePopupWithError(
                popup,
                resolve,
                StripeTerminalConstant.MESSAGE_TRANSACTION_STATUS_CANCELLED
            );
        }

        PurchasePopupService.showMessage(StripeTerminalConstant.CANCELING_TRANSACTION_MESSAGE);
        // make void transaction
        return this.cancelPendingPayment(
            () => this.closePopupWithError(
                popup,
                resolve,
                StripeTerminalConstant.MESSAGE_TRANSACTION_STATUS_CANCELLED
            )
        );
    }

    /**
     *  Prepare data to sale post
     *
     * @return {*}
     */
    getSendSaleRequestOrder() {
        let order;

        if (this.getOrder()) {
            order = {...this.order};
        } else {
            order = {...CheckoutService.getPreOrder()};
        }

        order.increment_id = PaymentHelper.generateRefCode(order, order.increment_id, this.payment);

        return order;
    }

    /**
     * Start create new sale
     *
     * @param resolve
     * @param reject
     * @return {Promise<*>}
     */
    async sendSaleRequest(resolve, reject) {
        const popup = PurchasePopupService.showPopup(
            this,
            () => this.tryCreateSaleRequest(popup, this.getSendSaleRequestOrder(), resolve, reject),
            () => this.cancelSendSaleRequest(popup, resolve)
        );
        return popup;
    }

    /**
     *
     * @returns {*|string}
     */
    getDeviceType() {
        return LocalStorageHelper.get(StripeTerminalConstant.CONFIG_DEVICE_TYPE) || StripeTerminalConstant.DEVICE_TYPE_SIMULATED;
    }

    /**
     *
     * @param value
     * @returns {*}
     */
    setDeviceType(value) {
        return LocalStorageHelper.set(StripeTerminalConstant.CONFIG_DEVICE_TYPE, value);
    }

    /**
     *
     * @param config
     * @return {Object|boolean}
     */
    getConnectedReaderFromConfig(config) {
        if (!config) {
            return false;
        }

        const connectedReaderSetting = config.settings.find(item => item.path === this.getConfigPath('connected_reader'));

        if (!connectedReaderSetting || !connectedReaderSetting.value) {
            return false;
        }
        return JSON.parse(connectedReaderSetting.value) || false;
    }

    /**
     *
     * @return {boolean|Object}
     */
    getConnectedReaderFromLocalStorage() {
        let connectedReader = LocalStorageHelper.get(StripeTerminalConstant.CONFIG_CONNECTED_READER);
        return connectedReader ? JSON.parse(connectedReader) : false;
    }

    /**
     *
     * @param config
     * @return {*}
     */
    getConnectedReader(config) {
        if (config) {
            return this.getConnectedReaderFromConfig(config);
        }

        return this.getConnectedReaderFromLocalStorage()
    }

    /**
     *
     * @param value
     * @returns {*}
     */
    setConnectedReader(value) {
        return LocalStorageHelper.set(StripeTerminalConstant.CONFIG_CONNECTED_READER, JSON.stringify(value));
    }

    /**
     *
     * @returns {*}
     */
    removeConnectedReader() {
        return LocalStorageHelper.remove(StripeTerminalConstant.CONFIG_CONNECTED_READER);
    }

    /**
     *
     * @return {Promise<*>}
     */
    createConnectionToken() {
        return this.doPost("/connection_token", {});
    }

    /**
     *
     * @param label
     * @param registration_code
     * @param doneCallback
     * @param failCallback
     * @returns {Promise<*>}
     */
    registerDevice = async (label, registration_code, doneCallback, failCallback) => {
        try {
            const reader = await this.doPost("/register_reader", {
                request: {
                    label,
                    registration_code,
                }
            });
            doneCallback(reader);
            return reader;
        } catch (e) {
            return failCallback(e.message || StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE);
        }
    };

    /**
     *
     * @param amount
     * @param currency
     * @param description
     * @return {Promise<*>}
     */
    createPaymentIntent({amount, currency, description}) {
        return this.doPost("/create_payment_intent", {
            request: {
                amount,
                currency,
                description,
            }
        });
    }

    /**
     *
     * @param paymentIntentId
     * @return {Promise<*>}
     */
    capturePaymentIntent({paymentIntentId}) {
        return this.doPost("/capture_payment_intent", {
            request: {
                payment_intent_id: paymentIntentId
            }
        });
    }

    /**
     *
     * @param url
     * @param body
     * @return {Promise<*>}
     */
    async doPost(url, body) {
        return await this.omc.post(this.api_url + url, body);
    }

    /**
     *  Stripe Terminal Initialization
     * @returns {any}
     */
    initializeBackendClientAndTerminal() {
        if (!StripeTerminalService.terminal) {
            StripeTerminalService.terminal = StripeTerminal.create({
                onFetchConnectionToken: async () => {
                    let connectionTokenResult = await this.createConnectionToken();
                    return connectionTokenResult.secret;
                },
                onUnexpectedReaderDisconnect: () => {
                    alert("Unexpected disconnect from the reader");
                },
                onConnectionStatusChange: () => {
                }
            });
        }

        return StripeTerminalService.terminal;
    }

    /**
     * Discover registered readers to connect to.
     *
     * @param deviceType
     * @param doneCallback
     * @param failCallback
     * @returns {Promise<*>}
     */
    discoverReaders = async (deviceType, doneCallback, failCallback) => {
        let discoverResult = {};
        try {
            discoverResult = await StripeTerminalService.terminal.discoverReaders({
                simulated: deviceType === StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                location: ConfigHelper.getConfig(this.getConfigPath(StripeTerminalConstant.STRIPE_LOCATION_ID))
            });
        } catch (error) {
            return failCallback(error.message || StripeTerminalConstant.FAILED_TO_READER_EXCEPTION_MESSAGE);
        }

        if (discoverResult.error) {
            return failCallback(discoverResult.error.message || StripeTerminalConstant.FAILED_TO_READER_EXCEPTION_MESSAGE);
        }

        const connected = this.getConnectedReader();

        if (connected) {
            let rememberedReaderId = connected.id;
            let preferredReader = discoverResult.discoveredReaders.find(reader => reader.id === rememberedReaderId);
            if (preferredReader) {
                return await this.connectToReader(
                    preferredReader,
                    () => doneCallback(discoverResult.discoveredReaders),
                    message => failCallback(message)
                );
            }
        }

        return doneCallback(discoverResult.discoveredReaders);
    };

    /**
     *  Connect to a discovered reader.
     *
     * @param selectedReader
     * @param doneCallback
     * @param failCallback
     * @return {Promise<*>}
     */
    connectToReader = async (selectedReader, doneCallback, failCallback) => {
        let connectResult = {};

        try {
            connectResult = await StripeTerminalService.terminal.connectReader(selectedReader);
        } catch (e) {
            return failCallback((e && e.message) || StripeTerminalConstant.FAILED_TO_CONNECT_READER_EXCEPTION_MESSAGE);
        }

        if (connectResult.error || !connectResult) {
            return failCallback(connectResult.error.message || StripeTerminalConstant.FAILED_TO_CONNECT_READER_EXCEPTION_MESSAGE);
        }

        const reader = connectResult.reader;
        this.setConnectedReader(reader);

        try {
            await this.saveConnectedReaderToPos(reader);
        } catch (e) {
            return failCallback((e && e.message) || StripeTerminalConstant.FAILED_TO_CONNECT_READER_EXCEPTION_MESSAGE);
        }

        doneCallback(reader);
        return connectResult;
    };

    /**
     *  save connected reader to pos
     * @param reader
     * @return {Promise<*>}
     */
    saveConnectedReaderToPos = async (reader) => {
        return await this.doPost('/save_connected_reader', {
            request: {
                pos_id: PosService.getCurrentPosId(),
                reader_id: reader.id,
                reader_label: reader.label,
                ip_address: reader.ip_address,
                serial_number: reader.serial_number,
            }
        });
    };

    /**
     * Disconnect from the reader, in case the user wants to switch readers.
     *
     * @param doneCallback
     * @param failCallback
     * @return {Promise<*>}
     */
    disconnectReader = async (doneCallback, failCallback) => {
        try {
            await StripeTerminalService.terminal.disconnectReader();
            this.removeConnectedReader();
            try {
                await this.saveConnectedReaderToPos({
                    pos_id: PosService.getCurrentPosId(),
                });
            } catch (e) {
                return failCallback((e && e.message) || StripeTerminalConstant.CONNECTION_ERROR_MESSAGE);
            }
            return doneCallback();
        } catch (e) {
            return failCallback(e.message || StripeTerminalConstant.CONNECTION_ERROR_MESSAGE);
        }
    };
    /**
     *
     * @param label
     * @param registration_code
     * @param doneCallback
     * @param failCallback
     * @returns {Promise<void>}
     */
    registerAndConnectNewReader = (label, registration_code, doneCallback, failCallback) => {
        try {
            return this.registerDevice(
                label,
                registration_code,
                (reader) => {
                    // After registering a new reader, we can connect immediately using the reader object returned from the server.
                    return this.connectToReader(reader, doneCallback, failCallback);
                },
                failCallback
            );
        } catch (e) {
            // Suppress backend errors since they will be shown in logs
            return failCallback(e.message || StripeTerminalConstant.CONNECTION_ERROR_MESSAGE);
        }
    };

    /**
     * Update the reader display to show cart contents to the customer
     *
     * @param items
     * @return {Promise<*>}
     */
    updateLineItems = async (items) => {
        let {amount_paid} = this.payment;
        let itemTotal = CurrencyHelper.round(amount_paid, 2);
        itemTotal = NumberHelper.multipleNumber(itemTotal, 100);
        let itemsDescription = [];
        items.forEach(item => itemsDescription.push(item.name));
        let lineItems = [
            {
                description: itemsDescription.join(', '),
                amount: itemTotal,
                quantity: 1
            }
        ];

        return await StripeTerminalService.terminal.setReaderDisplay({
            type: "cart",
            cart: {
                lineItems: lineItems,
                tax: 0,
                total: itemTotal,
                currency: CurrencyHelper.getCurrentCurrencyCode(),
            }
        });
    };


    /**
     *  Cancel a pending payment.
     *  Note this can only be done before calling `confirmPaymentIntent`.
     *
     * @param callback
     * @returns {Promise<*>}
     */
    cancelPendingPayment = async (callback) => {
        let result = false;
        try {
            result = await StripeTerminalService.terminal.cancelCollectPaymentMethod();
        } catch (e) {
            alert(e);
        }
        callback();
        return result;
    };

    /**
     *  Get stripe terminal config path
     *
     * @param node
     * @return {string}
     */
    getConfigPath(node = null) {
        let configPath = StripeTerminalConstant.CONFIG_PATH;
        if (node) {
            return `${configPath}/${node}`;
        }
        return configPath;
    }

    /**
     * Check enable payment
     *
     * @return {boolean}
     */
    isEnable() {
        return ConfigHelper.getConfig(this.getConfigPath('enable')) === '1';
    }

    /**
     *
     * @param response
     * @return {string}
     */
    makeError(response) {
        response = response || {};
        let error = response.error || {};
        let message = response.errorMessage
            || error.message
            || response.message
            || StripeTerminalConstant.UNKNOWN_EXCEPTION_MESSAGE;

        if (!error.items || !error.items.length) {
            return message;
        }

        message = [];
        error.items.forEach(item => {
            if (!message.includes(item.message)) {
                message.push(item.message);
            }
        });

        return message.join(', ');
    }

    /**
     *  Show error message on modal
     *
     * @param popup
     * @param response
     * @return {*}
     */
    showError(popup, response) {
        return popup.showError(this.makeError(response));
    }

    /**
     *
     * @param payment
     * @param prefix
     * @param excludePaymentMethod
     * @param excludeCardInformation
     * @return {string}
     */
    getOrderPaymentDetail(payment, prefix, excludePaymentMethod = false, excludeCardInformation = false) {
        let {title, card_type, cc_last4} = payment;
        let paymentTitle = `${prefix}${title}`;

        if (excludeCardInformation) {
            return paymentTitle;
        }

        let cardInformation = '';

        if (card_type || cc_last4) {
            const cardInformationItems = [cc_last4, card_type].filter(data => data);
            cardInformation = `(${cardInformationItems.join(' - ')})`
        }

        if (excludePaymentMethod) {
            return cardInformation;
        }

        return `${paymentTitle} ${cardInformation}`
    }

}

/**
 * @type {StripeTerminalService}
 */
let stripeTerminalService = ServiceFactory.get(StripeTerminalService);

export default stripeTerminalService;
