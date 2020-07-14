/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*jshint browser:true */
define([
    'jquery',
    'ko',
    'Magestore_BarcodeSuccess/js/full-screen-loader',
    'Magestore_BarcodeSuccess/js/alert',
    'mage/translate'
], function ($, ko, fullScreenLoader, Alert, Translate) {
    'use strict';
    $.widget('mage.osBarcodeScan', {
        /**
         * Create widget
         * @private
         */
        _create: function () {
            this.detailContainer = this.element.find('#detailContainer');
            this.scanInput = this.element.find('#os_barcode_scan');
            this.actionsEls = this.element.find('[data-role=action]');
            this.dataEls = this.element.find('[data-role=data]');
            this.hasResult = ko.observable(false);
            this.result = ko.observable();
            this.template = ko.observable();
            this.qty = ko.observable();
            this._bindEvent();
        },
        _bindEvent: function () {
            var self = this;
            if(self.actionsEls && self.actionsEls.length > 0){
                $.each(self.actionsEls,function(){
                    var element = this;
                    var actionName = element.getAttribute('data-key');
                    var eventName = element.getAttribute('data-type');
                    var renderOptionUrl = element.getAttribute('data-options');
                    switch(eventName){
                        case "click":
                            element.onclick = function(){
                                if(self[actionName]){
                                    self[actionName]();
                                }
                            }
                            break;
                        case "change":
                            element.onchange = function(event){
                                if(self[actionName]){
                                    self[actionName](event);
                                }
                            }
                            break;
                    }
                    if(renderOptionUrl && typeof self.options.urls[renderOptionUrl] != 'undefined'){
                        fullScreenLoader.startLoader();
                        $.ajax({
                            url: self.options.urls[renderOptionUrl],
                            data:{daniel:"ahihi"},
                            success: function(options){
                                if(options.html){
                                    element.innerHTML = options.html;
                                }
                                if(options.messages){
                                    Alert('Error',options.messages);
                                }
                                fullScreenLoader.stopLoader();
                            },
                            error: function(error){
                                fullScreenLoader.stopLoader();
                            }
                        });
                    }
                });
            }
            if(self.scanInput){
                self.scanInput.change(function(event){
                    self.scanBarcode(event.target.value);
                });
            }
        },
        processResponse: function(response){
            var self = this;
            if(response.success && response.data){
                if(self.dataEls && self.dataEls.length > 0){
                    self.result(response.data);
                    $.each(self.dataEls,function(){
                        var key = this.getAttribute('data-key');
                        var type = this.getAttribute('data-type');
                        if(response.data[key]){
                            if(type == 'text'){
                                this.innerHTML = response.data[key];
                            }
                            if(type == 'image'){
                                this.setAttribute('src', response.data[key]);
                            }
                            if(type == 'warehouses_stock'){
                                var qty_html = self.generateQtyHtml(response.data[key]);
                                this.innerHTML = qty_html;
                            }
                        }
                    });
                }
            }
            if(response.error && response.messages){
                Alert('Error',response.messages);
            }
            if(response.redirect){
                window.location = response.redirect;
            }
        },
        scanBarcode: function(barcode){
            var self = this;
            if(!barcode){
                return false;
            }
            fullScreenLoader.startLoader();
            var barcode = barcode.trim();
            var params = {
                barcode:barcode
            };
            $.ajax({
                url: self.options.urls.get_barcode_data_url,
                data:params,
                success: function(result){
                    self.scanInput.val("");
                    fullScreenLoader.stopLoader();
                    self.processResponse(result);
                },
                error: function(error){
                    self.scanInput.val("");
                    fullScreenLoader.stopLoader();
                }
            });
        },
        print: function(){
            var self = this;
            if(self.result()){
                var data = self.result();
                if(typeof self.options.urls.print_barcode_url != 'undefined'){
                    var qty = (self.qty())?self.qty():0;
                    if(parseFloat(qty) <= 0 || !$.isNumeric(qty)){
                        Alert('Error','Please enter valid qty number');
                        return;
                    }
                    var type = self.template() ? self.template() : 1;
                    var params = {
                        type,
                        qty:qty,
                        selected:data.id
                    };
                    fullScreenLoader.startLoader();
                    $.ajax({
                        url: self.options.urls.print_barcode_url,
                        data:params,
                        success: function(result){
                            if (result.error && result.messages) {
                                Alert('Error', result.messages);
                            } else if (result.html){
                                self.opentPrintWindow(result.html);
                            }
                            fullScreenLoader.stopLoader();
                        },
                        error: function(error){
                            fullScreenLoader.stopLoader();
                        }
                    });
                }
            }else{
                Alert('Warning','Please scan the barcode first');
            }
        },
        showDetail: function(){
            var self = this;
            if(self.result()){
                var data = self.result();
                if(data.more_detail_url){
                    window.location = data.more_detail_url;
                }
            }else{
                Alert('Warning','Please scan the barcode first');
            }
        },
        generateQtyHtml: function(stock_data){
            var html ="<table class='warehouse_qty_table'>";
            html +="<thead><th class='text-center'>"+Translate('Warehouse')+"</th><th class='text-center'>"+Translate('Available Qty')+"</th><th class='text-center'>"+Translate('Qty to Ship')+"</th><th class='text-center'>"+Translate('Qty In Warehouse')+"</th><th class='text-center'>"+Translate('Shelf Location')+"</th></thead>";
            html +="<tbody>";
            $.each(stock_data, function(index, data){
                if(index != 'all'){
                    html += "<tr>";
                    html += "<td class='text-left'><a href='"+data.link+"'>"+data.name+"</a></td>";
                    html += "<td class='text-center'>"+data.available_qty+"</td>";
                    html += "<td class='text-center'>"+data.qty_to_ship+"</td>";
                    html += "<td class='text-center'>"+data.total_qty+"</td>";
                    html += "<td class='text-center'>"+data.shelf_location+"</td>";
                    html += "</tr>";
                }else{
                    html += "<tr>";
                    html += "<td class='text-left'>"+data.name+"</td>";
                    html += "<td class='text-center'>"+data.available_qty+"</td>";
                    html += "<td class='text-center'>"+data.qty_to_ship+"</td>";
                    html += "<td class='text-center'>"+data.total_qty+"</td>";
                    html += "<td class='text-center'></td>";
                    html += "</tr>";
                }
            });
            html +="</tbody>";
            html +="</table>" ;
            return html;
        },
        opentPrintWindow: function(html){
            var print_window = window.open('', 'print', 'status=1,width=500,height=700');
            if(print_window){
                print_window.document.open();
                print_window.document.write(html);
                print_window.document.close();
                print_window.addEventListener('load',function () {
                    print_window.print();
                });
            }else{
                Alert('Message','Your browser has blocked the automatic popup, please change your browser settings or print the receipt manually');
            }
        },
        setQty: function(event){
            if(parseFloat(event.target.value) <= 0 || !$.isNumeric(event.target.value)){
                Alert('Error','Please enter valid qty number');
                return;
            }
            this.qty(event.target.value);
        },
        setTemplate: function(event){
            this.template(event.target.value);
        }
    });

    return $.mage.osBarcodeScan;
});
