/*
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/grid/filters/filters'
], function (filters) {
    'use strict';

    return filters.extend({
        defaults: {
            templates: {
                filters: {
                    select: {
                        component: 'Magento_Ui/js/form/element/ui-select',
                        template: 'ui/grid/filters/elements/ui-select',
                        options: '${ JSON.stringify($.$data.column.options) }',
                        selectedPlaceholders :
                            {
                                defaultPlaceholder: '${ $.$data.column.caption }'
                            },
                        filterOptions: '${ $.$data.column.filterOptions }',
                        multiple: '${ $.$data.column.multiple }'
                    }
                }
            }
        }
    });
});
