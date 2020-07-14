/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/columns/column'
], function ($, Column) {
    'use strict';

    return Column.extend({
        /**
         * Ment to preprocess data associated with a current columns' field.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {String}
         */
        getLabel: function (record) {
            var age = Number(record[this.index]);
            var hoursPerDay = 24;
            var days = Math.floor(age / (hoursPerDay * 3600));
            var hours = Math.floor(age / 3600) % hoursPerDay;
            var mins = Math.round(age / 60) % 60;
            var text = '';
            if(days > 0) {
                text += days + 'd ';
            }
            text += hours + 'h ';
            text += mins + 'm';
            return text;
        }
    });
});
