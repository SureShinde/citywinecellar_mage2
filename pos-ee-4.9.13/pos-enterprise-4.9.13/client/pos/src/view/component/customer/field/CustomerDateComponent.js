import React, {Fragment} from 'react';
import PropTypes from 'prop-types';
import {CoreComponent} from '../../../../framework/component/index'
import {DateTime} from "react-datetime-bootstrap";
import moment from "moment";
import $ from "jquery";
import {ButtonToolbar, OverlayTrigger, Popover} from "react-bootstrap";
import i18n from "../../../../config/i18n";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";

export class CustomerDateComponent extends CoreComponent {
    static className = 'CustomerDateComponent';
    select;

    /**
     * constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            requiredInValid: false,
            date_value: props.DefaultValue
        }
    }

    /**
     * validate
     */
    validate() {
        let { Required } = this.props;
        let validate = Required &&  !this.state.date_value;
        this.setState({
            requiredInValid:  validate,
        });

        return validate;
    }

    /**
     * onchange date
     * @param date
     */
    onChangeDate = date => {
        let d = date ? moment(date).format('YYYY-MM-DD') : '';
        this.setState({date_value: d}, () => this.validate());
        if (this.props.onSelect) {
            this.props.onSelect(this.props.Code, d);
        }
    };

    /**
     * show delivery date
     */
    showDateOfBirth() {
        $('#datepicker-dob input').data("DateTimePicker").ignoreReadonly(true);
        $('#datepicker-dob input').data("DateTimePicker").sideBySide(true);
        $('#datepicker-dob input').data("DateTimePicker").minDate(moment("1900-01-01"));
        if (this.state.delivery_date) {
            $('#datepicker-dob input').data("DateTimePicker").useCurrent(false);
        }
        $('#datepicker-dob input').data("DateTimePicker").showClear(true);
        $('#datepicker-dob input').data("DateTimePicker").allowInputToggle(true);
        $('#datepicker-dob input').data("DateTimePicker").toggle();
        $('#datepicker-dob input').data("DateTimePicker").format("L");
        $('#datepicker-dob input').data("DateTimePicker").widgetPositioning({horizontal: 'auto', vertical: 'bottom'});
        $('#datepicker-dob input').keydown(function (event) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        });
    }

    /**
     * onclick show message error
     */
    onClickShowMessageError() {
        this.setState({showMessageInValid: true});
    }

    template() {
        let classHiddenRequired = "";
        let classRequired = "hidden";
        let {Label, DefaultValue, IsOptional, OneRow} = this.props;
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
                <label className="block-title">
                    {Label}{ IsOptional ? <Fragment><i>{ i18n.translator.translate(' - Optional') }</i></Fragment> : "" }
                </label>
                <div id={'form-date-customer'}
                     className={"select-date" + (this.state.date_value ? ' active' : '')}>
                    <div onClick={() => this.showDateOfBirth()} className="select-delivery-label">
                        {i18n.translator.translate('Select date')}
                    </div>
                    <DateTime
                        id={'datepicker-dob'}
                        className={"value"}
                        pickerOptions={{
                            format: "L",
                            widgetPositioning: {horizontal: 'auto', vertical: 'bottom'}
                        }}
                        value={DefaultValue}
                        onChange={this.onChangeDate}
                        readOnly={true}
                    />
                </div>
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

CustomerDateComponent.propTypes = {
    Label: PropTypes.string,
    Required: PropTypes.bool,
    OneRow: PropTypes.bool,
    Code: PropTypes.string,
    onSelect: PropTypes.func
};

/**
 * CustomerDateComponent
 */
export default ComponentFactory.get(CustomerDateComponent);
