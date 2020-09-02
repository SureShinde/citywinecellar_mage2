/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'Magento_Ui/js/modal/modal'
], function (Column, $, modal) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            },
            modalId: 'item_detail_holder',
            itemKey: 'item_id',
        },
        getItemId: function (row) {
            return row[this.index + '_' + this.itemKey];
        },
        getLabel: function (row) {
            return row[this.index + '_html'];
        },
        getTitle: function (row) {
            return row[this.index + '_title'];
        },
        getUrl: function (row) {
            return row[this.index + '_url'];
        },
        preview: function (row) {
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

            modalHtml.modal('openModal');
        },
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});