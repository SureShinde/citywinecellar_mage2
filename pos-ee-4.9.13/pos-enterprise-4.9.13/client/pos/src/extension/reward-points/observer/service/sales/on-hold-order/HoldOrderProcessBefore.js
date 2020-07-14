import {listen} from "../../../../../../event-bus";
import {RewardPointService} from "../../../../service/RewardPointService";

export default class HoldOrderProcessBefore {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('hold_order_process_before', (eventData) => {

            let order = eventData.order;
            RewardPointService.filterDataHoldOrder(order);

        }, 'HoldOrderProcessBefore');
    }
}
