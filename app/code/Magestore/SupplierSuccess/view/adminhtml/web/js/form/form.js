/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/form'
], function (form) {
    'use strict';

    return form.extend({

        /**
         * Validate and save form.
         *
         * @param {String} redirect
         * @param {Object} data
         */
        save: function (redirect, data) {
            console.log('xxx2');
            this.validate();

            if (!this.additionalInvalid && !this.source.get('params.invalid')) {
                os_supplier_product_listingJsObject.resetFilter();
                this.setAdditionalData(data)
                    .submit(redirect);
            }
            os_supplier_product_listingJsObject.resetFilter();
        }
    });
});
