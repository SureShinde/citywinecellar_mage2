import ConfigResourceModel from "../../resource-model/config/ConfigResourceModel";
import LocalStorageHelper from "../../helper/LocalStorageHelper";
import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import i18n from "../../config/i18n";
import ConfigHelper from "../../helper/ConfigHelper";
import Config from "../../config/Config";

export class ConfigService extends CoreService {
    static className = 'ConfigService';
    resourceModel = ConfigResourceModel;

    /**
     * Save all config to Local storage
     * @param data
     */
    saveToLocalStorage(data) {
        LocalStorageHelper.set(LocalStorageHelper.CONFIG, JSON.stringify(data));
    }

    /**
     * get config from local storage
     * @returns {*|string}
     */
    getConfigFromLocalStorage() {
        return LocalStorageHelper.get(LocalStorageHelper.CONFIG);
    }

    /**
     * Change language
     * @param locale
     */
    changeLanguage(locale) {
        let path = window.location.pathname;
        path = path.split(Config.basename);
        path = path[0] ? path[0] : '/';

        if(!locale) {
            locale = ConfigHelper.getLocaleCode();
        }
        i18n.init({
            fallbackLng: false,
            lng: locale.toLowerCase(),

            // have a common namespace used around the full app
            ns: ['translations'],
            defaultNS: 'translations',
            nsSeparator: false,
            keySeparator: false,

            debug: false,

            interpolation: {
                escapeValue: false, // not needed for react!!
            },

            backend: {
                loadPath: path + Config.basename + '/locales/{{lng}}/{{ns}}.json'
            },

            react: {
                wait: true,
                withRef: false,
                bindI18n: 'languageChanged loaded',
                bindStore: 'added removed',
                nsMode: 'default'
            }
        });
    }
}
/** @type ConfigService */
let configService = ServiceFactory.get(ConfigService);

export default configService;

