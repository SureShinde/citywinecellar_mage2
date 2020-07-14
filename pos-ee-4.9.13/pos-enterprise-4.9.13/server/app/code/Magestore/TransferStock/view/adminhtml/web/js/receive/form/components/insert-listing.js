/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/insert-listing',
    'uiRegistry'
], function ($, Component, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                initParamsUpdate: "${$.provider}:data"
            },
            transfer_id: ''
        },

        initParamsUpdate: function(data) {
            this.transfer_id = data.transfer_summary.general_information.inventorytransfer_id;
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
                    value: selections[itemsType]
                };
            }

            selectionsData.paging = {
                notLimits: 1
            };

            if(this.transfer_id !== '') {
                selectionsData.inventorytransfer_id = this.transfer_id;
            }

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
        },
    });
});
