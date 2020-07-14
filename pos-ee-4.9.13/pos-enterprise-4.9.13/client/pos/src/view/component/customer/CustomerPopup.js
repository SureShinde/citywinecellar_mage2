import React, {Fragment} from "react";
import {Modal} from "react-bootstrap";
import CoreContainer from "../../../framework/container/CoreContainer";
import CoreComponent from "../../../framework/component/CoreComponent";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import AddressCustomerPopup from "./address/AddressCustomerPopup";
import AddCustomerPopupConstant from "../../constant/customer/AddCustomerPopupConstant";
import CustomerCheckboxComponent from "./field/CustomerCheckBoxComponent";
import CustomerInputComponent from "./field/CustomerInputComponent";
import CustomerGroupComponent from "./field/CustomerGroupComponent";
import CustomerDateComponent from "./field/CustomerDateComponent";
import _ from 'lodash';
import SmoothScrollbar from 'smooth-scrollbar';
import {toast} from "react-toastify";
import CustomerPopupService from "../../../service/customer/CustomerPopupService";
import cloneDeep from 'lodash/cloneDeep';
import CustomerAction from "../../action/CustomerAction";
import CustomerService from "../../../service/customer/CustomerService";
import CustomerDefaultFieldService from "../../../service/customer/CustomerDefaultFieldService";
import $ from 'jquery';
import CustomerConstant from "../../constant/CustomerConstant";
import CreditmemoAction from "../../action/order/CreditmemoAction";
import CustomAttributesProcessorService from "../../../service/customer/CustomAttributesProcessorService";

export class CustomerPopupComponent extends CoreComponent {
    static className = 'CustomerPopupComponent';
    canSaveCustomer = true;
    is_saving_customer = false;
    is_checking_email = false;
    setPopupCustomerElement = element => {
        this.popup_customer = element;
        if (!this.scrollbar && this.popup_customer) {
            this.scrollbar = SmoothScrollbar.init(this.popup_customer);
            this.heightPopup('.popup-edit-customer .modal-dialog');
        }
    };

    /**
     * constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            current_customer: {},
            arrField: [],
            customer_fields: [],
            current_address: {},
            isNewAddress: false,
        };
    }

    /**
     * componentWillReceiveProps
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        let customer = nextProps.customer;
        let current_customer = (
                Object.keys(customer).length > Object.keys(CustomerConstant.NEW_CUSTOMER_DATA).length
                && Object.keys(customer).length > Object.keys(this.state.current_customer).length
                && customer !== this.state.current_customer
            ) || (customer.id !== this.state.current_customer.id) || nextProps.isShipping ?
            cloneDeep(customer) : this.state.current_customer;
        this.setState({
            current_customer: current_customer,
            arrField: [],
            customer_fields: []},
            () => {
            this.setCustomerFields(CustomerPopupService.setRowCustomerField(
                CustomerDefaultFieldService.defaultCustomerField(this)));
            if (this.props.isOpenCustomerPopup && nextProps.isShipping) {
                this.props.resetIsShipping();
            }
        });
    }

    /**
     * get height popup
     * @param elm
     */
    heightPopup(elm) {
        var height = $( window ).height();
        $(elm).css('height', height + 'px');
    }

    /**
     * hide popup
     */
    hidePopup() {
        if (this.state.current_customer && this.state.current_customer.id) {
            this.props.showPopup();
        } else {
            this.props.showPopup(AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER_LIST);
        }
    }

    /**
     * cancel popup
     */
    cancelPopup(){
        let {current_customer} = this.state;
        let {customer} = this.props;
        current_customer.firstname = customer.firstname;
        current_customer.lastname = customer.lastname;
        current_customer.telephone = customer.telephone;
        current_customer.email = customer.email;
        this.props.showPopup();
    }

    /**
     * set customer fields
     * @param customerFields
     */
    setCustomerFields(customerFields) {
        this.setState({customer_fields: customerFields});
    }

    /**
     * add field to arrField
     * @param field
     */
    addFieldToArrField(field) {
        let fields = this.state.arrField;
        fields.push(field);
        this.setState({arrField: fields});
    }

    /**
     * get value
     * @param code
     * @returns {string}
     */
    getValue(code) {
        let {current_customer} = this.state;
        if(!current_customer) {
            return "";
        }
        let value = current_customer[code] ? current_customer[code] : "";
        if (CustomAttributesProcessorService.isCustomAttribute(code, AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER)) {
            value = CustomAttributesProcessorService.getCustomAttributeValue(current_customer, code);
        }
        return value;
    }

    /**
     * get custom attribute by Code
     * @param code
     * @returns {string}
     */
    getCustomAttributeByCode(code) {
        let {current_customer} = this.state;
        let attributeValue = "";
        if(!current_customer) {
            return "";
        }
        if(current_customer.custom_attributes){
            let attribute = current_customer.custom_attributes.find(item => item.attribute_code === code);
            if(attribute){
                attributeValue = attribute.value;
            }
        }
        return attributeValue;
    }
    /**
     * input change
     * @param code
     * @param value
     */
    inputFieldOnChange(code, value) {
        this.state.current_customer[code] = value;
        this.state.current_customer =
            CustomAttributesProcessorService.updateCustomerData(this.state.current_customer, code, value);
    }

    /**
     * on select
     * @param code
     * @param value
     */
    onSelect(code, value) {
        this.state.current_customer[code] = value;
        this.state.current_customer =
            CustomAttributesProcessorService.updateCustomerData(this.state.current_customer, code, value);
    }

    /**
     * check email
     * @param email
     */
    checkEmail(email) {
        this.canSaveCustomer = false;
        let allFields = _.flattenDeep(this.state.customer_fields);
        if (this.props.isNewCustomer) {
            this.searchEmail(allFields, email);
        } else {
            if(email !== this.props.customer.email) {
                this.searchEmail(allFields, email);
            } else {
                this.canSaveCustomer = true;
            }
        }
    }

    /**
     * search email
     * @param allFields
     * @param email
     */
    searchEmail(allFields, email) {
        this.is_checking_email = true;
        CustomerService.checkEmail(email).then(result => {
            this.is_checking_email = false;
            if (!result) {
                let field = CustomerPopupService.getField(allFields, 'email');
                CustomerPopupService.getFieldRef(field).setEmailUnAvailable();
            } else {
                this.canSaveCustomer = true;
                if(this.is_saving_customer && !this.is_checking_email) {
                    this.saveCustomer();
                }
            }
        });
    }

    /**
     * onclick save customer
     */
    saveCustomer() {
        this.is_saving_customer = true;
        let {
            isNewCustomer, isNewCustomerCreditmemo, order,
            createCustomer, editCustomer, creditmemoCreateCustomer
        } = this.props;
        let allFields = _.flattenDeep(this.state.customer_fields);
        // validate field customer
        let checkValidate = false;
        for (let field of allFields) {
            let validate = CustomerPopupService.getFieldRef(field).validate();
            if (validate) {
                checkValidate = validate;
            }
        }
        if (checkValidate) {
            return this;
        }
        let customer = CustomerPopupService.saveCustomer(this.state.current_customer, allFields);
        // request create or update customer
        if (isNewCustomer) {
            if (isNewCustomerCreditmemo && order) {
                creditmemoCreateCustomer(order, customer.email, isNewCustomer, customer);
            } else {
                createCustomer(customer);
            }
        } else {
            editCustomer(customer);
        }
        toast.success(
                isNewCustomer ? (
                        isNewCustomerCreditmemo ?
                            this.props.t('A customer account has been created successfully.')
                            :
                            this.props.t('Customer\'s information have been created successfully!')
                    ) :
                    this.props.t('Customer\'s information have been updated successfully!'),
            {
                position: toast.POSITION.BOTTOM_CENTER,
                className: 'wrapper-messages messages-success',
                autoClose: 2000
            }
        );
        this.is_saving_customer = false;
        this.hidePopup();
    }

    /**
     * save address
     * @param customer
     */
    saveAddress(customer) {
        this.setState({current_customer: customer});
    }

    /**
     * show popup address
     * @param isNewAddress
     * @param address
     */
    showPopUpAddress(isNewAddress, address) {
        if (isNewAddress) {
            address.id = new Date().getTime();
            address.sub_id = address.id;
        } else{
            if(address.custom_attributes) {
                address.custom_attributes.forEach(function (customAttribute) {
                    if(customAttribute.attribute_code === 'sub_id') {
                        address.sub_id = customAttribute.value;
                    }
                });
            }
        }
        this.setState({
            isNewAddress: isNewAddress,
            current_address: address,
        }, () => {
            this.props.showPopup(AddCustomerPopupConstant.POPUP_TYPE_ADDRESS);
        });
    }

    /**
     * get customer name
     * @returns {string}
     */
    getCustomerName() {
        let {current_customer} = this.state;
        return current_customer.firstname + " " + current_customer.lastname;
    }

    /**
     * @return boolean
     */
    isDisableTelephoneField(field) {
        if(field.code !== 'telephone') {
            return false;
        }

        if(this.state.current_customer.default_billing) {
            return true;
        }

        if((this.props.isNewCustomer || this.state.current_customer.is_creating)
            && this.state.current_customer.addresses
            && this.state.current_customer.addresses.length) {
            return true;
        }

        if(this.state.current_customer.addresses && this.state.current_customer.addresses.length) {
            let default_billing = null;
            this.state.current_customer.addresses.forEach(function (address) {
                if(default_billing) {
                    return;
                }

                if(address.default_billing) {
                    default_billing = address;
                }
            });
            if(default_billing) {
                return true;
            }
        }

        return false;
    }

    template() {
        let oneRow = false;
        let checkbox = false;
        if (!this.props.isOpenCustomerPopup) {
            if (this.popup_customer) {
                SmoothScrollbar.destroy(this.popup_customer);
                this.scrollbar = null;
            }
        }
        return (
            <Fragment>
                <Modal
                    bsSize={"lg"}
                    className={"popup-edit-customer"}
                    dialogClassName={"popup-create-customer in"}
                    show={this.props.isOpenCustomerPopup}
                >
                    <div className="modal-header">
                        <button
                            type="button"
                            className="cancel"
                            data-dismiss="modal"
                            aria-label="Close"
                            onClick={() => this.cancelPopup()}
                        >
                            {this.props.t('Cancel')}
                        </button>
                        <h4 className="modal-title">
                            {
                                this.props.isNewCustomer ? this.props.t('New Customer') : this.getCustomerName()
                            }</h4>
                        <button type="button" className="save" disabled={!this.canSaveCustomer}
                                onClick={() => {
                                    if(this.canSaveCustomer) this.saveCustomer()}
                                }>{this.props.t('Save')}</button>
                    </div>
                    <div data-scrollbar ref={this.setPopupCustomerElement} className="modal-body">
                        <div className="box-group">
                            {
                                this.state.customer_fields.map(arrField => {
                                    ((arrField.length === 1) &&
                                        arrField[0].type === AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX) ?
                                        checkbox = true : checkbox = false;
                                        return (
                                            <div
                                                className={checkbox ? "row form-group form-checkbox" : "row form-group"}
                                                key={Math.random()}>
                                                {
                                                    arrField.map((field) => {
                                                        (arrField.length > 1) ? oneRow = false : oneRow = true;
                                                        if (field.type === AddCustomerPopupConstant.TYPE_FIELD_INPUT) {
                                                            return (<CustomerInputComponent
                                                                key={Math.random()}
                                                                Code={field.code}
                                                                isDisabled={
                                                                    (field.code === 'email' &&
                                                                    this.state.current_customer.is_creating)
                                                                    ||
                                                                    (this.isDisableTelephoneField(field))
                                                                }
                                                                Label={this.props.t(field.label)}
                                                                ref={(node) => {
                                                                    field.ref = node
                                                                }}
                                                                DefaultValue={field.default_value}
                                                                OneRow={oneRow}
                                                                MaxLength={field.max_length}
                                                                Required={field.required}
                                                                RequiredEmail={field.required_email}
                                                                IsOptional={field.optional}
                                                                inputFieldOnChange={this.inputFieldOnChange.bind(this)}
                                                                checkEmail={this.checkEmail.bind(this)}/>)
                                                        }
                                                        if (field.type === AddCustomerPopupConstant.TYPE_FIELD_GROUP) {
                                                            return (<CustomerGroupComponent
                                                                key={Math.random()}
                                                                Code={field.code}
                                                                ref={(node) => {
                                                                    field.ref = node
                                                                }}
                                                                DefaultValue={field.default_value}
                                                                Options={field.options}
                                                                KeyValue={field.key_value}
                                                                KeyTitle={field.key_title}
                                                                OneRow={oneRow}
                                                                onSelect={this.onSelect.bind(this)}
                                                                Label={this.props.t(field.label)}
                                                                Required={field.required}
                                                                Optional = {field.optional}
                                                            />)
                                                        }
                                                        if (
                                                            field.type === AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX
                                                        ) {
                                                            return (
                                                                <CustomerCheckboxComponent
                                                                    key={Math.random()}
                                                                    Code={field.code}
                                                                    ref={(node) => {
                                                                        field.ref = node
                                                                    }}
                                                                    Label={this.props.t(field.label)}
                                                                    IsCheck={field.check}
                                                                    Disabled={field.disabled}
                                                                    onSelect={this.onSelect.bind(this)}
                                                                    OneRow={oneRow}/>
                                                            )
                                                        }
                                                        if (
                                                            field.type === AddCustomerPopupConstant.TYPE_FIELD_DATE
                                                        ) {
                                                            return (<CustomerDateComponent
                                                                key={Math.random()}
                                                                Code={field.code}
                                                                Label={this.props.t(field.label)}
                                                                ref={(node) => {
                                                                    field.ref = node
                                                                }}
                                                                DefaultValue={field.default_value}
                                                                Required={field.required}
                                                                onSelect={this.onSelect.bind(this)}
                                                                OneRow={oneRow}
                                                                IsOptional = {field.optional}
                                                                />
                                                            )
                                                        }
                                                        return false;
                                                    })
                                                }
                                            </div>
                                        )
                                    }
                                )
                            }
                        </div>
                        <div className="box-group modal-actions">
                            <div className="row">
                                <div className="col-sm-12 toggle-shipping-customer"
                                     onClick={() => this.showPopUpAddress(true, {})}>
                                    <span>Address</span>
                                    <a className="action"> </a>
                                </div>
                            </div>
                            <div className="address-content">
                                <div className="row">
                                    {
                                       this.state.current_customer ?
                                           this.listAddress(this.state.current_customer.addresses)
                                           : null
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </Modal>
                <AddressCustomerPopup isOpenCustomerAddress={this.props.isOpenCustomerAddress}
                                      address={this.state.current_address}
                                      customer={this.state.current_customer}
                                      onSaveAddress={this.saveAddress.bind(this)}
                                      showPopup={(type) => this.props.showPopup(type)}
                                      isNewCustomer={this.props.isNewCustomer}
                                      isNewAddress={this.state.isNewAddress}/>
            </Fragment>
        )
    }

    /**
     * list addresses
     * @param addresses
     * @returns {*}
     */
    listAddress(addresses) {
        if(!addresses || !addresses.length) return null;
        // sort default address to top
        // let sorted = addresses.sort((a, b) => b.default_shipping || b.default_billing);
        addresses.map((address, key) => {
            if(address.default_billing && address.default_shipping) {
                addresses.splice(key, 1);
                addresses.splice(0,0, address);
            }
            else if(address.default_billing) {
                addresses.splice(key, 1);
                addresses.splice(1,0, address);
            }
            else if(address.default_shipping){
                addresses.splice(key, 1);
                addresses.splice(0,0, address);
            }
            return address;
        });
        return (
            <Fragment>
                {
                    addresses.map(address => {
                        let default_billing = address.default_billing;
                        let default_shipping = address.default_shipping;
                        let label_default_address = "";
                        if (default_shipping) {
                            label_default_address = "Default Shipping Address";
                        }
                        if(default_billing) {
                            label_default_address = "Default Billing Address";
                        }
                        return (
                            (default_billing && default_shipping) ?
                                <Fragment key={Math.random()}>
                                    <div className="col-sm-6 " onClick={() => this.showPopUpAddress(false, address)}>
                                        <div className="box-address active">
                                            <p className="text-right text-theme">
                                                { this.props.t('Default Shipping Address') }
                                            </p>
                                            <strong className="title">
                                                {address.firstname + " " + address.lastname}
                                            </strong>
                                            <p>{CustomerPopupService.getInfoAddress(address)}</p>
                                            <p>{CustomerPopupService.getInfoCountry(address)}</p>
                                            <p>{address.telephone}</p>
                                        </div>
                                    </div>
                                    <div className="col-sm-6 " onClick={() => this.showPopUpAddress(false, address)}>
                                        <div className="box-address active">
                                            <p className="text-right text-theme">
                                                { this.props.t('Default Billing Address') }
                                            </p>
                                            <strong className="title">
                                                {address.firstname + " " + address.lastname}
                                            </strong>
                                            <p>{CustomerPopupService.getInfoAddress(address)}</p>
                                            <p>{CustomerPopupService.getInfoCountry(address)}</p>
                                            <p>{address.telephone}</p>
                                        </div>
                                    </div>
                                </Fragment>
                                :
                                <div key={Math.random()}
                                     className="col-sm-6 "
                                     onClick={() => this.showPopUpAddress(false, address)}>
                                    <div
                                        className={(default_shipping || default_billing) ?
                                            "box-address active" : "box-address"}>
                                        <p className={(default_shipping || default_billing) ?
                                            "text-right text-theme" : "hidden"}>
                                            { this.props.t(label_default_address) }
                                        </p>
                                        <strong className="title">{address.firstname + " " + address.lastname}</strong>
                                        <p>{CustomerPopupService.getInfoAddress(address)}</p>
                                        <p>{CustomerPopupService.getInfoCountry(address)}</p>
                                        <p>{address.telephone}</p>
                                    </div>
                                </div>
                        )
                    })
                }
            </Fragment>
        )
    }
}

/**
 *
 * @type {CustomerPopupComponent}
 */
const component = ComponentFactory.get(CustomerPopupComponent);

class CustomerPopupContainer extends CoreContainer {
    static className = 'CustomerPopupContainer';

    // This maps the state to the property of the component
    static mapState(state) {
        let {isShipping} = state.core.customer.customerList;
        return {isShipping: isShipping};
    }

    /**
     * Map actions
     * @param dispatch
     * @return {{createCustomer: function(*=): *, editCustomer: function(*=): *, creditmemoCreateCustomer: function(*=, *=, *=): *}}
     */
    static mapDispatch(dispatch) {
        return {
            createCustomer: (customer) => dispatch(CustomerAction.createCustomer(customer)),
            editCustomer: (customer) => dispatch(CustomerAction.editCustomer(customer)),
            resetIsShipping: () => dispatch(CustomerAction.resetIsShipping()),
            creditmemoCreateCustomer: (order, email, isNewAccount, newCustomer) => dispatch(
                CreditmemoAction.creditmemoCreateCustomer(order, email, isNewAccount, newCustomer)
            ),
        }
    }
}

/**
 *
 * @type {CustomerPopupContainer}
 */
const container = ContainerFactory.get(CustomerPopupContainer);

export default container.withRouter(component);
