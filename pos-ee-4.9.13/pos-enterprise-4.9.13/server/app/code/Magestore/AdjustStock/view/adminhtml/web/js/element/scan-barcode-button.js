/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/button'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            elementTmpl: 'Magestore_AdjustStock/element/scan-barcode-button'
        },

        handleOnclick: function () {
            $('#adjustment-barcode-field').toggle();
            $('#adjustment-barcode-input').focus();
        }
    });
});
