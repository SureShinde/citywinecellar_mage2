/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/search/search'
], function ($, Search) {
    'use strict';

    return Search.extend({
        apply: function (value) {
            value = value || this.inputValue;
            $('.admin__control-text').each(function(index){
                if(this.getAttribute('name') == 'actions') {
                    this.value = value;
                    var event = new Event('change');
                    this.dispatchEvent(event);
                }
            });

            $('.action-secondary').each(function(index){
                if(this.getAttribute('data-action') == 'grid-filter-apply') {
                    this.click();
                }
            });
        }
    });
});
