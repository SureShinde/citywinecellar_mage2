import CoreService from "../../../service/CoreService";
import ServiceFactory from "../../../framework/factory/ServiceFactory";

export class PaymentOfflineService extends CoreService {
    static className = 'PaymentOfflineService';
    /**
     * check exist pay later
     * @param paymentData
     * @param paymentsSelected
     * @returns {boolean}
     */
    checkExistedPayLater(paymentData, paymentsSelected) {
        let paymentCanPayLater = [];
        let existed = false;
        if (paymentData && paymentData.length > 0) {
            paymentData.forEach(function(pData) {
                if (pData.is_pay_later) {
                    paymentCanPayLater.push(pData.code);
                }
            });
        }
        if (paymentCanPayLater.length > 0) {
            paymentsSelected.forEach(function(paymentSelected) {
                if (paymentCanPayLater.includes(paymentSelected.method)) {
                    existed = true;
                }
            });
        }
        return existed;
    }

}

/**
 * @type {PaymentOfflineService} paymentOfflineService
 */
let paymentOfflineService = ServiceFactory.get(PaymentOfflineService);

export default paymentOfflineService;
