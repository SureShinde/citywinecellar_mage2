/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/text',
    'uiRegistry',
], function (_, Text, registry) {
    'use strict';

    return Text.extend({
        defaults: {
            template: 'Magestore_TransferStock/dynamic-rows/cells/barcode',
        },
        modifyData: function (data) {
            if (typeof data() !== 'undefined' && data()) {
                let arr = data().toString().split(',');
                return arr;
            }
        }
    });
});
