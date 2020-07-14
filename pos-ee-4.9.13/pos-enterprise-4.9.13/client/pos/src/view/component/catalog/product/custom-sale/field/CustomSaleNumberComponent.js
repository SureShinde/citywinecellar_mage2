import React from 'react';
import CoreComponent from '../../../../../../framework/component/CoreComponent'
import NumPad from "../../../../lib/react-numpad";
import CurrencyHelper from "../../../../../../helper/CurrencyHelper";
import NumberHelper from "../../../../../../helper/NumberHelper";
import ComponentFactory from "../../../../../../framework/factory/ComponentFactory";

export class CustomSaleNumberComponent extends CoreComponent {
    static className = 'CustomSaleNumberComponent';


    /**
     * Constructor
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            input_value: props.DefaultValue,
            canClear: false,
            decimal_symbol: CurrencyHelper.getCurrencyFormat().decimal_symbol
        };
        this.getContainer = this.getContainer.bind(this);
        this.isTouch = false
    }

    /**
     * get container
     * @param ref
     */
    getContainer(ref) {
        this.container = ref
    }

    /**
     * Component will receive props
     *
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        if (nextProps.isNew) {
            this.setState({
                input_value: this.props.DefaultValue,
                canClear: false
            });
            if (this.props.inputFieldOnChange) {
                this.props.inputFieldOnChange(this.props.Code, this.props.DefaultValue);
            }
        }
    }
    /**
     * Change input value
     *
     * @param value
     */
    changeInputValue(value) {
        this.setState({
            input_value: value,
            canClear: value !== this.props.DefaultValue
        });
        if (this.props.inputFieldOnChange) {
            this.props.inputFieldOnChange(this.props.Code, value);
        }
    }

    /**
     * Render template
     *
     * @return {*}
     */
    template() {
        let {Code, Label, DefaultValue, OneRow} = this.props;
        let id = "custom-sale-" + Code;
        return (
            <div ref={this.getContainer} className={OneRow ? "col-sm-12" : "col-sm-6"}>
                <label htmlFor={id}>{Label}</label>
                <NumPad.CustomNumber
                    key={id}
                    onChange={this.changeInputValue.bind(this)}
                    position="centerLeft"
                    value={DefaultValue}
                    isDecimal={true}
                    decimalSeparator={this.state.decimal_symbol}
                    min={0}
                    isShowAction={true}
                    arrow="left">
                        <span className="form-control">
                            {NumberHelper.formatDisplayGroupAndDecimalSeparator(this.state.input_value)}
                        </span>
                </NumPad.CustomNumber>
            </div>
        );
    }
}

/**
 * CustomSaleNumberComponent
 */
export default ComponentFactory.get(CustomSaleNumberComponent);
