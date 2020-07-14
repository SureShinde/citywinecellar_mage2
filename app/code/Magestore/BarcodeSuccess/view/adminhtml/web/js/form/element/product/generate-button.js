/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/text',
    'mageUtils',
    'uiRegistry',
    'jquery',
    'Magestore_BarcodeSuccess/js/alert',
    'Magestore_BarcodeSuccess/js/full-screen-loader'
], function (Element, utils, registry, $, Alert, fullScreenLoader) {
    'use strict';

    return Element.extend({
        defaults: {
            visible: true,
            label: '',
            labelVisible: true,
            error: '',
            uid: utils.uniqueid(),
            disabled: false,
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            },
            elementTmpl: 'Magestore_BarcodeSuccess/form/element/product/generate-button',
            content: '',
            url: '',
            barcodeElement: '',
            barcodeTemplateElement: '',
            productId: ''
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe('disabled visible value')
                .observe(['content']);
            return this;
        },

        clickButton: function(){
            var self = this;
            fullScreenLoader.startLoader();
            $.ajax({
                url: this.url,
                method: 'POST',
                data: {
                    'product_id': this.productId
                },
                success: function (response) {
                    if (response) {
                        window.location.reload();
                    } else {
                        fullScreenLoader.stopLoader();
                    }
                },
                fail: function (response) {
                    fullScreenLoader.startLoader();
                }
            });
        },
    });
});
