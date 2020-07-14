import { listen } from "../../../../../event-bus";
import SyncConstant from "../../../../../view/constant/SyncConstant";
import ShippingConstant from "../../../view/constant/order/ShippingConstant";

export default class ActionLogServiceObserver {
    constructor() {
        /**
         *  support take shipment to log action
         */
        listen('service_action_log_init_dependent_variable_after', ({ dependent }) => {
            dependent[ShippingConstant.REQUEST_ORDER_CREATE_SHIPMENT] = [
                {
                    type: SyncConstant.TYPE_ORDER,
                    key: 'order_increment_id'
                }
            ];
        });
        /**
         *  Add take shipment to log action
         */
        listen('service_action_save_data_request_action_log_after', async ({ allData, data, action_type, result }) => {
            if (action_type === ShippingConstant.REQUEST_ORDER_CREATE_SHIPMENT) {
                const ActionLogService = require("../../../../../service/sync/ActionLogService").default;
                return await ActionLogService.updateDataRequestCreateShipment(result);
            }
        });
    }
}