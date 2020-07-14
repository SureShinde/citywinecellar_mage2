import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import Config from "../../config/Config";
import AddCustomerPopupConstant from "../../view/constant/customer/AddCustomerPopupConstant";

export class CustomAttributesProcessorService extends CoreService {
    static className = 'CustomAttributesProcessorService';

    /**
     * Process customer
     *
     * @param customer
     * @returns {*}
     */
    processCustomer(customer) {
        let customAttributes = Config.config.customer_custom_attributes;
        return this.processCustomAttributes(customer, customAttributes);
    }

    /**
     * Process address
     *
     * @param address
     * @returns {*}
     */
    processAddress(address) {
        let customAttributes = Config.config.customer_address_custom_attributes;
        return this.processCustomAttributes(address, customAttributes);
    }

    /**
     * Process custom attributes
     *
     * @param object
     * @param customAttributes
     * @returns {*}
     */
    processCustomAttributes(object, customAttributes) {
        let savedCustomAttributes = Array.isArray(object.custom_attributes) ? object.custom_attributes : [];
        customAttributes.forEach(field => {
            if (object.hasOwnProperty(field.attribute_code)) {
                let value = object[field.attribute_code];
                let customAttribute = savedCustomAttributes.find(x => x.attribute_code === field.attribute_code);
                if (customAttribute) {
                    customAttribute.value = value;
                } else {
                    savedCustomAttributes.push({attribute_code: field.attribute_code, value: value});
                }
                delete object[field.attribute_code];
            }
        });
        object.custom_attributes = savedCustomAttributes;
        return object;
    }

    /**
     * Check if attribute is custom
     *
     * @param code
     * @param type
     * @returns {boolean}
     */
    isCustomAttribute(code, type = '') {
        let customAttributes = [];
        switch (type) {
            case AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER:
                customAttributes = Config.config.customer_custom_attributes;
                break;
            case AddCustomerPopupConstant.POPUP_TYPE_ADDRESS:
                customAttributes = Config.config.customer_address_custom_attributes;
                break;
            default:
                customAttributes = [];
                break;
        }
        customAttributes = (customAttributes) ? customAttributes : [];
        let customAttribute = customAttributes.find(x => x.attribute_code === code);
        return !!customAttribute;
    }

    /**
     * Get custom attribute's value
     *
     * @param object
     * @param code
     * @returns {string}
     */
    getCustomAttributeValue(object, code) {
        let customAttributes = object.custom_attributes;
        customAttributes = customAttributes ? customAttributes : [];
        let customAttribute = customAttributes.find(x => x.attribute_code === code);
        return customAttribute ? customAttribute.value : "";
    }

    /**
     *
     * @param customer
     * @param code
     * @param value
     * @returns {*}
     */
    updateCustomerData(customer, code, value) {
        if (this.isCustomAttribute(code, AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER)) {
            let customAttributes = customer.custom_attributes;
            customAttributes = (customAttributes) ? customAttributes : [];
            let savedCustomAttribute = customAttributes.find(customAttribute => {
                return (customAttribute.attribute_code === code)
            });
            if (!savedCustomAttribute) {
                customAttributes.push({attribute_code: code, value: value});
            } else {
                savedCustomAttribute.value = value;
            }
            customer.custom_attributes = customAttributes;

            delete customer[code];
        }
        return customer;
    }
}

/**
 * @type {CustomAttributesProcessorService} customAttributesProcessorService
 */
let customAttributesProcessorService = ServiceFactory.get(CustomAttributesProcessorService);

export default customAttributesProcessorService;
