/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magestore_Appadmin/js/grid/filters/elements/ui-select-text'
], function (uiSelectText) {
    return uiSelectText.extend({
        textField: ['location_ids', 'staff_id']
    });
});
