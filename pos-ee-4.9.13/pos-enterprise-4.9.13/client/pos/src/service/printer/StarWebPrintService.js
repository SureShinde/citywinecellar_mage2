import ServiceFactory from "../../framework/factory/ServiceFactory";
import LocalStorageHelper from "../../helper/LocalStorageHelper";
import StarWebPrintConstant from "../../view/constant/printer/StarWebPrintConstant";
import StarWebPrintTrader from "../../helper/star-webprnt/StarWebPrintTrader";
import StarWebPrintBuilder from "../../helper/star-webprnt/StarWebPrintBuilder";
import toNumber from "lodash/toNumber";

export class StarWebPrintService {
    static className = 'StarWebPrintService';

    /**
     *
     * @returns {boolean}
     */
    isEnable() {
        return !!toNumber(LocalStorageHelper.get(StarWebPrintConstant.ENABLE_KEY));
    }

    /**
     *
     * @param value
     * @returns {*}
     */
    toggleEnable(value) {
        return LocalStorageHelper.set(StarWebPrintConstant.ENABLE_KEY, value ? 1 : 0);
    }

    /**
     *
     * @returns {*|string}
     */
    getHost() {
        return LocalStorageHelper.get(StarWebPrintConstant.HOST_KEY);
    }

    /**
     *
     * @param value
     * @returns {*}
     */
    setHost(value) {
        return LocalStorageHelper.set(StarWebPrintConstant.HOST_KEY, value);
    }

    /**
     *
     * @returns {*|string}
     */
    getPort() {
        return LocalStorageHelper.get(StarWebPrintConstant.PORT_KEY);
    }

    /**
     *
     * @param value
     * @returns {*}
     */
    setPort(value) {
        return LocalStorageHelper.set(StarWebPrintConstant.PORT_KEY, value);
    }

    /**
     *
     * @param {string} request
     * @returns {Promise<any>}
     */
    sendToPrinter(request) {
        return new Promise((resolve, error) => {
            let url = `${window.location.protocol}//${this.getHost()}:${this.getPort()}/StarWebPRNT/SendMessage`;
            let trader = new StarWebPrintTrader({url: url, papertype: 'normal'});

            trader.onReceive = function (response) {
                let msg = '- onReceive -\n\n';
                msg += 'TraderSuccess : [ ' + response.traderSuccess + ' ]\n';
                msg += 'TraderStatus : [ ' + response.traderStatus + ',\n';

                if (trader.isCoverOpen({traderStatus: response.traderStatus})) {
                    msg += '\tCoverOpen,\n';
                }
                if (trader.isOffLine({traderStatus: response.traderStatus})) {
                    msg += '\tOffLine,\n';
                }
                if (trader.isCompulsionSwitchClose({traderStatus: response.traderStatus})) {
                    msg += '\tCompulsionSwitchClose,\n';
                }
                if (trader.isEtbCommandExecute({traderStatus: response.traderStatus})) {
                    msg += '\tEtbCommandExecute,\n';
                }
                if (trader.isHighTemperatureStop({traderStatus: response.traderStatus})) {
                    msg += '\tHighTemperatureStop,\n';
                }
                if (trader.isNonRecoverableError({traderStatus: response.traderStatus})) {
                    msg += '\tNonRecoverableError,\n';
                }
                if (trader.isAutoCutterError({traderStatus: response.traderStatus})) {
                    msg += '\tAutoCutterError,\n';
                }
                if (trader.isBlackMarkError({traderStatus: response.traderStatus})) {
                    msg += '\tBlackMarkError,\n';
                }
                if (trader.isPaperEnd({traderStatus: response.traderStatus})) {
                    msg += '\tPaperEnd,\n';
                }
                if (trader.isPaperNearEnd({traderStatus: response.traderStatus})) {
                    msg += '\tPaperNearEnd,\n';
                }

                msg += '\tEtbCounter = ' + trader.extractionEtbCounter({traderStatus: response.traderStatus})
                    .toString() + ' ]\n';
                resolve(msg);
            };

            trader.onError = function (response) {
                if (!response.responseText) {
                    return error("There was a problem connecting printer!");
                }

                error(response.responseText);
            };

            trader.sendMessage({request: request});
        });
    }

    test(success, error) {
        try {
            let builder = new StarWebPrintBuilder();
            let request = '';
            request += builder.createInitializationElement();
            request += builder.createTextElement({data: 'Testing connection from PWA POS\n'});
            request += builder.createCutPaperElement({feed: true});
            this.sendToPrinter(request)
                .then(data => success(data))
                .catch(reason => error(reason));
        }
        catch (e) {
            error(e.message);
        }
    }

    /**
     *
     * @param canvas
     * @param success
     * @param error
     * @returns {*}
     */
    print(canvas, success, error) {
        try {
            if (canvas.getContext) {
                let context = canvas.getContext('2d');

                let builder = new StarWebPrintBuilder();

                let request = '';
                let peripheralElement = {};
                peripheralElement.channel = 1;
                peripheralElement.on = 20;
                peripheralElement.off = 20;

                request += builder.createInitializationElement();
                request += builder.createPeripheralElement(peripheralElement);

                request += builder.createBitImageElement({
                    context: context,
                    x: 0,
                    y: 0,
                    width: canvas.width,
                    height: canvas.height
                });

                request += builder.createCutPaperElement({feed: true});
                return this.sendToPrinter(request)
                    .then(data => success && success(data))
                    .catch(reason => error && error(reason));
            }
        }
        catch (e) {
            error && error(e.message);
            return error;
        }
    }
}

/**
 * @type {StarWebPrintService}
 */
let starWebPrintService = ServiceFactory.get(StarWebPrintService);

export default starWebPrintService;
