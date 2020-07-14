import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import CustomerResourceModel from "../../../../resource-model/customer/CustomerResourceModel";
import {RewardPointHelper} from "../../helper/RewardPointHelper";
import Config from "../../../../config/Config";
import SyncConstant from "../../../../view/constant/SyncConstant";
import CoreService from "../../../../service/CoreService";
import NumberHelper from "../../../../helper/NumberHelper";

export class RewardPointCustomerService extends CoreService {
    static className = 'RewardPointCustomerService';
    resourceModel = CustomerResourceModel;

    /**
     * Change reward point
     *
     * @param id
     * @param point
     * @return {Promise<*>}
     */
    async rewardCustomerWithPoint(id, point) {
        if (Config.dataTypeMode && Config.dataTypeMode[SyncConstant.TYPE_CUSTOMER] === SyncConstant.OFFLINE_MODE) {
            let customerResource = this.getResourceModel();
            let customer = await customerResource.getById(id);

            if (!customer || typeof point === 'undefined') {
                return false;
            }
            let curPointBalance = (customer.extension_attributes && customer.extension_attributes.point_balance)
                ? parseFloat(customer.extension_attributes.point_balance)
                : 0;
            let newPointBalance = NumberHelper.addNumber(curPointBalance, point);
            if (
                RewardPointHelper.getEarningMaxBalance()
                && newPointBalance > RewardPointHelper.getEarningMaxBalance()
            ) {
                return customer
            }

            if (!customer.extension_attributes) {
                customer.extension_attributes = {};
            }
            customer.extension_attributes.point_balance = Math.max(newPointBalance, 0);
            return await this.updateCustomerByLoyalty(customer);
        }
        return false;
    }

    /**
     * Update customer by loyalty
     *
     * @param customer
     * @returns {Promise.<*>}
     */
    async updateCustomerByLoyalty(customer) {
        let customerResource = this.getResourceModel();
        return await customerResource.saveToDb([customer]);
    }
}

/** @type RewardPointCustomerService */
let rewardPointCustomerService = ServiceFactory.get(RewardPointCustomerService);

export default rewardPointCustomerService;
