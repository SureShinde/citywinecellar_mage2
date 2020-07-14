/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*jshint browser:true jquery:true*/
/*global alert*/
define([
        'Magento_Ui/js/modal/alert',
        'mage/translate'
    ], function (Alert, Translate) {
        'use strict';

        return function(title, message){
            Alert({
                title: Translate(title),
                content: Translate(message),
                autoOpen: true,
                clickableOverlay: true,
                focus: "",
                actions: {
                    always: function(){
                    }
                }
            });
        }
    }
);
