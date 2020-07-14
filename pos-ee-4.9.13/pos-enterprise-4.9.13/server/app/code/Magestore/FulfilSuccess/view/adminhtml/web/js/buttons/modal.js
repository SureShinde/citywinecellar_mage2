/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/button'
], function (jQuery, Button) {
    'use strict';

    return Button.extend({
        /**
         * Performs configured actions
         */
        action: function () {
            console.log(jQuery('#' + this.modal).html());
            
            jQuery('#' + this.modal).modal('openModal');
            console.log(this.modal);
            return;
            this.actions.forEach(this.applyAction, this);
        },

    });
});
