/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return {
        CLASS:{
            NOT_NEGATIVE: 'not-negative',
            DECREASE_BUTTON: 'os-decrease-button',
            INCREASE_BUTTON: 'os-increase-button'
        },
        DATA:{
            MAX:'max',
            MIN:'min',
            INCREMENT:'increment'
        },
        DEFAULT:{
            INCREMENT:1
        },
        init: function(selector){
            var self = this;
            if($(selector).length > 0){
                $(selector).each(function(){
                    self.initHtml(this);
                });
                $(selector).change(function(){
                    self.change(this);
                });
                $(selector).focus(function(){
                    self.focus(this);
                });
                self.initEvent();
            }
        },
        initHtml: function(el){
            if(el){
                var self = this;
                var elmentId = el.id;
                var decreaseButton = "<button type='button' class='"+self.CLASS.DECREASE_BUTTON+" btn-decrs' data-target='"+elmentId+"'>-</button>";
                var increaseButton = "<button type='button' class='"+self.CLASS.INCREASE_BUTTON+" btn-incrs' data-target='"+elmentId+"'>+</button>";
                if($(el).parent().children('.btn-decrs').length == 0){
                    $(el).before(decreaseButton);
                }
                if($(el).parent().children('.btn-incrs').length == 0){
                    $(el).after(increaseButton);
                }
            }
        },
        initEvent: function(){
            var self = this;
            if($('.'+self.CLASS.DECREASE_BUTTON).length > 0){
                $('.'+self.CLASS.DECREASE_BUTTON).click(function(){
                    var target = $(this).data('target');
                    self.decrease('#'+target);
                });
            }
            if($('.'+self.CLASS.INCREASE_BUTTON).length > 0){
                $('.'+self.CLASS.INCREASE_BUTTON).click(function(){
                    var target = $(this).data('target');
                    self.increase('#'+target);
                });
            }
        },
        decrease: function(selector){
            if($(selector).length > 0){
                var self = this;
                var value = parseFloat($(selector).val());
                var increment = parseFloat(($(selector).data(self.DATA.INCREMENT))?$(selector).data(self.DATA.INCREMENT):self.DEFAULT.INCREMENT);
                increment = ($.isNumeric(increment))?increment:1;
                value = ($.isNumeric(value))?value:0;
                value -= increment;
                $(selector).val(value);
                self.validate(selector, value);
                self.updateBundleChildrenQty($$(''+selector+'')[0]);
            }
        },
        increase: function(selector){
            if($(selector).length > 0){
                var self = this;
                var value = parseFloat($(selector).val());
                var increment = parseFloat(($(selector).data(self.DATA.INCREMENT))?$(selector).data(self.DATA.INCREMENT):self.DEFAULT.INCREMENT);
                increment = ($.isNumeric(increment))?increment:1;
                value = ($.isNumeric(value))?value:0;
                value += increment;
                $(selector).val(value);
                self.validate(selector, value);
                self.updateBundleChildrenQty($$(''+selector+'')[0]);
            }
        },
        change: function(el){
            if(el){
                var self = this;
                var value = parseFloat(el.value);
                if($.isNumeric(value)){
                    self.validate(value);
                    if (value < 0) {
                        el.value = 0;
                    }
                }else{
                    el.value = 0;
                }
                var max = parseFloat($(el).data(self.DATA.MAX));
                var min = parseFloat($(el).data(self.DATA.MIN));
                if (parseFloat(el.value) > parseFloat(max)) {
                    el.value = max;
                }
                if (parseFloat(el.value) < parseFloat(min)) {
                    el.value = min;
                }
                self.updateBundleChildrenQty(el);
            }
        },
        focus: function(el){
            if(el){
                el.select();
            }
        },
        validate: function(el, value){
            value = parseFloat(value);
            this.validateNegative(el, value);
            this.validateMax(el, value);
            this.validateMin(el, value);
        },
        validateNegative: function(el, value){
            var self = this;
            if(value < 0 && $(el).hasClass(self.CLASS.NOT_NEGATIVE)){
                $(el).val(0);
            }
        },
        validateMax: function(el, value){
            var self = this;
            var max = parseFloat($(el).data(self.DATA.MAX));
            max = ($.isNumeric(max))?max:false;
            if(max && max < value){
                $(el).val(max);
            }
        },
        validateMin: function(el, value){
            var self = this;
            var min = parseFloat($(el).data(self.DATA.MIN));
            min = ($.isNumeric(min))?min:false;
            if(min && min > value){
                $(el).val(min);
            }
        },
        updateBundleChildrenQty: function (el) {
            var packItemInput = el.up().down('.pack-item');
            if(!packItemInput) {
                return;
            }
            var itemId = packItemInput.value;
            var childItemQtyInputs = $$('.child-qty-'+ itemId);
            for(var i in childItemQtyInputs) {
                if(typeof childItemQtyInputs[i] == 'function') {
                    continue;
                }
                var childItemInput = childItemQtyInputs[i];
                var bundleQtyInput = childItemInput.up().down('.bundle-qty');
                var bundleQty = parseFloat(bundleQtyInput.value);
                childItemInput.value = bundleQty * el.value;
                var event = new Event('change');
                childItemInput.dispatchEvent(event);
            }
        }
    };
});
