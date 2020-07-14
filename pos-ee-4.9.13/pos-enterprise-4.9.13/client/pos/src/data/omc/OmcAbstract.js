import deepmerge from "../../framework/Merge";
import LocalStorageHelper from '../../helper/LocalStorageHelper'
import {fire} from "../../event-bus";
import Config from "../../config/Config"
import SyncConstant from "../../view/constant/SyncConstant";

export default class OmcAbstract {
    login_api = "/V1/webpos/staff/login";
    continue_login_api = "/V1/webpos/staff/continueLogin";
    assign_pos_api = "/V1/webpos/posassign";
    change_information_api = "/V1/webpos/staff/changepassword?";
    logout_api = "/V1/webpos/staff/logout";
    config_api = "/V1/webpos/config";
    logo_api = "/V1/webpos/website/information?";
    countries_api = "/V1/webpos/country/list?";
    get_deleted_product_api = "/V1/webpos/products/deleted";
    get_list_product_api = "/V1/webpos/products/sync";
    search_product_api = "/V1/webpos/products/search";
    get_list_payment_api = "/V1/webpos/payments";
    get_list_shipping_api = "/V1/webpos/shippings";
    place_order_api = "/V1/webpos/checkout/placeOrder";
    get_available_qty_api = "/V1/webpos/availableQty";
    get_external_stock_api = "/V1/webpos/getExternalStock";
    get_list_stock_api = "/V1/webpos/stocks/sync";
    get_color_swatch_api = "/V1/webpos/swatch";
    get_product_option = "/V1/webpos/options";
    get_list_customer_api = "/V1/webpos/customers/list";
    get_deleted_customer_api = "/V1/webpos/customers/deleted";
    get_customer_by_id_api = "/V1/webpos/customers";
    get_update_data_customer_api = "/V1/webpos/customers/updateLoyalty";
    get_list_location_api = "/V1/webpos/availableLocation";
    check_email_api = "/V1/customers/isEmailAvailable";
    search_barcode_product_api = "/V1/webpos/products/barcode";
    search_customer_api = "/V1/webpos/customers/search";
    save_customer_api = "/V1/webpos/customers";
    edit_customer_api = "/V1/webpos/customers/";
    submit_coupon_code = "/V1/webpos/checkout/checkPromotion";
    get_list_category_api = "/V1/webpos/categories";
    get_deleted_category_api = "/V1/webpos/categories/deleted";
    get_list_order_api = "/V1/webpos/orders/sync";
    search_order_api = "/V1/webpos/orders/search";
    order_take_payment_api = "/V1/webpos/order/takePayment";
    get_list_tax_rate_api = "/V1/webpos/taxRate/list";
    get_list_tax_rule_api = "/V1/webpos/taxRule/list";
    order_create_creditmemo_api = "/V1/webpos/creditmemos/create";
    get_token_stripe_payment = "https://api.stripe.com/v1/tokens";
    get_token_authorizenet_sandbox = "https://apitest.authorize.net/xml/v1/request.api";
    get_token_authorizenet_live = "https://api.authorize.net/xml/v1/request.api";
    authorizenet_finish_payment = "/V1/webpos/authorizenet/finishPayment";
    stripe_finish_payment = "/V1/webpos/stripe/finishPayment";
    hold_order_api = "/V1/webpos/order/hold";
    unhold_order_api = "/V1/webpos/order/unhold";
    delete_order_api = "/V1/webpos/order/delete";
    get_deleted_order_api = "/V1/webpos/orders/deleted";
    send_email_order_api = "/V1/webpos/order/sendEmail";
    add_comment_order_api = "/V1/webpos/order/comment";
    cancel_order_api = "/V1/webpos/order/cancel";
    send_email_creditmemo_api = "/V1/webpos/creditmemos/sendEmail";
    creditmemo_create_customer = "/V1/webpos/creditmemos/createCustomer";
    get_list_order_status_api = "/V1/webpos/orders/statuses";
    get_list_session_api = "/V1/webpos/shifts/getlist";
    shift_save_api = "/V1/webpos/shifts/save";
    cash_transaction_save_api = "/V1/webpos/cash_transaction/save";
    get_out_of_permission_orders_api = "/V1/webpos/orders/out-of-permission";
    get_list_catalog_rule_product_price_api = "/V1/webpos/catalogrule/product_price/sync";
    get_catalog_rule_product_price_ids_api = "/V1/webpos/catalogrule/product_price/get_all_ids";
    apply_giftcode = "/V1/webpos/giftcard/apply";
    reload_giftcodes = "/V1/webpos/giftcard/get_giftcard_by_customer";
    get_stock_items_to_refund_api = "/V1/webpos/stocks/getStockItemsToRefund";

    get_list_api = '';
    search_api = '';
    get_deleted_api = '';
    get_by_id_api = '';
    get_update_data_api = '';

    /**
     *
     * @return {string}
     */
    getStoreUrl() {
        let store_code = LocalStorageHelper.get(LocalStorageHelper.STORE_CODE);
        if(!store_code) {
            store_code = '';
        }

        return "rest/" + store_code;
    }

    /**
     * get base url
     * @returns {string}
     */
    getBaseUrl() {
        if (process.env.NODE_ENV !== 'production') {
            return process.env.REACT_APP_POS_URL + this.getStoreUrl();
        }
        return this.getUrlFromBrowser() + this.getStoreUrl();
    }


    /**
     * get url from browser link
     */
    getUrlFromBrowser() {
        let url = window.location.href;
        url = url.split(Config.basename)[0];
        return url;
    }

    /**
     * handle get request
     * @param url
     * @param opts
     * @param resolve
     * @param reject
     */
    handleGetRequest(url, opts = {}, resolve, reject) {
        let self = this;
        fetch(url,
            deepmerge.all([
                {
                    method: "GET",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + LocalStorageHelper.getToken()
                    },
                    mode: 'cors'
                },
                opts
            ])
        ).then(response => response.json()
            .then(function(data) {
                data = self.prepareResponse(data);
                if (response.ok) {
                    /*
                    * Use for async request
                    * If request has been enqueued on the server, recheck after 30 seconds
                    * */
                    if (data.hasOwnProperty('async_stage')) {
                        setTimeout(() => self.handleGetRequest(url, opts, resolve, reject), 10 * 1000);
                        return null;
                    }
                    // API Get list (product, customer...): append Date to sync data
                    if (Object.prototype.hasOwnProperty.call(data, 'total_count')
                        && data.hasOwnProperty('search_criteria')
                        && data.hasOwnProperty('items')
                    ) {
                        if (data.hasOwnProperty('cached_at')) {
                            data.cached_at = Date.parse(data.cached_at + 'Z');
                        } else if (response.headers.get('Date')) {
                            data.cached_at = Date.parse(response.headers.get('Date'));
                        }
                    }
                    return resolve(data);
                } else {
                    self.checkForceSignOut(data, response.status);
                    data.status = response.status;
                    data.statusText = response.statusText;
                    data.url = response.url;
                    data.method = SyncConstant.METHOD_GET;
                    data.created_at = new Date().getTime();
                    data.action_type = SyncConstant.TYPE_REQUEST_GET;
                    return reject(data);
                }
            })
        ).catch(function(error) {
            return reject(error.message);
        });
    }

    /**
     *
     * get request
     *
     * @param url string
     * @param opts mixed
     *
     * @return {Promise<any>}
     *
     * */
    get(url, opts = {}) {
        let self = this;
        url = self.addParamsToUrl(url, {pos_session:LocalStorageHelper.getSession()});
        return new Promise((resolve, reject) => {
            self.handleGetRequest(url, opts, resolve, reject);
        });
    }

    /**
     * pos request
     *
     * @param url
     * @param params
     *
     * @returns {Promise<any>}
     */
    post(url, params) {
        let self = this;
        url = self.addParamsToUrl(url, {pos_session:LocalStorageHelper.getSession()});
        return new Promise((resolve, reject) => {
            fetch(url,
                {
                    method: "POST",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + LocalStorageHelper.getToken()
                    },
                    mode: 'cors',
                    body: JSON.stringify(params)
                })
                .then(response => response.json()
                    .then(function(data) {
                        data = self.prepareResponse(data);
                        if (response.ok) {
                            return resolve(data);
                        } else {
                            self.checkForceSignOut(data, response.status);
                            return reject(data);
                        }
                    })
                ).catch(() => reject(''))
        })
    }

    /**
     * put request
     * @param url
     * @param params
     * @returns {Promise}
     */
    put(url, params) {
        let self = this;
        url = self.addParamsToUrl(url, {pos_session:LocalStorageHelper.getSession()});
        return new Promise((resolve, reject) => {
            fetch(url,
                {
                    method: "PUT",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + LocalStorageHelper.getToken()
                    },
                    mode: 'cors',
                    body: JSON.stringify(params)
                })
                .then(response => response.json()
                    .then(function(data) {
                        if (response.ok) {
                            return resolve(data);
                        } else {
                            self.checkForceSignOut(data, response.status);
                            return reject(data);
                        }
                    })
                ).catch(() => reject(''))
        })
    }

    /**
     * delete request
     * @param url
     * @param params
     * @returns {Promise}
     */
    delete(url, params) {
        let self = this;
        url = self.addParamsToUrl(url, {pos_session:LocalStorageHelper.getSession()});
        return new Promise((resolve, reject) => {
            fetch(url,
                {
                    method: "DELETE",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + LocalStorageHelper.getToken()
                    },
                    mode: 'cors',
                    body: JSON.stringify(params)
                })
                .then(response => response.json()
                    .then(function(data) {
                        if (response.ok) {
                            return resolve(data);
                        } else {
                            self.checkForceSignOut(data, response.status);
                            return reject(data);
                        }
                    })
                ).catch(() => reject(''))
        })
    }

    /**
     * Get order params array
     *
     * @param index
     * @param order
     */
    getOrderParamArray(index, order) {
        return [
            'searchCriteria[sortOrders][' + index + '][field]=' + order.field,
            'searchCriteria[sortOrders][' + index + '][direction]=' + order.direction
        ];
    }

    /**
     * Get search creria filter param array from filter object
     *
     * @param {number} filterGroup
     * @param {number} filtersIndex
     * @param {object} filter
     */
    getFilterParamArray(filterGroup, filtersIndex, filter) {
        return [
            'searchCriteria[filter_groups][' + filterGroup + '][filters][' + filtersIndex + '][field]=' + filter.field,
            'searchCriteria[filter_groups][' + filterGroup + '][filters][' + filtersIndex + '][value]=' +
            (filter.condition === 'like' ? '%' + filter.value + '%' : filter.value),
            'searchCriteria[filter_groups][' + filterGroup + '][filters][' + filtersIndex + '][condition_type]='
            + filter.condition
        ];
    }

    /**
     * Get query param for get list api
     *
     * @param queryService
     */
    getQueryParams(query = {}) {
        let queryParams = [],
            filterGroup = 0;
        if (query.orderParams.length > 0) {
            query.orderParams.map((item, index) => queryParams.push(...this.getOrderParamArray(index, item)))
        }
        if (query.queryString !== null) {
            queryParams.push('searchCriteria[queryString]=' + query.queryString);
        }

        if (query.filterParams.length > 0) {
            query.filterParams.map((item, index) => {
                queryParams.push(...this.getFilterParamArray(filterGroup, index, item));
                filterGroup++;
                return queryParams;
            });
        }
        if (query.orFilterParams.length > 0) {
            query.orFilterParams.map((orFilter) => {
                orFilter.map((item, index) =>
                    queryParams.push(...this.getFilterParamArray(filterGroup, index, item)));
                filterGroup++;
                return queryParams;
            });
        }

        if (query.pageSize) {
            queryParams.push(...[
                'searchCriteria[pageSize]=' + query.pageSize,
                'searchCriteria[currentPage]=' + query.currentPage
            ]);
        }
        if (query.params.length > 0) {
            query.params.map(param => queryParams.push(param.key + '=' + param.value));
        }
        return queryParams;
    }

    /**
     * Get list via api
     * @param queryService
     * @param isSync
     * @returns {Promise<any>}
     */
    getList(queryService = {}, isSync = false) {
        let query = Object.assign({}, queryService);
        let queryParams = this.getQueryParams(query);
        let opts = {};
        if (isSync) {
            opts.credentials = 'omit';
        }
        return this.get(this.getBaseUrl() +
            (query.queryString !== null ? this.search_api : this.get_list_api)
            + '?' + encodeURI(queryParams.join('&')), opts);
    }

    /**
     * Get deleted items via api
     * @param queryService
     * @param isSync
     * @returns {Promise<any>}
     */
    getDeleted(queryService = {}, isSync = false) {
        let queryParams = this.getQueryParams(Object.assign({}, queryService));
        let opts = {};
        if (isSync) {
            opts.credentials = 'omit';
        }
        return this.get(this.getBaseUrl()
            + this.get_deleted_api
            + '?' + encodeURI(queryParams.join('&')), opts);
    }

    /**
     * Check force sign out when request get error code
     *
     * @param data
     * @param responseCode
     */
    checkForceSignOut(data, responseCode) {
        fire('omc_check_force_sign_out', {
            data: data,
            responseCode: responseCode,
        });
    }

    /**
     * Prepare reponse - multi platform
     *
     * @param data
     */
    prepareResponse(data) {
        return data;
    }

    /**
     * get data by id
     * @param id
     * @return {Promise<any>}
     */
    getById(id) {
        return this.get(this.getBaseUrl() + this.get_by_id_api +'/'+id);
    }

    /**
     * Get update data via api
     *
     * @param queryService
     * @param isSync
     * @returns {Promise<any>}
     */
    getUpdateData(queryService = {}, isSync = false) {
        let queryParams = this.getQueryParams(Object.assign({}, queryService));
        let opts = {};
        if (isSync) {
            opts.credentials = 'omit';
        }
        return this.get(this.getBaseUrl()
            + this.get_update_data_api
            + '?' + encodeURI(queryParams.join('&')), opts);
    }

    /**
     * Add params to url
     *
     * @param {string} url
     * @param {object} params
     * @return {string}
     */
    addParamsToUrl(url, params){
        if(params && (typeof params === 'object')){
            for (let key in params) {
                let value = params[key];
                if (url.indexOf("?") !== -1) {
                    url = url + '&'+key+'=' + value;
                }
                else {
                    url = url + '?'+key+'=' + value;
                }
            }
        }
        return url;
    }

}

