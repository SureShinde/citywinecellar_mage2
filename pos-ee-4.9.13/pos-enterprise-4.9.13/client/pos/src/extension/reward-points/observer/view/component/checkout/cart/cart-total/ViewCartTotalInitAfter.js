import {listen} from "../../../../../../../../event-bus";

export default class ViewCartTotalInitAfter {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('constructor_cart_total_init_after', (eventData) => {
            eventData.showOnPages.push('SpendRewardPointComponent')

        }, 'ViewCartTotalInitAfter');
    }
}
