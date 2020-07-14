import React from 'react';
import CoreComponent from '../../../../../../framework/component/CoreComponent'
import CurrencyHelper from "../../../../../../helper/CurrencyHelper";
import NumPad from "../../../../lib/react-numpad";
import NumberHelper from "../../../../../../helper/NumberHelper";
import ComponentFactory from "../../../../../../framework/factory/ComponentFactory";

export class CustomSaleQuantityComponent extends CoreComponent {
    static className = 'CustomSaleQuantityComponent';

    /**
     * Constructor
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            input_value: props.DefaultValue,
            defaultValue: props.DefaultValue,
            decimal_symbol: CurrencyHelper.getCurrencyFormat().decimal_symbol
        };
        this.getContainer = this.getContainer.bind(this);
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
            canClear: true
        });
        if (this.props.inputFieldOnChange) {
            this.props.inputFieldOnChange(this.props.Code, value);
        }
    }


    template() {
        let {Code, Label, DefaultValue, OneRow} = this.props;
        let id = "custom-sale-" + Code;
        return (
            <div ref={this.getContainer} className={OneRow ? "col-sm-12" : "col-sm-6"}>
                <label htmlFor={id}>{Label}</label>
                <NumPad.Popover key={id}
                                onChange={this.changeInputValue.bind(this)}
                                position="centerRight"
                                arrow="right"
                                value={DefaultValue}
                                isDecimal={true}
                                decimalSeparator={this.state.decimal_symbol}
                                min={0}
                                isShowAction={true}
                >
                    <span className="form-control">{NumberHelper.formatDisplayGroupAndDecimalSeparator(this.state.input_value)}</span>
                </NumPad.Popover>
            </div>
        );
    }
}

/**
 * CustomSaleQuantityComponent
 */
export default ComponentFactory.get(CustomSaleQuantityComponent);
