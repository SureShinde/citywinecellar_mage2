/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [],
    function (ko, priceUtils) {
        'use strict';

        var NumberHelper = {
            /**
             * Count the number of decimal
             *
             * @param {number} number
             * @return {number}
             */
            countDecimal: function (number) {
                if ((parseFloat(number) % 1) !== 0) {
                    if (number.toString().indexOf('e') !== -1) {
                        return Math.abs(number.toString().split('e')[1]);
                    }
                    return number.toString().split(".")[1].length;
                }
                return 0;
            },

            /**
             * sum an array number
             *
             * @param {Array.<number>} numbers
             * @return {*|any}
             */
            addNumber: function (a, b) {
                    if (!a) {
                        a = 0;
                    }
                    if (!b) {
                        b = 0;
                    }
                    let maxCountNumberDecimal = Math.max(this.countDecimal(a), this.countDecimal(b));
                    return parseFloat((+a + +b).toFixed(maxCountNumberDecimal));
            },

            /**
             * Minus 2 numbers
             * @param {number} a
             * @param {number} b
             * @return {number}
             */
            minusNumber: function (a, b) {
                if (!a) {
                    a = 0;
                }
                if (!b) {
                    b = 0;
                }
                let maxCountNumberDecimal = Math.max(this.countDecimal(a), this.countDecimal(b));
                return parseFloat((+a - +b).toFixed(maxCountNumberDecimal));
            },

            /**
             * Multiple 2 numbers
             * @param {Array.<number>} numbers
             * @return {number}
             */
            multipleNumber: function (...numbers) {
                return numbers.reduce(function (a, b) {
                    if (!a) {
                        a = 0;
                    }
                    if (!b) {
                        b = 0;
                    }
                    let maxCountNumberDecimal = this.countDecimal(a) + this.countDecimal(b);
                    return parseFloat((+a * +b).toFixed(maxCountNumberDecimal));
                }.bind(this));
            }
        };
        return NumberHelper;
    }
);
