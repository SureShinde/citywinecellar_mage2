/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'Magento_Catalog/js/price-utils',
        'Magestore_Webpos/js/accounting.min'
    ],
    function (ko, priceUtils,accounting) {
        'use strict';

        var CurrencyHelper = {
            data: null,
            currentCurrencyCode: ko.observable(),
            baseCurrencyCode: ko.observable(),
            currentCurrencySymbol: ko.observable(),
            baseCurrencySymbol: ko.observable(),
            priceFormat: ko.observable(),
            basePriceFormat: ko.observable(),
            /**
             * Init Data
             * @param data
             */
            initData: function (data) {
                if (data) {
                    if(typeof data === 'string') {
                        data = JSON.parse(data);
                    }
                    this.currentCurrencyCode(data.currentCurrencyCode);
                    this.baseCurrencyCode(data.baseCurrencyCode);
                    this.currentCurrencySymbol(data.currentCurrencySymbol);
                    this.baseCurrencySymbol(data.baseCurrencySymbol);
                    this.priceFormat(data.priceFormat);
                    this.basePriceFormat(data.basePriceFormat);
                }
            },
            formatPrice: function (amount) {
                amount = parseFloat(amount);
                var format = this.priceFormat();
                if (format && typeof (format.precision) != 'undefined') {
                    format.precision = 2;
                }
                if (format && typeof (format.requiredPrecision) != 'undefined') {
                    format.requiredPrecision = 2;
                }
                return priceUtils.formatPrice(amount, format);
            },
            toNumber : function (string) {
                var decimalSymbolNumber = this.currentCurrencySymbol();
                var result = accounting.unformat(string, decimalSymbolNumber);
                return result;
            }
        };
        return CurrencyHelper;
    }
);
