/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/element/multiselect'
], function (registry, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.type:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            if(value === 'jewelry') {
                this.visible(false);
            } else {
                this.visible(true);
            }
        }
    });
});

