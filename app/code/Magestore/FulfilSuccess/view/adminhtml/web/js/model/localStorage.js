/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mageUtils'
], function ($, utils) {
    'use strict';

    return {
        /**
         * Get data from localStorage for specific key
         * @param key
         * @returns {*}
         */
        get: function(key){
            if (window["localStorage"] !== null) {
                return localStorage.getItem(key);
            }
            else {
                return $.cookie(key);
            }
        },
        /**
         * Set value for specific key on localStorage
         * @param key
         * @param value
         */
        set: function(key, value){
            if (window["localStorage"] !== null) {
                localStorage.setItem(key, value);
            }
            else {
                $.cookie(key, value);
            }
        },
        /**
         * Remove data of specific key on localStorage
         * @param key
         * @param value
         */
        remove: function(key){
            if (window["localStorage"] !== null) {
                localStorage.removeItem(key);
            }
            else {
                $.cookie(key, '');
            }
        },
        /**
         * Get path to child element
         * @param scope
         * @param child
         * @returns {*}
         */
        getKeyPath: function(scope, child){
            var source = { scope: scope, child: child };
            return utils.template('${ $.scope }.${ $.child }', source);
        }
    };
});
