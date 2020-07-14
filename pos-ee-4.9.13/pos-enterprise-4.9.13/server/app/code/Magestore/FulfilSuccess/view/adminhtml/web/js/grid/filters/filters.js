/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/filters/filters'
], function ($, Filter) {
    'use strict';
    return Filter.extend({
        clear: function (filter) {
            filter ?
                filter.clear() :
                _.invoke(this.active, 'clear');
            if($('#anonymous_filterSearch_component_0') != undefined){
                $('#anonymous_filterSearch_component_0').val('');
            }
            this.apply();

            return this;
        }
    });
});
