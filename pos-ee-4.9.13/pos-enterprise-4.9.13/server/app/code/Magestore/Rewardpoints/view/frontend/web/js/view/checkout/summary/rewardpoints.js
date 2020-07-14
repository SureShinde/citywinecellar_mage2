/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magestore_Rewardpoints/js/model/earningpoints',
        'Magento_Checkout/js/model/quote',
        'mage/translate'
    ],
    function ($,Component, rewardpoints, quote, $t) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Rewardpoints/checkout/summary/rewardpoints'
            },
            rewardpoints:  rewardpoints.getData(),
            totals: quote.getTotals(),

            /**
             * Check is displayed use point
             * @returns {boolean}
             */
            isDisplayedUsePoint: function() {
                if(!this.rewardpoints().displayUsePoint || !this.rewardpoints().enableReward){
                    $('tr.rewardpoint-use_point').hide();
                }
                return true;
            },
            /**
             * Get Earning Point
             * @returns {number}
             */
            getUsePoint: function() {
                var point = 0;
                if (this.totals() && this.totals()['total_segments']) {
                    this.totals()['total_segments'].forEach(function (total) {
                        if (total.code && total.code == 'rewardpoints') {
                            point = -Math.abs(total.value);
                            document.dispatchEvent(new Event('updated_rewardpoint'));
                        }
                    });
                }
                return this.getFormattedPrice(point);
            },

            /**
             * Check is displayed earning point
             * @returns {boolean}
             */
            isDisplayedEarning: function() {
                return this.getPureEarningValue() != 0;
            },
            /**
             * Get Earning Label
             * @returns text
             */
            getEarningLabel: function() {
                var earningLabel = 'You will earn';
                if (this.totals() && this.totals()['total_segments']) {
                    this.totals()['total_segments'].forEach(function (total) {
                        if (total.code && total.code == 'rewardpointsearning') {
                            earningLabel = total.title;
                        }
                    });
                }
                return earningLabel;
            },
            /**
             * Get Earning Point
             * @returns {number}
             */
            getEarningPoint: function() {
                return $t('%s Points').replace('%s', this.getPureEarningValue());
            },
            /**
             * Check is displayed spending point
             * @returns {boolean}
             */
            isDisplayedSpending: function() {
                if(!this.rewardpoints().displaySpending || !this.rewardpoints().enableReward){
                    $('tr.rewardpoint-spending').hide();
                }
                return true;
            },
            /**
             *Get Spending Label
             * @returns text
             */
            getSpendingLabel: function() {
                var spendingLabel = 'You will spend';
                if(this.rewardpoints().spendingLabel){
                    spendingLabel  = this.rewardpoints().spendingLabel;
                }
                return spendingLabel;
            },
            /**
             *Get Spending Point
             * @returns {number}
             */
            getSpendingPoint: function() {
                return $t('%s Points').replace('%s', this.getSpendingValue());
            },

            getPureEarningValue: function(){
                var points = 0;
                if (this.totals() && this.totals()['total_segments']) {
                    this.totals()['total_segments'].forEach(function(total){
                        if(total.code && total.code == 'rewardpointsearning'){
                            points = total.value;
                        }
                    });
                }
                return points;
            },
            getSpendingValue: function(){
                var points = 0;
                if (this.totals() && this.totals()['total_segments']) {
                    this.totals()['total_segments'].forEach(function(total){
                        if(total.code && total.code == 'rewardpoints_spent'){
                            points = Math.abs(total.value);
                        }
                    });
                }
                return points;
            }
        });
    }
);