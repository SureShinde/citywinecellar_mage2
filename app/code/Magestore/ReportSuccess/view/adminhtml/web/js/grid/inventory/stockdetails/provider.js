/*
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magestore_ReportSuccess/js/grid/inventory/provider'
], function ($, ko, Provider) {
    'use strict';

    return Provider.extend({
        listTotal: [
            {id: 'qty_on_hand', key: 'qty_on_hand'},
            {id: 'available_qty', key: 'available_qty'},
            {id: 'qty_to_ship', key: 'qty_to_ship'},
            {id: 'incoming_qty', key: 'incoming_qty'}
        ],
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
                if(value.key === 'incoming_qty' && !window.isPurchaseOrderEnable) {
                    $('#' + value.id).hide();
                }
                $('#' + value.id + ' .count').html(self.bindDataForTotal(data, value.key));
            });

            return data;
        }
    });
});