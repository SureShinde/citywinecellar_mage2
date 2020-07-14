/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/grid/export',
    'uiRegistry'
], function (_, Element, registry) {
    'use strict';

    return Element.extend({
        getParams: function () {
            var selections = this.selections(),
                data = selections ? selections.getSelections() : null,
                itemsType,
                result = {};

            if (data) {
                itemsType = data.excludeMode ? 'excluded' : 'selected';
                result.filters = data.params.filters;
                result.search = data.params.search;
                result.namespace = data.params.namespace;
                result[itemsType] = data[itemsType];

                if (!result[itemsType].length) {
                    result[itemsType] = false;
                }
            }
            if(registry.get(selections.provider))
            {
                _.extend(result, result, registry.get(selections.provider).params);
            }

            return result;
        }
    });
});
