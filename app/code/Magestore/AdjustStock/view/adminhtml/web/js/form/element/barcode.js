/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/text',
], function (_, Text) {
    'use strict';

    return Text.extend({
        defaults: {
            template: 'Magestore_AdjustStock/dynamic-rows/cells/barcode',
        },
        modifyData: function (data) {
            if (typeof data() !== 'undefined' && data()) {
                let arr = data().toString().split(',');
                return arr;
            }
        }
    });
});
