/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/select',
    'ko'
], function (Select, ko) {
    'use strict';

    return Select.extend({
        optionsAfterRender: function (option, item) {
           if(item != undefined) {
               if(item.value == 'remove' || item.value == 'na') {
                   ko.applyBindingsToNode(option, {style: {background: '#FFFFFF'}}, item);
               }else {
                   ko.applyBindingsToNode(option, {style: {background: item.value}}, item);
               }
            }
        },
        setSelectColor: function (item, event) {
            if(event.target.value != undefined)
                event.target.style='background-color:'+event.target.value;
        }
    });
});
