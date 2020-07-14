import "../../../../view/style/css/PaymentOffline.css";

/**
 * Mixin to check payment existed
 */
export default {
    /**
     * check payment extisted
     * @param paymentsSelected
     * @returns {boolean}
     */
    existedPayLater: function (paymentsSelected) {
        let PaymentOfflineService = require('../../../../service/PaymentOfflineService').default;
        let paymentData = this.props.payments;
        let existed = PaymentOfflineService.checkExistedPayLater(paymentData, paymentsSelected);
        return existed;
    }
};
