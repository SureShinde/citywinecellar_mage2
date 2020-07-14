import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import LocalStorageHelper from "../../helper/LocalStorageHelper";
import ActionLogResourceModel from "../../resource-model/sync/ActionLogResourceModel";
import OrderResourceModel from "../../resource-model/order/OrderResourceModel";
import SyncConstant from "../../view/constant/SyncConstant";
import ErrorLogResourceModel from "../../resource-model/sync/ErrorLogResourceModel";
import QueryService from "../QueryService";
import Config from "../../config/Config";
import {fire} from "../../event-bus";
import CustomerService from "../customer/CustomerService";
import CustomerResourceModel from "../../resource-model/customer/CustomerResourceModel";
import _ from 'lodash';
import OrderService from "../sales/OrderService";
import ScanConstant from "../../view/constant/ScanConstant";
import CustomerConstant from "../../view/constant/CustomerConstant";
import ApiResponseConstant from "../../view/constant/ApiResponseConstant";

class ActionLogService extends CoreService {
    static className = 'ActionLogService';
    resourceModel = ActionLogResourceModel;

    dependent = {
        [SyncConstant.REQUEST_PLACE_ORDER]: [
            {
                type: SyncConstant.TYPE_CUSTOMER,
                key: 'order.customer_id'
            }
        ],
        [SyncConstant.REQUEST_EDIT_CUSTOMER]: [
            {
                type: SyncConstant.TYPE_CUSTOMER,
                key: 'customer.id'
            }
        ],
        [SyncConstant.REQUEST_TAKE_PAYMENT_ORDER]: [
            {
                type: SyncConstant.TYPE_ORDER,
                key: 'increment_id'
            }
        ],
        [SyncConstant.REQUEST_CREATE_CREDITMEMO_ORDER]: [
            {
                type: SyncConstant.TYPE_ORDER,
                key: 'creditmemo.order_increment_id'
            }
        ],
        [SyncConstant.REQUEST_SEND_EMAIL_ORDER]: [
            {
                type: SyncConstant.TYPE_ORDER,
                key: 'increment_id'
            }
        ],
        [SyncConstant.REQUEST_ADD_COMMENT_ORDER]: [
            {
                type: SyncConstant.TYPE_ORDER,
                key: 'increment_id'
            }
        ],
        [SyncConstant.REQUEST_CANCEL_ORDER]: [
            {
                type: SyncConstant.TYPE_ORDER,
                key: 'increment_id'
            }
        ],
        [SyncConstant.REQUEST_DELETE_ORDER]: [
            {
                type: SyncConstant.TYPE_ORDER,
                key: 'increment_id'
            }
        ],
        [SyncConstant.REQUEST_SEND_EMAIL_CREDITMEMO_ORDER] : [
            {
                type: SyncConstant.TYPE_ORDER,
                key: 'increment_id'
            }
        ],
        [SyncConstant.REQUEST_CREDITMEMO_CREATE_CUSTOMER] : [
            {
                type: SyncConstant.TYPE_ORDER,
                key: 'increment_id'
            }
        ],
        [SyncConstant.REQUEST_SHIFT_SAVE_SESSION] : [
            {
                type: SyncConstant.TYPE_SESSION,
                key: 'shift.shift_increment_id'
            }
        ],
        [SyncConstant.REQUEST_CASH_TRANSACTION_SAVE_SESSION] : [
            {
                type: SyncConstant.TYPE_SESSION,
                key: 'cash_transactions.transaction_increment_id'
            }
        ],
        [SyncConstant.REQUEST_VOID_PURCHASE_PAYMENT] : [
            {
                type: SyncConstant.TYPE_VOID_PURCHASE_PAYMENT,
                key: 'refCode'
            }
        ],
        [SyncConstant.REQUEST_VOID_REFUND_PAYMENT] : [
            {
                type: SyncConstant.TYPE_VOID_REFUND_PAYMENT,
                key: 'refCode'
            }
        ]
    };

    /**
     * Request types that can be saved to server's log
     *
     * @type {string[]}
     */
    SAVED_TO_SERVER_REQUESTS = [
        SyncConstant.REQUEST_PLACE_ORDER,
        SyncConstant.REQUEST_TAKE_PAYMENT_ORDER,
        SyncConstant.REQUEST_CREATE_CREDITMEMO_ORDER
    ];

    /**
     * Constructor
     */
    constructor() {
        super();
        let eventData = {dependent: this.dependent};
        fire('service_action_log_init_dependent_variable_after', eventData);
        this.dependent = eventData.dependent;
    }

    /**
     * et all data in table action log
     *
     * @returns {Object|*|FormDataEntryValue[]|string[]}
     */
    getAllDataActionLog() {
        return this.getResourceModel().getAllDataActionLog();
    }

    /**
     * Save isSyncingActionLog to local storage
     * @param value
     */
    saveIsSyncingActionLog(value) {
        Config.isSyncingActionLog = value;
        LocalStorageHelper.set(LocalStorageHelper.IS_SYNCING_ACTION_LOG, value);
    }

    /**
     * Get isSyncingActionLog's value from local storage
     */
    getIsSyncingActionLog() {
        return LocalStorageHelper.get(LocalStorageHelper.IS_SYNCING_ACTION_LOG);
    }

    /**
     * Sync action log
     * @returns {Promise<any>}
     */
    async syncActionLog() {
        let actionLogResourceModel = this.getResourceModel();
        let errorLogResourceModel = this.getResourceModel(ErrorLogResourceModel);
        let allData = await this.getAllDataActionLog();
        allData = allData.filter(data => data.status !== SyncConstant.STATUS_REQUESTING);
        let staff_id = LocalStorageHelper.get(LocalStorageHelper.STAFF_ID);
        if (Config.session) {
            // request Action Log
            await this.requestActionLog(actionLogResourceModel, allData, staff_id);
            // move all request error from ActionLog to ErrorLog
            this.moveRequestErrorActionLog(actionLogResourceModel, errorLogResourceModel, allData);
        }
        return null;
    }

    /**
     * request Action Log
     * @param actionLogResourceModel
     * @param allData
     * @param staff_id
     * @returns {Promise<void>}
     */
    async requestActionLog(actionLogResourceModel, allData, staff_id) {
        for (let data of allData) {
            if (data.staff_id === staff_id && Config.session) {
                let checkDependent = await actionLogResourceModel.checkDependent(data);
                if (!checkDependent || this.canBeSavedToServer(data)) {
                    try {
                        await this.checkDataBeforeRequest(data);
                        data.status = SyncConstant.STATUS_REQUESTING;
                        await actionLogResourceModel.saveToDb([data]);
                        let result = await actionLogResourceModel.requestActionLog(data);
                        // save data result from request action log in to indexDB
                        await this.saveDataRequestActionLog(allData, data, data.action_type, result);
                        // delete request ActionLog in indexDb
                        await this.deleteRequestActionLog(actionLogResourceModel, data.action_id);
                    } catch (e) {
                        if (e.code && e.code === ApiResponseConstant.EXCEPTION_CODE_SAVED_REQUEST_TO_SERVER) {
                            await this.deleteRequestActionLog(actionLogResourceModel, data.action_id);
                        } else {
                            // increase count request error and save to indexDb again
                            data.count_request_error++;
                            data.error_content = e;
                            let sameUuidList = allData.filter(item =>
                                item.uuid === data.uuid && item.order >= data.order
                            );
                            sameUuidList.forEach(item => item.status = SyncConstant.STATUS_ERROR);
                            await actionLogResourceModel.saveToDb(sameUuidList);
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if action log can be saved to server
     *
     * @param data
     * @returns {boolean}
     */
    canBeSavedToServer(data) {
        return this.SAVED_TO_SERVER_REQUESTS.includes(data.action_type);
    }

    /**
     * Check data before request
     *
     * @param data
     * @returns {Promise<void>}
     */
    async checkDataBeforeRequest(data) {
        switch (data.action_type) {
            case SyncConstant.REQUEST_PLACE_ORDER: {
                let order = data.params.order;
                let customerIdWithPrefix = CustomerConstant.TMP_CUSTOMER_ID_PREFIX + order.customer_id;
                if (customerIdWithPrefix === order.tmp_customer_id) {
                    let customer = await CustomerService.get(order.tmp_customer_id, "tmp_customer_id");
                    if (customer && Object.keys(customer).length) {
                        order.customer_id = customer.id;
                    }
                }
                return;
            }
            default: {
                return;
            }
        }
    }

    /**
     * Save data result from request action log in to indexDB
     * @param allData
     * @param data
     * @param action_type
     * @param result
     */
    async saveDataRequestActionLog(allData, data, action_type, result) {
        if (
            action_type === SyncConstant.REQUEST_PLACE_ORDER
        ) {
          return await this.updateDataRequestPlaceOrder(data, result, allData);
        }
        if (
            action_type === SyncConstant.REQUEST_TAKE_PAYMENT_ORDER ||
            action_type === SyncConstant.REQUEST_CREATE_CREDITMEMO_ORDER
        ) {
            return await this.updateDataRequestTakePayment(result);
        }
        if (action_type === SyncConstant.REQUEST_CREATE_CUSTOMER) {
            return await this.updateDataRequestCreateCustomer(data, result, allData);
        }
        if (action_type === SyncConstant.REQUEST_EDIT_CUSTOMER) {
            return await this.updateDataRequestEditCustomer(data, result);
        }
        if (action_type === SyncConstant.REQUEST_HOLD_ORDER) {
            return await this.updateDataRequestHoldOrder(data, result);
        }
        if (action_type === SyncConstant.REQUEST_ADD_COMMENT_ORDER) {
            return await this.updateDataRequestAddComment(result);
        }
        if (action_type === SyncConstant.REQUEST_CANCEL_ORDER) {
            return await this.updateDataRequestCancel(result);
        }
        if (action_type === SyncConstant.REQUEST_CREDITMEMO_CREATE_CUSTOMER) {
            return await this.updateDataRequestCreditmemoCreateCustomer(data, result, allData);
        }
        if (action_type === SyncConstant.REQUEST_VOID_PURCHASE_PAYMENT) {
            return await this.deleteRequestVoidPurchasePaymentActionLog(data);
        }
        if (action_type === SyncConstant.REQUEST_VOID_REFUND_PAYMENT) {
            return await this.deleteRequestVoidRefundPaymentActionLog(data);
        }
        fire('service_action_save_data_request_action_log_after', { allData, data, action_type, result });
    }

    /**
     * update data request place order
     * @param data
     * @param result
     * @param allData
     * @returns {Promise.<void>}
     */
    async updateDataRequestPlaceOrder(data, result, allData) {
        let resource = this.getResourceModel(OrderResourceModel);
        resource.saveToDb([result]);
        fire('service_sync_log_order_update_data_finish', {result: result});
        let actions = await this.getDependentAction(data);
        actions.map(action => {
            if (action.action_type === SyncConstant.REQUEST_CREATE_CREDITMEMO_ORDER) {
                let creditmemo = action.params.creditmemo;
                creditmemo.order_id = result.entity_id;
                creditmemo.items.forEach(item => {
                    let orderItem = result.items.find(orderItem => +orderItem.tmp_item_id === +item.order_item_id);
                    if (orderItem) {
                        item.order_item_id = orderItem.item_id;
                    }
                })
            }
            this.updateActionLogData(action, allData);
            return null;
        });
        this.saveToDb(actions);
    }

    /**
     * update data request take payment
     * @param result
     */
    updateDataRequestTakePayment(result) {
        let resource = this.getResourceModel(OrderResourceModel);
        resource.saveToDb([result]);
        fire('service_sync_log_order_update_data_finish', {result: result});
    }


    /**
     * update data request add comment
     * @param result
     */
    updateDataRequestAddComment(result) {
        let resource = this.getResourceModel(OrderResourceModel);
        resource.saveToDb([result]);
        fire('service_sync_log_order_update_data_finish', {result: result});
    }

    /**
     * update data request cancel order
     * @param result
     */
    updateDataRequestCancel(result) {
        let resource = this.getResourceModel(OrderResourceModel);
        resource.saveToDb([result]);
        fire('service_sync_log_order_update_data_finish', {result: result});
    }

    /**
     * update actions data after request create customer
     * @param data
     * @param result
     * @param allData
     * @return {Promise<void>}
     */
    async updateActionsDataAfterCreateCustomer(data, result, allData) {
        let actions = await this.getDependentAction(data);
        for (let action of actions) {
            if (action.action_type === SyncConstant.REQUEST_EDIT_CUSTOMER) {
                let oldActionId = action.action_id;
                let oldActionIdSplited = oldActionId.split('_');
                await this.deleteRequestActionLog(this.getResourceModel(), oldActionId);
                action.action_id = action.action_type + result.id + oldActionIdSplited[oldActionIdSplited.length - 1];
                action.api_url = CustomerService.getPathSaveCustomer() + "/" + result.id;
                let customer = action.params.customer;
                customer.id = result.id;
                customer.website_id = result.website_id;
                customer.store_id = result.store_id;
                customer.custom_attributes = result.custom_attributes;
                customer.disable_auto_group_change = result.disable_auto_group_change;
                if (customer.addresses) {
                    for (let address of customer.addresses) {
                        address.customer_id = result.id;
                    }
                }
                let indexAddress = _.findIndex(allData, {'action_id': oldActionId});
                if (indexAddress !== -1) {
                    allData.splice(indexAddress, 1, action);
                }
                continue;
            } else if (action.action_type === SyncConstant.REQUEST_PLACE_ORDER) {
                action.params.order.customer_id = result.id;
            } else if (action.action_type === SyncConstant.REQUEST_CREATE_CREDITMEMO_ORDER) {
                action.params.creditmemo.customer_id = result.id;
            }
            this.updateActionLogData(action, allData);
        }
        this.saveToDb(actions);
    }

    /**
     * update data request credit memo create customer
     * @param data
     * @param result
     * @param allData
     * @return {Promise<void>}
     */
    async updateDataRequestCreditmemoCreateCustomer(data, result, allData) {
        let resourceCustomer = this.getResourceModel(CustomerResourceModel);
        if (Config.dataTypeMode && Config.dataTypeMode[SyncConstant.TYPE_ORDER] === SyncConstant.OFFLINE_MODE) {
            let resourceOrder = this.getResourceModel(OrderResourceModel);
            let order = await OrderService.getById(data.params.increment_id);
            order.customer_id = result.id;
            order.customer_firstname = result.firstname;
            order.customer_lastname = result.lastname;
            order.customer_email = result.email;
            order.customer_is_guest = 0;
            order.customer_group_id = result.group_id;
            await resourceOrder.saveToDb([order]);
        }

        await resourceCustomer.updateCustomer(result);

        await this.updateActionsDataAfterCreateCustomer(data, result, allData);

        fire('service_sync_save_customer', {result: result});
    }

    /**
     * delete data request void purchase payment
     * @param payload
     * @return {Promise<void>}
     */
    async deleteRequestVoidPurchasePaymentActionLog(payload) {
        let actionLogResourceModel = this.getResourceModel();
        let voidPurchaseRequests = await this.getAllDataActionLog();
        voidPurchaseRequests = voidPurchaseRequests.filter(requestAction =>
            requestAction.status !== SyncConstant.STATUS_REQUESTING
            && requestAction.action_type === SyncConstant.REQUEST_VOID_PURCHASE_PAYMENT
            && requestAction.params.refCode === payload.refCode
        );
        if (Config.session) {
            voidPurchaseRequests.forEach(async requestAction =>
                await this.deleteRequestActionLog(actionLogResourceModel, requestAction.action_id))
        }
    }
    /**
     * delete data request void refund payment
     * @param payload
     * @return {Promise<void>}
     */
    async deleteRequestVoidRefundPaymentActionLog(payload) {
        let actionLogResourceModel = this.getResourceModel();
        let voidRefundRequests = await this.getAllDataActionLog();
        voidRefundRequests = voidRefundRequests.filter(requestAction =>
            requestAction.status !== SyncConstant.STATUS_REQUESTING
            && requestAction.action_type === SyncConstant.REQUEST_VOID_REFUND_PAYMENT
            && requestAction.params.refCode === payload.refCode
        );
        if (Config.session) {
            voidRefundRequests.forEach(async requestAction =>
                await this.deleteRequestActionLog(actionLogResourceModel, requestAction.action_id))
        }
    }

    /**
     * update data request create customer
     * @param data
     * @param result
     * @param allData
     * @returns {Promise.<void>}
     */
    async updateDataRequestCreateCustomer(data, result, allData) {
        let resource = this.getResourceModel(CustomerResourceModel);

        await resource.updateCustomer(result);

        await this.updateActionsDataAfterCreateCustomer(data, result, allData);

        fire('service_sync_save_customer', {result: result});
    }

    /**
     * update data request edit customer
     * @param data
     * @param result
     * @return {Promise<void>}
     */
    async updateDataRequestEditCustomer(data, result) {
        let resource = this.getResourceModel(CustomerResourceModel);
        resource.saveToDb([result]);
        fire('service_sync_save_customer', {result: result});
    }

    /**
     * update data request hold order
     * @param data
     * @param result
     * @param allData
     * @return {Promise<void>}
     */
    async updateDataRequestHoldOrder(data, result) {
        let resource = this.getResourceModel(OrderResourceModel);
        resource.saveToDb([result]);
        fire('service_sync_update_on_hold_order_finish', {result: result});
    }

    /**
     * get dependent action
     * @param data
     * @returns {Promise.<*>}
     */
    async getDependentAction(data) {
        let queryService = QueryService.reset();
        queryService.addFieldToFilter('uuid', data.uuid, 'eq');
        let actions = await this.getResourceModel().getListOffline(queryService);
        actions = actions.items.filter(action => action.action_id !== data.action_id);
        return actions;
    }

    /**
     * update action log data
     * @param action
     * @param allData
     */
    updateActionLogData(action, allData) {
        let indexAddress = _.findIndex(allData, _.pick(action, 'action_id'));
        if (indexAddress !== -1) {
            allData.splice(indexAddress, 1, action);
        }
    }

    /**
     * move all request error from ActionLog to ErrorLog
     * @param actionLogResourceModel
     * @param errorLogResourceModel
     * @param allData
     */
    moveRequestErrorActionLog(actionLogResourceModel, errorLogResourceModel, allData) {
        let error_uuid = this.getAllUuidError(allData);
        if (error_uuid.length) {
            for (let data of allData) {
                for (let uuid of error_uuid) {
                    if (uuid === data.uuid) {
                        // save request error ActionLog to table ErrorLog in IndexDb
                        this.saveDataErrorToDb(errorLogResourceModel, data);
                        // delete request ActionLog in indexDb
                        this.deleteRequestActionLog(actionLogResourceModel, data.action_id)
                    }
                }
            }
        }
    }

    /**
     * get all uuid request error
     * @param allData
     * @returns {Array}
     */
    getAllUuidError(allData) {
        let error_uuid = [];
        for (let data of allData) {
            if (data.count_request_error >= SyncConstant.MAX_COUNT_ERROR_REQUEST) {
                error_uuid.push(data.uuid);
            }
        }
        return error_uuid;
    }

    /**
     * save request error ActionLog to table ErrorLog in IndexDb
     * @param errorLogResourceModel
     * @param data
     */
    saveDataErrorToDb(errorLogResourceModel, data) {
        errorLogResourceModel.saveToDb([data]);
    }

    /**
     * delete request ActionLog in indexDb
     * @param actionLogResourceModel
     * @param id
     * @return {*}
     */
    deleteRequestActionLog(actionLogResourceModel, id) {
        return actionLogResourceModel.getResourceOffline().delete(id);
    }

    /**
     * create data action log
     * @param action_type
     * @param url
     * @param method
     * @param params
     * @returns {Promise<*>}
     */
    async createDataActionLog(action_type, url, method, params) {
        // set default data insert
        var data = {
            action_type: action_type,
            action_id: '',
            api_url: url,
            method: method,
            params: params,
            uuid: '',
            order: 0,
            staff_id: LocalStorageHelper.get(LocalStorageHelper.STAFF_ID),
            count_request_error: 0,
            status: SyncConstant.STATUS_PENDING,
            created_at: new Date().getTime()
        };
        // change data with action type
        data = await this.createDataWithActionType(action_type, params, data);

        return this.getResourceModel().saveToDb([data]);
    }

    /**
     * change data with action type
     * @param action_type
     * @param params
     * @param data
     * @returns {*}
     */
    async createDataWithActionType(action_type, params, data) {
        switch (action_type) {
            case SyncConstant.TYPE_USER:
                data.action_id = action_type + '_' + params.id;
                break;
            case SyncConstant.REQUEST_PLACE_ORDER:
                data.action_id = action_type + '_' + params.order.increment_id;
                break;
            case SyncConstant.REQUEST_CREATE_CUSTOMER:
                data.action_id = action_type + '_' + params.customer.id;
                delete params.customer.id;
                break;
            case SyncConstant.REQUEST_EDIT_CUSTOMER:
                data.action_id = action_type + '_' + params.customer.id;
                break;
            case SyncConstant.REQUEST_TAKE_PAYMENT_ORDER:
                data.action_id = action_type + '_' + params.increment_id;
                break;
            case SyncConstant.REQUEST_CREATE_CREDITMEMO_ORDER:
                data.action_id = action_type + '_' + params.creditmemo.order_increment_id;
                break;
            case SyncConstant.REQUEST_HOLD_ORDER:
                data.action_id = action_type + '_' + params.order.increment_id;
                break;
            case SyncConstant.REQUEST_DELETE_ORDER:
                data.action_id = action_type + '_' + params.increment_id;
                break;
            case SyncConstant.REQUEST_SEND_EMAIL_ORDER:
                data.action_id = action_type + '_' + params.increment_id;
                break;
            case SyncConstant.REQUEST_ADD_COMMENT_ORDER:
                data.action_id = action_type + '_' + params.increment_id;
                break;
            case SyncConstant.REQUEST_CANCEL_ORDER:
                data.action_id = action_type + '_' + params.increment_id;
                break;
            case SyncConstant.REQUEST_SEND_EMAIL_CREDITMEMO_ORDER:
                data.action_id = action_type + '_' + params.increment_id;
                break;
            case SyncConstant.REQUEST_CREDITMEMO_CREATE_CUSTOMER:
                data.action_id = action_type + '_' + params.customer.id;
                if (params.isNewAccount) {
                    if (params.customer.id) {
                        delete params.customer.id;
                    }
                }
                delete params.isNewAccount;
                break;
            case SyncConstant.REQUEST_SHIFT_SAVE_SESSION:
                data.action_id = action_type + '_' + params.shift.shift_increment_id;
                break;
            case SyncConstant.REQUEST_CASH_TRANSACTION_SAVE_SESSION:
                data.action_id = action_type + '_' + params.cash_transactions[0].transaction_increment_id;
                break;
            case SyncConstant.REQUEST_VOID_PURCHASE_PAYMENT:
            case SyncConstant.REQUEST_VOID_REFUND_PAYMENT:
                data.action_id = action_type + '_' + Date.now();
                break;
            default:
                break;
        }

        data = await this.prepareUUID(action_type, params, data);
        data.action_id = data.action_id + '_' + new Date().getTime();
        return data;
    }

    /**
     * prepare UUID of action
     * @param action_type
     * @param params
     * @param data
     * @returns {Promise<*>}
     */
    async prepareUUID(action_type, params, data) {
        if (this.dependent[action_type]) {
            for (let item of this.dependent[action_type]) {
                let searchKey = "";
                searchKey = item.type + '_' + this.getValueByKeyFromParams(params, item.key);

                let queryService = QueryService.reset();
                queryService.addFieldToFilter(
                    [
                        [this.getResourceModel().getPrimaryKey(), searchKey, 'like'],
                    ]
                );

                let result = await this.getList(queryService);
                if (result.items.length > 0) {
                    data.uuid = result.items[result.items.length - 1].uuid;
                }
            }
        }

        if (!data.uuid) {
            data.uuid = 'UU' + new Date().getTime();
            data.order = 0;
        } else {
            let lastOrder = await this.getResourceModel().getLastOrder(data.uuid);
            data.order = lastOrder + 1;
        }
        return data;
    }

    /**
     * Get Value By Key From Params
     * @param params
     * @param key
     * @return {*}
     */
    getValueByKeyFromParams(params, key) {
        let value = params;
        key.split('.').map(x => {
            value = value[x];
            if (Array.isArray(value)) {
                value = value[0];
            }
            return null;
        });
        return value;
    }

    /**
     * get List
     * @param queryService
     * @returns {*|Promise<any>|Object}
     */
    getList(queryService) {
        return this.getResourceModel().getListOffline(queryService);
    }

    /**
     * get item
     * @param id
     */
    get(id) {
        return this.getResourceModel().get(id);
    }

    /**
     * get list request place order
     * @returns {*|Promise.<any>|Object}
     */
    getListRequestPlaceOrder() {
        // filter log request place order
        let queryService = QueryService.reset();
        queryService.addFieldToFilter('action_type', SyncConstant.REQUEST_PLACE_ORDER, 'eq');
        return this.getList(queryService);
    }

    /**
     * reset actions's status
     * @return {Promise<void>}
     */
    async resetActionsStatus() {
        let allData = await this.getAllDataActionLog();
        allData.forEach(action => {
            action.status = SyncConstant.STATUS_PENDING;
        });
        await this.saveToDb(allData);
    }

    /**
     * check able to reload pos
     *
     * @param countOrder
     * @param state
     * @returns {Promise<number>}
     * 0: Haven't reach number of orders for reloading page
     * 1: Can reload pos
     * 2: Reach number of orders for reloading page but missing some needed condition (user is doing some thing on POS)
     */
    async checkCanReload(countOrder, state) {
        if (countOrder >= SyncConstant.NUMBER_OF_ORDERS_FOR_RELOADING_PAGE) {
            let {scanPage} = state.core.scan;
            let {search_key} = state.core.product.productList;
            let {product} = state.core.product.viewProduct;
            let quote = state.core.checkout.quote;
            let data = await this.getAllDataActionLog();
            let hasWaitingRequest = data.some(item =>
                item.status === SyncConstant.STATUS_REQUESTING
                || item.status === SyncConstant.STATUS_PENDING
            );

            if (
                scanPage === ScanConstant.SCAN_PAGES.PRODUCT
                && !search_key
                && !product
                && (!quote.items || !quote.items.length)
                && (!hasWaitingRequest || !window.navigator.onLine)
            ) {
                return SyncConstant.CAN_RELOAD;
            }
            return SyncConstant.NEED_TO_RELOAD_BUT_USER_IS_DOING_SOMETHING;
        }
        return SyncConstant.CAN_NOT_RELOAD;
    }
}

/**
 * @type {ActionLogService}
 */
let actionLogService = ServiceFactory.get(ActionLogService);

export default actionLogService;
