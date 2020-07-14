/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'mage/translate',
        'Magestore_Webpos/js/smooth-scrollbar',
        'Magestore_Webpos/js/model/event',
        'Magestore_Webpos/js/model/pos/session/management',
        'Magestore_Webpos/js/helper/number-helper',
        'Magestore_Webpos/js/helper/currency-helper'
    ],
    function ($,
              ko,
              Component,
              __,
              scrollbar,
              Event,
              PosManagement,
              NumberHelper,
              CurrencyHelper
    ) {
        "use strict";
        return Component.extend({
            TYPE_CHECKOUT: 0,
            TYPE_REFUND: 1,
            CASH_TRANSACTION_CHANGE: 'change',
            CASH_TRANSACTION_ADD : 'add',
            CASH_TRANSACTION_REMOVE: 'remove',
            CASH : 'cashforpos',
            is_show_report : ko.observable(false),
            session_id : ko.observable(),
            location : ko.observable(),
            pos : ko.observable(),
            staff : ko.observable(),
            opened_at : ko.observable(),
            closed_at : ko.observable(),
            net_amount : ko.observable(),
            opening_amount : ko.observable(),
            closing_amount : ko.observable(),
            theoretical_closing_amount : ko.observable(),
            different_amount : ko.observable(),
            cash_sales : ko.observable(),
            cash_refund : ko.observable(),
            cash_in : ko.observable(),
            cash_out : ko.observable(),
            payments : ko.observable(),
            total_amount : ko.observable(),
            refund_amount : ko.observable(),
            print_at : ko.observable(),
            closed_note: ko.observable(),
            isShowReason: ko.observable(0),
            isClosed: ko.observable(0),

            defaults: {
                template: 'Magestore_Webpos/pos/session/report-session'
            },
            initialize: function () {
                this._super();
                /* close session */
                var self = this;

                Event.observer('print_x_report', function(){
                    var todayDate = PosManagement.getShowCurrentDate();
                    self.print_at(todayDate);
                    self.printAction();
                });

                PosManagement.shiftData.subscribe(function () {
                    self.initData();
                });
                self.initData();
            },

            initData : function (){
                var session = PosManagement.shiftData();
                if (session) {
                    var isClosed = session && +session.status == PosManagement.CLOSED_STATUS;
                    this.isClosed(isClosed);
                    this.session_id(session.shift_increment_id);
                    let location_address = session.location_address ? session.location_address : null;
                    this.location(location_address);
                    this.pos(session.pos_name);
                    this.staff(session.staff_name);
                    this.opened_at(session.opened_at);
                    this.closed_at(session.closed_at);
                    this.net_amount(CurrencyHelper.formatPrice(this.getNetAmount(session)));
                    this.opening_amount(CurrencyHelper.formatPrice(session.opening_amount));
                    this.closing_amount(CurrencyHelper.formatPrice(session.closed_amount));
                    this.theoretical_closing_amount(CurrencyHelper.formatPrice(this.getTheoreticalAmount(session)));
                    this.different_amount(CurrencyHelper.formatPrice(this.getCashDifference(session)));
                    this.cash_sales(CurrencyHelper.formatPrice(this.getCashSales(session)));
                    this.cash_refund(CurrencyHelper.formatPrice(-this.getCashRefund(session)));
                    this.cash_in(CurrencyHelper.formatPrice(this.getDisplayTotalAmountCashIn(session)));
                    this.cash_out(CurrencyHelper.formatPrice(-this.getDisplayTotalAmountCashOut(session)));
                    this.payments(this.getInfoPaymentMethod(session));
                    this.total_amount(CurrencyHelper.formatPrice(this.getTotalAmount(session)));
                    this.refund_amount(CurrencyHelper.formatPrice(this.getDisplayTotalRefund(session)));
                    this.closed_note(session.closed_note);
                    if (this.getCashDifference(session)){
                        this.isShowReason(1);
                    } else {
                        this.isShowReason(0);
                    }
                }

            },

            formatCurrency: function(amount) {
                return CurrencyHelper.formatPrice(amount);
            },

            getNetAmount : function (session) {
                var netAmount =  (this.getTotalAmount(session) - this.getTotalRefund(session));
                return netAmount;
            },

            getTotalAmount : function(session) {
                var self = this;
                let baseTotalAmount = 0;
                session.sale_summary.forEach(item => {
                    if (item.type == self.TYPE_CHECKOUT) {
                        baseTotalAmount = NumberHelper.addNumber(baseTotalAmount, item.amount_paid);
                    }
                });
                session.cash_transaction.forEach(item => {
                    if (item.type == self.CASH_TRANSACTION_CHANGE) {
                        baseTotalAmount = NumberHelper.minusNumber(baseTotalAmount, item.value);
                    }
                });
                return baseTotalAmount;
            },
            getDisplayTotalRefund : function (session) {
                var refund = this.getTotalRefund(session);
                if(refund != 0){
                    refund = "-" + refund;
                }
                return refund;
            },

            getTotalRefund : function (session) {
                var self = this;
                let baseTotalRefund = 0;
                session.sale_summary.map(item => {
                    if (item.type == self.TYPE_REFUND) {
                        baseTotalRefund = NumberHelper.addNumber(baseTotalRefund, item.amount_paid);
                    }
                    return null;
                });
                return baseTotalRefund;
            },

            getTheoreticalAmount(session) {
                var self = this;
                let opening_amount = parseFloat(session.opening_amount);
                let total_cash = 0;
                session.cash_transaction.map(transaction => {
                    if (transaction.type == self.CASH_TRANSACTION_ADD) {
                        total_cash += parseFloat(transaction.value);
                    } else {
                        total_cash -= parseFloat(transaction.value);
                    }
                    return transaction;
                });
                return opening_amount + total_cash;
            },

            getCashDifference(session) {
                let closedAmount = session.closed_amount ? session.closed_amount : 0;
                let cashDifference = closedAmount - this.getTheoreticalAmount(session);
                return cashDifference;
            },

            getCashSales(session) {
                var self = this;
                let total_cash = 0;
                session.cash_transaction.forEach(transaction => {
                    if(transaction.order_increment_id) {
                        if (transaction.type == self.CASH_TRANSACTION_ADD) {
                            total_cash += parseFloat(transaction.value);
                        } else if (transaction.type == self.CASH_TRANSACTION_CHANGE) {
                            total_cash -= parseFloat(transaction.value);
                        }
                    }
                });
                return total_cash;
            },

            getCashRefund(session) {
                var self = this;
                let cashRefundAmount = 0;
                session.cash_transaction.forEach(transaction => {
                    if (transaction.type == self.CASH_TRANSACTION_REMOVE && transaction.order_increment_id ) {
                        cashRefundAmount += parseFloat(transaction.value);
                    }
                });
                return cashRefundAmount;
            },

            /**
             * Pay ins
             * @param session
             * @returns {*|*[]}
             */
            getDisplayTotalAmountCashIn(session) {
                let payIns = 0;
                var self = this;
                session.cash_transaction.map(item => {
                    if (item.type == self.CASH_TRANSACTION_ADD && !item.order_increment_id) {
                        payIns = NumberHelper.addNumber(payIns, item.value);
                    }
                    return null;
                });
                return payIns;
            },

            /**
             * Display Pay out
             * @param session
             * @returns {string}
             */
            getDisplayTotalAmountCashOut(session) {
                var self = this;
                let payOuts = 0;
                session.cash_transaction.map(item => {
                    if (item.type == self.CASH_TRANSACTION_REMOVE && !item.order_increment_id) {
                        payOuts = NumberHelper.addNumber(payOuts, item.value);
                    }
                    return null;
                });
                return payOuts;
            },

            /**
             * get info payment method
             * @param session
             * @returns {Array}
             */
            getInfoPaymentMethod(session){
                var self = this;
                let infoPayments = [];
                let saleSummary = session.sale_summary ? session.sale_summary : [];
                saleSummary.forEach((payment) => {
                    let hasExisted = false;
                    infoPayments.map((infoPayment) => {
                        if (infoPayment.method == payment.method) {
                            hasExisted = true;
                            if (payment.type == self.TYPE_REFUND) {
                                var value = parseFloat(infoPayment.amount_paid) - parseFloat(payment.amount_paid);
                                infoPayment.amount_paid = value;
                            } else {
                                var value = parseFloat(infoPayment.amount_paid) + parseFloat(payment.amount_paid);
                                infoPayment.amount_paid = value;
                            }
                        }
                        return infoPayments;
                    });

                    if (!hasExisted) {
                        let infoPayment = {};
                        infoPayment.method = payment.method;
                        infoPayment.title = payment.title;
                        if (payment.type == self.TYPE_REFUND) {
                            infoPayment.amount_paid = 0 - payment.amount_paid;
                        } else {
                            infoPayment.amount_paid = payment.amount_paid;
                        }
                        infoPayments.push(infoPayment);
                    }

                });

                let totalChange = 0;
                let cashTransaction = session.cash_transaction ? session.cash_transaction : [];
                cashTransaction.forEach((transaction) => {
                    if (transaction.type == self.CASH_TRANSACTION_CHANGE) {
                        totalChange += parseFloat(transaction.value);
                    }
                });

                infoPayments.map((infoPayment) => {
                    if (infoPayment.method == self.CASH) {
                        infoPayment.amount_paid -= totalChange;
                    }
                    return infoPayments;
                });
                return infoPayments;
            },

            printAction : function () {
                var html = document.getElementById('block-printreceipt').innerHTML;
                var html = "<html>\n" +
                    "<body>";
                html+= document.getElementById('block-printreceipt').innerHTML;
                html+= "</body>\n" +
                    "</html>";

                var print_window = window.open('', 'print_offline', 'status=1,width=500,height=700');
                if(print_window){
                    print_window.document.open();
                    print_window.document.write(html);
                    print_window.document.close();
                    print_window.addEventListener('load',function(){
                        print_window.print();
                    });
                }else{
                    alert("Your browser has blocked the automatic popup, please change your browser setting or print the receipt manually");
                }

            },

        });
    }
);
