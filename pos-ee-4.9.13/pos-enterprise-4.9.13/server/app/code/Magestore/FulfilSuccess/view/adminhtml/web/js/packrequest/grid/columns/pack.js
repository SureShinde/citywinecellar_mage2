/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_FulfilSuccess/js/grid/columns/detail',
    'jquery',
], function (Column, $) {
    'use strict';

    return Column.extend({
        defaults: {
            modalId: 'pack_request_detail_holder',
            itemKey: 'pack_request_id'
        },
        preview: function (row) {
            var self = this;
            var modalHtml = $('#' + this.modalId);
            if(!modalHtml) {
                return;
            }
            /**
             * Set content of modal to blank
             */
            modalHtml.html('');
            var postData = {};
            postData[this.itemKey] = this.getItemId(row);

            $.ajax({
                showLoader: true,
                method: "POST",
                url: this.getUrl(row),
                data: postData
            }).done(function (data) {
                modalHtml.html(data);
            });
        },
    });
});