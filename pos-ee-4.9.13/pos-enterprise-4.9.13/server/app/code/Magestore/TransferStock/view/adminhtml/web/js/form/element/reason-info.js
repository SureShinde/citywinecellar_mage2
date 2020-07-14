/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry'
], function (Abstract, registry) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Magestore_TransferStock/form/element/html',
            imports: {
                canVisibleOnForm: "${$.provider}:data.transfer_summary.general_information.stage"
            },

            canVisibleOnForm: function (value) {
                let status = registry.get(this.provider).data.transfer_summary.general_information.status;
                if(value === 'new' && status === 'open') {
                    this.visible(false);
                } else {
                    this.visible(true);
                }
            }
        }
    });
});
