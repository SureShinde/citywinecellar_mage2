/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/columns/actions'
], function ($, Actions) {
    'use strict';

    return Actions.extend({
      
        /**
         * Default action callback. Redirects to
         * the specified in actions' data url.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @param {(Number|String)} recordId - Id of the record accociated
         *      with a specfied action.
         * @param {Object} action - Actions' data.
         */
        defaultCallback: function (actionIndex, recordId, action) {
            var self = this;
            window.open(action.url,'','height=600, width=800, top=50, left=50, scrollable=yes, menubar=yes, resizable=yes');
        },

    });
});
