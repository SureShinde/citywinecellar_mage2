import SyncConstant from "../../constant/SyncConstant";
import SyncAction from "../../action/SyncAction";
import ProductService from "../../../service/catalog/ProductService";
import SyncService from "../../../service/sync/SyncService";
import QueryService from "../../../service/QueryService";
import StockService from "../../../service/catalog/StockService";
import CustomerService from "../../../service/customer/CustomerService";
import Config from "../../../config/Config";
import OrderService from "../../../service/sales/OrderService";
import SessionService from "../../../service/session/SessionService";
import Permission from "../../../helper/Permission";
import PermissionConstant from "../../constant/PermissionConstant";
import UserService from "../../../service/user/UserService";
import CatalogRuleProductPriceService from "../../../service/catalog/rule/CatalogRuleProductPriceService";
import CategoryService from "../../../service/catalog/CategoryService";
import GeneralService from "../../../service/settings/GeneralService";
import {fire} from "../../../event-bus";
/**
 * Request Data with type
 * @param action$
 * @returns {Observable<any>}
 */
export default function syncDataWithType(action$) {
    return action$.ofType(SyncConstant.SYNC_DATA_WITH_TYPE)
        .mergeMap(async function (action) {
            if(!GeneralService.isUseOfflineData()) {
                return SyncAction.syncData();
            }

            let data = action.data;
            let pageSize = 100;
            let currentPage;
            let result;
            let service;

            if (data.type === SyncConstant.TYPE_PRODUCT) {
                service = ProductService;
                pageSize = 50;
            } else if (data.type === SyncConstant.TYPE_STOCK) {
                service = StockService;
            } else if (data.type === SyncConstant.TYPE_CUSTOMER) {
                service = CustomerService;
            } else if (data.type === SyncConstant.TYPE_ORDER) {
                service = OrderService;
            } else if (data.type === SyncConstant.TYPE_SESSION) {
                service = SessionService;
            } else if (data.type === SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE) {
                service = CatalogRuleProductPriceService;
            } else if (data.type === SyncConstant.TYPE_CATEGORY) {
                service = CategoryService;
            }

            if (data.type === SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE) {
                pageSize = 2000;
            }

            let eventDataBefore = {
                data: data,
                service: service,
                pageSize: pageSize
            };
            /* Event update data with type before */
            fire('epic_sync_data_with_type_before', eventDataBefore);
            service = eventDataBefore.service;
            pageSize = eventDataBefore.pageSize;

            // Request each page of data
            if (data.total === SyncConstant.DEFAULT_TOTAL && Config.session) {
                currentPage = 1;
                result = await requestData(data, pageSize, currentPage, service);
                data = result.data;
                if (result.error) {
                    // If request is failed, recall syncData action to sync next data type
                    return SyncAction.syncData();
                }

            }
            if (data.count < data.total) {
                currentPage = Number((data.count / pageSize).toFixed()) + 1;
                let totalPage = Number(Math.ceil(data.total / pageSize));
                for (let i = currentPage; i <= totalPage; i++) {
                    if (Config.session) {
                        if(!GeneralService.isUseOfflineData()) {
                            return SyncAction.syncData();
                        }

                        result = await requestData(data, pageSize, i, service);
                        data = result.data;
                        if (result.error) {
                            // If request is failed, recall syncData action to sync next data type
                            return SyncAction.syncData();
                        }
                    }
                }
            }

            // After finished syncing this data type, reindex table and recall syncData action to sync next data type
            if (service.reindexTable) {
                service.reindexTable();
            }
            return SyncAction.syncData();
        });
}

/**
 * Request data from server
 * @param data
 * @param pageSize
 * @param page
 * @param service
 * @return {Promise<{data: *, error: boolean}>}
 */
async function requestData(data, pageSize, page, service) {
    try {
        let queryService = QueryService.reset();
        queryService.setPageSize(pageSize).setCurrentPage(page);
        queryService.addParams('show_option', '1');

        if (
            data.type === SyncConstant.TYPE_SESSION
            && !Permission.isAllowed(PermissionConstant.PERMISSION_VIEW_SESSIONS_CREATED_BY_OTHER_STAFF)
        ) {
            queryService.addFieldToFilter('staff_id', UserService.getStaffId(), 'eq');
        }

        let response = await service.getDataOnline(queryService, true);
        data.count += response.items.length;
        data.total = response.total_count;

        // Process updated_time
        !data.updated_time && (data.updated_time = Date.now());
        if (response.hasOwnProperty('cached_at') && response.cached_at < data.updated_time) {
            data.updated_time = response.cached_at;
        }

        // Process updated_data_time
        !data.updated_data_time && (data.updated_data_time = Date.now());
        if (response.hasOwnProperty('cached_at') && response.cached_at < data.updated_data_time) {
            data.updated_data_time = response.cached_at;
        }

        service.saveToDb(response.items);

        delete data.isFailed;
        await SyncService.saveToDb([data]);
        return {
            data: data,
            error: false
        }
    }
    catch (error) {
        // If request is failed, set data's isFailed attribute as true and save to indexedDb
        data.isFailed = true;
        await SyncService.saveToDb([data]);
        return {
            data: data,
            error: true
        }
    }
}
