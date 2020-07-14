/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/text',
    'mageUtils',
    'uiRegistry'
], function (Element, utils, registry) {
    'use strict';

    return Element.extend({
        defaults: {
            visible: true,
            label: '',
            labelVisible: true,
            error: '',
            uid: utils.uniqueid(),
            disabled: false,
            loading: false,
            value: '',
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            },
            elementTmpl: 'Magestore_BarcodeSuccess/form/element/product/print-button',
            previewButton: ''
        },
        
        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe('disabled visible value');
            return this;
        },
        
        print: function(){
            if(this.previewButton){
                registry.get(this.previewButton).previewTemplate('print', this.value());
            }
        }
    });
});
