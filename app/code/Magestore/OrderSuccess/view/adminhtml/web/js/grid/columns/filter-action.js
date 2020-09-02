/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/columns/actions',
], function ($, Actions) {
    'use strict';

    return Actions.extend({
      
        /**
         * Default action callback. Redirects to
         * the specified in actions' data url.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @param {(Number|String)} recordId - Id of the record accociated
         *      with a specfied action.
         * @param {Object} action - Actions' data.
         */
        defaultCallback: function (actionIndex, recordId, action) {
            var self = this;
            $('.admin__control-select').each(function(index){
                if(this.getAttribute('name') == self.index) {
                    this.value = action.itemid;
                    var event = new Event('change');
                    this.dispatchEvent(event);
                }
            });
            
            $('.action-secondary').each(function(index){
                if(this.getAttribute('data-action') == 'grid-filter-apply') {
                    this.click();
                }
            });
        },

    });
});
