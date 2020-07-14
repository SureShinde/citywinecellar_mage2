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
            imports: {
                canVisibleOnForm: "${$.provider}:data.transfer_summary.general_information.stage"
            },
        },

        canVisibleOnForm: function (value) {
            let status = registry.get(this.provider).data.transfer_summary.general_information.status;
            if(value === 'new' && status === 'open') {
                this.visible(true);
            } else {
                this.visible(false);
            }
        }
    });
});
