/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    'underscore'
], function($, _){
    'use strict';

    /**
     * Check wether the incoming string is not empty or if doesn't consist of spaces.
     *
     * @param {String} value - Value to check.
     * @returns {Boolean}
     */
    function isEmpty(value) {
        return (value.length === 0) || (value == null) || /^\s+$/.test(value);
    }

    $.widget('magestore.searchGiftCodes', {
        options: {
            submitBtn: 'button[type="submit"]'
        },

        query: '',

        _create: function() {
            this.searchForm = $(this.options.formSelector);
            this.submitBtn = this.searchForm.find(this.options.submitBtn)[0];

            _.bindAll(this, '_onPropertyChange', '_onSubmit');
            this.query = this.element.val() ? this.element.val() : '';
            this.submitBtn.disabled = Boolean(!this.query.length);

            this.element.on('input propertychange', this._onPropertyChange);

            this.searchForm.on('submit', $.proxy(function(e) {
                this._onSubmit(e);
            }, this));
        },

        /**
         * Executes when the search box is submitted
         */
        _onSubmit: function(e) {
            if (!this.isValid()) {
                e.preventDefault();
            }
        },

        /**
         * Executes when the value of the search input field changes
         */
        _onPropertyChange: function() {
            if (this.query.length && Boolean(!this.element.val().length)) {
                return;
            }
            this.submitBtn.disabled = !this.isValid();
            this.query = this.element.val();
        },
        /**
         *
         * @returns {boolean}
         */
        isValid: function () {
            let current = this.element.val();
            return !isEmpty(current) || (this.query.length && Boolean(!current.length))
        }
    });

    return $.magestore.searchGiftCodes;
});
