/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/grid/provider'
], function (_, GridProvider) {
    'use strict';

    return GridProvider.extend({
        defaults: {
            reportFilters: {}
        },

        /**
         * Apply filters after click Show Report button
         */
        applyReportFilters: function () {
            this.set('params.reportFilters', {});
            this.set('params.reportFilters', this.reportFilters);
        }

    });
});
