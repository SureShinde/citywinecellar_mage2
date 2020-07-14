/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/components/insert-listing',
    'underscore'
], function (Insert, _) {
    'use strict';

    return Insert.extend({
        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            return this._super();
        },

        /**
         * Updates externalValue, from ajax request to grab selected rows data
         *
         * @param {Object} selections
         * @param {String} itemsType
         *
         * @returns {Object} request - deferred that will be resolved when ajax is done
         */
        updateFromServerData: function (selections, itemsType) {
            var filterType = selections && selections.excludeMode ? 'nin' : 'in',
                selectionsData = {},
                request;

            _.extend(selectionsData, this.params || {}, selections.params);

            if (selections[itemsType] && selections[itemsType].length) {
                selectionsData.filters = {};
                selectionsData['filters_modifier'] = {};
                selectionsData['filters_modifier'][this.indexField] = {
                    'condition_type': filterType,
                    value: JSON.stringify(selections[itemsType])
                };
            }

            selectionsData.paging = {
                notLimits: 1
            };

            request = this.requestData(selectionsData, {
                method: this.requestConfig.method
            });
            request
                .done(function (data) {
                    this.setExternalValue(data.items || data);
                    this.loading(false);
                }.bind(this))
                .fail(this.onError);

            return request;
        }
    });
});
