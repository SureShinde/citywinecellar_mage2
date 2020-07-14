/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'uiRegistry'
], function ($, _, Form, registry) {
    'use strict';

    return Form.extend({
        defaults: {
            listens: {
                responseData: 'processResponseData'
            }
        },
        /**
         * Validate and save form.
         *
         * @param {String} redirect
         * @param {Object} data
         */
        save: function (redirect, data) {

        },
        /**
         * Process response data
         *
         * @param {Object} data
         */
        processResponseData: function (response) {

        }
    });
});
