/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'Magestore_Webpos/js/helper/currency-helper',
    ],
    function (ko, CurrencyHelper) {
        'use strict';

        var DenominationHelper = {
            data: null,
            denomination: ko.observableArray(),
            /**
             * Init Data
             * @param data
             */
            initData: function (data) {
                if (data) {
                    if(typeof data === 'string') {
                        data = JSON.parse(data);
                    }
                    this.denomination(data.denomination);
                }
            },
            getTotalRealDenomination : function(data){
                var listDeno = data;
                let total_closing = listDeno.reduce((price, deno, index, listDeno) => {
                    return price += deno.denomination_total ? deno.denomination_total : 0
                }, 0);
                return total_closing;
            },

            processDenominationData : function (id, action, oldDenomination) {
                let Denomination = oldDenomination.map((deno, index, oldDenomination) => {
                    if(id == deno.denomination_id){
                        var count = deno.location_ids ? deno.location_ids : 0;
                        action ? count++ : count--;
                        count = Math.max(count,0);
                        deno.location_ids = count;
                        deno.denomination_total_format = CurrencyHelper.formatPrice(parseFloat(deno.denomination_value)*count);
                        deno.denomination_total = parseFloat(deno.denomination_value)*count;
                        return deno;
                    }else {
                        return deno;
                    }
                });
                return Denomination;
            }
        };
        return DenominationHelper;
    }
);
