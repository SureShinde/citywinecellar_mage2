/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'mageUtils',
    'Magento_Ui/js/grid/tree-massactions',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (_, registry, utils, TreeMassactions, confirm, alert, $t) {
    'use strict';

    return TreeMassactions.extend({
      
        /**
         * Default action callback. Sends selections data
         * via POST request.
         *
         * @param {Object} action - Action data.
         * @param {Object} data - Selections data.
         */
        defaultCallback: function (action, data) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                selections = {};

            selections[itemsType] = data[itemsType];

            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }
            
            _.extend(selections, data.params || {});
            
            if(action.popup) {
                window.open('', 'popupform', 'scrollbars=no, menubar=no, height=600,width=1024, resizable=no,toolbar=no,status=no');

                utils.submit({
                    url: action.url,
                    data: selections
                },{target: 'popupform'});
                
            } else {
                utils.submit({
                    url: action.url,
                    data: selections
                });                
            }
        }
    });
});
