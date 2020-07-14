/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_BarcodeSuccess/js/form/element/barcode/template/preview',
    'jquery'
], function (Preview, $) {
    'use strict';

    return Preview.extend({
        initData: function(){
            var self = this;
            self.data(self.source.data);
            if(self.data()[self.index]){
                self.url(self.data()[self.index]);
            }else{
                if(self.url()){
                    self.data()[self.index] = self.url();
                }
            }
            self.data()['is_print_preview'] = true;
        },
        afterRender: function(){
            var self = this;
            if($(".form-inline div[data-index='barcode_template_information'] select[name='type']").length > 0){
                self.useDefault();
                $(".form-inline div[data-index='barcode_template_information'] select[name='type']").change(function(){
                    self.useDefault();
                });
            }
        },
        useDefault: function(){
            var self = this;
            self.previewTemplate();
        }
    });
});
