/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';
    return Abstract.extend({
        defaults: {
            elementTmpl: 'Magestore_Webpos/form/element/location-grid'
        }
    })
});
