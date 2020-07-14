import AbstractResourceModel from "../AbstractResourceModel";
import SyncConstant from "../../view/constant/SyncConstant";

export default class CustomerResourceModel extends AbstractResourceModel {
    static className = 'CustomerResourceModel';

    /**
     * Constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            resourceName : 'Customer',
            dataType: SyncConstant.TYPE_CUSTOMER
        };
    }

    /**
     * check email
     * @param email
     * @returns {*|{type: string, email: *}|Dexie.Promise.<any|T>|Dexie.Promise.<any>|Promise.<any>}
     */
    checkEmail(email) {
        return this.getResource().checkEmail(email);
    }

    /**
     * create customer
     * @param customer
     * @returns {Promise.<TResult>}
     */
    createCustomer(customer) {
        return this.saveToDb([customer], true).then(() => customer);
    }

    /**
     * edit customer
     * @param customer
     */
    editCustomer(customer) {
        return this.getResourceOffline().updateCustomer(customer);
    }

    /**
     * update customer
     * @param customer
     * @returns {*|Promise}
     */
    updateCustomer(customer) {
        return this.getResourceOffline().updateCustomer(customer);
    }

    /**
     * get customer by id
     * @param id
     * @param field
     */
    get(id, field = null) {
        return this.getResourceOffline().get(id, field);
    }
}
