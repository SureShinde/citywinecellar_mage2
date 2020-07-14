import {AbstractOrderService} from "../../../../../service/sales/AbstractService";
import ServiceFactory from "../../../../../framework/factory/ServiceFactory";

export class ShipmentFactoryService extends AbstractOrderService {
    static className = 'ShipmentFactoryService';

    /**
     *
     * @param item
     * @param qty
     * @return {number}
     */
    castQty(item, qty) {
        if (item.is_qty_decimal) {
            qty = Number.parseFloat(qty);
        } else {
            qty = Number.parseInt(qty, 10);
        }
        return qty > 0 ? qty : 0;
    }
}

/** @type ShipmentFactoryService */
let shipmentFactoryService = ServiceFactory.get(ShipmentFactoryService);

export default shipmentFactoryService;