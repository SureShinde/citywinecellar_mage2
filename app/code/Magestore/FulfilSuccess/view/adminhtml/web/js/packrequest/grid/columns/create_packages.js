/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/columns/actions',
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
            var postData = {};
            postData['pack_request_id'] = action.itemid;

            $.ajax({
                showLoader: true,
                method: "POST",
                url: action.callback,
                data: postData
            }).done(function (data) {
                packaging.showWindow();
            });
        },

    });
});
