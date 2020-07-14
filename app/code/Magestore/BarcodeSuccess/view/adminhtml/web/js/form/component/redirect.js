/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/components/button',
    'Magestore_BarcodeSuccess/js/alert'
], function (Button, Alert) {
    'use strict';

    return Button.extend({
        initialize: function () {
            this._super();
        },
        redirect: function(){
            if(this.source && this.source.data && this.source.data[this.index]){
                window.location = this.source.data[this.index];
            }else{
                Alert('Error',Translate('Cannot find the redirect URL for "')+this.title()+'"');
            }
        }
    });
});
