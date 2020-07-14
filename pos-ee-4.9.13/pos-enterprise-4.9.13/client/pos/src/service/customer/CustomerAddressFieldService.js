import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import CustomerPopupService from "./CustomerPopupService";
import AddCustomerPopupConstant from "../../view/constant/customer/AddCustomerPopupConstant";
import CountryHelper from "../../helper/CountryHelper";
import Config from "../../config/Config";
import _ from "lodash";


export class CustomerAddressFieldService extends CoreService {
    static className = 'CustomerAddressFieldService';

    /**
     * default field box customer address
     * @param component
     * @return {Array}
     */
    defaultFieldBoxCustomerAddress(component) {
        let addressFormFields = Config.config.customer_address_form ? Config.config.customer_address_form : [];
        addressFormFields = _.orderBy(addressFormFields, 'sort_order');
        addressFormFields.forEach(field => {
            if (
                field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_HIDDEN
                || !field.visible
            ) {
                return;
            }
            if (field.attribute_code === AddCustomerPopupConstant.ATTRIBUTE_CODE_REGION) {
                component.addFieldToArrFieldBoxAddress(
                    CustomerPopupService.createCustomerFieldState(
                        'state',
                        component.state.states,
                        'state',
                        AddCustomerPopupConstant.TYPE_FIELD_STATE,
                        "State or Province",
                        component.state.default_state,
                        !field.required,
                        field.required,
                        false,
                        255,
                        false,
                        "id",
                        "name",
                        false)
                );
                return;
            }

            if (
                field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_TEXT
                || field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_MULTILINE
            ) {
                this.addInputField(component, field);
            } else if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_SELECT) {
                this.addSelectField(component, field);
            } else if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_BOOLEAN) {
                this.addCheckboxField(component, field);
            } else if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_DATE) {
                this.addDateField(component, field);
            }
        });
        return component.state.arrFieldBoxAddress
    }

    /**
     * add input field to form
     * @param component
     * @param field
     */
    addInputField(component, field) {
        let maxLengthRule = field.validation_rules.find(
            rule => rule.name === AddCustomerPopupConstant.VALIDATION_RULE_MAX_LENGTH
        );
        let maxLength = maxLengthRule ? Number(maxLengthRule.value) : 255;
        let defaultValue = component.checkDefaultField(field.attribute_code);
        if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_MULTILINE) {
            defaultValue = defaultValue[0];
        }
        component.addFieldToArrFieldBoxAddress(
            CustomerPopupService.createCustomerFieldInput(
                field.attribute_code,
                field.attribute_code,
                AddCustomerPopupConstant.TYPE_FIELD_INPUT,
                field.frontend_label,
                defaultValue,
                !field.required,
                field.required,
                false,
                maxLength,
                field.attribute_code === AddCustomerPopupConstant.ATTRIBUTE_CODE_STREET,
                field.attribute_code === AddCustomerPopupConstant.ATTRIBUTE_CODE_VAT
            )
        );

        if (field.multiline_count > 1) {
            for (let i = 2; i <= field.multiline_count; i++) {
                component.addFieldToArrFieldBoxAddress(
                    CustomerPopupService.createCustomerFieldInput(
                        field.attribute_code + '_' + i,
                        field.attribute_code + '_' + i,
                        AddCustomerPopupConstant.TYPE_FIELD_INPUT,
                        field.frontend_label + ' ' + i,
                        (component.getValue(field.attribute_code).length > 1)
                            ? component.getValue(field.attribute_code)[i-1] : "",
                        true,
                        false,
                        false,
                        maxLength,
                        false,
                        field.attribute_code === AddCustomerPopupConstant.ATTRIBUTE_CODE_VAT
                    )
                );
            }
        }
    }

    /**
     * add select field to form
     * @param component
     * @param field
     */
    addSelectField(component, field) {
        let options = field.options;
        let keyValue = 'value';
        let keyTitle = 'label';
        let defaultValue = component.checkDefaultField(field.attribute_code);
        if (field.attribute_code === AddCustomerPopupConstant.ATTRIBUTE_CODE_COUNTRY) {
            options = CountryHelper.getAllCountries();
            defaultValue = component.state.default_country;
            keyValue = "id";
            keyTitle = "name";
        }
        component.addFieldToArrFieldBoxAddress(
            CustomerPopupService.createCustomerFieldGroup(
                field.attribute_code,
                field.attribute_code,
                AddCustomerPopupConstant.TYPE_FIELD_GROUP,
                field.frontend_label,
                defaultValue,
                field.required,
                options,
                keyValue,
                keyTitle,
                false
            )
        );
    }

    /**
     * add checkbox field
     * @param component
     * @param field
     */
    addCheckboxField(component, field) {
        component.addFieldToArrFieldBoxAddress(
            CustomerPopupService.createCustomerFieldCheckBox(
                field.attribute_code,
                field.attribute_code,
                AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX,
                field.frontend_label,
                component.checkDefaultField(field.attribute_code),
                false,
                false
            )
        );
    }

    /**
     * add date field
     * @param component
     * @param field
     */
    addDateField(component, field) {
        component.addFieldToArrFieldBoxAddress(
            CustomerPopupService.createCustomerFieldDate(
                field.attribute_code, /*code*/
                field.attribute_code, /*ref*/
                AddCustomerPopupConstant.TYPE_FIELD_DATE, /*type*/
                field.frontend_label, /*label*/
                component.checkDefaultField(field.attribute_code), /*default_value*/
                field.required, /*required*/
                false /*oneRow*/,
                !field.required /*optional*/
            )
        );
    }

    /**
     * default field shipping
     * @returns {Array}
     */
    defaultFieldBoxDefaultShipping(component) {
        component.addFieldToArrFieldBoxDefaultShipping(
            CustomerPopupService.createCustomerFieldCheckBox(
                'default_shipping',
                'default_shipping',
                AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX,
                "Use as default Shipping Address",
                this.checkShippingAddress(component),
                this.checkDisableShippingAddress(component),
                true)
        );
        return component.state.arrFieldBoxDefaultShipping;
    }

    /**
     * default field billing
     * @returns {Array}
     */
    defaultFieldBoxDefaultBilling(component) {
        component.addFieldToArrFieldBoxDefaultBilling(
            CustomerPopupService.createCustomerFieldCheckBox(
                'default_billing',
                'default_billing',
                AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX,
                "Use as default Billing Address",
                this.checkBillingAddress(component),
                this.checkDisableBillingAddress(component),
                true)
        );
        return component.state.arrFieldBoxDefaultBilling;
    }

    /**
     * check shipping address
     * @returns {*}
     */
    checkShippingAddress(component) {
        let {isNewAddress, customer} = component.props;
        if (customer.addresses && customer.addresses.length) {
            if(isNewAddress) {
                let default_shipping = customer.addresses.find(item => item.default_shipping);
                if (default_shipping) {
                    return false;
                } else {
                    return true
                }
            } else {
                return component.getValue('default_shipping');
            }
        } else {
            return true;
        }
    }

    /**
     * check billing address
     * @returns {*}
     */
    checkBillingAddress(component) {
        let {isNewAddress, customer} = component.props;
        if (customer.addresses && customer.addresses.length) {
            if(isNewAddress) {
                let default_billing = customer.addresses.find(item => item.default_billing);
                if (default_billing) {
                    return false;
                } else {
                    return true
                }
            } else {
                return component.getValue('default_billing');
            }
        } else {
            return true;
        }
    }

    /**
     * check disable shipping address
     * @returns {*}
     */
    checkDisableShippingAddress(component) {
        let {address, customer} = component.props;
        if (customer.addresses && customer.addresses.length) {
            if (address) {
                if(address.default_shipping) {
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    /**
     * check disable billing address
     * @returns {*}
     */
    checkDisableBillingAddress(component) {
        let {address, customer} = component.props;
        if (customer.addresses && customer.addresses.length) {
            if (address) {
                if(address.default_billing) {
                    return true;
                }
            }
        } else {
            return true;
        }
    }
}

/** @type CustomerAddressFieldService */
let customerAddressFieldService = ServiceFactory.get(CustomerAddressFieldService);

export default customerAddressFieldService;