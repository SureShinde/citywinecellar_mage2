// @flow
import 'babel-polyfill';
import React, {Component} from 'react';
import {ButtonToolbar, Popover, OverlayTrigger} from "react-bootstrap";

import {
    Label
} from './utils/styles';

import "./utils/popup.css"
import ConfigHelper from "../../../../helper/ConfigHelper";
const inputRenderer = ({ props }: Object) => <input {...props} />;
const BACKSPACE_KEY_CODE = 8;

type Props = {
    emailInputProps: Object,
    translator: Object
};

export class PayViaEmail extends Component<Props, State> {
    containerEmail;
    inputRenderer: any;

    static defaultProps = {
        emailInputProps: {},
        translator: {}
    };

    constructor(props: Props) {
        super(props);
        this.state = {
            errorText: null
        };
        this.inputRenderer = inputRenderer;
    }

    /**
     * get container Email
     * @param ref
     */
    getContainerEmail(ref) {
        this.containerEmail = ref
    };

    handleKeyDown = (ref: any) => {
        return (e: SyntheticInputEvent<*>) => {
            if (e.keyCode === BACKSPACE_KEY_CODE && !e.target.value) {
                this.props.autoFocus && ref.focus();
            }
        };
    };

    setFieldInvalid = (errorText: string, mapState: Object) => {
        const { invalidClassName, afterValidateCard } = this.props;

        const mainWrapper = document.getElementById('field-wrapper');
        mainWrapper && mainWrapper.classList.add(invalidClassName);

        const fieldWrapper = document.getElementById(
            mapState['state'].replace('ErrorText', '')
        );
        fieldWrapper && fieldWrapper.classList.add(invalidClassName);

        this.setState({
            errorText: this.translate(errorText),
            [mapState['state']]: this.translate(errorText)
        });
        afterValidateCard && afterValidateCard(false);
    };

    setEmail = async email => {
        this.emailField.value = email;
        this.handleEmailChange()({ target: { value: email } });
        this.handleEmailBlur()({ target: { value: email } });
    };

    isEmail = email => {
        let re = ConfigHelper.regexEmail;
        return re.test(String(email).toLowerCase());
    };

    handleEmailBlur = ({ onBlur }: { onBlur?: ?Function } = { onBlur: null }) => (
        e: SyntheticInputEvent<*>
    ) => {
        const { value } = e.target;
        if (!this.isEmail(value)) {
            let message = value.length
                ? 'Please enter a valid email address'
                : 'This is a required filed';
            this.setFieldInvalid(message, {
                state: 'ccEmailErrorText'
            });
            this.setState({errorMessageEmail: this.translate(message), showErrorMessageEmail: true});
        } else {
            this.setState({showErrorMessageEmail: false});
            this.setFieldValid({ state: 'ccEmailErrorText' });
        }
        const { emailInputProps } = this.props;
        emailInputProps.onBlur && emailInputProps.onBlur(e);
        onBlur && onBlur(e);
    };

    setFieldValid = mapState => {
        const { invalidClassName, afterValidateCard } = this.props;
        const mainWrapper = document.getElementById('field-wrapper');
        mainWrapper && mainWrapper.classList.remove(invalidClassName);

        const fieldWrapper = document.getElementById(
            mapState['state'].replace('ErrorText', '')
        );
        fieldWrapper && fieldWrapper.classList.remove(invalidClassName);

        this.setState({ errorText: null, [mapState['state']]: null });

        afterValidateCard && afterValidateCard(this.formIsValid(mapState['state']));
    };

    formIsValid = ignore => {
        let errorList = {};
        let requiredFieldValueList = [];
        if (this.state.isCardMode) {
            errorList = {
                ccNumberErrorText: this.state.ccNumberErrorText,
                ccExpDateErrorText: this.state.ccExpDateErrorText,
                ccCIDErrorText: this.state.ccCIDErrorText,
                ccZipErrorText: this.state.ccZipErrorText
            };

            requiredFieldValueList = [
                this.cardNumberField.value,
                this.cardExpiryField.value,
                this.cvcField.value
            ];
        } else {
            errorList = {
                ccEmailErrorText: this.state.ccEmailErrorText
            };

            requiredFieldValueList = [this.emailField.value];
        }

        ignore && delete errorList[ignore];

        let isValid = true;

        Object.values(errorList).forEach(errorText => {
            isValid &= !errorText;
        });

        requiredFieldValueList.forEach(value => {
            isValid &= !!value;
        });

        return isValid;
    };

    translate = word => {
        return this.props.translator[word] || word;
    };

    handleEmailChange = (
        { onChange }: { onChange?: ?Function } = { onChange: null }
    ) => (e: SyntheticInputEvent<*>) => {
        this.setState({showErrorMessageEmail: false});
        if (!this.isEmail(e.target.value) && this.props.autoFocus) {
            this.setFieldInvalid('Please enter a valid email address', {
                state: 'ccEmailErrorText'
            });
            this.setState({errorMessageEmail: this.translate('Please enter a valid email address'), showErrorMessageEmail: true});
        } else {
            this.setFieldValid({ state: 'ccEmailErrorText' });
        }
        const { emailInputProps } = this.props;
        emailInputProps.onChange && emailInputProps.onChange(e);
        onChange && onChange(e);
    };

    handleEmailKeyPress = (e: any) => {};

    /**
     * popover view
     * @param message
     */
    popoverView(message) {
        return (
            <Popover id="popover">
                <div>
                    { message }
                </div>
            </Popover>
        );
    }

    render = () => {
        const {
            errorMessageEmail,
            showErrorMessageEmail
        } = this.state;
        const {
            emailInputProps,
            containerClassName,
            containerStyle,
            inputClassName
        } = this.props;
        const popoverEmail = this.popoverView(errorMessageEmail);
        return (
            <div className={containerClassName} styled={containerStyle}>
                <div role="tabpanel" className="tab-pane by-email">
                    <div className="form-group last" ref={this.getContainerEmail.bind(this)}>
                        <Label onClick={() => this.emailField.focus()}>
                            {this.translate('Email')}
                        </Label>
                        {this.inputRenderer({
                            props: {
                                id: 'ccEmail',
                                ref: emailField => {
                                    this.emailField = emailField;
                                },
                                className: `form-control ${inputClassName}`,
                                placeholder: 'email@company.com',
                                type: 'email',
                                ...emailInputProps,
                                onBlur: this.handleEmailBlur(),
                                onChange: this.handleEmailChange(),
                                onKeyDown: this.handleKeyDown(this.emailField),
                                onKeyPress: this.handleEmailKeyPress
                            }
                        })}
                        <ButtonToolbar className={showErrorMessageEmail ? "validation-advice" : ""}>
                            <OverlayTrigger
                                trigger={['click', 'hover', 'focus']}
                                rootClose placement="bottom"
                                overlay={popoverEmail}
                                container={this.containerEmail}>
                        <span className={"dropdown-toggle"}
                        > </span>
                            </OverlayTrigger>
                        </ButtonToolbar>
                    </div>
                </div>
            </div>
        );
    };
}
