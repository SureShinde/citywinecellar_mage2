/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/lib/validation/validator',
    'jquery',
    'mage/translate'
], function (Select, validator, $, $t) {
    'use strict';
    return Select.extend({
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

            if (value && $("[name='source_warehouse_code']").val() && (value === $("[name='source_warehouse_code']").val())) {
                message = $t('From Source and To Source must be different');
                isValid = false;
            }

            this.error(message);
            this.bubble('error', message);

            //TODO: Implement proper result propagation for form
            if (this.source && !isValid) {
                this.source.set('params.invalid', true);
            }

            return {
                valid: isValid,
                target: this
            };
        },
    });
});
