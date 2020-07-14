import { OmcUser, OmcConfig, OmcProduct,
        OmcActionLog, OmcPayment, OmcShipping,
        OmcStock, OmcOrder, OmcColorSwatch,
        OmcCustomer, OmcQuote, OmcCategory,
        OmcTaxRate, OmcTaxRule, OmcSession,
        OmcCatalogRuleProductPrice, OmcGiftcard } from '../data/omc/index';
import Config from '../config/Config';
import {IndexedDbProduct, IndexedDbSync,
        IndexedDbActionLog, IndexedDbPayment,
        IndexedDbShipping, IndexedDbOrder,
        IndexedDbStock, IndexedDbErrorLog,
        IndexedDbCustomer, IndexedDbCategory,
        IndexedDbTaxRate, IndexedDbTaxRule, IndexedDbSession,
        IndexedDbCatalogRuleProductPrice } from '../data/indexeddb/index';
import OmcLocation from "../data/omc/OmcLocation";
import IndexedDbCart from "../data/indexeddb/IndexedDbCart";
import DataResourceFactory from "../framework/factory/DataResourceFactory";
import {fire} from "../event-bus";
import ObjectManager from "../framework/ObjectManager";

let resources = {
    'OmcUser' : OmcUser,
    'OmcLocation': OmcLocation,
    'OmcConfig' : OmcConfig,
    'OmcProduct' : OmcProduct,
    'OmcActionLog' : OmcActionLog,
    'OmcPayment' : OmcPayment,
    'OmcShipping' : OmcShipping,
    'OmcStock' : OmcStock,
    'OmcOrder' : OmcOrder,
    'OmcColorSwatch' : OmcColorSwatch,
    'OmcCustomer': OmcCustomer,
    'OmcQuote': OmcQuote,
    'OmcCategory': OmcCategory,
    'OmcTaxRate': OmcTaxRate,
    'OmcTaxRule': OmcTaxRule,
    'OmcSession': OmcSession,
    'OmcCatalogRuleProductPrice': OmcCatalogRuleProductPrice,
    'OmcGiftcard': OmcGiftcard,
    'IndexedDbProduct': IndexedDbProduct,
    'IndexedDbSync': IndexedDbSync,
    'IndexedDbActionLog': IndexedDbActionLog,
    'IndexedDbPayment': IndexedDbPayment,
    'IndexedDbShipping': IndexedDbShipping,
    'IndexedDbOrder': IndexedDbOrder,
    'IndexedDbStock': IndexedDbStock,
    'IndexedDbErrorLog': IndexedDbErrorLog,
    'IndexedDbCustomer': IndexedDbCustomer,
    'IndexedDbCategory': IndexedDbCategory,
    'IndexedDbTaxRate': IndexedDbTaxRate,
    'IndexedDbTaxRule': IndexedDbTaxRule,
    'IndexedDbCart': IndexedDbCart,
    'IndexedDbSession': IndexedDbSession,
    'IndexedDbCatalogRuleProductPrice': IndexedDbCatalogRuleProductPrice,
};

export class Singleton{
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        let eventDataBefore = {
            resources: resources
        };

        fire('constructor_singleton_before', eventDataBefore);
        resources = eventDataBefore.resources;
    }
    /**
    {
    return new (this.getObject(`Service * get  online resource by name
     * @param: string
     *
     * @return: object
     */
    getOnline(name) {
        return this.get(Config.platform, name);
    }

    /**
     * get  offline resource by name
     * @param: string
     *
     * @return: object
     */
    getOffline(name) {
        return this.get(Config.db, name);
    }

    /**
     * get object form name and prefix
     * @param: string
     * @param: string
     *
     * @return: object
     *
     */
    get(prefix, name) {
        return ObjectManager.get(DataResourceFactory.get(resources[prefix + name]));
    }
}

let singleton = new Singleton();

export default singleton;
