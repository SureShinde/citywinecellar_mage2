/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'uiElement',
    'uiRegistry',
    'jquery',
    'knockout'
], function (_, Element, registry, $, ko) {
    'use strict';

    return Element.extend({
        defaults: {
            visible: true,
            template: 'Magestore_PurchaseOrderSuccess/form/element/currency-extra',
            currenciesField: '',
            currencyRateField: '',
            baseCurrencyCode: '',
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();
            
            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Abstract} Chainable.
         */
        initObservable: function () {
            var rules = this.validation = this.validation || {};

            this._super();
            
            this.observe('error disabled focused preview visible value warn isDifferedFromDefault');

            return this;
        },
        
        afterRender: function(){
            var self = this;
            if(this.currenciesField == '')
                this.currenciesField = 
                    registry.get('os_purchase_order_form.os_purchase_order_form.general_information.currency_code');
            if(this.currencyRateField == '')
                this.currencyRateField = 
                    registry.get('os_purchase_order_form.os_purchase_order_form.general_information.currency_rate');
            this.currenciesField.value.subscribe(function () {
                $('#'+this.currencyRateField.noticeId+' span').html(
                    '(1 ' + this.baseCurrencyCode + ' = ' + 
                    this.currencyRateField.value() + ' ' + 
                    this.currenciesField.value()+')'
                );
            }.bind(this));
            this.currencyRateField.value.subscribe(function () {
                $('#'+this.currencyRateField.noticeId+' span').html(
                    '(1 ' + this.baseCurrencyCode + ' = ' + 
                    this.currencyRateField.value() + ' ' + 
                    this.currenciesField.value()+')'
                );
            }.bind(this));
            var notice = '(1 ' + this.baseCurrencyCode + ' = ' +
                this.currencyRateField.value() + ' ' +
                this.currenciesField.value()+')';
            this.currencyRateField.notice = notice;
            $('#'+this.currencyRateField.noticeId+' span').html(notice);
        }
    });
});
