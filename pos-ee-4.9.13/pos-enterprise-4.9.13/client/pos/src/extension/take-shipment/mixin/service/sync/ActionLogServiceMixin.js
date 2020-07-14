import {fire} from "../../../../../event-bus";

export default {
    /**
     * update data request create shipment
     * @param result
     */
    updateDataRequestCreateShipment(result) {
        const ResourceModelFactory = require("../../../../../framework/factory/ResourceModelFactory").default;
        const OrderResourceModel  = require("../../../../../resource-model/order/OrderResourceModel").default;

        let resource = new (ResourceModelFactory.get(OrderResourceModel))();
        resource.saveToDb([result]);
        fire('service_sync_log_order_update_data_finish', {result: result});
    }
}