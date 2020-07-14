import React, { Component } from 'react';
import PropTypes from 'prop-types';
import NumberHelper from "../../../../../helper/NumberHelper";

export default ({
                    element,
                    validation,
                    formatInputValue,
                    displayRule,
                    setValue,
                    inputButtonContent,
                    keyValid,
                }) => {
    class ContentCustomPriceNumPad extends Component {
        constructor(props) {
            super(props);
            this.state = {
                value: formatInputValue(props.value),
                customPriceDiscountType: props.customPriceDiscountType,
                unitCustomPriceDiscount: props.unitCustomPriceDiscount
            };
            this.confirm = this.confirm.bind(this);
            this.update = this.update.bind(this);
        }

        /**
         * component will receive props
         * @param nextProps
         */
        componentWillReceiveProps(nextProps) {
            if (this.props.value !== nextProps.value) {
                this.setState({
                    value: formatInputValue(nextProps.value),
                    customPriceDiscountType: nextProps.customPriceDiscountType,
                    unitCustomPriceDiscount: nextProps.unitCustomPriceDiscount,
                });
            }
        }

        /**
         * update value display
         * @param value
         */
        update(value, customPriceDiscountType, unitCustomPriceDiscount) {
            const { onChange } = this.props;
            let price = setValue(value);
            let isEmpty = value === "";
            onChange(price, isEmpty, customPriceDiscountType, unitCustomPriceDiscount);
        }

        /**
         * confirm value input by validate
         * @param value
         */
        confirm(value, customPriceDiscountType, unitCustomPriceDiscount) {
            let updateValue = {};
            if (validation(value)) {
                updateValue = { value, customPriceDiscountType, unitCustomPriceDiscount };
                this.update(value, customPriceDiscountType, unitCustomPriceDiscount);
            }
            this.setState({updateValue});
        }

        /**
         * render
         * @returns {*}
         */
        render() {
            const {value, customPriceDiscountType, unitCustomPriceDiscount} = this.state;
            const {
                locale,
                markers,
                sync,
                isDecimal,
                terminalAlign,
                active,
                finish,
                cancel
            } = this.props;


            return React.createElement(
                element,
                {
                    ...this.props,
                    cancel: cancel,
                    confirm: this.confirm,
                    update: this.update,
                    finish: finish,
                    eventTypes: ['click', 'touchend'],
                    displayRule,
                    validation,
                    keyValid,
                    locale,
                    markers,
                    value,
                    customPriceDiscountType,
                    unitCustomPriceDiscount,
                    sync,
                    isDecimal,
                    terminalAlign,
                    contentOnly: true,
                    active: active
                },
                null
            )
                ;
        }
    }

    ContentCustomPriceNumPad.defaultProps = {
        locale: 'en',
        value: '',
        customPriceDiscountType: '%',
        sync: false,
        unitCustomPriceDiscount: 0,
        markers: [],
        isDecimal: false,
        terminalAlign: 'center',
        max: NumberHelper.MAX_CURRENCY_DISPLAY,
        min: 0,
    };

    ContentCustomPriceNumPad.propTypes = {
        onChange: PropTypes.func.isRequired,
        locale: PropTypes.string,
        markers: PropTypes.arrayOf(PropTypes.string),
        value: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
        customPriceDiscountType: PropTypes.string,
        unitCustomPriceDiscount: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
        sync: PropTypes.bool,
        isDecimal: PropTypes.bool,
        terminalAlign: PropTypes.oneOf(['left', 'right', 'center']),
        max: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
        min: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
    };

    return ContentCustomPriceNumPad;
};
