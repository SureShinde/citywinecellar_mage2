/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/constant/session-contant',
        'Magestore_Webpos/js/helper/number-helper'
    ],
    function (SessionConstant, NumberHelper) {
        'use strict';

        var BalanceService = {
            getTotalCash: function(shiftData, isCashIn = true) {
                let total_cash = 0;
                if(shiftData && shiftData.shift_id) {
                    if(shiftData.cash_transaction && shiftData.cash_transaction.length){
                        shiftData.cash_transaction.forEach(function(transaction) {
                            if ((isCashIn && transaction.type === SessionConstant.CASH_TRANSACTION_ADD) || 
                                (!isCashIn && transaction.type !== SessionConstant.CASH_TRANSACTION_ADD)) {
                                total_cash = NumberHelper.addNumber(total_cash, transaction.value);
                            }
                        });
                    }
                }
                return total_cash;
            },
            /**
             * Get theoretical current amount
             * @param shiftData
             * @returns {*}
             */
            getTheoreticalAmount: function (shiftData) {
                if(shiftData && shiftData.shift_id) {
                    let opening_amount = shiftData.opening_amount;
                    let total_cash = 0;
                    if(shiftData.cash_transaction && shiftData.cash_transaction.length){
                        shiftData.cash_transaction.forEach(function(transaction) {
                            if (transaction.type === SessionConstant.CASH_TRANSACTION_ADD) {
                                total_cash = NumberHelper.addNumber(total_cash, transaction.value);
                            } else {
                                total_cash = NumberHelper.minusNumber(total_cash, transaction.value);
                            }
                        });
                    }
                    return NumberHelper.addNumber(opening_amount, total_cash);
                }
                return 0;
            },
        };
        return BalanceService;
    }
);
