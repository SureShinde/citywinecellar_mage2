import CoreService from "../CoreService";
import CustomerPopupService from "./CustomerPopupService";
import AddCustomerPopupConstant from "../../view/constant/customer/AddCustomerPopupConstant";
import CustomerGroupHelper from "../../helper/CustomerGroupHelper";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import Config from "../../config/Config";

export class CustomerDefaultFieldService extends CoreService {
    static className = 'CustomerDefaultFieldService';

    /**
     * default customer field
     * @returns {Array}
     */
    defaultCustomerField(component) {
        /*get config customer from API*/
        if(Config.config.customer_form){
            Config.config.customer_form.forEach(function(element) {
                if(element.visible === true){
                    let default_value = component.getValue(element.attribute_code);
                    if(element.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_TEXT){
                        let required_email = false;
                        if(element.attribute_code === AddCustomerPopupConstant.ATTRIBUTE_CODE_EMAIL){
                            required_email = true;
                        }
                        component.addFieldToArrField(
                            CustomerPopupService.createCustomerFieldInput(
                                element.attribute_code, /*code*/
                                element.attribute_code, /*ref*/
                                AddCustomerPopupConstant.TYPE_FIELD_INPUT, /*type*/
                                element.frontend_label, /*label*/
                                default_value, /*default_value*/
                                !element.required, /*optional*/
                                element.required, /*required*/
                                required_email, /*required_email*/
                                255, /*max_length*/
                                false, /*google_suggest*/
                                false /*oneRow*/
                            )
                        );
                    }
                    else if(element.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_BOOLEAN){
                        component.addFieldToArrField(
                            CustomerPopupService.createCustomerFieldCheckBox(
                                element.attribute_code, /*code*/
                                element.attribute_code, /*ref*/
                                AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX, /*type*/
                                element.frontend_label, /*label*/
                                default_value, /*check*/
                                false, /*disabled*/
                                true /*oneRow*/
                            )
                        );
                    }
                    else if(element.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_SELECT){
                        let options = element.options;
                        let key_value = 'value';
                        let key_title = 'label';
                        if(element.attribute_code === AddCustomerPopupConstant.ATTRIBUTE_CODE_GROUP_ID){
                            options = CustomerGroupHelper.getShowCustomerGroup();
                            key_value = 'id';
                            key_title = 'code';
                        }
                        component.addFieldToArrField(
                            CustomerPopupService.createCustomerFieldGroup(
                                element.attribute_code, /*code*/
                                element.attribute_code, /*ref*/
                                AddCustomerPopupConstant.TYPE_FIELD_GROUP, /*type*/
                                element.frontend_label, /*label*/
                                default_value, /*default_value*/
                                element.required, /*required*/
                                options, /*options*/
                                key_value, /*key_value*/
                                key_title, /*key_title*/
                                false, /*oneRow*/
                                !element.required /*optional*/
                            )
                        );
                    }
                    else if(element.frontend_input === AddCustomerPopupConstant.FRONTEND_INPUT_TYPE_DATE){
                        component.addFieldToArrField(
                            CustomerPopupService.createCustomerFieldDate(
                                element.attribute_code, /*code*/
                                element.attribute_code, /*ref*/
                                AddCustomerPopupConstant.TYPE_FIELD_DATE, /*type*/
                                element.frontend_label, /*label*/
                                default_value, /*default_value*/
                                element.required, /*required*/
                                false, /*oneRow*/
                                !element.required, /*optional*/
                            )
                        );
                    }
                }
            });
        }
        let telephone = (component.getValue('telephone'))? component.getValue('telephone'): component.getCustomAttributeByCode('customer_telephone');
        /*telephone*/
        component.addFieldToArrField(
            CustomerPopupService.createCustomerFieldInput(
                'telephone',
                'telephone',
                AddCustomerPopupConstant.TYPE_FIELD_INPUT,
                "Phone",
                telephone,
                false,
                true,
                false,
                255,
                false,
                false)
        );
        /*subscribe newsletter*/
        component.addFieldToArrField(
            CustomerPopupService.createCustomerFieldCheckBox(
                'subscriber_status',
                'subscriber_status',
                AddCustomerPopupConstant.TYPE_FIELD_CHECKBOX,
                "Subscribe Newsletter",
                component.props.isNewCustomer ? true : component.getValue('subscriber_status'),
                false,
                true)
        );
        return component.state.arrField;
    }
}

/** @type CustomerDefaultFieldService */
let customerDefaultFieldService = ServiceFactory.get(CustomerDefaultFieldService);

export default customerDefaultFieldService;