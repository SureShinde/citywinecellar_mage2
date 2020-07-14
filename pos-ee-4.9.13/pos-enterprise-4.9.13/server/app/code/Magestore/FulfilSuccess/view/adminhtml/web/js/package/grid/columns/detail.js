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
        defaults: {
            modalId: 'package_detail_holder',
            itemKey: 'package_id'
        },
        /**
         * Default action callback. Redirects to
         * the specified in actions' data url.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @param {(Number|String)} recordId - Id of the record associated
         *      with a specified action.
         * @param {Object} action - Actions' data.
         */
        defaultCallback: function (actionIndex, recordId, action) {
            var modalHtml = $('#' + this.modalId);
            if(!modalHtml) {
                return;
            }
            /**
             * Set content of modal to blank
             */
            modalHtml.html('');
            var postData = {};
            postData[this.itemKey] = recordId;

            $.ajax({
                showLoader: true,
                method: "POST",
                url: action.url,
                data: postData
            }).done(function (data) {
                modalHtml.html(data);
            });

            modalHtml.modal('openModal');
        }

    });
});
