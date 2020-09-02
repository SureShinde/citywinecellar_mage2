/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Ui/js/grid/filters/filters'
], function ($, Filters) {
    'use strict';

    return Filters.extend({
        defaults: {
            templates: {
                filters: {
                    filterTag: {
                        component: 'Magestore_OrderSuccess/js/form/element/tag_filters',
                        template: 'ui/grid/filters/field',
                        options: '${ JSON.stringify($.$data.column.options)}',
                        caption: ' ',
                        elementTmpl: 'Magestore_OrderSuccess/form/element/tag_filters'
                    },
                    filterBatch: {
                        component: 'Magestore_OrderSuccess/js/form/element/batch_filters',
                        template: 'ui/grid/filters/field',
                        options: '${ JSON.stringify($.$data.column.options)}',
                        caption: ' ',
                        elementTmpl: 'Magestore_OrderSuccess/form/element/batch_filters'
                    }
                }
            }
        }
    });
});
