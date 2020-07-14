/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'mage/translate',
    'Magento_Ui/js/grid/columns/multiselect'
], function (_, $t, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            headerTmpl: 'Magestore_PurchaseOrderSuccess/grid/columns/multiselect',
        },

        /**
         * Selects or deselects all records.
         *
         * @returns {Multiselect} Chainable.
         */
        toggleSelectAll: function () {
            if(this.isPageSelected(true)){
                this.deselectPage();
            }else{
                this.selectPage();
            }
            return this;
        },
    });
});
