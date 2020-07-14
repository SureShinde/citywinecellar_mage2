import React, {Fragment} from "react";
import CoreComponent from "../../../../framework/component/CoreComponent";
import PropTypes from 'prop-types';
import CustomerGroupComponent from "./CustomerGroupComponent";
import CustomerInputComponent from "./CustomerInputComponent";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";

export class CustomerStateComponent extends CoreComponent {
    static className = 'CustomerStateComponent';
    component;

    /**
     * set input
     * @param input
     */
    setComponent(input) {
        this.component = input;
    }

    /**
     * constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            options: props.STATES
        }
    }

    /**
     * get component ref
     * @returns {*}
     */
    getComponentRef() {
        return this.component.getWrappedInstance();
    }

    /**
     * get value from component
     */
    getValue() {
        let data = {
            type: '',
            value: '',
            state: {}
        };
        let componentRef = this.getComponentRef();
        if(this.state.options && this.state.options.length > 0) {
            data.type = 'group';
            data.value = componentRef.select.value;
            data.state = this.state.options.find(item => item.id === Number(data.value));
            return data;
        } else {
            data.type = 'input';
            data.value = componentRef.input.value;
            return data;
        }
    }

    /**
     * Set value
     * @param value
     */
    setValue(value) {
        let ref;
        if(this.state.options && this.state.options.length > 0) {
            ref = this.getComponentRef().select;
            value = this.state.options.find(item => item.name === value)
                ? this.state.options.find(item => item.name === value).id : "";
        } else {
            ref = this.getComponentRef().input;
        }

        if (value) {
            ref.value = value;
        } else {
            if (!this.state.options)
                ref.value = '';
        }
    }

    /**
     * validate
     */
    validate() {}

    /**
     * set options
     * @param options
     */
    setOptions(options) {
        this.setState({options: options});
    }

    /**
     * on select
     * @param ref
     * @param value
     */
    onSelect(ref, value) {
        this.props.onSelect(ref, value);
    }

    template() {
        let {Code, Label, DefaultValue, MaxLength, Required,
            RequiredEmail, OneRow, IsOptional,
            KeyValue, KeyTitle, checkEmail} = this.props;
        let {options} = this.state;
        return (
            <Fragment>
                {
                    (options && options.length > 0) ?
                        <CustomerGroupComponent
                            ref={this.setComponent.bind(this)}
                            Options={options}
                            KeyValue={KeyValue}
                            KeyTitle={KeyTitle}
                            Label={Label}
                            Code={Code}
                            DefaultValue={
                                (DefaultValue ?
                                        (DefaultValue.region_id ? DefaultValue.region_id : DefaultValue.id)
                                        : ''
                                ) +''
                            }
                            onSelect={this.onSelect.bind(this)}/>
                        :
                        <CustomerInputComponent
                            Label={Label}
                            Code={Code}
                            ref={this.setComponent.bind(this)}
                            IsOptional={IsOptional}
                            OneRow={OneRow}
                            MaxLength={MaxLength}
                            Required={Required}
                            checkEmail={checkEmail}
                            DefaultValue={(DefaultValue && DefaultValue.region) ? DefaultValue.region : ''}
                            RequiredEmail={RequiredEmail}/>
                }
            </Fragment>
        )
    }
}

CustomerStateComponent.propTypes = {
    STATES: PropTypes.array,
    Required: PropTypes.bool,
    RequiredEmail: PropTypes.bool,
    Code: PropTypes.string,
    Label: PropTypes.string,
    IsOptional: PropTypes.bool,
    DefaultValue: PropTypes.object,
    MaxLength: PropTypes.number,
    OneRow: PropTypes.bool,
    GoogleSuggest: PropTypes.bool,
    KeyValue: PropTypes.string,
    KeyTitle: PropTypes.string,
    onSelect: PropTypes.func
};

/**
 * CustomerStateComponent
 */
export default ComponentFactory.get(CustomerStateComponent);
