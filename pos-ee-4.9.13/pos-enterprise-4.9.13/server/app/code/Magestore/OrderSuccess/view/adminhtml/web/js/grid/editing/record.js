/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/editing/record'
], function (Record) {
    'use strict';

    return Record.extend({
        defaults: {
            templates: {
                fields: {
                    tag: {
                        component: 'Magestore_OrderSuccess/js/form/element/tag',
                        template: 'Magestore_OrderSuccess/form/element/tag',
                        options: '${ JSON.stringify($.$data.column.options) }'
                    },
                    batch: {
                        component: 'Magestore_OrderSuccess/js/form/element/batch',
                        template: 'Magestore_OrderSuccess/form/element/batch',
                        options: '${ JSON.stringify($.$data.column.options) }'
                    },
                    note: {
                        component: 'Magestore_OrderSuccess/js/form/element/note',
                        template: 'Magestore_OrderSuccess/form/element/note'
                    }
                }
            },
        },
    });
});
