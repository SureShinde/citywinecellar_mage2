import React, {Fragment} from 'react';
import PropTypes from 'prop-types';
import {CoreComponent} from '../../../../framework/component/index';
import {ButtonToolbar, OverlayTrigger, Popover} from "react-bootstrap";
import i18n from "../../../../config/i18n";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";

export class CustomerGroupComponent extends CoreComponent {
    static className = 'CustomerGroupComponent';
    select;

    /**
     * constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            options: props.Options,
            requiredInValid: false,
            defaultValue: props.DefaultValue
        }
    }

    /**
     * componentWillReceiveProps
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        this.setState({options: nextProps.Options});
    }

    /**
     * set select
     * @param select
     */
    setSelect(select) {
        this.select = select;
    }

    /**
     * Set value
     * @param value
     */
    setValue(value) {
        if (value) {
            this.select.value = value;
            this.setState({defaultValue: this.select.value});
        } else {
            this.select.value = '';
        }
    }

    /**
     * onchange
     * @param event
     */
    onChange(event) {
        this.setState({defaultValue: event.target.value}, () => this.validate());
        if (this.props.onSelect) {
            this.props.onSelect(this.props.Code, event.target.value);
        }
    }

    /**
     * set options
     * @param options
     */
    setOptions(options) {
        this.setState({options: options});
    }

    /**
     * validate
     * @return {CustomerGroupComponent.props.Required|boolean}
     */
    validate() {
        let { Required } = this.props;
        let validate = Required && !this.select.value;
        this.setState({
            requiredInValid:  validate,
        });
        return validate;
    }

    /**
     * clear
     */
    clear() {
        if (this.select) {
            this.select.value = "";
        }

        this.validate();
    }

    /**
     * onclick show message error
     */
    onClickShowMessageError() {
        this.setState({showMessageInValid: true});
    }

    template() {
        let { Label, OneRow, KeyValue, KeyTitle, Optional} = this.props;
        let classHiddenRequired = "";
        let classRequired = "hidden";
        let {requiredInValid} = this.state;
        let messageError = "";
        if(requiredInValid === true){
            messageError ="This is a required field";
            classRequired = "validation-advice";
        }
        const popoverRight = (
            <Popover id="popover">
                <div>
                    { i18n.translator.translate(messageError) }
                </div>
            </Popover>
        );
        return (
            <div className={OneRow ? "col-sm-12" : "col-sm-6"}>
                <label>
                    { Label } { Optional ? <Fragment><i>{ i18n.translator.translate(' - Optional') }</i></Fragment> : "" }
                </label>
                <select value={ this.state.defaultValue }
                        className="form-control"
                        ref={this.setSelect.bind(this)}
                        onChange={this.onChange.bind(this)}>
                    {
                        this.state.options ? this.state.options.map(option => {
                            return (<option key={Math.random()} value={option[KeyValue]}> {option[KeyTitle]} </option>)
                        }) : null
                    }
                </select>
                <ButtonToolbar className={classRequired}>
                    <OverlayTrigger
                        trigger={['click', 'hover', 'focus']}
                        rootClose placement="bottom"
                        overlay={popoverRight}
                        container={this}>
                             <span className={"dropdown-toggle" + classHiddenRequired}
                                   onClick={this.onClickShowMessageError.bind(this)}> </span>
                    </OverlayTrigger>
                </ButtonToolbar>
            </div>
        )
    }
}

CustomerGroupComponent.propTypes = {
    Label: PropTypes.string,
    Options: PropTypes.array,
    KeyValue: PropTypes.string,
    KeyTitle: PropTypes.string,
    Required: PropTypes.bool,
    OneRow: PropTypes.bool,
    Code: PropTypes.string,
    onSelect: PropTypes.func
};

/**
 * CustomerGroupComponent
 */
export default ComponentFactory.get(CustomerGroupComponent);
