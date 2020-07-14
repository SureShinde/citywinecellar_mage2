import {AbstractOrderService} from "../../AbstractService";
import cloneDeep from 'lodash/cloneDeep';
import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import OrderResourceModel from "../../../../resource-model/order/OrderResourceModel";
import SyncConstant from "../../../../view/constant/SyncConstant";
import ActionLogService from "../../../sync/ActionLogService";
import CustomerResourceModel from "../../../../resource-model/customer/CustomerResourceModel";
import CustomerService from "../../../customer/CustomerService";
import CustomerConstant from "../../../../view/constant/CustomerConstant";

export class CreditmemoSuccessService extends AbstractOrderService {
    static className = 'CreditmemoSuccessService';

    /**
     * send email credit memo
     * @param increment_id
     * @param email
     * @param creditmemo_increment_id
     * @returns {Promise.<boolean>}
     */
    async sendEmailCreditmemo(increment_id, email, creditmemo_increment_id) {
        let orderResource = this.getResourceModel(OrderResourceModel);
        let url_api = orderResource.getResourceOnline().getPathSendEmailCreditmemo();
        let params = {
            creditmemo_increment_id: creditmemo_increment_id,
            email: email,
            increment_id: increment_id
        };
        await ActionLogService.createDataActionLog(
            SyncConstant.REQUEST_SEND_EMAIL_CREDITMEMO_ORDER, url_api, SyncConstant.METHOD_POST, params
        );
        return true;
    }

    /**
     * credit memo create customer
     * @param order
     * @param email
     * @param isNewAccount
     * @param newCustomer
     * @return {Promise<void>}
     */
    async creditmemoCreateCustomer(order, email, isNewAccount, newCustomer = null) {
        let orderResource = this.getResourceModel(OrderResourceModel);
        let url_api = orderResource.getResourceOnline().getPathCreditmemoCreateCustomer();
        let param_customer;
        if (isNewAccount) {
            newCustomer.tmp_customer_id = CustomerConstant.TMP_CUSTOMER_ID_PREFIX + newCustomer.id;
            param_customer = newCustomer;
        } else {
            param_customer = await CustomerService.get(email, 'email');
        }
        if (isNewAccount) {
            let customerResource = this.getResourceModel(CustomerResourceModel);
            await customerResource.createCustomer(param_customer);
        }
        let customer = isNewAccount ?
            CustomerService.convertParamsCustomer(param_customer)
            : this.convertParamsCustomer(param_customer);
        let newOrder = await this.mergeCustomerToOrder(param_customer, order.increment_id, orderResource);
        await orderResource.saveToDb([newOrder], true);
        let increment_id = order.increment_id;
        let params = {
            customer,
            increment_id,
            isNewAccount: isNewAccount
        };
        await ActionLogService.createDataActionLog(
            SyncConstant.REQUEST_CREDITMEMO_CREATE_CUSTOMER, url_api, SyncConstant.METHOD_POST, params
        );
        return newOrder;
    }

    /**
     * merge customer to order
     * @param customer
     * @param increment_id
     * @param orderResource
     * @return {Promise<void>}
     */
    async mergeCustomerToOrder(customer, increment_id, orderResource) {
        let order = await orderResource.getOrderByIncrementIdOffline(increment_id);
        order.customer_id = customer.id;
        order.customer_firstname = customer.firstname;
        order.customer_lastname = customer.lastname;
        order.customer_email = customer.email;
        order.customer_is_guest = 0;
        order.customer_group_id = customer.group_id;
        return order;
    }

    /**
     * convert params customer
     * @param customer
     * @return {*}
     */
    convertParamsCustomer(customer) {
        let new_customer = cloneDeep(customer);
        delete new_customer.full_name;
        delete new_customer.search_string;
        return new_customer;
    }
}

/** @type CreditmemoSuccessService */
let creditmemoSuccessService = ServiceFactory.get(CreditmemoSuccessService);
export default creditmemoSuccessService;
