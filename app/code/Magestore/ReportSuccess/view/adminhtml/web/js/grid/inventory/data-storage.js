/*
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/grid/data-storage'
], function ($, ko, _, DataStorage) {
    'use strict';

    return DataStorage.extend({

        /**
         * Forms data object associated with provided request.
         *
         * @param {Object} request - Request object.
         * @returns {jQueryPromise}
         */
        getRequestData: function (request) {
            var defer = $.Deferred(),
                resolve = defer.resolve.bind(defer),
                delay = this.cachedRequestDelay,
                result;

            result = {
                items: this.getByIds(request.ids),
                totalRecords: request.totalRecords,
                totals: request.totals
            };

            delay ?
                _.delay(resolve, delay, result) :
                resolve(result);

            return defer.promise();
        },
        /**
         * Caches requests object with provdided parameters
         * and data object associated with it.
         *
         * @param {Object} data - Data associated with request.
         * @param {Object} params - Request parameters.
         * @returns {DataStorage} Chainable.
         */
        cacheRequest: function (data, params) {
            var cached = this.getRequest(params);

            if (cached) {
                this.removeRequest(cached);
            }

            this._requests.push({
                ids: this.getIds(data.items),
                params: params,
                totalRecords: data.totalRecords,
                totals: data.totals
            });

            return this;
        }
    });
});