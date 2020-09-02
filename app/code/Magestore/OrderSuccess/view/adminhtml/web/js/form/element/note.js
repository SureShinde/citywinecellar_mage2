/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'mage/translate'
], function (Abstract, __) {
    'use strict';

    return Abstract.extend({
        defaults: {
            placeholder: __('Add Note'),
            visible: false
        },
        removeValueAfterRender: function (element) {
            element.value = '';
        }
    });
});
