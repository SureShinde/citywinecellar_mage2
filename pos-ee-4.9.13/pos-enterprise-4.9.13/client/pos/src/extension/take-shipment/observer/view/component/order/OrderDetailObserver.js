import {listen} from "../../../../../../event-bus";

export default class OrderDetailObserver {
    constructor() {
        /**
         *  Validate order can take shipment or can not
         */
        listen('component_order_detail_component_will_receive_props_calculate_order_abilities_after', ({ abilities, nextProps }) => {
            const OrderService = require("../../../../../../service/sales/OrderService").default;
            abilities.canShip = OrderService.canShip(nextProps.order);
        })
    }
}