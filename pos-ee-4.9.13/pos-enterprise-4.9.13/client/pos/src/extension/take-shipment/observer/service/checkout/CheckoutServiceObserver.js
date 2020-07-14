import { listen } from "../../../../../event-bus";

export default class CheckoutServiceObserver {
    /**
     *
     */
    constructor() {
        listen('service_checkout_init_create_shipment_variable_after', ({ payload }) => {
            const AppStore = require('../../../../../view/store/store').default;
            const {checkoutCreateShipmentReducer} = AppStore.getState().extension;
            payload.create_shipment = checkoutCreateShipmentReducer.isCreateShipment ? 1 : 0;
        })
    }
}