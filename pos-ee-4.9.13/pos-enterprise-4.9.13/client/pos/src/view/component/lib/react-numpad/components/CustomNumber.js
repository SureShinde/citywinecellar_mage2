import React from 'react';
import IconEdit from 'react-icons/lib/md/edit';
import CustomNumPad from './CustomNumPad';
import {CustomKeyPad} from '../elements';
import NumberHelper from "../../../../../helper/NumberHelper";
import CurrencyHelper from "../../../../../helper/CurrencyHelper";

/**
 * Default validation
 * @type {{float: function(): boolean, negative: function(): boolean}}
 */
const DefaultValidation = {
    float: () => true,
    negative: () => true,
};

/**
 * Positive validation
 * @type {{float: function(): boolean, negative: function(*=): boolean}}
 */
const PositiveValidation = {
    float: () => true,
    negative: value => parseInt(value, 10) > 0,
};

/**
 * Integer validation
 * @type {{float: function(*=): boolean, negative: function(): boolean}}
 */
const IntegerValidation = {
    float: value => parseFloat(value) % 1 === 0,
    negative: () => true,
    displayRule: value => {
        let next = value.toString().replace(/\D+/g, '');
        return parseInt(next, 10) || 0;
    },
    isInteger: true
};

/**
 * Positive integer validation
 * @type {{float: function(*=): boolean, negative: function(*=): boolean}}
 */
const PositiveIntegerValidation = {
    float: value => parseFloat(value) % 1 === 0,
    negative: value => parseInt(value, 10) > 0,
};

/**
 * init default prop validation
 * @param Validation
 * @returns {{element, validation: function(*): boolean, formatInputValue: function(*): string, keyValid: keyValid, displayRule: function(*): string, inputButtonContent: *}}
 */
const defaultProps = Validation => ({
    element: CustomKeyPad,
    validation: value => value.length > 0,
    formatInputValue: value => value.toString().replace(/\D+/g, ''),
    keyValid: (value = '', key) => {
        let next;
        if (key === CurrencyHelper.getDecimalSymbol()) {
            return false;
        }
        if (key === '-') {
            next = value.charAt(0) === '-' ? value.substr(1) : key + value;
        } else {
            next = value + key;
        }
        // eslint-disable-next-line no-restricted-globals
        return !isNaN(next) && Validation.float(next) && Validation.negative(next);
    },
    setValue: (value) => {
        let precision = CurrencyHelper.DEFAULT_DISPLAY_PRECISION;
        if (Validation.isInteger) {
            precision = 0;
        }
        return NumberHelper.convertNumberToPriceHasPrecision(value, precision);
    },
    displayRule: (value) => {
        let next = value.toString().replace(/\D+/g, '');
        if (next === '') return next;
        let precision = CurrencyHelper.DEFAULT_DISPLAY_PRECISION;
        if (Validation.isInteger) {
            precision = 0;
        }
        let nextDisplay = NumberHelper.convertNumberToPriceHasPrecision(next, precision);
        return NumberHelper.formatDisplayGroupAndDecimalSeparator(nextDisplay);
    },
    inputButtonContent: <IconEdit/>,
});

const CustomNumber = CustomNumPad(defaultProps(DefaultValidation));
const CustomPositiveNumber = CustomNumPad(defaultProps(PositiveValidation));
const CustomIntegerNumber = CustomNumPad(defaultProps(IntegerValidation));
const CustomPositiveIntegerNumber = CustomNumPad(defaultProps(PositiveIntegerValidation));

export {CustomNumber, CustomPositiveNumber, CustomIntegerNumber, CustomPositiveIntegerNumber};
