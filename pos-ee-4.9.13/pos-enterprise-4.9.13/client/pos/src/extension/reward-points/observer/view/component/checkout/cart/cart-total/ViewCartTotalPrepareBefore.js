import {listen} from "../../../../../../../../event-bus";

export default class ViewCartTotalPrepareBefore {
    /**
     * constructor
     * @param props
     */
    constructor(props) {
        listen('view_cart_total_prepare_before', (eventData) => {
            let self = eventData.cartTotal;
            const {quote} = self.props;

            eventData.pointDiscount = -quote.rewardpoints_discount;

        }, 'ViewCartTotalPrepareBefore');
    }
}
