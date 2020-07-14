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
    class ContentNumPad extends Component {
        constructor(props) {
            super(props);
            this.state = {
                value: formatInputValue(props.value),
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
                });
            }
        }

        /**
         * update value display
         * @param value
         */
        update(value) {
            const { onChange } = this.props;
            let price = setValue(value);
            let isEmpty = value === "";
            onChange(price, isEmpty);
        }

        /**
         * confirm value input by validate
         * @param value
         */
        confirm(value) {
            let updateValue = {};
            if (validation(value)) {
                updateValue = { value };
                this.update(value);
            }
            this.setState({updateValue});
        }

        /**
         * render
         * @returns {*}
         */
        render() {
            const {value} = this.state;
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

    ContentNumPad.defaultProps = {
        locale: 'en',
        value: '',
        sync: false,
        markers: [],
        isDecimal: false,
        terminalAlign: 'center',
        max: NumberHelper.MAX_CURRENCY_DISPLAY,
        min: 0,
    };

    ContentNumPad.propTypes = {
        onChange: PropTypes.func.isRequired,
        locale: PropTypes.string,
        markers: PropTypes.arrayOf(PropTypes.string),
        value: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
        sync: PropTypes.bool,
        isDecimal: PropTypes.bool,
        terminalAlign: PropTypes.oneOf(['left', 'right', 'center']),
        max: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
        min: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
    };

    return ContentNumPad;
};
