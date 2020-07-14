import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import CountryHelper from "../../helper/CountryHelper";
import AddCustomerPopupConstant from "../../view/constant/customer/AddCustomerPopupConstant";
import Config from "../../config/Config";
import CustomAttributesProcessorService from "./CustomAttributesProcessorService";

export class CustomerPopupService extends CoreService {
    static className = 'CustomerPopupService';

    /**
     * set row customer field
     * @param arrField
     * @returns {Array}
     */
    setRowCustomerField(arrField) {
        let allRow = [];
        for (let field of arrField) {
            let key = this.checkOneRowField(allRow);
            if (key >= 0 && field.oneRow === false) {
                allRow[key].push(field);
            } else {
                allRow.push([field]);
            }
        }
        return allRow;
    }

    /**
     * check one row field
     * @param allRow
     * @returns {number}
     */
    checkOneRowField(allRow) {
        // Fixbug firstname and lastname showed in two lines
        // Solution: only appends to last line
        if (allRow.length > 0) {
            let row = allRow[allRow.length - 1];
            if (row.length === 1 && row[0].oneRow === false) {
                return allRow.length - 1;
            }
        }
        return -1;
        /*
        let key = -1;
        for (let i = 0; i < allRow.length; i++) {
            let row = allRow[i];
            if (row.length === 1 && row[0].oneRow === false) {
                key = i;
                break;
            }
        }
        return key;
        */
    }

    /**
     * create customer field input
     * @param code
     * @param ref
     * @param type
     * @param label
     * @param default_value
     * @param optional
     * @param required
     * @param required_email
     * @param max_length
     * @param google_suggest
     * @param oneRow
     * @return {{code: *, ref: *, type: *, label: *, default_value: *, optional: *, required: *, required_email: *, max_length: *, google_suggest: *, oneRow: *}}
     */
    createCustomerFieldInput(code, ref, type, label, default_value, optional, required, required_email, max_length, google_suggest, oneRow) {
        let field = {
            code: code,
            ref: ref,
            type: type,
            label: label,
            default_value: default_value,
            optional: optional,
            required: required,
            required_email: required_email,
            max_length: max_length,
            google_suggest: google_suggest,
            oneRow: oneRow
        };
        return field;
    }

    /**
     * create field group
     * @param code
     * @param ref
     * @param type
     * @param label
     * @param default_value
     * @param required
     * @param options
     * @param key_value
     * @param key_title
     * @param oneRow
     * @param optional
     * @return {{code: *, ref: *, type: *, label: *, default_value: *, required: *, options: *, key_value: *, key_title: *, oneRow: *. optional: *}}
     */
    createCustomerFieldGroup(code, ref, type, label, default_value, required, options, key_value, key_title, oneRow, optional) {
        let field = {
            code: code,
            ref: ref,
            type: type,
            label: label,
            default_value: default_value,
            required: required,
            options: options,
            key_value: key_value,
            key_title: key_title,
            oneRow: oneRow,
            optional: optional
        };
        return field;
    }

    /**
     * create field date
     * @param code
     * @param ref
     * @param type
     * @param label
     * @param default_value
     * @param required
     * @param oneRow
     * @param optional
     * @returns {{code: *, ref: *, type: *, label: *, default_value: *, options: *, required: *, oneRow: *, optional: *},}
     */
    createCustomerFieldDate(code, ref, type, label, default_value, required, oneRow, optional) {
        let field = {
            code: code,
            ref: ref,
            type: type,
            label: label,
            default_value: default_value,
            required: required,
            oneRow: oneRow,
            optional: optional
        };
        return field;
    }

    /**
     * create field check
     * @param code
     * @param ref
     * @param type
     * @param label
     * @param check
     * @param disabled
     * @param oneRow
     * @return {{code: *, ref: *, type: *, label: *, check: *, disabled: *, oneRow: *}}
     */
    createCustomerFieldCheckBox(code, ref, type, label, check, disabled ,oneRow) {
        let field = {
            code: code,
            ref: ref,
            type: type,
            label: label,
            check: check,
            disabled: disabled,
            oneRow: oneRow
        };
        return field;
    }

    /**
     * create customer field input
     * @param code
     * @param states
     * @param ref
     * @param type
     * @param label
     * @param default_value
     * @param optional
     * @param required
     * @param required_email
     * @param max_length
     * @param google_suggest
     * @param key_value
     * @param key_title
     * @param oneRow
     * @returns {{code: *,
     * states: *,
     * ref: *,
     * type: *,
     * label: *,
     * default_value: *,
     * optional: *,
     * required: *,
     * required_email: *,
     * max_length: *,
     * google_suggest: *,
     * key_value: *,
     * key_title: *,
     * oneRow: *}}
     */
    createCustomerFieldState(code, states, ref, type, label, default_value,
                             optional, required, required_email, max_length,
                             google_suggest, key_value, key_title, oneRow) {
        let field = {
            code: code,
            states: states,
            ref: ref,
            type: type,
            label: label,
            default_value: default_value,
            optional: optional,
            required: required,
            required_email: required_email,
            max_length: max_length,
            google_suggest: google_suggest,
            key_value: key_value,
            key_title: key_title,
            oneRow: oneRow
        };
        return field;
    }

    /**
     * save customer
     * @param customer
     * @param data
     */
    saveCustomer(customer, data) {
        customer.subscriber_status = this.getFieldRef(this.getField(data, 'subscriber_status')).input.checked ? 1 : 0;
        customer = this.getFormData(customer, data, Config.config.customer_form);
        customer = CustomAttributesProcessorService.processCustomer(customer);
        return customer;
    }

    /**
     * save address
     * @param customer
     * @param current_address
     * @param data
     * @param is_new_address
     * @return {*}
     */
    saveAddress(customer, current_address, data, is_new_address) {
        let defaultShippingField = this.getField(data, 'default_shipping');
        let defaultBillingField = this.getField(data, 'default_billing');
        current_address = {
            id: current_address.id,
            sub_id: current_address.sub_id ? current_address.sub_id : current_address.id,
            customer_id: customer.id,
            is_new_address: is_new_address,
            region: this.getRegion(data),
            region_id: this.getRegion(data).region_id,
            default_shipping: defaultShippingField ? this.getFieldRef(defaultShippingField).input.checked : true,
            default_billing: defaultBillingField ? this.getFieldRef(defaultBillingField).input.checked : true
        };
        current_address = this.getFormData(current_address, data, Config.config.customer_address_form);
        current_address = CustomAttributesProcessorService.processAddress(current_address);
        this.checkDefaultAddress(customer, current_address.default_shipping, current_address.default_billing);
        return current_address;
    }

    /**
     * get form data
     * @param object
     * @param data
     * @param formAttributes
     * @return {*}
     */
    getFormData(object, data, formAttributes = []) {
        formAttributes.forEach(field => {
            let displayFormField = this.getField(data, field.attribute_code);
            if (!displayFormField) {
                return;
            }

            let displayFormFieldRef = this.getFieldRef(displayFormField);
            if (displayFormField.type === AddCustomerPopupConstant.TYPE_FIELD_GROUP) {
                object[field.attribute_code] = displayFormFieldRef.select.value;
            } else if (displayFormField.type === AddCustomerPopupConstant.TYPE_FIELD_INPUT) {
                if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_TEXT) {
                    object[field.attribute_code] = displayFormFieldRef.input.value;
                } else if (field.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_MULTILINE) {
                    object[field.attribute_code] = this.getMultilineFieldData(
                        displayFormField, field.multiline_count, data
                    );
                }
            } else if (displayFormField.type === AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX) {
                object[field.attribute_code] = displayFormFieldRef.input.checked ? 1 : 0;
            } else if (displayFormField.type === AddCustomerPopupConstant.TYPE_FIELD_DATE) {
                object[field.attribute_code] = displayFormFieldRef.state.date_value;
            }
        });
        return object;
    }

    /**
     * Get field
     * @param data
     * @param code
     */
    getField(data, code) {
        return data.find(item => item.code === code);
    }

    /**
     * Get field's ref
     * @param field
     * @returns {null}
     */
    getFieldRef(field) {
        return field && field.ref ? field.ref.getWrappedInstance() : null;
    }

    /**
     * get multiline field's data
     * @param displayFormField
     * @param multilineCount
     * @param data
     * @return {Array}
     */
    getMultilineFieldData(displayFormField, multilineCount, data) {
        let fieldData = [];
        fieldData.push(this.getFieldRef(displayFormField).input.value);
        for (let i = 2; i <= multilineCount; i++) {
            let code = displayFormField.code + '_' + i;
            let fieldRef = this.getFieldRef(this.getField(data, code));
            if (fieldRef && fieldRef.input.value) {
                fieldData.push(fieldRef.input.value);
            }
        }
        return fieldData;
    }

    /**
     * get region
     * @param data
     * @returns {{region_code: *, region: *, region_id: number}}
     */
    getRegion(data) {
        let field = this.getFieldRef(this.getField(data, 'state')).getValue();
        let isInput = field.type === 'input';
        let region = {
            region_code: isInput ? field.value : field.state.code,
            region: isInput ? field.value : field.state.name,
            region_id: isInput ? 0 : field.state.id
        };
        return region;
    }

    /**
     * check default address
     * @param customer
     * @param default_shipping
     * @param default_billing
     */
    checkDefaultAddress(customer, default_shipping, default_billing) {
        if (customer.id) {
            if (default_shipping) {
                let address_shipping = customer.addresses.find(item => item.default_shipping === true);
                if(address_shipping) {
                    address_shipping.default_shipping = false;
                }
            }
            if (default_billing) {
                let address_billing = customer.addresses.find(item => item.default_billing === true);
                if(address_billing) {
                    address_billing.default_billing = false;
                }
            }
        }
    }

    /**
     * get information address
     * @param address
     * @returns {string}
     */
    getInfoAddress(address) {
        let info = address.street[0] + ", " + address.city + ", " + address.postcode;
        return info;
    }

    /**
     * get information country
     * @param address
     * @returns {string}
     */
    getInfoCountry(address) {
        let info = "";
        if (address.country_id) {
            let country = CountryHelper.getCountry(address.country_id).name;
            let region = address.region.region;
            if (address.region) {
                if (country && region) {
                    info = country + ", " + region;
                } else {
                    if(!country && region) {
                        info = region;
                    } else if(country && !region) {
                        info = country;
                    }
                }
            }
        }
        return info;
    }
}

/** @type CustomerPopupService */
let customerPopupService = ServiceFactory.get(CustomerPopupService);

export default customerPopupService;
