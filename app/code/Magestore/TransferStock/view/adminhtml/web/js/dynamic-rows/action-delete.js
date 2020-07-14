/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/dynamic-rows/action-delete',
    'uiRegistry'
], function (Abstract, registry) {
    'use strict';

    return Abstract.extend({
        defaults: {
            imports: {
                canVisibleOnForm: "${$.provider}:data.transfer_summary.general_information.stage"
            },

            canVisibleOnForm: function (value) {
                let status = registry.get(this.provider).data.transfer_summary.general_information.status;
                if(value === 'new' && status === 'open') {
                    this.visible(true);
                } else {
                    this.visible(false);
                }
            }
        }
    });
});
