/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/button',
    'uiRegistry'
], function ($, Component, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            elementTmpl: 'Magestore_TransferStock/element/scan-barcode-button'
        },

        handleOnclick: function () {
            $('#receive-transfer-barcode-field').toggle();
            $('#receive-transfer-barcode-input').focus();
        }
    });
});
