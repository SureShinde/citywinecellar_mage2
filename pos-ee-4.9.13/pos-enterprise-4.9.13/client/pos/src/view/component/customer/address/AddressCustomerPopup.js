import React, {Fragment} from 'react';
import {Modal} from 'react-bootstrap'
import SmoothScrollbar from "smooth-scrollbar";
import CoreContainer from "../../../../framework/container/CoreContainer";
import CoreComponent from "../../../../framework/component/CoreComponent";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../../framework/factory/ContainerFactory";
import AddCustomerPopupConstant from "../../../constant/customer/AddCustomerPopupConstant";
import CustomerPopupService from "../../../../service/customer/CustomerPopupService";
import CustomerInputComponent from "../field/CustomerInputComponent";
import CustomerGroupComponent from "../field/CustomerGroupComponent";
import CustomerCheckboxComponent from "../field/CustomerCheckBoxComponent";
import CustomerStateComponent from "../field/CustomerStateComponent";
import CustomerDateComponent from "../field/CustomerDateComponent";
import CountryHelper from "../../../../helper/CountryHelper";
import _ from 'lodash';
import $ from 'jquery';
import cloneDeep from 'lodash/cloneDeep';
import CustomerAction from "../../../action/CustomerAction";
import CustomerAddressFieldService from "../../../../service/customer/CustomerAddressFieldService";
import ShippingConstant from "../../../constant/ShippingConstant";
import CustomAttributesProcessorService from "../../../../service/customer/CustomAttributesProcessorService";

export class AddressCustomerPopupComponent extends CoreComponent {
    static className = 'AddressCustomerPopupComponent';
    setPopupCustomerAddressElement = element => {
        this.popup_customer_address = element;
        if (!this.scrollbar && this.popup_customer_address) {
            this.scrollbar = SmoothScrollbar.init(this.popup_customer_address);
            setTimeout(() => {
                this.heightPopup('.popup-edit-customer .modal-dialog');
            }, 500);
        }
    };

    /**
     * constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            current_address: {},
            arrFieldBoxCustomer: [],
            arrFieldBoxAddress: [],
            default_country: "",
            default_state: CountryHelper.getDefaultState(),
            states: [],
            arrFieldBoxDefaultBilling: [],
            arrFieldBoxDefaultShipping: [],
            customer_fields: [],
            address_fields: [],
            default_shipping_fields: [],
            default_billing_fields: []
        };
    }

    /**
     * componentWillReceiveProps
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        let current_address = cloneDeep(nextProps.address);
        let country_id = current_address.country_id;
        let default_country_id = CountryHelper.getDefaultCountry() ? CountryHelper.getDefaultCountry().id : "";
        let default_regions = CountryHelper.getDefaultCountry() ? CountryHelper.getDefaultCountry().regions : [];
        let regions = CountryHelper.getCountry(country_id) ? CountryHelper.getCountry(country_id).regions : null;
        let default_state = current_address.region ? current_address.region : CountryHelper.getDefaultState();
        let default_country = country_id ? country_id : default_country_id;
        let default_zip_required = CountryHelper.isZipCodeRequired(default_country);
        let {customer} = this.props;
        this.setState({
            current_address: current_address,
            arrFieldBoxCustomer: [],
            arrFieldBoxAddress: [],
            default_country: country_id ? country_id : default_country_id,
            default_state: default_state,
            states: country_id ? regions : default_regions,
            default_zip_required: default_zip_required,
            arrFieldBoxDefaultBilling: [],
            arrFieldBoxDefaultShipping: [],
            customer_fields: [],
            address_fields: [],
            default_shipping_fields: [],
            default_billing_fields: []
        }, () => {
            this.setAddressFields(CustomerPopupService.setRowCustomerField(
                CustomerAddressFieldService.defaultFieldBoxCustomerAddress(this)));
            if (!$.isEmptyObject(customer) && (customer.addresses.length > 0)) {
                this.setDefaultShippingFields(CustomerPopupService.setRowCustomerField(
                    CustomerAddressFieldService.defaultFieldBoxDefaultShipping(this)));
                this.setDefaultBillingFields(CustomerPopupService.setRowCustomerField(
                    CustomerAddressFieldService.defaultFieldBoxDefaultBilling(this)));
            }
        });
    }

    /**
     * get height popup
     * @param elm
     */
    heightPopup(elm) {
        var height = $(window).height();
        $(elm).css('height', height + 'px');
    }

    /**
     * set customer field
     * @param customerFields
     */
    setCustomerFields(customerFields) {
        this.setState({customer_fields: customerFields});
    }

    /**
     * set address field
     * @param addressFields
     */
    setAddressFields(addressFields) {
        this.setState({address_fields: addressFields});
    }

    /**
     * set default shipping field
     * @param shippingFields
     */
    setDefaultShippingFields(shippingFields) {
        this.setState({default_shipping_fields: shippingFields});
    }

    /**
     * set default billing field
     * @param billingFields
     */
    setDefaultBillingFields(billingFields) {
        this.setState({default_billing_fields: billingFields});
    }

    /**
     * get value
     * @param code
     * @returns {string}
     */
    getValue(code) {
        let {current_address} = this.state;
        if (!current_address) {
            return "";
        }
        let value = current_address[code] ? current_address[code] : "";
        if (CustomAttributesProcessorService.isCustomAttribute(code, AddCustomerPopupConstant.POPUP_TYPE_ADDRESS)) {
            value = CustomAttributesProcessorService.getCustomAttributeValue(current_address, code);
        }
        return value;
    }

    /**
     * check default field
     * @param code
     * @returns {*}
     */
    checkDefaultField(code) {
        let {customer, isNewAddress} = this.props;
        if (isNewAddress) {
            return customer[code] ? customer[code] : "";
        } else {
            return this.getValue(code);
        }
    }

    /**
     * add field to arrFieldBoxCustomer
     * @param field
     */
    addFieldToArrFieldBoxCustomer(field) {
        let fields = this.state.arrFieldBoxCustomer;
        fields.push(field);
        this.setState({arrFieldBoxCustomer: fields});
    }

    /**
     * add field to arrFieldBoxAddress
     * @param field
     */
    addFieldToArrFieldBoxAddress(field) {
        let fields = this.state.arrFieldBoxAddress;
        fields.push(field);
        this.setState({arrFieldBoxAddress: fields});
    }

    /**
     * add field to arrFieldBoxDefaultShipping
     * @param field
     */
    addFieldToArrFieldBoxDefaultShipping(field) {
        let fields = this.state.arrFieldBoxDefaultShipping;
        fields.push(field);
        this.setState({arrFieldBoxDefaultShipping: fields});
    }

    /**
     * add field to arrFieldBoxDefaultBilling
     * @param field
     */
    addFieldToArrFieldBoxDefaultBilling(field) {
        let fields = this.state.arrFieldBoxDefaultBilling;
        fields.push(field);
        this.setState({arrFieldBoxDefaultBilling: fields});
    }

    /**
     * select value in group field
     * @param ref
     * @param value
     */
    onSelectGroupField(ref, value) {
        if (ref === AddCustomerPopupConstant.ATTRIBUTE_CODE_COUNTRY) {
            let addressFields = _.flattenDeep(this.state.address_fields);
            CustomerPopupService.getFieldRef(
                CustomerPopupService.getField(addressFields, 'state')
            ).setOptions(CountryHelper.getCountry(value).regions);

            let refPostCode = CustomerPopupService.getFieldRef(
                CustomerPopupService.getField(addressFields, 'postcode')
            );
            refPostCode.setState({IsRequired:CountryHelper.isZipCodeRequired(value)}, function () {
                refPostCode.validate();
            });
        }
    }

    /**
     * Set location info
     * @param locationInfo
     */
    setLocationInfo(locationInfo) {
        let allFieldAddress = _.flattenDeep(this.state.address_fields);
        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'street')
        ).setValue(locationInfo.street.street1);
        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'street')
        ).hiddenClearInput();

        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'street_2')
        ).setValue(locationInfo.street.street2);
        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'street_2')
        ).hiddenClearInput();

        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'city')
        ).setValue(locationInfo.city);
        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'city')
        ).hiddenClearInput();

        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'postcode')
        ).setValue(locationInfo.postcode);
        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'postcode')
        ).hiddenClearInput();

        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'country_id')
        ).setValue(locationInfo.country_id);

        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'state')
        ).setOptions(CountryHelper.getCountry(locationInfo.country_id).regions);
        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'state')
        ).setValue(locationInfo.region_id);

        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'postcode')
        ).setState({IsRequired: CountryHelper.isZipCodeRequired(locationInfo.country_id)});

        CustomerPopupService.getFieldRef(
            CustomerPopupService.getField(allFieldAddress, 'street')
        ).hiddenGoogleSuggest();
    }

    /**
     * save address
     */
    saveAddress() {
        let {
            customer, isNewAddress, isNewCustomer,
            onSaveAddress, editCustomer, showPopup, isShipping
        } = this.props;
        let allField = [];
        // allField.push(_.flattenDeep(this.state.customer_fields));
        allField.push(_.flattenDeep(this.state.address_fields));
        allField.push(_.flattenDeep(this.state.default_billing_fields));
        allField.push(_.flattenDeep(this.state.default_shipping_fields));
        // validate field address
        let checkValidate = false;
        for (let field of _.flattenDeep(allField)) {
            let validate = CustomerPopupService.getFieldRef(field).validate();
            if (validate) {
                checkValidate = validate;
            }
        }
        if (checkValidate) return;
        let address = CustomerPopupService.saveAddress(
            customer,
            this.state.current_address,
            _.flattenDeep(allField), isNewAddress);

        if(address.default_billing === true) {
            customer.telephone = address.telephone;
        }

        if (isNewAddress) {
            if (customer.addresses) {
                customer.addresses.push(address);
            } else {
                let addresses = [];
                addresses.push(address);
                customer.addresses = addresses;
            }
        } else {
            let indexAddress = _.findIndex(customer.addresses, _.pick(address, 'id'));
            if (indexAddress !== -1) {
                customer.addresses.splice(indexAddress, 1, address);
            }
        }
        if (isShipping) {
            customer.id ? editCustomer(customer, isShipping) : onSaveAddress(customer);
            showPopup(ShippingConstant.POPUP_TYPE_SHIPPING);
        } else {
            isNewCustomer ? onSaveAddress(customer) : editCustomer(customer);
            showPopup(AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER);
        }
    }

    /**
     * cancel popup address
     */
    cancelPopup() {
        if (this.props.isShipping) {
            this.props.showPopup(ShippingConstant.POPUP_TYPE_SHIPPING);
        } else {
            this.props.showPopup(AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER);
        }
    }

    /**
     * get header
     * @returns {XML}
     */
    getHeader() {
        let title = this.props.isNewAddress ? 'Add New Address' : 'Edit Address';
        return (
            <div className="modal-header">
                <button type="button"
                        className="cancel close-shipping-customer"
                        onClick={() => this.cancelPopup()}>
                    {this.props.t('Cancel')}
                </button>
                <h4 className="modal-title">{this.props.t(title)}</h4>
                <button type="button" className="save" onClick={() => this.saveAddress()}>
                    {this.props.t('Save')}
                </button>
            </div>
        )
    }

    /**
     * get body
     * @returns {XML}
     */
    getBody() {
        if (!this.props.isOpenCustomerAddress) {
            if (this.popup_customer_address) {
                SmoothScrollbar.destroy(this.popup_customer_address);
                this.scrollbar = null;
            }
        }
        return (
            <div data-scrollbar ref={this.setPopupCustomerAddressElement} className="modal-body">
                {
                    /*this.getField(this.state.customer_fields)*/
                }
                {
                    this.getField(this.state.address_fields)
                }
                {
                    this.getField(this.state.default_shipping_fields)
                }
                {
                    this.getField(this.state.default_billing_fields)
                }
            </div>
        )
    }

    /**
     * get field
     * @param fields
     * @returns {XML}
     */
    getField(fields) {
        let oneRow = false;
        let checkbox = false;
        let className = 'box-group';
        if (!fields || !fields.length) {
            className = '';
        }
        return (
            <div className={className}>
                {
                    fields.map(arrField => {
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
                                            if (field.type === AddCustomerPopupConstant.TYPE_FIELD_GROUP) {
                                                return (<CustomerGroupComponent
                                                    key={Math.random()}
                                                    Code={field.code}
                                                    ref={(node) => {
                                                        field.ref = node
                                                    }}
                                                    Options={field.options}
                                                    KeyValue={field.key_value}
                                                    KeyTitle={field.key_title}
                                                    OneRow={oneRow}
                                                    DefaultValue={field.default_value}
                                                    Label={this.props.t(field.label)}
                                                    onSelect={this.onSelectGroupField.bind(this)}
                                                    Required={field.required}/>)
                                            }
                                            else if (
                                                field.type === AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX
                                            ) {
                                                return (
                                                    <CustomerCheckboxComponent
                                                        key={Math.random()}
                                                        Code={field.code}
                                                        ref={(node) => {
                                                            field.ref = node
                                                        }}
                                                        Disabled={field.disabled}
                                                        OneRow={oneRow}
                                                        Label={this.props.t(field.label)}
                                                        IsCheck={field.check}/>
                                                )
                                            }
                                            else if (field.type === AddCustomerPopupConstant.TYPE_FIELD_STATE) {
                                                return (<CustomerStateComponent
                                                    STATES={field.states}
                                                    key={Math.random()}
                                                    Label={this.props.t(field.label)}
                                                    Code={field.code}
                                                    ref={(node) => {
                                                        field.ref = node
                                                    }}
                                                    refKey={field.ref}
                                                    DefaultValue={field.default_value}
                                                    OneRow={oneRow}
                                                    MaxLength={field.max_length}
                                                    Required={field.required}
                                                    IsOptional={field.optional}
                                                    KeyValue={field.key_value}
                                                    KeyTitle={field.key_title}
                                                    RequiredEmail={field.required_email}
                                                    onSelect={this.onSelectGroupField.bind(this)}/>)
                                            }
                                            else if (
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
                                                        OneRow={oneRow}
                                                        IsOptional={field.optional}
                                                    />
                                                )
                                            }
                                            else if (
                                                field.code === AddCustomerPopupConstant.ATTRIBUTE_CODE_POSTCODE
                                            ) {
                                                return (<CustomerInputComponent
                                                    key={Math.random()}
                                                    Code={field.code}
                                                    Label={this.props.t(field.label)}
                                                    ref={(node) => {
                                                        field.ref = node
                                                    }}
                                                    DefaultValue={field.default_value}
                                                    OneRow={oneRow}
                                                    MaxLength={field.max_length}
                                                    Required={this.state.default_zip_required}
                                                    IsOptional={!this.state.default_zip_required}
                                                    RequiredEmail={field.required_email}
                                                    GoogleSuggest={field.google_suggest}
                                                    setLocationInfo={this.setLocationInfo.bind(this)}

                                                />)
                                            }
                                            else {
                                                return (<CustomerInputComponent
                                                    key={Math.random()}
                                                    Code={field.code}
                                                    Label={this.props.t(field.label)}
                                                    ref={(node) => {
                                                        field.ref = node
                                                    }}
                                                    DefaultValue={field.default_value}
                                                    OneRow={oneRow}
                                                    MaxLength={field.max_length}
                                                    Required={field.required}
                                                    IsOptional={field.optional}
                                                    RequiredEmail={field.required_email}
                                                    GoogleSuggest={field.google_suggest}
                                                    setLocationInfo={this.setLocationInfo.bind(this)}

                                                />)
                                            }
                                        })
                                    }
                                </div>
                            )
                        }
                    )
                }
            </div>
        )
    }

    template() {
        return (
            <Fragment>
                <Modal
                    bsSize={"lg"}
                    className={"popup-edit-customer"}
                    dialogClassName={"popup-addBillingAddress in"}
                    show={this.props.isOpenCustomerAddress}
                >
                    {
                        this.getHeader()
                    }
                    {

                        this.getBody()
                    }
                </Modal>
            </Fragment>
        )
    }
}

/**
 *
 * @type {AddressCustomerPopupComponent}
 */
const component = ComponentFactory.get(AddressCustomerPopupComponent);

export class AddressCustomerPopupContainer extends CoreContainer {
    static className = 'AddressCustomerPopupContainer';

    /**
     * Map actions
     * @param dispatch
     * @returns editCustomer: (function(*=): *)}}
     */
    static mapDispatch(dispatch) {
        return {
            editCustomer: (customer, isShipping = false) => dispatch(CustomerAction.editCustomer(customer, isShipping))
        }
    }
}

/**
 *
 * @type {AddressCustomerPopupContainer}
 */
const container = ContainerFactory.get(AddressCustomerPopupContainer);

export default container.withRouter(component)
