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
            imports: {
                canVisibleOnForm: "${$.provider}:data.transfer_summary.general_information.stage"
            }
        },

        canVisibleOnForm: function(value) {
            let status = registry.get(this.provider).data.transfer_summary.general_information.status;
            if(value === 'new' && status === 'open') {
                this.visible(true);
            } else {
                this.visible(false);
            }
        }
    });
});
