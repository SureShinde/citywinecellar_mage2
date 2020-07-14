/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/abstract',
    'Magestore_BarcodeSuccess/js/full-screen-loader',
    'Magestore_BarcodeSuccess/js/alert',
    'mage/translate'
], function (_, $, ko, Abstract, fullScreenLoader, Alert, Translate) {
    'use strict';

    return Abstract.extend({
        url: ko.observable(),
        content: ko.observable(),
        data: ko.observable(),
        defaultData: ko.observable(),
        initialize: function () {
            this._super();
        },
        initData: function(){
            var self = this;
            self.data(self.source.data);
            if(self.data()[self.index]){
                if(typeof self.data()[self.index] == "string"){
                    self.data()[self.index] = JSON.parse(self.data()[self.index]);
                }
                self.url(self.data()[self.index]['url']);
                self.defaultData(self.data()[self.index]['default']);
            }else{
                self.data()[self.index] = {};
                if(self.url()){
                    self.data()[self.index]['url'] = self.url();
                }
                if(self.defaultData()){
                    self.data()[self.index]['default'] = self.defaultData();
                }
            }
        },
        afterRender: function(){
            var self = this;
            if($(".form-inline div[data-index='barcode_template_information'] select[name='type']").length > 0){
                $(".form-inline div[data-index='barcode_template_information'] select[name='type']").change(function(){
                    self.useDefault();
                });
            }
        },
        useDefault: function(){
            var self = this;
            self.fillDefaultData();
            self.previewTemplate();
        },
        fillDefaultData: function(){
            var self = this;
            self.initData();
            if(self.data()['type'] && self.defaultData()[self.data()['type']]){
                _.forEach(self.defaultData()[self.data()['type']], function(value,key){
                    if(key != 'type'){
                        self.data()[key] = value;
                        $("input[name='"+key+"']").val(value).trigger("change");
                        $("select[name='"+key+"']").val(value).trigger("change");
                    }
                })
            }
        },
        previewTemplate: function(callback){
            var self = this;
            self.initData();
            if(self.data() && self.url()){
                var url = self.url();
                var data = self.data();
                delete data[self.index];
                var params = {data:data};

                fullScreenLoader.startLoader();
                $.ajax({
                    url: url,
                    data: params,
                    success: function(result){
                        fullScreenLoader.stopLoader();
                        self.processResponse(result, callback);
                    },
                    error: function(error){
                        fullScreenLoader.stopLoader();
                    }
                });
            }else{
                Alert('Error',Translate('Cannot find the data for ')+this.label);
            }
        },
        processResponse: function(response, callback){
            var self = this;
            if(response.success && response.html){
                self.content(response.html);
                if(typeof callback == "function"){
                    setTimeout(function(){
                        callback();
                    },500)
                }
            }
            if(response.error && response.messages){
                Alert('Error',response.messages);
            }
        },
        printTemplate: function(){
            var self = this;
            if(self.content()){
                if (self.content().indexOf('Zend_Barcode') < 0) {
                    var print_window = window.open('', 'print', 'status=1,width=500,height=500');
                    if(print_window){
                        print_window.document.open();
                        print_window.document.write(self.content());
                        print_window.document.close();
                        print_window.print();
                    }else{
                        Alert('Message','Your browser has blocked the automatic popup, please change your browser settings or print the receipt manually');
                    }
                }
            }else{
                self.previewTemplate($.proxy(self.printTemplate,self));
            }
        }
    });
});
