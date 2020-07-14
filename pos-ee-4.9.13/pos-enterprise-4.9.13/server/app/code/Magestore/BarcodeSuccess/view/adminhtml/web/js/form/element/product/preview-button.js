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
            elementTmpl: 'Magestore_BarcodeSuccess/form/element/product/preview-button',
            content: '',
            url: '',
            barcodeElement: '',
            barcodeTemplateElement: ''
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

        previewTemplate: function(callback, qty){
            var self = this;
            fullScreenLoader.startLoader();
            $.ajax({
                url: this.url,
                data: {
                    barcode: this.barcodeElement.value(),
                    data: {
                        is_print_preview: 'true', 
                        type: this.barcodeTemplateElement.value()
                    },
                    qty: qty?qty:null
                },
                success: function(result){
                    fullScreenLoader.stopLoader();
                    if(result.error && result.messages){
                        Alert('Error',result.messages);
                    }
                    this[callback](result);
                }.bind(this),
                error: function(error){
                    fullScreenLoader.stopLoader();
                }
            });
        },

        preview: function(response){
            if(response.success && response.html){
                this.content(response.html);
            }
        },
        
        print: function(response){
            if(response.success && response.html){
                var print_window = window.open('', 'print', 'status=1,width=500,height=500');
                if(print_window){
                    print_window.document.open();
                    print_window.document.write(response.html);
                    print_window.document.close();
                    print_window.print();
                }else{
                    Alert('Message','Your browser has blocked the automatic popup, please change your browser settings or print the receipt manually');
                }
            }
        },

        afterRender: function(){
            this.barcodeElement = registry.get(this.barcodeElement);
            this.barcodeTemplateElement = registry.get(this.barcodeTemplateElement);
            this.previewTemplate('preview');
        },

        clickButton: function(){
            this.previewTemplate('preview');
        },
    });
});
