/*
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/components/fieldset'
], function (Fieldset) {
    'use strict';

    return Fieldset.extend({
            defaults: {
                template: 'Magestore_ReportSuccess/form/stock-by-location/select-location',
                collapsible: false,
                changed: false,
                loading: false,
                error: false,
                opened: false,
                level: 0,
                visible: true,
                disabled: false,
                additionalClasses: {}
            },
        }
    );
});
