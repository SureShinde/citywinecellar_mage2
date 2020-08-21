/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/components/insert-listing',
    'uiRegistry'
], function (InsertListing, uiRegistry) {
    'use strict';

    return InsertListing.extend({

        /**
         * Validate filter form
         *
         * @returns {*}
         */
        validateFilterParams: function () {
            var self = this;
            let form = uiRegistry.get(self.parentName);
            if (form && (typeof form.validate != "undefined")) {
                form.validate();
                if (!form.additionalInvalid && !form.source.get('params.invalid')) {
                    return true;
                } else {
                    form.focusInvalid();
                }
            }
            return false;
        },

        /**
         * Show reports by filter data
         */
        showReport: function () {
            var self = this;
            if (self.validateFilterParams() && self.externalSource()) {
                self.externalSource().applyReportFilters();
            }
        },
    });
});
