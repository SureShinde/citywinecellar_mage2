import OrderConstant from '../../../constant/OrderConstant';
import CustomerService from "../../../../service/customer/CustomerService";
import ConfigHelper from "../../../../helper/ConfigHelper";
import PaymentConstant from "../../../constant/PaymentConstant";
import CreditmemoConstant from "../../../constant/order/CreditmemoConstant";
import Action from "../../../action";

/**
 * Receive action type(PLACE_ORDER_AFTER | CREATE_CREDITMEMO_AFTER) and request
 * @param action$
 */
export default function UpdateLoyaltyEpic(action$) {
    return action$.ofType(OrderConstant.PLACE_ORDER_AFTER, CreditmemoConstant.CREATE_CREDITMEMO_AFTER)
        .mergeMap(async action => {
            const object = action.order || action.creditmemo;

            if (Array.isArray(object.payments)) {
                let customerId = object.customer_id;
                if (action.type === CreditmemoConstant.CREATE_CREDITMEMO_AFTER) {
                    customerId = object.order.customer_id;
                }
                let store_credit = object.payments.find((payment) => payment.method === PaymentConstant.STORE_CREDIT);
                if (ConfigHelper.isEnableStoreCredit() && customerId && store_credit) {
                    let base_amount_paid = store_credit.base_amount_paid;
                    if (action.type === CreditmemoConstant.CREATE_CREDITMEMO_AFTER) {
                        base_amount_paid = -base_amount_paid;
                    }
                    await CustomerService.updateCustomerCredit(customerId, base_amount_paid);
                }
            }

            return Action.empty();
        });
}
