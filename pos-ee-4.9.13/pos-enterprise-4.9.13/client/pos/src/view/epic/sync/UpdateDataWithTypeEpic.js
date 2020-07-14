import SyncService from "../../../service/sync/SyncService";
import QueryService from "../../../service/QueryService";
import SyncConstant from "../../constant/SyncConstant";
import SyncAction from "../../action/SyncAction";
import ProductService from "../../../service/catalog/ProductService";
import CustomerService from "../../../service/customer/CustomerService";
import StockService from "../../../service/catalog/StockService";
import ActionLogService from "../../../service/sync/ActionLogService";
import OrderService from "../../../service/sales/OrderService";
import LocalStorageHelper from "../../../helper/LocalStorageHelper";
import _ from 'lodash';
import SessionService from "../../../service/session/SessionService";
import UserService from "../../../service/user/UserService";
import PermissionConstant from "../../constant/PermissionConstant";
import Permission from "../../../helper/Permission";
import cloneDeep from "lodash/cloneDeep";
import CatalogRuleProductPriceService from "../../../service/catalog/rule/CatalogRuleProductPriceService";
import CategoryService from "../../../service/catalog/CategoryService";
import Config from "../../../config/Config";
import {fire} from "../../../event-bus";
import AppStore from "../../store/store";

/**
 * Receive action type(UPDATE_DATA_WITH_TYPE) and update data from server
 * @param action$
 * @returns {Observable<any>}
 */
export default function updateDataWithType(action$) {
    return action$.ofType(SyncConstant.UPDATE_DATA_WITH_TYPE)
        .mergeMap(async action => {
            let data = action.data, service, pageSize = 100;
            if (data.type === SyncConstant.TYPE_PRODUCT) {
                // Update product
                service = ProductService;
                pageSize = 50;
            } else if (data.type === SyncConstant.TYPE_CUSTOMER) {
                // Update customer
                service = CustomerService;
            } else if (data.type === SyncConstant.TYPE_ORDER) {
                // Update order
                service = OrderService;
            } else if (data.type === SyncConstant.TYPE_STOCK) {
                try {
                    // Check for order is synced completely
                    let actions = await ActionLogService.getAllDataActionLog();
                    let isCompleted = true;
                    for (let index = actions.length; index > 0; ) {
                        if (actions[--index].action_type === SyncConstant.REQUEST_PLACE_ORDER) {
                            isCompleted = false;
                            break;
                        }
                    }
                    if (isCompleted) {
                        service = StockService;
                    } else {
                        service = false;
                    }
                } catch (e) {
                    service = false;
                }
            } else if (data.type === SyncConstant.TYPE_SESSION) {
                // Update session
                service = SessionService;
            } else if (data.type === SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE) {
                // Update catalog rule product price
                service = CatalogRuleProductPriceService;
                pageSize = 2000;
            } else if (data.type === SyncConstant.TYPE_CATEGORY) {
                // Update catalog rule product price
                service = CategoryService;
            }

            let eventDataBefore = {
                data: data,
                service: service,
                pageSize: pageSize
            };
            /* Event update data with type before */
            fire('epic_update_data_with_type_before', eventDataBefore);
            service = eventDataBefore.service;
            pageSize = eventDataBefore.pageSize;

            try {
                if (!service) {
                    return {type: ''};
                }

                // Update items
                let needReindex = await service.needReindex();
                let updated_time, hasDelete = false;
                let resync = false;
                let needSyncOrder = LocalStorageHelper.get(LocalStorageHelper.NEED_SYNC_ORDER);
                let needUpdateSession = LocalStorageHelper.get(LocalStorageHelper.NEED_UPDATE_SESSION);
                if (
                    (data.type === SyncConstant.TYPE_ORDER && needSyncOrder)
                    || (data.type === SyncConstant.TYPE_SESSION && needUpdateSession)
                ) {
                    resync = true;
                }

                let eventDataUpdateItemsBefore = {
                    data: data,
                    needReindex: needReindex,
                    resync: resync
                };
                /* Event update data with type - update items before */
                fire('epic_update_data_with_type_update_items_before', eventDataUpdateItemsBefore);
                needReindex = eventDataUpdateItemsBefore.needReindex;
                resync = eventDataUpdateItemsBefore.resync;

                let result = await updateItems(service, data, pageSize, 1, resync, needReindex);
                if (result.updated_time) {
                    updated_time = result.updated_time;
                }
                AppStore.dispatch(SyncAction.updateDataFinish(data, result.items));

                if (result.total) {

                    let total = Math.ceil(result.total / pageSize) + 1;
                    for (let page = 2; page < total; page++) {
                        result = await updateItems(service, data, pageSize, page, resync, needReindex);
                        AppStore.dispatch(SyncAction.updateDataFinish(data, result.items));
                    }
                }
                if (resync) {
                    if (data.type === SyncConstant.TYPE_ORDER && needSyncOrder) {
                        LocalStorageHelper.remove(LocalStorageHelper.NEED_SYNC_ORDER);
                    } else if (data.type === SyncConstant.TYPE_SESSION && needUpdateSession) {
                        LocalStorageHelper.remove(LocalStorageHelper.NEED_UPDATE_SESSION);
                    }
                }

                // Update data
                let updated_data_time;
                let canUpdateList = [
                    SyncConstant.TYPE_CUSTOMER
                ];

                let eventDataUpdateDataBefore = {
                    canUpdateList: canUpdateList
                };
                /* Event update data before */
                fire('epic_update_data_with_type_update_data_before', eventDataUpdateDataBefore);
                canUpdateList = eventDataUpdateDataBefore.canUpdateList;

                if (
                    canUpdateList.includes(data.type)
                    && service.needUpdateData()
                ) {
                    let updatedData = await updateData(service, data, pageSize, 1);
                    if (updatedData.updated_data_time) {
                        updated_data_time = updatedData.updated_data_time;
                    }
                    AppStore.dispatch(SyncAction.updateDataFinish(data, updatedData.items));

                    if (updatedData.total) {
                        let total = Math.ceil(updatedData.total / pageSize) + 1;
                        for (let page = 2; page < total; page++) {
                            updatedData = await updateData(service, data, pageSize, page);
                            AppStore.dispatch(SyncAction.updateDataFinish(data, updatedData.items));
                        }
                    }
                }

                // Delete items
                let canDeleteList = [
                    SyncConstant.TYPE_PRODUCT,
                    SyncConstant.TYPE_CUSTOMER,
                    SyncConstant.TYPE_ORDER,
                    SyncConstant.TYPE_SESSION,
                    SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE,
                    SyncConstant.TYPE_CATEGORY
                ];

                let eventDataDeleteDataBefore = {
                    canDeleteList: canDeleteList
                };
                /* Event delete data before */
                fire('epic_update_data_with_type_delete_data_before', eventDataDeleteDataBefore);
                canDeleteList = eventDataDeleteDataBefore.canDeleteList;

                if (canDeleteList.includes(data.type)) {
                    let deleted = await deleteItems(service, data);
                    if (deleted.length) {
                        hasDelete = true;
                        AppStore.dispatch(SyncAction.deleteDataFinish(data, deleted));
                    }
                }

                // Reindex data
                if ((hasDelete || resync || needReindex) && service.reindexTable) {
                    await service.reindexTable();
                }
                if (updated_time) {
                    data.updated_time = updated_time;
                }
                if (updated_data_time) {
                    data.updated_data_time = updated_data_time;
                }
                // clear updating flag
                data.updating = false;
                await SyncService.saveToDb([data]);
                AppStore.dispatch(SyncAction.updateDataFinishResult(data));
                // After updated this data type, recall executeUpdateData action to update next data type
                return SyncAction.executeUpdateData(action.nextActions);
            } catch (e) {
                data.updating = false;
                await SyncService.saveToDb([data]);
                return SyncAction.executeUpdateData(action.nextActions);
            }
        });
}

/**
 * Update items
 * @param service
 * @param data
 * @param pageSize
 * @param page
 * @param resync
 * @param needReindex
 * @returns {Promise<{error: boolean}|{updated_time: number, total: *, items: *}>}
 */
async function updateItems(service, data, pageSize, page, resync = false, needReindex = false) {
    try {
        let queryService = QueryService.reset();
        queryService.setPageSize(pageSize).setCurrentPage(page);

        if (!resync) {
            let updatedAtKey = data.type !== SyncConstant.TYPE_ORDER ? 'updated_at' : 'main_table.updated_at';
            if (data.type === SyncConstant.TYPE_PRODUCT) {
                queryService.addParams('show_option', '1');
            }
            queryService.addFieldToFilter(
                (
                    data.type === SyncConstant.TYPE_STOCK
                    || data.type === SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE
                ) ? 'updated_time' : updatedAtKey,
                (new Date(data.updated_time)).toISOString().substring(0, 19).replace('T', ' '),
                'gteq'
            );
        }

        if (
            data.type === SyncConstant.TYPE_SESSION
            && !Permission.isAllowed(PermissionConstant.PERMISSION_VIEW_SESSIONS_CREATED_BY_OTHER_STAFF)
        ) {
            queryService.addFieldToFilter('staff_id', UserService.getStaffId(), 'eq');
        }

        let eventDataBefore = {
            data: data,
            queryService: queryService,
            pageSize: pageSize,
            page: page,
            resync: resync,
            needReindex: needReindex
        };
        /* Event update data with type - request update items before */
        fire('epic_update_data_with_type_request_update_items_before', eventDataBefore);
        queryService = eventDataBefore.queryService;

        let response = await service.getDataOnline(queryService, true);

        // Process updated_time
        let updated_time = Date.now();
        if (response.hasOwnProperty('cached_at')) {
            updated_time = response.cached_at;
        }

        // prepare session data
        if (data.type === SyncConstant.TYPE_SESSION) {
            response.items = await SessionService.prepareUpdateSessionData(response.items);
            SessionService.checkCurrentSessionIsClosed(response.items);
        }

        // Save items
        let updateIndex = !(resync || needReindex);
        await service.saveToDb(response.items, updateIndex);

        return {
            items: response.items,
            updated_time: updated_time,
            total: response.total_count
        };
    } catch (error) {
        return {error: true};
    }
}

/**
 * Delete items and return total deleted
 *
 * @param {object} service
 * @param {object} data
 * @return {Array}
 */
async function deleteItems(service, data) {
    try {
        // Get deleted items
        let response = {};

        if (data.type === SyncConstant.TYPE_ORDER) {
            let queryService = QueryService.reset();
            queryService.addFieldToFilter(
                'updated_at',
                (new Date(data.updated_time)).toISOString().substring(0, 19).replace('T', ' '),
                'gteq'
            );
            queryService = cloneDeep(queryService);
            let deletedOrders = await service.getDeleted(queryService, true);
            let outOfPermissionOrders = await OrderService.getOutOfPermissionOrders(queryService, true);
            let outDateOrders = await OrderService.getOutDateOrders();

            response.ids = _.union(deletedOrders.ids, outDateOrders.ids, outOfPermissionOrders.ids);
        } else if (data.type === SyncConstant.TYPE_SESSION) {
            response = await SessionService.getOutDateSessions();
        } else if (data.type === SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE) {
            let ids = await CatalogRuleProductPriceService.getAllIds(true);
            response.ids = await CatalogRuleProductPriceService.getNotExistedIds(ids);
        } else {
            let queryService = QueryService.reset();
            queryService.addFieldToFilter(
                'updated_at',
                (new Date(data.updated_time)).toISOString().substring(0, 19).replace('T', ' '),
                'gteq'
            );
            if (data.type === SyncConstant.TYPE_CATEGORY) {
                queryService.addFieldToFilter(
                    'root_category_id',
                    Config.config.root_category_id,
                    'eq'
                );
            }
            response = await service.getDeleted(queryService, true);
        }
        // Delete from indexeddb
        if (response.ids && response.ids.length) {
            await service.deleteItems(response.ids);
            return response.ids;
        }
    } catch (error) {
        return [];
    }
    return [];
}

/**
 * Update data
 *
 * @param {object} service
 * @param {object} data
 * @param {int} pageSize
 * @param {int} page
 * @return {object}
 */
async function updateData(service, data, pageSize, page) {
    try {
        let queryService = QueryService.reset();
        queryService.setPageSize(pageSize).setCurrentPage(page);
        queryService.addParams('show_option', '1');

        let updatedAtKey = data.type !== SyncConstant.TYPE_ORDER ? 'updated_at' : 'main_table.updated_at';

        queryService.addFieldToFilter(
            updatedAtKey,
            (new Date(data.updated_data_time)).toISOString().substring(0, 19).replace('T', ' '),
            'gteq'
        );

        let eventDataBefore = {
            data: data,
            queryService: queryService,
            pageSize: pageSize,
            page: page,
        };
        /* Event update data with type - request update data before */
        fire('epic_update_data_with_type_request_update_data_before', eventDataBefore);
        queryService = eventDataBefore.queryService;

        let response = await service.getUpdateData(queryService, true);

        // Process updated_data_time
        let updated_data_time = Date.now();
        if (response.hasOwnProperty('cached_at')) {
            updated_data_time = response.cached_at;
        }

        // Save items
        await service.saveToDb(response.items);

        return {
            items: response.items,
            updated_data_time: updated_data_time,
            total: response.total_count
        };
    } catch (error) {
        return {error: true};
    }
}
