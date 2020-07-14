/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/form/element/text',
    'mage/translate',
    'Magestore_BarcodeSuccess/js/alert',
    'Magestore_BarcodeSuccess/js/full-screen-loader'
], function ($, ko, _, Text, Translate, Alert, fullScreenLoader) {
    'use strict';

    return Text.extend({
        qty: ko.observable(),
        setQty: function(el, event){
            this.qty(event.tatget.value);
        },
        getData: function(){
            var data = {};
            if(this.source && this.source.data && this.source.data[this.index]){
                var datasource = JSON.parse(this.source.data[this.index]);
                data.params = datasource.params;
                data.params.qty = this.qty();
                data.url = datasource.url;
                if(typeof this.source.data['type'] != 'undefined'){
                    data.params.type = this.source.data['type'];
                }
            }
            return data;
        },
        getHtml: function(){
            var data = this.getData();
            if(data.url){
                var params = data.params;
                var url = data.url;
                var self = this;
                fullScreenLoader.startLoader();
                $.ajax({
                    url: url,
                    data: params,
                    success: function(result){
                        fullScreenLoader.stopLoader();
                        self.processResponse(result);
                    },
                    error: function(error){
                        fullScreenLoader.stopLoader();
                    }
                });
            }else{
                Alert('Error',Translate('Cannot find the data for "')+this.title()+'"');
            }
        },
        processResponse: function(response){
            var self = this;
            if(response.success && response.html){
                self.print(response.html);
            }
            if(response.error && response.messages){
                Alert('Error',response.messages);
            }
        },
        print: function(html){
            var print_window = window.open('', 'print', 'status=1,width=500,height=500');
            if(print_window){
                print_window.document.open();
                print_window.document.write(html);
                print_window.document.close();
                print_window.addEventListener('load',function(){
                    print_window.print();
                });
            }else{
                Alert('Message','Your browser has blocked the automatic popup, please change your browser settings or print the receipt manually');
            }
        }
    });
});
