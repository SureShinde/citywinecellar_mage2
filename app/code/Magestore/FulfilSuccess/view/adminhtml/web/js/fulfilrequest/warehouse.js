/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'mage/translate',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/confirm'
], function (_, $t, ElementSelect, Confirm) {
    'use strict';
    
    return ElementSelect.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input',
            elementTmpl: 'Magestore_FulfilSuccess/fulfilrequest/warehouse',
            actionUrl: '',
            userClick: false,
        },
        
        /**
         * Use click on Warehouse Selector
         */
        beforeChange: function(data, event) {
            this.userClick = true;       
            this.previousIndex = event.target.selectedIndex;
        },
        
        /**
         *  Callback when value is changed by user
         */
        changeWarehouse: function(data, event) {
            var element = event.target;
            var self = this;
            var selectedIndex = event.target.selectedIndex;
            var selectedWarehouse = this.options()[selectedIndex].value;
            var confirmMessage = $t('Are you sure to change the working Warehouse?');
            if (this.isMSIEnable) {
                confirmMessage = $t('Are you sure to change the working Source?');
            }
            if(this.userClick) {
                var url = this.actionUrl + '?warehouse_id=' + selectedWarehouse;
                Confirm({
                    content: confirmMessage,
                    actions: {
                        confirm: function () {
                            location.href= url;
                        },
                        cancel: function () {
                            element.selectedIndex = self.previousIndex;
                            return false;
                        },
                        always: function () {
                            return false;
                        }
                    }
                });                
            }
        }
    });
});