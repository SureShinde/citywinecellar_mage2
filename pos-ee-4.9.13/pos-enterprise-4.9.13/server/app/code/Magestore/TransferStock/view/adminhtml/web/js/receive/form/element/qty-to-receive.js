/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator'
], function (_, registry, Abstract, validator) {
    'use strict';

    return Abstract.extend({

        /**
         * Validates itself by it's validation rules using validator object.
         * If validation of a rule did not pass, writes it's message to
         * 'error' observable property.
         *
         * @returns {Object} Validate information.
         */
        validate: function () {
            var value = this.value(),
                result = validator(this.validation, value, this.validationParams),
                message = !this.disabled() && this.visible() ? result.message : '',
                isValid = this.disabled() || !this.visible() || result.passed;

            if(isValid && (message === null || message === '')) {
                var qty = registry.get(this.parentName + '.' + 'available_qty_to_receive');
                var qtyToReceive = registry.get(this.parentName + '.' + 'qty_to_receive');
                if(parseFloat(qtyToReceive.value()) > parseFloat(qty.value())) {
                    isValid = false;
                    message = 'Quantity to receive cannot be greater than available qty to receive.';
                }
            }

            this.error(message);
            this.error.valueHasMutated();
            this.bubble('error', message);

            //TODO: Implement proper result propagation for form
            if (this.source && !isValid) {
                this.source.set('params.invalid', true);
            }

            return {
                valid: isValid,
                target: this
            };
        }
    });
});
