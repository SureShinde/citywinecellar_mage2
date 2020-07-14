/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/provider',
    'Magento_Ui/js/modal/alert'
], function ($, Element, alert) {
    'use strict';

    return Element.extend({
        /**
         * Saves currently available data.
         *
         * @param {Object} [options] - Addtitional request options.
         * @returns {Provider} Chainable.
         */
        save: function (options) {
            var data = this.get('data');
            if(typeof data.dynamic_grid != 'undefined' && data.dynamic_grid.length < 1)
                return alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__('Please select at least one item.')
                });
            
            this.client.save(data, options);

            return this;
        },
    });
});
