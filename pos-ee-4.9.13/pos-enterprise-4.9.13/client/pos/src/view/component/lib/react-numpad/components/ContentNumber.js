import React from 'react';
import IconEdit from 'react-icons/lib/md/edit';
import ContentNumPad from './ContentNumPad';
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
 * init default prop validation
 * @param Validation
 * @returns {{element, validation: function(*): boolean, formatInputValue: function(*): string, keyValid: keyValid, displayRule: function(*): string, inputButtonContent: *}}
 */
const defaultProps = Validation => ({
    element: CustomKeyPad,
    validation: value => value.length >= 0,
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
    setValue: (value) => NumberHelper.convertNumberToPriceHasPrecision(value),
    displayRule: (value) => {
        let next = value.toString().replace(/\D+/g, '');
        if (next === '') return next;
        let nextDisplay = NumberHelper.convertNumberToPriceHasPrecision(next);
        return NumberHelper.formatDisplayGroupAndDecimalSeparator(nextDisplay);
    },
    inputButtonContent: <IconEdit/>,
});

const ContentNumber = ContentNumPad(defaultProps(DefaultValidation));

export {ContentNumber};
