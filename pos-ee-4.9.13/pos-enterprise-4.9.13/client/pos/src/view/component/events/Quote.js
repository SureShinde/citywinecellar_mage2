import CoreContainer from "../../../framework/container/CoreContainer";
import CoreComponent from "../../../framework/component/CoreComponent";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import {listen} from "../../../event-bus";
import TotalsAction from "../../action/checkout/quote/TotalsAction";
import QuoteAction from "../../action/checkout/QuoteAction";

export class QuoteComponent extends CoreComponent {
    static className = 'QuoteComponent';

    constructor(props) {
        super(props);
        listen('service_quote_init_total_collectors', eventData => {
            this.props.initTotalCollectors(eventData.service);
        }, 'QuoteComponent');
        listen('service_quote_collect_totals_before', eventData => {
            this.props.collectTotalsBefore(eventData.quote);
        }, 'QuoteComponent');
        listen('service_quote_collect_totals_after', eventData => {
            this.props.collectTotalsAfter(eventData.quote);
        }, 'QuoteComponent');
        listen('service_quote_change_customer_after', eventData => {
            this.props.changeCustomerAfter(eventData.quote);
        }, 'QuoteComponent');
        listen('service_quote_add_product_after', eventData => {
            this.props.addProductAfter(eventData.quote);
        }, 'QuoteComponent');
        listen('service_quote_update_qty_cart_item_after', eventData => {
            this.props.updateQtyCartItemAfter(eventData.quote);
        }, 'QuoteComponent');
        listen('service_quote_remove_cart_item_after', eventData => {
            this.props.removeCartItemAfter(eventData.quote);
        }, 'QuoteComponent');
        listen('service_quote_place_order_before', eventData => {
            this.props.placeOrderBefore(eventData.quote);
        }, 'QuoteComponent');
    }

    render() {
        return (null)
    }
}

class QuoteContainer extends CoreContainer {
    static className = 'QuoteContainer';

    static mapDispatch(dispatch) {
        return {
            initTotalCollectors: (service) => dispatch(
                TotalsAction.salesQuoteInitTotalCollectors(service)
            ),
            collectTotalsBefore: (quote) => dispatch(
                TotalsAction.salesQuoteCollectTotalsBefore(quote)
            ),
            collectTotalsAfter: (quote) => dispatch(
                TotalsAction.salesQuoteCollectTotalsAfter(quote)
            ),
            changeCustomerAfter: (quote) => dispatch(
                QuoteAction.changeCustomerAfter(quote)
            ),
            addProductAfter: (quote) => dispatch(
                QuoteAction.addProductAfter(quote)
            ),
            updateQtyCartItemAfter: (quote) => dispatch(
                QuoteAction.updateQtyCartItemAfter(quote)
            ),
            removeCartItemAfter: (quote) => dispatch(
                QuoteAction.removeCartItemAfter(quote)
            ),
            placeOrderBefore: (quote) => dispatch(
                QuoteAction.placeOrderBefore(quote)
            ),
        }
    }
}

export default ContainerFactory.get(QuoteContainer).withRouter(
    ComponentFactory.get(QuoteComponent)
);
