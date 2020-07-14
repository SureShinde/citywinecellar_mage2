/*
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Catalog/js/price-utils',
    'Magento_Ui/js/grid/provider'
], function ($, ko, _, PriceUtils, Provider) {
    'use strict';

    return Provider.extend({
        defaults: {
            storageConfig: {
                component: 'Magestore_ReportSuccess/js/grid/inventory/data-storage',
                provider: '${ $.storageConfig.name }',
                name: '${ $.name }_storage',
                updateUrl: '${ $.update_url }'
            }
        },
        listTotal: [],
        listPrice: [],
        /**
         * Processes data before applying it.
         *
         * @param {Object} data - Data to be processed.
         * @returns {Object}
         */
        processData: function (data) {
            var self = this;
            var items = data.items;

            _.each(items, function (record, index) {
                record._rowIndex = index;
            });

            // fill data to block total
            $.each(this.listTotal, function (key, value) {
                $('#' + value.id + ' .count').html(self.bindDataForTotal(data, value.key));
            });

            return data;
        },

        bindDataForTotal: function (data, key) {
            var total = 0;
            if(typeof data.totals !== 'undefined' && typeof data.totals[key] !== 'undefined') {
                total = parseFloat(data.totals[key]);
            }
            if($.inArray(key, this.listPrice) != '-1') {
                total = PriceUtils.formatPrice(total, window.priceFormat);
            } else {
                var curency = $.extend({}, window.priceFormat);
                curency.pattern = '';
                total = PriceUtils.formatPrice(total, curency);
            }
            return total;
        }
    });
});