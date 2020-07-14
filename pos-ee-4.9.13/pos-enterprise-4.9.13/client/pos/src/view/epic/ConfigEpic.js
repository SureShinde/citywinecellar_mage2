import ConfigAction from "../action/ConfigAction";
import ConfigConstant from '../constant/ConfigConstant';
import LoadingAction from "../action/LoadingAction";
import {Observable} from 'rxjs';
import ConfigService from "../../service/config/ConfigService";
import SyncService from "../../service/sync/SyncService";
import ConfigHelper from "../../helper/ConfigHelper";
import OrderConstant from "../constant/OrderConstant";
import LocalStorageHelper from "../../helper/LocalStorageHelper";
import Config from "../../config/Config";
import SearchConstant from "../constant/SearchConstant";
import SyncAction from "../action/SyncAction";
import SyncConstant from "../constant/SyncConstant";
import AppStore from "../../view/store/store";
import SessionConstant from "../constant/SessionConstant";
import PermissionConstant from "../constant/PermissionConstant";
import OrderService from "../../service/sales/OrderService";
import SessionService from "../../service/session/SessionService";
import {toast} from "react-toastify";
import i18n from "../../config/i18n";
import CategoryService from "../../service/catalog/CategoryService";
import {fire} from "../../event-bus";
import ErrorLogService from "../../service/sync/ErrorLogService";

/**
 * Receive action type(GET_CONFIG) and request, response data config
 * @param action$
 * @returns {Observable<any>}
 */
export default action$ => {
    let requestTime = 0;
    let loadingErrorLogs = {};
    return action$.ofType(ConfigConstant.GET_CONFIG)
        .mergeMap(action => {
            requestTime++;
            return Observable.from(SyncService.getConfig())
                .mergeMap(async (response) => {
                    if (response.current_currency_code && response.currencies) {
                        let currentCurrency = response.currencies.find(item =>
                            item.code === response.current_currency_code
                        );
                        if (!currentCurrency) {
                            toast.error(
                                i18n.translator.translate(
                                    'Undefined currency rate from {{base_currency_code}} to {{current_currency_code}}',
                                    {
                                        base_currency_code: response.base_currency_code,
                                        current_currency_code: response.current_currency_code
                                    }
                                ),
                                {
                                    className: 'wrapper-messages messages-warning'
                                }
                            );
                            response.current_currency_code = response.base_currency_code;
                        }
                    }


                    ConfigService.saveToLocalStorage(response);

                    if (response && Array.isArray(response.settings)) {
                        let locale = response.settings.find(item =>
                            item.path === ConfigConstant.CONFIG_XML_PATH_GENERAL_LOCALE_CODE
                        );
                        if (locale) {
                            ConfigService.changeLanguage(locale.value);
                        }
                    }

                    if (Config.config) {
                        let needSyncData = false;
                        // check config sync order
                        checkSyncOrderConfig(response);

                        // check config barcode
                        let isChangeBarcodeConfig = await checkBarcodeConfig(response);
                        if (isChangeBarcodeConfig) needSyncData = true;

                        // check config sync session
                        checkSyncSessionConfig(response);

                        // check order permission
                        let isChangeSessionPermission = await checkSessionPermission(response);
                        if (isChangeSessionPermission) needSyncData = true;

                        // check order permission
                        let isChangeOrderPermission = await checkOrderPermission(response);
                        if (isChangeOrderPermission) needSyncData = true;

                        // check root category
                        let isChangeRootCategory = await checkRootCategory(response);
                        if (isChangeRootCategory) needSyncData = true;

                        if (needSyncData) {
                            AppStore.dispatch(SyncAction.syncData());
                        }
                    }

                    fire('epic_config_get_config_after', { config: response });
                    requestTime = 0;
                    if (action.atLoadingPage) {
                        AppStore.dispatch(LoadingAction.updateFinishedList(ConfigConstant.TYPE_GET_CONFIG));
                    }
                    return ConfigAction.getConfigResult(response);
                }).catch(error => {
                    let message = "Failed to get configurable data. Please contact technical support.";
                    ErrorLogService.handleLoadingPageErrors(
                        error,
                        ConfigConstant.TYPE_GET_CONFIG,
                        loadingErrorLogs,
                        requestTime,
                        action,
                        message
                    );
                    return Observable.of(ConfigAction.getConfigError(error));
                })
        });
}

/**
 * check sync order config
 * @param newConfig
 */
function checkSyncOrderConfig(newConfig) {
    let oldConfigOrderSince = ConfigHelper.getConfig(OrderConstant.XML_PATH_CONFIG_SYNC_ORDER_SINCE);
    let newConfigOrderSince = newConfig.settings.find(
        item => item.path === OrderConstant.XML_PATH_CONFIG_SYNC_ORDER_SINCE
    );
    if (typeof newConfigOrderSince !== "undefined") {
        newConfigOrderSince = newConfigOrderSince.value;
        if (oldConfigOrderSince !== newConfigOrderSince) {
            LocalStorageHelper.set(LocalStorageHelper.NEED_SYNC_ORDER, 1);
        }
    }
}

/**
 * check barcode config
 * @param newConfig
 * @return {Promise<boolean>}
 */
async function checkBarcodeConfig(newConfig) {
    let oldConfigBarcode = ConfigHelper.getConfig(SearchConstant.BARCODE_CONFIG);
    let newConfigBarcode = newConfig.settings.find(
        item => item.path === SearchConstant.BARCODE_CONFIG
    );
    if (typeof newConfigBarcode !== "undefined") {
        newConfigBarcode = newConfigBarcode.value;
        if (oldConfigBarcode !== newConfigBarcode) {
            await SyncService.resetData([SyncConstant.TYPE_PRODUCT]);
            return true;
        }
    }
    return false;
}

/**
 * check sync session config
 * @param newConfig
 */
function checkSyncSessionConfig(newConfig) {
    let oldConfigSessionSince = ConfigHelper.getConfig(SessionConstant.XML_PATH_CONFIG_SYNC_SESSION_SINCE);
    let newConfigSessionSince = newConfig.settings.find(
        item => item.path === SessionConstant.XML_PATH_CONFIG_SYNC_SESSION_SINCE
    );
    if (typeof newConfigSessionSince !== "undefined") {
        newConfigSessionSince = newConfigSessionSince.value;
        if (oldConfigSessionSince !== newConfigSessionSince) {
            LocalStorageHelper.set(LocalStorageHelper.NEED_UPDATE_SESSION, 1);
        }
    }
}

/**
 * check order permission
 * @param newConfig
 * @return {Promise<boolean>}
 */
async function checkOrderPermission(newConfig) {
    let oldPermissions = Config.config.permissions;
    let newPermissions = newConfig.permissions;
    let permissionKeys = [
        PermissionConstant.PERMISSION_MANAGE_ORDER,
        PermissionConstant.PERMISSION_MANAGE_ORDER_CREATED_AT_LOCATION_OF_STAFF,
        PermissionConstant.PERMISSION_MANAGE_ORDER_CREATED_AT_ALL_LOCATION,
        PermissionConstant.PERMISSION_MANAGE_ORDER_THAT_ARE_CREATED_AT_OR_ASSIGNED_TO_LOCATION_OF_STAFF,
        PermissionConstant.PERMISSION_MANAGE_ALL_ORDERS_IN_SYSTEM,
    ];
    for (let permission of permissionKeys) {
        if ((newPermissions.indexOf(permission) > -1) !== (oldPermissions.indexOf(permission) > -1)) {
            await OrderService.clear();
            let dataTypeMode = {
                [SyncConstant.TYPE_ORDER]: SyncConstant.ONLINE_MODE
            };
            await AppStore.dispatch(SyncAction.changeDataTypeMode(dataTypeMode));
            await SyncService.resetData([SyncConstant.TYPE_ORDER]);
            return true;
        }
    }
    return false;
}

/**
 * check session permission
 * @param newConfig
 * @return {Promise<boolean>}
 */
async function checkSessionPermission(newConfig) {
    let oldPermissions = Config.config.permissions;
    let newPermissions = newConfig.permissions;
    let permissionKeys = [
        PermissionConstant.PERMISSION_VIEW_SESSIONS_CREATED_BY_OTHER_STAFF,
    ];
    for (let permission of permissionKeys) {
        if ((newPermissions.indexOf(permission) > -1) !== (oldPermissions.indexOf(permission) > -1)) {
            await SessionService.clear();
            let dataTypeMode = {
                [SyncConstant.TYPE_SESSION]: SyncConstant.ONLINE_MODE
            };
            await AppStore.dispatch(SyncAction.changeDataTypeMode(dataTypeMode));
            await SyncService.resetData([SyncConstant.TYPE_SESSION]);
            return true;
        }
    }
    return false;
}

/**
 * check root category
 * @param newConfig
 * @returns {Promise<boolean>}
 */
async function checkRootCategory(newConfig) {
    if (Config.config.root_category_id !== newConfig.root_category_id) {
        await CategoryService.clear();
        let dataTypeMode = {
            [SyncConstant.TYPE_CATEGORY]: SyncConstant.ONLINE_MODE
        };
        await AppStore.dispatch(SyncAction.changeDataTypeMode(dataTypeMode));
        await SyncService.resetData([SyncConstant.TYPE_CATEGORY]);
        return true;
    }
    return false;
}
