/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template',
    'text!Magestore_OrderSuccess/template/grid/cells/note/preview.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, notePreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magestore_OrderSuccess/grid/cells/note',
            fieldClass: {
                'data-grid-note-cell': true
            }
        },
        getSrc: function (row) {
            return row[this.index + '_src']
        },
        getOrigSrc: function (row) {
            return row[this.index + '_orig_src'];
        },
        getHtmlContent: function (row) {
            return row[this.index + '_content'];
        },
        getLink: function (row) {
            return row[this.index + '_link'];
        },
        getAlt: function (row) {
            return row[this.index + '_alt']
        },
        isPreviewAvailable: function() {
            return this.has_preview || false;
        },
        preview: function (row) {
            // alert(this.getHtmlContent(row));
            var modalHtml = mageTemplate(
                notePreviewTemplate,
                {
                    html_content: unescape(this.getHtmlContent(row)),
                    src: 'Notes for this Sales',
                    alt: 'Notes for this Sales',
                    linkText: $.mage.__('Notes for this Sales')
                }
            );
            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                title: this.getAlt(row),
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []}).trigger('openModal');
        },
        getFieldHandler: function (row) {
            if (this.isPreviewAvailable()) {
                return this.preview.bind(this, row);
            }
        }
    });
});
